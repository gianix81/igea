<?php
require __DIR__ . '/../bootstrap.php';

$cardId = (int) request_input('card_id', 0);
$stmt = db()->prepare('SELECT * FROM payments WHERE card_id = ? ORDER BY created_at DESC');
$stmt->execute([$cardId]);
json_response(['success' => true, 'payments' => $stmt->fetchAll()]);
