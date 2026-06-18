<?php
require __DIR__ . '/../bootstrap.php';

$cardId = (int) request_input('card_id', 0);
if ($cardId <= 0) {
    json_response(['success' => false, 'message' => 'card_id mancante.'], 422);
}
$balance = (new BalanceService())->calculate($cardId);
json_response(['success' => true, 'balance' => $balance, 'formatted' => money($balance)]);
