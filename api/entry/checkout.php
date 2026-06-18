<?php
require __DIR__ . '/../bootstrap.php';
require_role(['admin', 'reception', 'cassa']);

try {
    $card = (new CardService())->findByCode((string) request_input('card_code'));
    if (!$card) {
        throw new RuntimeException('Card non trovata.');
    }
    json_response((new EntryService())->checkout((int) $card['id'], current_user()['id']));
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 422);
}
