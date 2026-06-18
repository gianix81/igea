<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$reservationId = (int) request_input('reservation_id', 0);
if (!$reservationId) {
    json_response(['success' => false, 'error' => 'Dati mancanti.']);
}

$pdo = db();
$pdo->beginTransaction();
try {
    $placesStmt = $pdo->prepare("SELECT place_id FROM reservation_places WHERE reservation_id = ?");
    $placesStmt->execute([$reservationId]);
    $placeIds = array_column($placesStmt->fetchAll(), 'place_id');

    $pdo->prepare("UPDATE reservations SET status = 'cancellata' WHERE id = ?")
        ->execute([$reservationId]);
    $pdo->prepare("UPDATE reservation_places SET status = 'cancellato' WHERE reservation_id = ?")
        ->execute([$reservationId]);

    foreach ($placeIds as $pid) {
        $inUse = $pdo->prepare("
            SELECT COUNT(*) FROM entry_places ep
            JOIN entries e ON e.id = ep.entry_id
            WHERE ep.place_id = ? AND ep.status = 'assegnato' AND e.status = 'dentro'
        ");
        $inUse->execute([(int) $pid]);
        if ($inUse->fetchColumn() == 0) {
            $pdo->prepare("UPDATE pool_places SET status = 'disponibile' WHERE id = ?")
                ->execute([(int) $pid]);
        }
    }

    audit_log('cancel_reservation', 'reservation', $reservationId);
    $pdo->commit();
    json_response(['success' => true]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
