<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$cardId     = (int) request_input('card_id', 0);
$customerId = (int) request_input('customer_id', 0);

if (!$cardId || !$customerId) {
    json_response(['success' => false, 'error' => 'Dati mancanti.']);
}

$pdo  = db();
$stmt = $pdo->prepare('DELETE FROM card_members WHERE card_id = ? AND customer_id = ?');
$stmt->execute([$cardId, $customerId]);

if ($stmt->rowCount() === 0) {
    json_response(['success' => false, 'error' => 'Membro non trovato.']);
}

audit_log('remove_family_member', 'card', $cardId, ['customer_id' => $customerId]);
json_response(['success' => true]);
