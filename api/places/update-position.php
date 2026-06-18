<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$placeId = (int) request_input('place_id', 0);
$posRow  = (int) request_input('pos_row', 0);
$posCol  = (int) request_input('pos_col', 0);

if (!$placeId) {
    json_response(['success' => false, 'error' => 'ID lettino mancante.']);
}
if ($posRow < 0 || $posRow > 99 || $posCol < 0 || $posCol > 99) {
    json_response(['success' => false, 'error' => 'Posizione non valida.']);
}

$pdo = db();

// Impedisce collisioni: la cella di destinazione deve essere libera
$occupied = $pdo->prepare("SELECT id FROM pool_places WHERE pos_row = ? AND pos_col = ? AND id <> ?");
$occupied->execute([$posRow, $posCol, $placeId]);
if ($occupied->fetch()) {
    json_response(['success' => false, 'error' => 'Cella già occupata.']);
}

$pdo->prepare("UPDATE pool_places SET pos_row = ?, pos_col = ? WHERE id = ?")
    ->execute([$posRow, $posCol, $placeId]);

audit_log('move_place', 'pool_place', $placeId, ['pos_row' => $posRow, 'pos_col' => $posCol]);
json_response(['success' => true]);
