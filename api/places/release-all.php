<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$pdo = db();

// Ingressi attivi di oggi: stessa logica del checkout automatico di fine giornata
$entries = $pdo->query("
    SELECT e.card_id
    FROM entries e
    WHERE e.status IN ('dentro','bloccato')
      AND e.entry_date = CURDATE()
")->fetchAll(\PDO::FETCH_ASSOC);

$svc = new EntryService();
$closed = 0;
$errors = [];
foreach ($entries as $entry) {
    try {
        $svc->checkout((int) $entry['card_id'], (int) current_user()['id'], true);
        $closed++;
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

// Prenotazioni odierne non ancora occupate: liberano il lettino senza cancellare la prenotazione
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("
        SELECT rp.id, rp.place_id
        FROM reservation_places rp
        JOIN reservations r ON r.id = rp.reservation_id
        WHERE rp.status = 'prenotato'
          AND r.usage_date = CURDATE()
          AND r.status IN ('confermata','in attesa')
    ");
    $stmt->execute();
    $pending = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $freeRp = $pdo->prepare("UPDATE reservation_places SET status = 'liberato' WHERE id = ?");
    $freePl = $pdo->prepare("UPDATE pool_places SET status = 'disponibile' WHERE id = ? AND status = 'prenotato'");
    foreach ($pending as $row) {
        $freeRp->execute([(int) $row['id']]);
        $freePl->execute([(int) $row['place_id']]);
    }

    audit_log('release_all_places', 'pool_places', null, [
        'entries_closed' => $closed,
        'reservations_freed' => count($pending),
        'errors' => $errors,
    ]);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}

json_response([
    'success' => true,
    'entries_closed' => $closed,
    'reservations_freed' => count($pending),
    'errors' => $errors,
]);
