<?php

function app_config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../../config/app.php';
    }
    return $config[$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/../../config/database.php';
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function url(string $path = ''): string
{
    $base = rtrim(app_config('base_url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function has_role(array|string $roles): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }
    return in_array($user['role'], (array) $roles, true);
}

function require_login(): void
{
    if (!current_user()) {
        redirect('/login');
    }
}

function require_role(array|string $roles): void
{
    require_login();
    if (!has_role($roles) && !has_role('admin')) {
        http_response_code(403);
        include __DIR__ . '/../../views/errors/403.php';
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('Token CSRF non valido.');
        }
    }
}

function request_input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function money(mixed $amount): string
{
    return number_format((float) $amount, 2, ',', '.') . ' EUR';
}

function audit_log(string $action, string $entityType, ?int $entityId = null, ?array $newValue = null, ?array $oldValue = null): void
{
    $stmt = db()->prepare('INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        current_user()['id'] ?? null,
        $action,
        $entityType,
        $entityId,
        $oldValue ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : null,
        $newValue ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}
