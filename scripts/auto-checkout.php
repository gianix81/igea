<?php
/**
 * Checkout automatico di fine giornata.
 * Eseguire via Task Scheduler di Windows alle 19:00:
 *   php d:\igea\scripts\auto-checkout.php
 */
if (PHP_SAPI !== 'cli') {
    exit('Solo CLI.' . PHP_EOL);
}

require __DIR__ . '/../app/Helpers/functions.php';
require __DIR__ . '/../app/Services/BalanceService.php';
require __DIR__ . '/../app/Services/CardService.php';
require __DIR__ . '/../app/Services/EntryService.php';

$appCfg = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appCfg['timezone']);

// Operator fittizio per audit log (nessuna sessione in CLI)
$_SESSION = [];
$_SESSION['user'] = ['id' => 0, 'name' => 'Sistema', 'role' => 'admin'];
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$pdo = db();

// Tutte le card con ingresso attivo (dentro o bloccato) di oggi
$entries = $pdo->query("
    SELECT e.id, e.card_id, c.card_code, CONCAT(cu.first_name,' ',cu.last_name) customer_name
    FROM entries e
    JOIN cards c ON c.id = e.card_id
    JOIN customers cu ON cu.id = c.customer_id
    WHERE e.status IN ('dentro','bloccato')
      AND e.entry_date = CURDATE()
")->fetchAll(PDO::FETCH_ASSOC);

if (empty($entries)) {
    echo '[' . date('H:i:s') . '] Nessun ingresso attivo da chiudere.' . PHP_EOL;
    exit(0);
}

$svc = new EntryService();
$ok  = 0;
$err = 0;

foreach ($entries as $entry) {
    try {
        $result = $svc->checkout((int)$entry['card_id'], 0, true);
        $status = $result['success'] ? 'OK' : 'WARN';
        echo "[{$status}] {$entry['customer_name']} ({$entry['card_code']}): {$result['message']}" . PHP_EOL;
        $ok++;
    } catch (Throwable $e) {
        echo "[ERR] {$entry['customer_name']} ({$entry['card_code']}): {$e->getMessage()}" . PHP_EOL;
        $err++;
    }
}

echo PHP_EOL . 'Completato: ' . $ok . ' uscite registrate, ' . $err . ' errori.' . PHP_EOL;
exit($err > 0 ? 1 : 0);
