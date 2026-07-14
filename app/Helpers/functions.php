<?php

function app_config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../../config/app.php';
    }
    return $config[$key] ?? $default;
}

/**
 * Ritaglia al centro un quadrato dall'immagine sorgente e la ridimensiona a
 * $size x $size, restituendo bytes JPEG ottimizzati. Così la foto è già pronta
 * e coerente in ogni punto dell'app (miniatura, card, modal) indipendentemente
 * dalle proporzioni dello scatto originale, invece di affidarsi solo al
 * ritaglio CSS in visualizzazione.
 */
function square_crop_image(string $bytes, int $size = 640, int $quality = 82): ?string
{
    $src = @imagecreatefromstring($bytes);
    if (!$src) {
        return null;
    }

    $srcW = imagesx($src);
    $srcH = imagesy($src);
    $cropSize = min($srcW, $srcH);
    $srcX = (int) (($srcW - $cropSize) / 2);
    $srcY = (int) (($srcH - $cropSize) / 2);

    $dst = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $cropSize, $cropSize);

    ob_start();
    imagejpeg($dst, null, $quality);
    $out = ob_get_clean();

    imagedestroy($src);
    imagedestroy($dst);

    return $out !== '' ? $out : null;
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
    $roles = (array) $roles;
    // Il "gestore" ha accesso operativo pieno come l'admin, tranne dove una sezione è
    // riservata esclusivamente all'amministratore (richiesta solo ['admin']: utenti,
    // eliminazioni permanenti di clienti o posti).
    $adminOnly = ($roles === ['admin']);
    $allowed   = has_role($roles) || has_role('admin') || (!$adminOnly && has_role('gestore'));
    if (!$allowed) {
        http_response_code(403);
        include __DIR__ . '/../../views/errors/403.php';
        exit;
    }
}

/**
 * Sezioni dell'app assegnabili singolarmente a un operatore (tutte tranne
 * "Utenti", riservata all'admin). Chiave => etichetta mostrata nell'editor
 * permessi in /utenti.
 */
function app_sections(): array
{
    return [
        'dashboard' => 'Dashboard',
        'clienti'   => 'Clienti',
        'reception' => 'Reception (ingressi)',
        'piscina'   => 'Mappa piscina',
        'tariffe'   => 'Tariffe',
        'food'      => 'Food (consumazioni)',
        'cassa'     => 'Cassa',
        'prodotti'  => 'Prodotti',
        'report'    => 'Report',
    ];
}

/**
 * true se l'utente loggato può accedere alla sezione $key. Se l'admin non ha
 * personalizzato i permessi dell'utente (permissions = null), vale il
 * comportamento di sempre: la sezione è visibile a chiunque il ruolo lo
 * consenta (require_role() nella rotta fa comunque da filtro). Con
 * permissions valorizzato, invece, diventa una vera lista di sezioni
 * abilitate per quello specifico operatore.
 */
function has_section(string $key): bool
{
    $user = current_user();
    if (!$user) return false;
    if (($user['role'] ?? null) === 'admin') return true;
    $perms = $user['permissions'] ?? null;
    if ($perms === null) return true;
    return in_array($key, $perms, true);
}

function require_section(string $key): void
{
    if (!has_section($key)) {
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

function json_response(array $payload, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
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
