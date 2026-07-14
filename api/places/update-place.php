<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$placeId = (int) request_input('place_id', 0);
$code    = strtoupper(trim((string) request_input('code', '')));
$areaId  = (int) request_input('area_id', 0);
$type    = (string) request_input('type', 'lettino');
$price   = (float) request_input('base_price', 0);
$notes   = trim((string) request_input('notes', ''));

if (!$placeId) {
    json_response(['success' => false, 'error' => 'ID lettino mancante.']);
}
if (!preg_match('/^[A-Z0-9_\-]{1,32}$/', $code)) {
    json_response(['success' => false, 'error' => 'Codice non valido. Usa solo lettere, numeri, trattini (max 32 caratteri).']);
}
if (!$areaId) {
    json_response(['success' => false, 'error' => 'Area obbligatoria.']);
}

$pdo = db();

$place = $pdo->prepare("SELECT id FROM pool_places WHERE id = ?");
$place->execute([$placeId]);
if (!$place->fetch()) {
    json_response(['success' => false, 'error' => 'Lettino non trovato.']);
}

$areaExists = $pdo->prepare("SELECT id FROM pool_areas WHERE id = ? AND active = 1");
$areaExists->execute([$areaId]);
if (!$areaExists->fetch()) {
    json_response(['success' => false, 'error' => 'Area non trovata.']);
}

try {
    $pdo->prepare("UPDATE pool_places SET code = ?, area_id = ?, type = ?, base_price = ?, notes = ? WHERE id = ?")
        ->execute([$code, $areaId, $type, $price, $notes ?: null, $placeId]);
    audit_log('update_place', 'pool_place', $placeId, ['code' => $code, 'area_id' => $areaId, 'type' => $type, 'base_price' => $price]);
    json_response(['success' => true]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23000') {
        json_response(['success' => false, 'error' => "Il codice \"$code\" esiste già."]);
    }
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
