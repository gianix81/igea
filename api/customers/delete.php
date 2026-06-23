<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin']);

$id = (int) request_input('id', 0);
if (!$id) json_response(['success' => false, 'error' => 'ID mancante.']);

$pdo = db();
$customer = $pdo->prepare("SELECT * FROM customers WHERE id=?")->execute([$id])
    ? $pdo->query("SELECT * FROM customers WHERE id=$id")->fetch()
    : null;
if (!$customer) json_response(['success' => false, 'error' => 'Cliente non trovato.']);

$openBalance = (float) $pdo->query("SELECT COALESCE(SUM(current_balance),0) FROM cards WHERE customer_id=$id AND current_balance > 0.001")->fetchColumn();
if ($openBalance > 0) {
    json_response(['success' => false, 'error' => "Impossibile eliminare: il cliente ha un saldo aperto di € " . number_format($openBalance, 2, ',', '.')]);
}

$isInside = (int) $pdo->query("SELECT COUNT(*) FROM cards WHERE customer_id=$id AND is_inside=1")->fetchColumn();
if ($isInside > 0) {
    json_response(['success' => false, 'error' => 'Impossibile eliminare: il cliente è attualmente dentro la struttura.']);
}

$pdo->beginTransaction();
try {
    $pdo->prepare("UPDATE cards SET status='chiusa' WHERE customer_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM customers WHERE id=?")->execute([$id]);
    audit_log('delete_customer', 'customer', $id, ['name' => $customer['first_name'] . ' ' . $customer['last_name']]);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}

json_response(['success' => true]);
