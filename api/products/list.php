<?php
require __DIR__ . '/../bootstrap.php';

$stmt = db()->query('SELECT p.*, pc.name category_name, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id WHERE p.active = 1 ORDER BY pc.department, pc.name, p.name');
json_response(['success' => true, 'products' => $stmt->fetchAll()]);
