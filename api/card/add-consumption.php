<?php
require __DIR__ . '/../bootstrap.php';
require_role(['admin', 'bar', 'ristorazione']);

try {
    $card = (new CardService())->findByCode((string) request_input('card_code'));
    if (!$card) {
        throw new RuntimeException('Card non trovata.');
    }
    (new CardService())->requireActiveInside((int) $card['id']);
    $stmt = db()->prepare('SELECT p.*, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id WHERE p.id = ? AND p.active = 1');
    $stmt->execute([(int) request_input('product_id')]);
    $product = $stmt->fetch();
    if (!$product) {
        throw new RuntimeException('Prodotto non valido.');
    }
    $qty = max(1, (float) request_input('quantity', 1));
    $total = round($qty * (float) $product['price'], 2);
    $insert = db()->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, product_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id) VALUES (?, ?, ?, ?, 'charge', ?, ?, ?, ?, ?, 'open', ?)");
    $insert->execute([(int) $card['id'], (int) $card['customer_id'], (int) $card['active_entry_id'], (int) $product['id'], $product['department'], $product['name'], $qty, (float) $product['price'], $total, current_user()['id']]);
    $balance = (new BalanceService())->calculate((int) $card['id']);
    audit_log('api_charge', 'card', (int) $card['id'], ['product_id' => $product['id'], 'total' => $total]);
    json_response(['success' => true, 'balance' => $balance, 'line_total' => $total]);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 422);
}
