<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id = (int) request_input('id', 0);
if (!$id) json_response(['success' => false, 'error' => 'ID mancante.']);

$pdo = db();

// Controlla se il prodotto è usato in movimenti
$used = $pdo->prepare("SELECT COUNT(*) FROM card_movements WHERE product_id = ?");
$used->execute([$id]);
if ((int) $used->fetchColumn() > 0) {
    // Soft delete: disattiva invece di eliminare
    $pdo->prepare("UPDATE products SET active = 0 WHERE id = ?")->execute([$id]);
    audit_log('deactivate_product', 'product', $id, []);
    json_response(['success' => true, 'soft' => true, 'message' => 'Prodotto disattivato (ha storico movimenti).']);
}

$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
audit_log('delete_product', 'product', $id, []);
json_response(['success' => true, 'soft' => false]);
