<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

// ── Campi anagrafici ────────────────────────────────────────────────────────
$firstName  = trim((string) request_input('first_name', ''));
$lastName   = trim((string) request_input('last_name', ''));
$phone      = trim((string) request_input('phone', ''));
$email      = trim((string) request_input('email', ''));
$fiscalCode = strtoupper(trim((string) request_input('fiscal_code', '')));
$birthDate  = request_input('birth_date', '') ?: null;
$address    = trim((string) request_input('address', ''));
$status     = 'attivo';
$notes      = trim((string) request_input('notes', ''));

// ── Documento ────────────────────────────────────────────────────────────────
$docType    = (string) request_input('doc_type', '');
$docNumber  = strtoupper(trim((string) request_input('doc_number', '')));
$docExpiry  = request_input('doc_expiry', '') ?: null;
$docIssuer  = trim((string) request_input('doc_issuer', ''));

// ── Card ─────────────────────────────────────────────────────────────────────
$cardType   = (string) request_input('card_type', 'nominale');
$cardNotes  = trim((string) request_input('card_notes', ''));
$nfcUid     = strtoupper(trim((string) request_input('nfc_uid', ''))) ?: null;

// ── Files (base64) ───────────────────────────────────────────────────────────
$photoData  = (string) request_input('photo_data', '');
$sigData    = (string) request_input('signature_data', '');

// Validazioni
if (!$firstName || !$lastName)  json_response(['success' => false, 'error' => 'Nome e cognome obbligatori.']);
if (!$phone)                    json_response(['success' => false, 'error' => 'Telefono obbligatorio.']);

$validDeptTypes = ['carta_identita', 'passaporto', 'patente', 'permesso_soggiorno'];
if ($docType && !in_array($docType, $validDeptTypes, true)) {
    json_response(['success' => false, 'error' => 'Tipo documento non valido.']);
}
if (!in_array($cardType, ['nominale', 'abbonamento', 'ospite', 'staff'], true)) {
    $cardType = 'nominale';
}

// ── Salva foto ───────────────────────────────────────────────────────────────
$photoPath = null;
if ($photoData && str_starts_with($photoData, 'data:image/')) {
    $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $photoData));
    if ($imgData !== false && strlen($imgData) > 100) {
        $dir = is_dir(__DIR__ . '/../../public') ? __DIR__ . '/../../public/uploads/customers/' : __DIR__ . '/../../uploads/customers/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fname = 'c_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
        file_put_contents($dir . $fname, $imgData);
        $photoPath = 'uploads/customers/' . $fname;
    }
}

// ── Salva firma ───────────────────────────────────────────────────────────────
$sigPath = null;
if ($sigData && str_starts_with($sigData, 'data:image/')) {
    $sData = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $sigData));
    if ($sData !== false && strlen($sData) > 100) {
        $dir = is_dir(__DIR__ . '/../../public') ? __DIR__ . '/../../public/uploads/signatures/' : __DIR__ . '/../../uploads/signatures/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fname = 's_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
        file_put_contents($dir . $fname, $sData);
        $sigPath = 'uploads/signatures/' . $fname;
    }
}

$privacySigned = $sigPath ? date('Y-m-d H:i:s') : null;

// ── Auto-genera codice card ───────────────────────────────────────────────────
$year   = date('Y');
$prefix = 'IGA-' . $year . '-';
$pdo    = db();
$last   = $pdo->prepare("SELECT MAX(CAST(SUBSTRING(card_code, ?) AS UNSIGNED)) FROM cards WHERE card_code LIKE ?");
$last->execute([strlen($prefix) + 1, $prefix . '%']);
$seq      = (int)($last->fetchColumn() ?? 0) + 1;
$cardCode = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

// ── Inserimento in transazione ────────────────────────────────────────────────
$pdo->beginTransaction();
try {
    $pdo->prepare("
        INSERT INTO customers
            (first_name, last_name, phone, email, fiscal_code, birth_date, address,
             privacy_consent, status, notes,
             doc_type, doc_number, doc_expiry, doc_issuer,
             photo_path, signature_path, privacy_signed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, 'attivo', ?,
                ?, ?, ?, ?,
                ?, ?, ?)
    ")->execute([
        $firstName, $lastName, $phone, $email ?: null, $fiscalCode ?: null,
        $birthDate, $address ?: null, $notes ?: null,
        $docType ?: null, $docNumber ?: null, $docExpiry, $docIssuer ?: null,
        $photoPath, $sigPath, $privacySigned,
    ]);
    $customerId = (int) $pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO cards (card_code, nfc_uid, customer_id, card_type, status, notes)
        VALUES (?, ?, ?, ?, 'attiva', ?)
    ")->execute([$cardCode, $nfcUid, $customerId, $cardType, $cardNotes ?: null]);
    $cardId = (int) $pdo->lastInsertId();

    $pdo->commit();
    audit_log('register_customer', 'customer', $customerId, [
        'name'      => "$firstName $lastName",
        'card_code' => $cardCode,
        'doc_type'  => $docType,
    ]);
} catch (Throwable $e) {
    $pdo->rollBack();
    if (str_contains($e->getMessage(), 'nfc_uid')) {
        json_response(['success' => false, 'error' => 'UID NFC già registrato su un\'altra card.']);
    }
    json_response(['success' => false, 'error' => $e->getMessage()]);
}

json_response([
    'success'     => true,
    'customer_id' => $customerId,
    'card_id'     => $cardId,
    'card_code'   => $cardCode,
]);
