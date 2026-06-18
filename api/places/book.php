<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$customerId = (int) request_input('customer_id', 0);
$date       = trim((string) request_input('date', date('Y-m-d')));
$timeSlot   = request_input('time_slot', 'intera giornata');
$notes      = request_input('notes', '');
$status     = request_input('res_status', 'confermata');
$people     = max(1, (int) request_input('people_count', 1));
$total      = (float) request_input('total_amount', 0);

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

// Supporta sia place_ids[] (multi) che place_id (singolo, retrocompatibilità)
$placeIds = array_filter(array_map('intval', (array) ($_POST['place_ids[]'] ?? $_POST['place_ids'] ?? [])));
if (empty($placeIds)) {
    $single = (int) request_input('place_id', 0);
    if ($single) $placeIds = [$single];
}
$placeIds = array_values(array_unique($placeIds));

if (!$customerId || empty($placeIds)) {
    json_response(['success' => false, 'error' => 'Dati mancanti (cliente o lettini).']);
}

$pdo = db();

// Carica i posti e controlla conflitti
$in  = implode(',', $placeIds);
$places = $pdo->query("SELECT * FROM pool_places WHERE id IN ($in)")->fetchAll();
if (count($places) !== count($placeIds)) {
    json_response(['success' => false, 'error' => 'Uno o più lettini non trovati.']);
}

$conflictCheck = $pdo->prepare("
    SELECT rp.place_id FROM reservation_places rp
    JOIN reservations r ON r.id = rp.reservation_id
    WHERE rp.place_id IN ($in)
      AND r.usage_date = ?
      AND r.status IN ('confermata','in attesa')
      AND rp.status = 'prenotato'
");
$conflictCheck->execute([$date]);
$conflicts = $conflictCheck->fetchAll(\PDO::FETCH_COLUMN);
if (!empty($conflicts)) {
    $codes = array_column(array_filter($places, fn($p) => in_array((int)$p['id'], array_map('intval', $conflicts))), 'code');
    json_response(['success' => false, 'error' => 'Lettini già prenotati per questa data: ' . implode(', ', $codes)]);
}

$pdo->beginTransaction();
try {
    $code = 'PRE' . date('YmdHis') . random_int(10, 99);
    $pdo->prepare("
        INSERT INTO reservations
          (reservation_code, customer_id, reservation_date, usage_date, time_slot, people_count, status, total_amount, notes, created_by)
        VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?)
    ")->execute([$code, $customerId, $date, $timeSlot, $people, $status, $total, $notes, current_user()['id']]);
    $reservationId = (int) $pdo->lastInsertId();

    $insRp = $pdo->prepare("INSERT INTO reservation_places (reservation_id, place_id, price, status) VALUES (?, ?, ?, 'prenotato')");
    $updPl = $pdo->prepare("UPDATE pool_places SET status = 'prenotato' WHERE id = ? AND status = 'disponibile'");
    foreach ($places as $pl) {
        $insRp->execute([$reservationId, (int)$pl['id'], (float)$pl['base_price']]);
        $updPl->execute([(int)$pl['id']]);
    }

    audit_log('book_places', 'reservation', $reservationId, ['place_ids' => $placeIds, 'date' => $date]);
    $pdo->commit();
    json_response(['success' => true, 'reservation_id' => $reservationId, 'code' => $code]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
