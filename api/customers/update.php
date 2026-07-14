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
$docType     = (string) request_input('doc_type', '');
$docNumber   = strtoupper(trim((string) request_input('doc_number', '')));
$docExpiry   = request_input('doc_expiry', '') ?: null;
$docIssuer   = trim((string) request_input('doc_issuer', ''));
$photoData   = (string) request_input('photo_data', '');
$clearPhoto  = (bool) request_input('clear_photo', 0);
$sigData     = (string) request_input('signature_data', '');
$clearSig    = (bool) request_input('clear_signature', 0);

if (!$id)        json_response(['success' => false, 'error' => 'ID mancante.']);
if (!$firstName) json_response(['success' => false, 'error' => 'Nome obbligatorio.']);
if (!$lastName)  json_response(['success' => false, 'error' => 'Cognome obbligatorio.']);

$validDocTypes = ['carta_identita', 'passaporto', 'patente', 'permesso_soggiorno'];
if ($docType && !in_array($docType, $validDocTypes, true)) $docType = '';
if (!in_array($status, ['attivo', 'sospeso', 'blacklist'], true)) $status = 'attivo';

$pdo = db();
$current = $pdo->prepare("SELECT photo_path FROM customers WHERE id=?")->execute([$id]) ? $pdo->query("SELECT photo_path FROM customers WHERE id=$id")->fetch() : null;

// Gestione foto
$newPhotoPath = false; // false = non cambiata
if ($clearPhoto) {
    $newPhotoPath = null;
} elseif ($photoData && str_starts_with($photoData, 'data:image/')) {
    $uploadsDir = is_dir(__DIR__ . '/../../public')
        ? __DIR__ . '/../../public/uploads/customers/'
        : __DIR__ . '/../../uploads/customers/';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
    $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $photoData));
    if ($imgData !== false && strlen($imgData) > 100) {
        $fname = 'c_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
        file_put_contents($uploadsDir . $fname, $imgData);
        $newPhotoPath = 'uploads/customers/' . $fname;
    }
}

// Gestione firma
$newSigPath = false;
if ($clearSig) {
    $newSigPath = null;
} elseif ($sigData && str_starts_with($sigData, 'data:image/')) {
    $sigDir = is_dir(__DIR__ . '/../../public')
        ? __DIR__ . '/../../public/uploads/signatures/'
        : __DIR__ . '/../../uploads/signatures/';
    if (!is_dir($sigDir)) mkdir($sigDir, 0755, true);
    $sBytes = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $sigData));
    if ($sBytes !== false && strlen($sBytes) > 100) {
        $fname = 's_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
        file_put_contents($sigDir . $fname, $sBytes);
        $newSigPath = 'uploads/signatures/' . $fname;
    }
}

$params = [$firstName, $lastName, $phone ?: null, $email ?: null, $fiscalCode ?: null,
           $birthDate, $address ?: null, $privacy, $status, $notes ?: null,
           $docType ?: null, $docNumber ?: null, $docExpiry, $docIssuer ?: null];

$photoSql = '';
if ($newPhotoPath !== false) {
    $photoSql = ', photo_path=?';
    $params[] = $newPhotoPath;
}
$sigSql = '';
if ($newSigPath !== false) {
    $sigSql = ', signature_path=?, privacy_signed_at=' . ($newSigPath ? 'NOW()' : 'NULL');
    $params[] = $newSigPath;
}
$params[] = $id;

$pdo->prepare("
    UPDATE customers
    SET first_name=?, last_name=?, phone=?, email=?, fiscal_code=?,
        birth_date=?, address=?, privacy_consent=?, status=?, notes=?,
        doc_type=?, doc_number=?, doc_expiry=?, doc_issuer=?
        $photoSql $sigSql
    WHERE id=?
")->execute($params);

audit_log('update_customer', 'customer', $id, [
    'name' => "$firstName $lastName", 'status' => $status,
]);

json_response(['success' => true]);
