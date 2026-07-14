<?php
// Script temporaneo - eseguire una sola volta, poi eliminare
$pdo = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
    getenv('DB_USERNAME'), getenv('DB_PASSWORD'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Conta prima
$before = $pdo->query("SELECT COUNT(*) FROM customers WHERE photo_path IS NOT NULL OR signature_path IS NOT NULL")->fetchColumn();
echo "Clienti con percorsi immagine prima: $before\n";

// Elenca i percorsi presenti
$rows = $pdo->query("SELECT id, first_name, last_name, photo_path, signature_path FROM customers WHERE photo_path IS NOT NULL OR signature_path IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "ID {$r['id']} {$r['first_name']} {$r['last_name']}: photo={$r['photo_path']} sig={$r['signature_path']}\n";
}

// Azzera i percorsi
$pdo->exec("UPDATE customers SET photo_path = NULL, signature_path = NULL, privacy_signed_at = NULL WHERE photo_path IS NOT NULL OR signature_path IS NOT NULL");
echo "\nAzzerati. I clienti potranno ri-caricare le foto dal pannello.\n";
