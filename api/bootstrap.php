<?php

require __DIR__ . '/../app/Helpers/functions.php';
require __DIR__ . '/../app/Services/BalanceService.php';
require __DIR__ . '/../app/Services/CardService.php';
require __DIR__ . '/../app/Services/EntryService.php';
require __DIR__ . '/../app/Services/PaymentService.php';

$app = require __DIR__ . '/../config/app.php';
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
