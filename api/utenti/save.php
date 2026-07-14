<?php
require __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}
require_role(['admin']);

$validRoles = ['admin', 'gestore', 'reception', 'bar', 'ristorazione', 'cassa'];

$id            = (int)    request_input('id', 0);
$name          = trim((string) request_input('name', ''));
$email         = trim((string) request_input('email', ''));
$password      = (string) request_input('password', '');
$role          = (string) request_input('role', '');
$active        = (int)    (bool) request_input('active', 1);
$permissionsIn = trim((string) request_input('permissions', ''));

if (!$name)  json_response(['success' => false, 'error' => 'Nome obbligatorio.']);
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) json_response(['success' => false, 'error' => 'Email non valida.']);
if (!in_array($role, $validRoles, true)) json_response(['success' => false, 'error' => 'Ruolo non valido.']);
if (!$id && strlen($password) < 8) json_response(['success' => false, 'error' => 'Password obbligatoria (almeno 8 caratteri) per un nuovo utente.']);
if ($password !== '' && strlen($password) < 8) json_response(['success' => false, 'error' => 'La password deve avere almeno 8 caratteri.']);

// permissions = null -> nessuna restrizione personalizzata (usa il default del ruolo).
// L'admin non è mai restringibile: ha sempre accesso a tutto.
$permissions = null;
if ($role !== 'admin' && $permissionsIn !== '') {
    $decoded = json_decode($permissionsIn, true);
    if (!is_array($decoded)) json_response(['success' => false, 'error' => 'Permessi non validi.']);
    $validSections = array_keys(app_sections());
    $permissions   = array_values(array_intersect($decoded, $validSections));
}
$permissionsJson = $permissions !== null ? json_encode($permissions, JSON_UNESCAPED_UNICODE) : null;

$pdo = db();

// Non lasciare la struttura senza almeno un amministratore attivo.
if ($id) {
    $cur = $pdo->prepare('SELECT role, active FROM users WHERE id = ?');
    $cur->execute([$id]);
    $cur = $cur->fetch();
    if (!$cur) json_response(['success' => false, 'error' => 'Utente non trovato.']);
    $losesAdmin = $cur['role'] === 'admin' && ($role !== 'admin' || !$active);
    if ($losesAdmin) {
        $otherAdminsStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND active = 1 AND id <> ?");
        $otherAdminsStmt->execute([$id]);
        if ((int) $otherAdminsStmt->fetchColumn() < 1) {
            json_response(['success' => false, 'error' => 'Deve restare almeno un amministratore attivo.']);
        }
    }
}

try {
    if ($id) {
        if ($password !== '') {
            $pdo->prepare('UPDATE users SET name=?, email=?, role=?, permissions=?, active=?, password_hash=? WHERE id=?')
                ->execute([$name, $email, $role, $permissionsJson, $active, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            $pdo->prepare('UPDATE users SET name=?, email=?, role=?, permissions=?, active=? WHERE id=?')
                ->execute([$name, $email, $role, $permissionsJson, $active, $id]);
        }
        audit_log('update_user', 'user', $id, ['name' => $name, 'email' => $email, 'role' => $role, 'active' => $active, 'permissions' => $permissions]);
        json_response(['success' => true, 'id' => $id]);
    } else {
        $pdo->prepare('INSERT INTO users (name, email, password_hash, role, permissions, active) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $permissionsJson, $active]);
        $newId = (int) $pdo->lastInsertId();
        audit_log('create_user', 'user', $newId, ['name' => $name, 'email' => $email, 'role' => $role, 'permissions' => $permissions]);
        json_response(['success' => true, 'id' => $newId]);
    }
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23000') {
        json_response(['success' => false, 'error' => 'Questa email è già in uso.']);
    }
    json_response(['success' => false, 'error' => $e->getMessage()]);
}
