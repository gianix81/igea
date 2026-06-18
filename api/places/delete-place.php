<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin']);

$placeId = (int) request_input('place_id', 0);
if (!$placeId) {
    json_response(['success' => false, 'error' => 'ID mancante.']);
}

$pdo = db();

$place = $pdo->prepare("SELECT id, code FROM pool_places WHERE id = ?");
$place->execute([$placeId]);
$place = $place->fetch();
if (!$place) {
    json_response(['success' => false, 'error' => 'Lettino non trovato.']);
}

// Blocca se ha prenotazioni attive future
$activeRes = $pdo->prepare("
    SELECT COUNT(*) FROM reservation_places rp
    JOIN reservations r ON r.id = rp.reservation_id
    WHERE rp.place_id = ? AND r.status IN ('confermata','in attesa') AND r.usage_date >= CURDATE()
");
$activeRes->execute([$placeId]);
if ((int) $activeRes->fetchColumn() > 0) {
    json_response(['success' => false, 'error' => 'Impossibile eliminare: il lettino ha prenotazioni attive future.']);
}

// Blocca se occupato oggi
$activeEntry = $pdo->prepare("
    SELECT COUNT(*) FROM entry_places ep
    JOIN entries e ON e.id = ep.entry_id
    WHERE ep.place_id = ? AND ep.status = 'assegnato' AND e.entry_date = CURDATE() AND e.status = 'dentro'
");
$activeEntry->execute([$placeId]);
if ((int) $activeEntry->fetchColumn() > 0) {
    json_response(['success' => false, 'error' => 'Impossibile eliminare: il lettino è attualmente occupato.']);
}

try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM entry_places WHERE place_id = ?")->execute([$placeId]);
    $pdo->prepare("DELETE FROM reservation_places WHERE place_id = ?")->execute([$placeId]);
    $pdo->prepare("DELETE FROM pool_places WHERE id = ?")->execute([$placeId]);
    audit_log('delete_place', 'pool_place', $placeId, ['code' => $place['code']]);
    $pdo->commit();
    json_response(['success' => true, 'code' => $place['code']]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
