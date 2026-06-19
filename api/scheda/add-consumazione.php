<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'bar', 'ristorazione', 'reception']);

$entryId   = (int)    request_input('entry_id', 0);
$cardId    = (int)    request_input('card_id', 0);
$dept      = (string) request_input('department', '');
$desc      = trim((string) request_input('description', ''));
$qty       = max(0.5, (float) request_input('quantity', 1));
$price     = (float)  request_input('price', 0);
$productId = (int)    request_input('product_id', 0) ?: null;

if (!$entryId || !$cardId)                          json_response(['success' => false, 'error' => 'Dati mancanti.']);
if (!in_array($dept, ['bar', 'ristorante'], true))  json_response(['success' => false, 'error' => 'Reparto non valido.']);
if ($desc === '')                                    json_response(['success' => false, 'error' => 'Descrizione obbligatoria.']);
if ($price <= 0)                                    json_response(['success' => false, 'error' => 'Prezzo non valido.']);

$pdo = db();

$eRow = $pdo->prepare("SELECT id, customer_id FROM entries WHERE id = ? AND status = 'dentro'");
$eRow->execute([$entryId]);
$eRow = $eRow->fetch();
if (!$eRow) json_response(['success' => false, 'error' => 'Ingresso non attivo.']);

$total = round($qty * $price, 2);

$pdo->prepare("
    INSERT INTO card_movements
        (card_id, customer_id, entry_id, product_id, movement_type, department,
         description, quantity, unit_price, total_amount, status, operator_id)
    VALUES (?, ?, ?, ?, 'charge', ?, ?, ?, ?, ?, 'open', ?)
")->execute([
    $cardId, (int)$eRow['customer_id'], $entryId, $productId,
    $dept, $desc, $qty, $price, $total,
    current_user()['id'],
]);

$movId = (int) $pdo->lastInsertId();
(new BalanceService())->calculate($cardId);
audit_log('add_consumazione_scheda', 'card_movement', $movId, [
    'dept' => $dept, 'desc' => $desc, 'qty' => $qty, 'total' => $total,
]);

json_response(['success' => true, 'id' => $movId, 'total' => $total]);
