<?php

// In locale api/ e app/ sono cartelle sorelle (1 livello sopra api/ basta).
// Su alcuni hosting (es. IONOS di questo progetto) api/ vive dentro httpdocs/
// mentre app/ resta fuori per sicurezza: serve un livello in più. Questo
// blocco si adatta automaticamente a entrambe le strutture.
$appRoot = is_dir(__DIR__ . '/../app') ? dirname(__DIR__) : dirname(__DIR__, 2);

require $appRoot . '/app/Helpers/functions.php';
require $appRoot . '/app/Services/BalanceService.php';
require $appRoot . '/app/Services/CardService.php';
require $appRoot . '/app/Services/EntryService.php';
require $appRoot . '/app/Services/PaymentService.php';
require $appRoot . '/app/Services/PriceListService.php';

$app = require $appRoot . '/config/app.php';
date_default_timezone_set($app['timezone']);
ini_set('session.name', $app['session_name']);
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    require_login();
    verify_csrf();
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 401);
}
