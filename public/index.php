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

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $app['session_timeout']) {
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base !== '/' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base)) ?: '/';
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    include __DIR__ . '/../views/' . $view . '.php';
    $content = ob_get_clean();
    include __DIR__ . '/../views/layout.php';
}

try {
    verify_csrf();

    if ($path === '/login') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND active = 1');
            $stmt->execute([request_input('email')]);
            $user = $stmt->fetch();
            if ($user && password_verify((string) request_input('password'), $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
                redirect('/');
            }
            render('auth/login', ['error' => 'Credenziali non valide.']);
            exit;
        }
        render('auth/login');
        exit;
    }

    if ($path === '/logout') {
        session_destroy();
        redirect('/login');
    }

    require_login();

    if ($path === '/') {
        $today = date('Y-m-d');
        $stats = [
            'inside' => db()->query("SELECT COUNT(*) FROM entries WHERE status = 'dentro'")->fetchColumn(),
            'active_cards' => db()->query("SELECT COUNT(*) FROM cards WHERE status = 'attiva'")->fetchColumn(),
            'open_cards' => db()->query("SELECT COUNT(*) FROM cards WHERE current_balance > 0")->fetchColumn(),
            'charges' => db()->query("SELECT COALESCE(SUM(total_amount),0) FROM card_movements WHERE movement_type = 'charge' AND status <> 'cancelled' AND DATE(created_at) = CURDATE()")->fetchColumn(),
            'payments' => db()->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
            'reservations' => db()->prepare('SELECT COUNT(*) FROM reservations WHERE usage_date = ?'),
        ];
        $stats['reservations']->execute([$today]);
        $stats['reservations'] = $stats['reservations']->fetchColumn();
        $latest = db()->query('SELECT m.*, c.card_code FROM card_movements m JOIN cards c ON c.id = m.card_id ORDER BY m.created_at DESC LIMIT 8')->fetchAll();
        render('dashboard/index', compact('stats', 'latest'));
        exit;
    }

    if ($path === '/customers') {
        require_role(['admin', 'reception', 'cassa']);
        $q = trim((string) request_input('q', ''));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = db()->prepare('INSERT INTO customers (first_name, last_name, phone, email, fiscal_code, birth_date, address, privacy_consent, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                request_input('first_name'), request_input('last_name'), request_input('phone'), request_input('email'), request_input('fiscal_code'),
                request_input('birth_date') ?: null, request_input('address'), request_input('privacy_consent') ? 1 : 0, request_input('status', 'attivo'), request_input('notes'),
            ]);
            audit_log('create', 'customer', (int) db()->lastInsertId(), $_POST);
            redirect('/customers');
        }
        if ($q !== '') {
            $stmt = db()->prepare('SELECT * FROM customers WHERE first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ? ORDER BY last_name, first_name LIMIT 100');
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like, $like, $like]);
            $customers = $stmt->fetchAll();
        } else {
            $customers = db()->query('SELECT * FROM customers ORDER BY created_at DESC LIMIT 100')->fetchAll();
        }
        render('customers/index', compact('customers', 'q'));
        exit;
    }

    if ($path === '/cards') {
        require_role(['admin', 'reception', 'bar', 'ristorazione', 'cassa']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_role(['admin', 'reception']);
            $stmt = db()->prepare('INSERT INTO cards (card_code, customer_id, card_type, status, expires_at, notes) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([request_input('card_code'), request_input('customer_id'), request_input('card_type'), request_input('status'), request_input('expires_at') ?: null, request_input('notes')]);
            audit_log('create', 'card', (int) db()->lastInsertId(), $_POST);
            redirect('/cards?code=' . urlencode((string) request_input('card_code')));
        }
        $code = trim((string) request_input('code', ''));
        $card = $code !== '' ? (new CardService())->findByCode($code) : null;
        $movements = $card ? (new BalanceService())->movements((int) $card['id']) : [];
        $customers = db()->query("SELECT id, CONCAT(first_name, ' ', last_name) name FROM customers WHERE status = 'attivo' ORDER BY last_name, first_name LIMIT 200")->fetchAll();
        render('cards/index', compact('code', 'card', 'movements', 'customers'));
        exit;
    }

    if ($path === '/entries') {
        require_role(['admin', 'reception']);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = db();
            $pdo->beginTransaction();
            try {
                $card = (new CardService())->findByCode((string) request_input('card_code'));
                if (!$card || $card['status'] !== 'attiva') {
                    throw new RuntimeException('Card non attiva o inesistente.');
                }
                $stmt = $pdo->prepare("INSERT INTO entries (customer_id, card_id, entry_date, checkin_at, status, people_count, entry_fee, paid_amount, payment_method, notes, created_by) VALUES (?, ?, CURDATE(), NOW(), 'dentro', ?, ?, ?, ?, ?, ?)");
                $stmt->execute([(int) $card['customer_id'], (int) $card['id'], (int) request_input('people_count', 1), (float) request_input('entry_fee', 0), (float) request_input('paid_amount', 0), request_input('payment_method') ?: null, request_input('notes'), current_user()['id']]);
                $entryId = (int) $pdo->lastInsertId();
                $pdo->prepare('UPDATE cards SET is_inside = 1, active_entry_id = ? WHERE id = ?')->execute([$entryId, (int) $card['id']]);

                $entryFee = (float) request_input('entry_fee', 0);
                if ($entryFee > 0) {
                    $pdo->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id) VALUES (?, ?, ?, 'charge', 'reception', 'Ingresso piscina', 1, ?, ?, 'open', ?)")->execute([(int) $card['id'], (int) $card['customer_id'], $entryId, $entryFee, $entryFee, current_user()['id']]);
                }
                $paid = (float) request_input('paid_amount', 0);
                if ($paid > 0) {
                    $pdo->prepare('INSERT INTO payments (card_id, customer_id, entry_id, amount, payment_method, reason, operator_id) VALUES (?, ?, ?, ?, ?, ?, ?)')->execute([(int) $card['id'], (int) $card['customer_id'], $entryId, $paid, request_input('payment_method', 'contanti'), 'ingresso', current_user()['id']]);
                    $pdo->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id) VALUES (?, ?, ?, 'payment', 'cassa', 'Pagamento ingresso', 1, ?, ?, 'paid', ?)")->execute([(int) $card['id'], (int) $card['customer_id'], $entryId, $paid, $paid, current_user()['id']]);
                }

                $placeIds = array_filter((array) request_input('place_ids', []));
                foreach ($placeIds as $placeId) {
                    $check = $pdo->prepare("SELECT COUNT(*) FROM entry_places ep JOIN entries e ON e.id = ep.entry_id WHERE ep.place_id = ? AND ep.status = 'assegnato' AND e.entry_date = CURDATE() AND e.status = 'dentro'");
                    $check->execute([(int) $placeId]);
                    if ((int) $check->fetchColumn() > 0) {
                        throw new RuntimeException('Un lettino selezionato risulta gia occupato.');
                    }
                    $pdo->prepare("INSERT INTO entry_places (entry_id, place_id, status) VALUES (?, ?, 'assegnato')")->execute([$entryId, (int) $placeId]);
                    $pdo->prepare("UPDATE pool_places SET status = 'occupato' WHERE id = ?")->execute([(int) $placeId]);
                }
                (new BalanceService())->calculate((int) $card['id']);
                audit_log('checkin', 'entry', $entryId, $_POST);
                $pdo->commit();
                redirect('/cards?code=' . urlencode((string) $card['card_code']));
            } catch (Throwable $e) {
                $pdo->rollBack();
                $error = $e->getMessage();
            }
        }
        $places = db()->query("SELECT p.*, a.name area_name FROM pool_places p JOIN pool_areas a ON a.id = p.area_id WHERE p.status = 'disponibile' ORDER BY a.name, p.code")->fetchAll();
        $entries = db()->query("SELECT e.*, c.card_code, CONCAT(cu.first_name, ' ', cu.last_name) customer_name FROM entries e JOIN cards c ON c.id = e.card_id JOIN customers cu ON cu.id = e.customer_id WHERE e.entry_date = CURDATE() ORDER BY e.created_at DESC")->fetchAll();
        render('entries/index', compact('places', 'entries', 'error'));
        exit;
    }

    if ($path === '/checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_role(['admin', 'reception', 'cassa']);
        $card = (new CardService())->findByCode((string) request_input('card_code'));
        if (!$card) {
            redirect('/cashdesk?error=' . urlencode('Card non trovata.'));
        }
        $result = (new EntryService())->checkout((int) $card['id'], current_user()['id']);
        redirect('/cashdesk?code=' . urlencode($card['card_code']) . '&message=' . urlencode($result['message']));
    }

    if ($path === '/bar') {
        require_role(['admin', 'bar', 'ristorazione']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $card = (new CardService())->findByCode((string) request_input('card_code'));
            if (!$card) {
                throw new RuntimeException('Card non trovata.');
            }
            (new CardService())->requireActiveInside((int) $card['id']);
            $product = db()->prepare('SELECT p.*, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id WHERE p.id = ? AND p.active = 1');
            $product->execute([(int) request_input('product_id')]);
            $product = $product->fetch();
            if (!$product) {
                throw new RuntimeException('Prodotto non valido.');
            }
            $qty = max(1, (float) request_input('quantity', 1));
            $total = round($qty * (float) $product['price'], 2);
            $stmt = db()->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, product_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id) VALUES (?, ?, ?, ?, 'charge', ?, ?, ?, ?, ?, 'open', ?)");
            $stmt->execute([(int) $card['id'], (int) $card['customer_id'], (int) $card['active_entry_id'], (int) $product['id'], $product['department'], $product['name'], $qty, (float) $product['price'], $total, current_user()['id']]);
            (new BalanceService())->calculate((int) $card['id']);
            audit_log('charge', 'card', (int) $card['id'], ['product' => $product['name'], 'qty' => $qty, 'total' => $total]);
            redirect('/bar?code=' . urlencode($card['card_code']));
        }
        $code = trim((string) request_input('code', ''));
        $card = $code !== '' ? (new CardService())->findByCode($code) : null;
        $products = db()->query('SELECT p.*, pc.name category_name, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id WHERE p.active = 1 ORDER BY pc.department, pc.name, p.name')->fetchAll();
        render('bar/index', compact('code', 'card', 'products'));
        exit;
    }

    if ($path === '/cashdesk') {
        require_role(['admin', 'cassa', 'reception']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $card = (new CardService())->findByCode((string) request_input('card_code'));
            if (!$card) {
                throw new RuntimeException('Card non trovata.');
            }
            (new PaymentService())->pay((int) $card['id'], (float) request_input('amount'), (string) request_input('payment_method'), (string) request_input('reason', 'saldo finale'), current_user()['id'], request_input('notes'));
            redirect('/cashdesk?code=' . urlencode($card['card_code']));
        }
        $code = trim((string) request_input('code', ''));
        $card = $code !== '' ? (new CardService())->findByCode($code) : null;
        $movements = $card ? (new BalanceService())->movements((int) $card['id']) : [];
        render('cashdesk/index', compact('code', 'card', 'movements'));
        exit;
    }

    if ($path === '/places') {
        require_role(['admin', 'reception']);
        $places = db()->query("SELECT p.*, a.name area_name FROM pool_places p JOIN pool_areas a ON a.id = p.area_id ORDER BY a.name, p.code")->fetchAll();
        render('places/index', compact('places'));
        exit;
    }

    if ($path === '/reservations') {
        require_role(['admin', 'reception']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = 'PRE' . date('YmdHis');
            $stmt = db()->prepare("INSERT INTO reservations (reservation_code, customer_id, reservation_date, usage_date, time_slot, people_count, status, deposit_amount, total_amount, paid_amount, payment_method, notes, created_by) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$code, request_input('customer_id'), request_input('usage_date'), request_input('time_slot'), request_input('people_count'), request_input('status'), request_input('deposit_amount'), request_input('total_amount'), request_input('paid_amount'), request_input('payment_method') ?: null, request_input('notes'), current_user()['id']]);
            audit_log('create', 'reservation', (int) db()->lastInsertId(), $_POST);
            redirect('/reservations');
        }
        $customers = db()->query("SELECT id, CONCAT(first_name, ' ', last_name) name FROM customers ORDER BY last_name, first_name LIMIT 200")->fetchAll();
        $reservations = db()->query("SELECT r.*, CONCAT(c.first_name, ' ', c.last_name) customer_name FROM reservations r JOIN customers c ON c.id = r.customer_id ORDER BY r.usage_date DESC, r.id DESC LIMIT 100")->fetchAll();
        render('reservations/index', compact('customers', 'reservations'));
        exit;
    }

    if ($path === '/reports') {
        require_role(['admin', 'cassa']);
        $date = request_input('date', date('Y-m-d'));
        $stmt = db()->prepare("SELECT COUNT(*) entries, SUM(entry_fee) entry_total FROM entries WHERE entry_date = ?");
        $stmt->execute([$date]);
        $summary = $stmt->fetch();
        $charges = db()->prepare("SELECT department, COUNT(*) rows_count, SUM(total_amount) total FROM card_movements WHERE movement_type = 'charge' AND status <> 'cancelled' AND DATE(created_at) = ? GROUP BY department");
        $charges->execute([$date]);
        $payments = db()->prepare('SELECT payment_method, SUM(amount) total FROM payments WHERE DATE(created_at) = ? GROUP BY payment_method');
        $payments->execute([$date]);
        render('reports/index', ['date' => $date, 'summary' => $summary, 'charges' => $charges->fetchAll(), 'payments' => $payments->fetchAll()]);
        exit;
    }

    http_response_code(404);
    render('errors/404');
} catch (Throwable $e) {
    http_response_code(500);
    render('errors/500', ['error' => app_config('debug') ? $e->getMessage() : 'Errore applicativo.']);
}
