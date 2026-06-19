<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id         = (int)    request_input('id', 0);
$name       = trim((string) request_input('name', ''));
$department = (string) request_input('department', 'bar');
$active     = (int)    (bool) request_input('active', 1);

if (!$name) json_response(['success' => false, 'error' => 'Nome obbligatorio.']);
if (!in_array($department, ['bar','ristorante','reception','extra'], true)) {
    json_response(['success' => false, 'error' => 'Reparto non valido.']);
}

$pdo = db();

if ($id > 0) {
    $pdo->prepare("UPDATE product_categories SET name=?, department=?, active=? WHERE id=?")
        ->execute([$name, $department, $active, $id]);
    audit_log('update_category', 'product_category', $id, ['name' => $name]);
    json_response(['success' => true, 'id' => $id]);
} else {
    $pdo->prepare("INSERT INTO product_categories (name, department, active) VALUES (?, ?, ?)")
        ->execute([$name, $department, $active]);
    $newId = (int) $pdo->lastInsertId();
    audit_log('create_category', 'product_category', $newId, ['name' => $name]);
    json_response(['success' => true, 'id' => $newId]);
}
