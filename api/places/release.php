<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin', 'reception']);

$placeId = (int) request_input('place_id', 0);
if (!$placeId) {
    json_response(['success' => false, 'error' => 'Dati mancanti.']);
}

$pdo = db();
$pdo->beginTransaction();
try {
    $pdo->prepare("UPDATE entry_places SET status = 'liberato', released_at = NOW() WHERE place_id = ? AND status = 'assegnato'")
        ->execute([$placeId]);
    $pdo->prepare("UPDATE pool_places SET status = 'disponibile' WHERE id = ?")
        ->execute([$placeId]);

    audit_log('release_place', 'pool_places', $placeId);
    $pdo->commit();
    json_response(['success' => true]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
