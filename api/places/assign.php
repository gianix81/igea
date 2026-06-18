<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$placeId  = (int) request_input('place_id', 0);
$cardCode = trim((string) request_input('card_code', ''));

if (!$placeId || $cardCode === '') {
    json_response(['success' => false, 'error' => 'Dati mancanti.']);
}

$card = (new CardService())->findByCode($cardCode);
if (!$card) {
    json_response(['success' => false, 'error' => 'Card non trovata.']);
}
if ($card['status'] !== 'attiva') {
    json_response(['success' => false, 'error' => 'Card non attiva.']);
}

$pdo = db();

$occupied = $pdo->prepare("
    SELECT COUNT(*) FROM entry_places ep
    JOIN entries e ON e.id = ep.entry_id
    WHERE ep.place_id = ? AND ep.status = 'assegnato' AND e.entry_date = CURDATE() AND e.status = 'dentro'
");
$occupied->execute([$placeId]);
if ($occupied->fetchColumn() > 0) {
    json_response(['success' => false, 'error' => 'Il lettino è già occupato.']);
}

$existingEntry = $pdo->prepare("SELECT * FROM entries WHERE card_id = ? AND entry_date = CURDATE() AND status = 'dentro' LIMIT 1");
$existingEntry->execute([(int) $card['id']]);
$entry = $existingEntry->fetch();

$pdo->beginTransaction();
try {
    if (!$entry) {
        $pdo->prepare("INSERT INTO entries (customer_id, card_id, entry_date, checkin_at, status, people_count, entry_fee, paid_amount, notes, created_by) VALUES (?, ?, CURDATE(), NOW(), 'dentro', 1, 0, 0, 'Assegnazione lettino da mappa', ?)")
            ->execute([(int) $card['customer_id'], (int) $card['id'], current_user()['id']]);
        $entryId = (int) $pdo->lastInsertId();
        $pdo->prepare("UPDATE cards SET is_inside = 1, active_entry_id = ? WHERE id = ?")
            ->execute([$entryId, (int) $card['id']]);
    } else {
        $entryId = (int) $entry['id'];
    }

    $pdo->prepare("INSERT INTO entry_places (entry_id, place_id, status) VALUES (?, ?, 'assegnato')")
        ->execute([$entryId, $placeId]);
    $pdo->prepare("UPDATE pool_places SET status = 'occupato' WHERE id = ?")
        ->execute([$placeId]);

    // Mark any pending reservation for today as occupied
    $resPlace = $pdo->prepare("
        SELECT rp.id FROM reservation_places rp
        JOIN reservations r ON r.id = rp.reservation_id
        WHERE rp.place_id = ? AND r.usage_date = CURDATE() AND r.status IN ('confermata','in attesa') AND rp.status = 'prenotato'
        LIMIT 1
    ");
    $resPlace->execute([$placeId]);
    if ($rpRow = $resPlace->fetch()) {
        $pdo->prepare("UPDATE reservation_places SET status = 'occupato' WHERE id = ?")
            ->execute([(int) $rpRow['id']]);
    }

    audit_log('assign_place', 'entry', $entryId, ['place_id' => $placeId, 'card_code' => $cardCode]);
    $pdo->commit();
    json_response(['success' => true, 'entry_id' => $entryId]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
