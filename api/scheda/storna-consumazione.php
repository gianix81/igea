<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'bar', 'ristorazione', 'reception', 'cassa']);

$movId = (int) request_input('movement_id', 0);
if (!$movId) json_response(['success' => false, 'error' => 'ID mancante.']);

$pdo = db();
$mov = $pdo->prepare("SELECT id, card_id, status, department FROM card_movements WHERE id = ?");
$mov->execute([$movId]);
$mov = $mov->fetch();

if (!$mov)                        json_response(['success' => false, 'error' => 'Movimento non trovato.']);
if ($mov['status'] === 'cancelled') json_response(['success' => false, 'error' => 'Già stornato.']);

$pdo->prepare("UPDATE card_movements SET status = 'cancelled', cancelled_at = NOW(), cancelled_by = ? WHERE id = ?")
    ->execute([current_user()['id'], $movId]);

(new BalanceService())->calculate((int) $mov['card_id']);
audit_log('storno_consumazione', 'card_movement', $movId, []);

json_response(['success' => true]);
