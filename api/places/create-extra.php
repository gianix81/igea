<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$code   = strtoupper(trim((string) request_input('code', '')));
$areaId = (int) request_input('area_id', 0);
$type   = (string) request_input('type', 'lettino');
$price  = (float) request_input('base_price', 0);
$notes  = trim((string) request_input('notes', ''));

if (!preg_match('/^[A-Z0-9_\-]{1,32}$/', $code)) {
    json_response(['success' => false, 'error' => 'Codice non valido. Usa solo lettere, numeri, trattini (max 32 caratteri).']);
}
if (!$areaId) {
    json_response(['success' => false, 'error' => 'Area obbligatoria.']);
}

$pdo = db();

$areaExists = $pdo->prepare("SELECT id FROM pool_areas WHERE id = ? AND active = 1");
$areaExists->execute([$areaId]);
if (!$areaExists->fetch()) {
    json_response(['success' => false, 'error' => 'Area non trovata.']);
}

$maxNum = (int) $pdo->query("SELECT COALESCE(MAX(number),0) FROM pool_places WHERE row_label = 'EXTRA'")->fetchColumn();

try {
    $pdo->prepare("INSERT INTO pool_places (area_id, code, row_label, number, type, base_price, notes) VALUES (?, ?, 'EXTRA', ?, ?, ?, ?)")
        ->execute([$areaId, $code, $maxNum + 1, $type, $price, $notes ?: null]);
    $id = (int) $pdo->lastInsertId();
    audit_log('create_extra_place', 'pool_place', $id, ['code' => $code, 'area_id' => $areaId]);
    json_response(['success' => true, 'id' => $id, 'code' => $code]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23000') {
        json_response(['success' => false, 'error' => "Il codice \"$code\" esiste già."]);
    }
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
