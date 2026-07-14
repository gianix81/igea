<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id     = (int)   request_input('id', 0);
$price  = (float) request_input('price', -1);
$active = (int)   (bool) request_input('active', 1);

if (!$id)      json_response(['success' => false, 'error' => 'Tariffa non trovata.']);
if ($price < 0) json_response(['success' => false, 'error' => 'Prezzo non valido.']);

$pdo  = db();
$stmt = $pdo->prepare('SELECT id, code FROM price_list WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) json_response(['success' => false, 'error' => 'Tariffa non trovata.']);

$pdo->prepare('UPDATE price_list SET price = ?, active = ? WHERE id = ?')->execute([$price, $active, $id]);
audit_log('update_price_list', 'price_list', $id, ['code' => $row['code'], 'price' => $price, 'active' => $active]);
json_response(['success' => true]);
