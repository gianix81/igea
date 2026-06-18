<?php
require __DIR__ . '/../bootstrap.php';

$code = trim((string) request_input('code', ''));
$card = $code !== '' ? (new CardService())->findByCode($code) : null;
json_response(['success' => (bool) $card, 'card' => $card, 'message' => $card ? null : 'Card non trovata.'], $card ? 200 : 404);
