<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id           = (int)   request_input('id', 0);
$categoryId   = (int)   request_input('category_id', 0);
$name         = trim((string) request_input('name', ''));
$price        = (float) request_input('price', 0);
$vatRate      = request_input('vat_rate', null);
$vatRate      = ($vatRate !== null && $vatRate !== '') ? (float) $vatRate : null;
$stockEnabled = (int)   (bool) request_input('stock_enabled', 0);
$stockQty     = request_input('stock_qty', null);
$stockQty     = ($stockEnabled && $stockQty !== null && $stockQty !== '') ? (float) $stockQty : null;
$active       = (int)   (bool) request_input('active', 1);
$notes        = trim((string) request_input('notes', ''));

if (!$name)       json_response(['success' => false, 'error' => 'Nome obbligatorio.']);
if (!$categoryId) json_response(['success' => false, 'error' => 'Categoria obbligatoria.']);
if ($price < 0)   json_response(['success' => false, 'error' => 'Prezzo non valido.']);

$pdo = db();

$cat = $pdo->prepare("SELECT id FROM product_categories WHERE id = ? AND active = 1");
$cat->execute([$categoryId]);
if (!$cat->fetch()) json_response(['success' => false, 'error' => 'Categoria non trovata.']);

if ($id > 0) {
    $pdo->prepare("
        UPDATE products SET category_id=?, name=?, price=?, vat_rate=?,
               stock_enabled=?, stock_qty=?, active=?, notes=?
        WHERE id=?
    ")->execute([$categoryId, $name, $price, $vatRate, $stockEnabled, $stockQty, $active, $notes ?: null, $id]);
    audit_log('update_product', 'product', $id, ['name' => $name, 'price' => $price]);
    json_response(['success' => true, 'id' => $id]);
} else {
    $pdo->prepare("
        INSERT INTO products (category_id, name, price, vat_rate, stock_enabled, stock_qty, active, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ")->execute([$categoryId, $name, $price, $vatRate, $stockEnabled, $stockQty, $active, $notes ?: null]);
    $newId = (int) $pdo->lastInsertId();
    audit_log('create_product', 'product', $newId, ['name' => $name, 'price' => $price]);
    json_response(['success' => true, 'id' => $newId]);
}
