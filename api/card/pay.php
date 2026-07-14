<?php
require __DIR__ . '/../bootstrap.php';
require_role(['admin', 'cassa']);

try {
    $card = (new CardService())->findByCode((string) request_input('card_code'));
    if (!$card) {
        throw new RuntimeException('Card non trovata.');
    }
    $confirmed = (string) request_input('outcome', 'ok') !== 'non_ok';
    (new PaymentService())->pay((int) $card['id'], (float) request_input('amount'), (string) request_input('payment_method', 'contanti'), (string) request_input('reason', 'saldo finale'), current_user()['id'], request_input('notes'), $confirmed);
    $balance = (new BalanceService())->calculate((int) $card['id']);
    json_response(['success' => true, 'balance' => $balance, 'confirmed' => $confirmed]);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 422);
}
