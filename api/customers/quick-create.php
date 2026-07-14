<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$firstName = trim((string) request_input('first_name', ''));
$lastName  = trim((string) request_input('last_name', ''));
$phone     = trim((string) request_input('phone', ''));

if ($firstName === '' || $lastName === '') {
    json_response(['success' => false, 'error' => 'Nome e cognome sono obbligatori.']);
}

$pdo = db();
$pdo->prepare('INSERT INTO customers (first_name, last_name, phone, notes) VALUES (?, ?, ?, ?)')
    ->execute([$firstName, $lastName, $phone ?: null, 'Creato al volo da una prenotazione. Da completare con dati anagrafici e privacy alla prima visita.']);
$id = (int) $pdo->lastInsertId();

audit_log('quick_create_customer', 'customer', $id, ['first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone]);

json_response(['success' => true, 'customer' => [
    'id'         => $id,
    'first_name' => $firstName,
    'last_name'  => $lastName,
    'phone'      => $phone,
    'birth_date' => null,
]]);
