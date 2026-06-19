<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id     = (int)    request_input('id', 0);
$status = (string) request_input('status', '');
$notes  = trim((string) request_input('notes', ''));

if (!$id) json_response(['success' => false, 'error' => 'ID mancante.']);

$valid = ['attiva', 'chiusa', 'bloccata', 'smarrita'];
if (!in_array($status, $valid, true)) {
    json_response(['success' => false, 'error' => 'Stato non valido.']);
}

db()->prepare("UPDATE cards SET status=?, notes=? WHERE id=?")
    ->execute([$status, $notes ?: null, $id]);

audit_log('update_card', 'card', $id, ['status' => $status]);
json_response(['success' => true]);
