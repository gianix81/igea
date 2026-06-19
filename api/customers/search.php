<?php
require __DIR__ . '/../bootstrap.php';
require_role(['admin', 'reception', 'cassa']);

$q    = trim((string) request_input('q', ''));
$like = '%' . $q . '%';

// Tutti i clienti attivi con card attiva — nessun filtro sull'ingresso di oggi
if ($q === '') {
    $stmt = db()->prepare("
        SELECT
            c.id              AS card_id,
            c.card_code,
            c.card_type,
            c.current_balance AS balance,
            c.is_inside,
            cu.first_name,
            cu.last_name,
            cu.phone
        FROM cards c
        JOIN customers cu ON cu.id = c.customer_id
        WHERE c.status = 'attiva'
          AND cu.status = 'attivo'
        ORDER BY cu.last_name, cu.first_name
        LIMIT 60
    ");
    $stmt->execute();
} else {
    $stmt = db()->prepare("
        SELECT
            c.id              AS card_id,
            c.card_code,
            c.card_type,
            c.current_balance AS balance,
            c.is_inside,
            cu.first_name,
            cu.last_name,
            cu.phone
        FROM cards c
        JOIN customers cu ON cu.id = c.customer_id
        WHERE c.status = 'attiva'
          AND cu.status = 'attivo'
          AND (
              c.card_code         LIKE ?
              OR cu.last_name     LIKE ?
              OR cu.first_name    LIKE ?
              OR cu.phone         LIKE ?
              OR cu.fiscal_code   LIKE ?
              OR CONCAT(cu.last_name, ' ', cu.first_name) LIKE ?
              OR CONCAT(cu.first_name, ' ', cu.last_name) LIKE ?
          )
        ORDER BY cu.last_name, cu.first_name
        LIMIT 40
    ");
    $stmt->execute([$like, $like, $like, $like, $like, $like, $like]);
}

$rows    = $stmt->fetchAll();
$results = [];
foreach ($rows as $r) {
    $results[] = [
        'card_code'     => $r['card_code'],
        'card_type'     => $r['card_type'],
        'customer_name' => $r['last_name'] . ' ' . $r['first_name'],
        'phone'         => $r['phone'] ?? '',
        'balance'       => (float) $r['balance'],
        'is_inside'     => (bool)  $r['is_inside'],
    ];
}

json_response(['results' => $results]);
