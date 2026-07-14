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

$pdo = db();

$card = $pdo->prepare('SELECT id, customer_id FROM cards WHERE id = ?');
$card->execute([$cardId]);
$card = $card->fetch();
if (!$card) {
    json_response(['success' => false, 'error' => 'Card non trovata.']);
}
if ((int) $card['customer_id'] === $customerId) {
    json_response(['success' => false, 'error' => 'Il cliente è già l\'intestatario di questa card.']);
}

$cust = $pdo->prepare("SELECT id FROM customers WHERE id = ? AND status = 'attivo'");
$cust->execute([$customerId]);
if (!$cust->fetch()) {
    json_response(['success' => false, 'error' => 'Cliente non trovato.']);
}

try {
    $pdo->prepare('INSERT INTO card_members (card_id, customer_id) VALUES (?, ?)')->execute([$cardId, $customerId]);
    audit_log('add_family_member', 'card', $cardId, ['customer_id' => $customerId]);
    json_response(['success' => true]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23000') {
        json_response(['success' => false, 'error' => 'Il cliente è già nel nucleo di questa card.']);
    }
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
