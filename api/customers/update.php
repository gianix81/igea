<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$id          = (int)    request_input('id', 0);
$firstName   = trim((string) request_input('first_name', ''));
$lastName    = trim((string) request_input('last_name', ''));
$phone       = trim((string) request_input('phone', ''));
$email       = trim((string) request_input('email', ''));
$fiscalCode  = trim((string) request_input('fiscal_code', ''));
$birthDate   = request_input('birth_date', '') ?: null;
$address     = trim((string) request_input('address', ''));
$privacy     = (int) (bool) request_input('privacy_consent', 0);
$status      = (string) request_input('status', 'attivo');
$notes       = trim((string) request_input('notes', ''));

if (!$id)        json_response(['success' => false, 'error' => 'ID mancante.']);
if (!$firstName) json_response(['success' => false, 'error' => 'Nome obbligatorio.']);
if (!$lastName)  json_response(['success' => false, 'error' => 'Cognome obbligatorio.']);

if (!in_array($status, ['attivo', 'sospeso', 'blacklist'], true)) $status = 'attivo';

db()->prepare("
    UPDATE customers
    SET first_name=?, last_name=?, phone=?, email=?, fiscal_code=?,
        birth_date=?, address=?, privacy_consent=?, status=?, notes=?
    WHERE id=?
")->execute([$firstName, $lastName, $phone, $email, $fiscalCode,
             $birthDate, $address, $privacy, $status, $notes ?: null, $id]);

audit_log('update_customer', 'customer', $id, [
    'name' => "$firstName $lastName", 'status' => $status,
]);

json_response(['success' => true]);
