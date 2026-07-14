<?php
require __DIR__ . '/../bootstrap.php';
require_role(['admin', 'bar', 'ristorazione', 'reception', 'cassa']);

$q = trim((string) request_input('q', ''));

// Solo card con ingresso OGGI (dentro o bloccato)
$todayJoin = "INNER JOIN entries e ON e.card_id = c.id
               AND e.entry_date = CURDATE()
               AND e.status IN ('dentro','bloccato')";

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
            cu.phone,
            cu.photo_path
        FROM cards c
        JOIN customers cu ON cu.id = c.customer_id
        $todayJoin
        WHERE c.status = 'attiva'
          AND cu.status = 'attivo'
        ORDER BY c.is_inside DESC, cu.last_name, cu.first_name
        LIMIT 40
    ");
    $stmt->execute();
} else {
    // Ricerca per testo: cerca tra TUTTE le card attive (non solo gli ingressi di oggi),
    // così è possibile selezionare un cliente anche per nome/telefono/CF.
    $like = '%' . $q . '%';
    $stmt = db()->prepare("
        SELECT
            c.id              AS card_id,
            c.card_code,
            c.card_type,
            c.current_balance AS balance,
            c.is_inside,
            cu.first_name,
            cu.last_name,
            cu.phone,
            cu.photo_path
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
        ORDER BY c.is_inside DESC, cu.last_name, cu.first_name
        LIMIT 20
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
        'photo_url'     => !empty($r['photo_path']) ? url('/' . $r['photo_path']) : '',
    ];
}

json_response(['results' => $results]);
