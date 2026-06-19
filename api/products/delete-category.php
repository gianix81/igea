<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id = (int) request_input('id', 0);
if (!$id) json_response(['success' => false, 'error' => 'ID mancante.']);

$pdo = db();

$count = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$count->execute([$id]);
if ((int) $count->fetchColumn() > 0) {
    json_response(['success' => false, 'error' => 'Impossibile eliminare: la categoria contiene prodotti. Spostali prima.']);
}

$pdo->prepare("DELETE FROM product_categories WHERE id = ?")->execute([$id]);
audit_log('delete_category', 'product_category', $id, []);
json_response(['success' => true]);
