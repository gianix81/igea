<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'bar', 'ristorazione', 'reception', 'cassa']);

$entryId = (int)    request_input('entry_id', 0);
$cardId  = (int)    request_input('card_id', 0);
$amount  = (float)  request_input('amount', 0);
$method  = (string) request_input('payment_method', 'contanti');
$note    = trim((string) request_input('note', ''));

if (!$entryId || !$cardId) json_response(['success' => false, 'error' => 'Dati mancanti.']);
if ($amount <= 0)          json_response(['success' => false, 'error' => 'Importo non valido.']);

if (!in_array($method, ['contanti', 'carta', 'bonifico', 'satispay', 'altro'], true)) {
    $method = 'contanti';
}

$pdo = db();

$eRow = $pdo->prepare("SELECT id, customer_id FROM entries WHERE id = ?");
$eRow->execute([$entryId]);
$eRow = $eRow->fetch();
if (!$eRow) json_response(['success' => false, 'error' => 'Ingresso non trovato.']);

$desc = 'Acconto scheda consumazioni' . ($note ? ': ' . $note : '');

$pdo->beginTransaction();
try {
    $pdo->prepare("
        INSERT INTO payments (card_id, customer_id, entry_id, amount, payment_method, reason, operator_id, notes)
        VALUES (?, ?, ?, ?, ?, 'consumazioni', ?, ?)
    ")->execute([$cardId, (int)$eRow['customer_id'], $entryId, $amount, $method, current_user()['id'], $note ?: null]);

    $payId = (int) $pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO card_movements
            (card_id, customer_id, entry_id, movement_type, department,
             description, quantity, unit_price, total_amount, status, operator_id)
        VALUES (?, ?, ?, 'payment', 'cassa', ?, 1, ?, ?, 'paid', ?)
    ")->execute([
        $cardId, (int)$eRow['customer_id'], $entryId,
        $desc, $amount, $amount, current_user()['id'],
    ]);

    (new BalanceService())->calculate($cardId);
    $pdo->commit();

    audit_log('acconto_scheda', 'payment', $payId, ['amount' => $amount, 'method' => $method]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}

json_response(['success' => true]);
