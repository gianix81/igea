<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$resId    = (int)    request_input('reservation_id', 0);
$custId   = (int)    request_input('customer_id', 0);
$date     = trim((string) request_input('usage_date', date('Y-m-d')));
$timeSlot = (string) request_input('time_slot', 'intera giornata');
$status   = (string) request_input('res_status', 'confermata');
$people   = max(1, (int) request_input('people_count', 1));
$total    = (float)  request_input('total_amount', 0);
$notes    = trim((string) request_input('notes', ''));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

// Nuovi posti (opzionale — solo se si vuole cambiare la selezione)
$newPlaceIds = array_values(array_unique(array_filter(
    array_map('intval', (array) ($_POST['place_ids[]'] ?? $_POST['place_ids'] ?? []))
)));
$changingSeats = !empty($newPlaceIds);

if (!$resId || !$custId) {
    json_response(['success' => false, 'error' => 'Dati mancanti.']);
}

$pdo = db();

$resStmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
$resStmt->execute([$resId]);
$res = $resStmt->fetch();
if (!$res) {
    json_response(['success' => false, 'error' => 'Prenotazione non trovata.']);
}

$pdo->beginTransaction();
try {
    $pdo->prepare("
        UPDATE reservations
           SET customer_id   = ?,
               usage_date    = ?,
               time_slot     = ?,
               people_count  = ?,
               status        = ?,
               total_amount  = ?,
               notes         = ?
         WHERE id = ?
    ")->execute([$custId, $date, $timeSlot, $people, $status, $total, $notes, $resId]);

    if ($changingSeats) {
        // Libera i vecchi posti
        $oldRp = $pdo->prepare("SELECT place_id FROM reservation_places WHERE reservation_id = ? AND status = 'prenotato'");
        $oldRp->execute([$resId]);
        $freeOld = $pdo->prepare("UPDATE pool_places SET status = 'disponibile' WHERE id = ? AND status = 'prenotato'");
        foreach ($oldRp->fetchAll() as $row) {
            $freeOld->execute([$row['place_id']]);
        }
        $pdo->prepare("DELETE FROM reservation_places WHERE reservation_id = ?")->execute([$resId]);

        // Controlla conflitti per i nuovi posti
        $in = implode(',', $newPlaceIds);
        $conflicts = $pdo->prepare("
            SELECT rp.place_id FROM reservation_places rp
            JOIN reservations r ON r.id = rp.reservation_id
            WHERE rp.place_id IN ($in)
              AND r.usage_date = ?
              AND r.id <> ?
              AND r.status IN ('confermata','in attesa')
              AND rp.status = 'prenotato'
        ");
        $conflicts->execute([$date, $resId]);
        if ($conflicts->fetchColumn() !== false) {
            $pdo->rollBack();
            json_response(['success' => false, 'error' => 'Uno o più lettini sono già prenotati per questa data.']);
        }

        // Assegna i nuovi posti
        $newPlaces = $pdo->query("SELECT * FROM pool_places WHERE id IN ($in)")->fetchAll();
        $priceSvc = new PriceListService();
        $insRp = $pdo->prepare("INSERT INTO reservation_places (reservation_id, place_id, price, status) VALUES (?, ?, ?, 'prenotato')");
        $updPl = $pdo->prepare("UPDATE pool_places SET status = 'prenotato' WHERE id = ? AND status = 'disponibile'");
        foreach ($newPlaces as $pl) {
            $price = $priceSvc->seatPrice($pl['type'], $date) ?? (float) $pl['base_price'];
            $insRp->execute([$resId, (int)$pl['id'], $price]);
            $updPl->execute([(int)$pl['id']]);
        }
    }

    audit_log('edit_reservation', 'reservation', $resId, ['customer_id' => $custId, 'date' => $date, 'status' => $status]);
    $pdo->commit();
    json_response(['success' => true, 'reservation_id' => $resId]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
