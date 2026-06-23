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
        $pdo = db();
        $inside  = (int) $pdo->query("SELECT COUNT(*) FROM entries WHERE status IN ('dentro','bloccato') AND entry_date = CURDATE()")->fetchColumn();
        $resPren = 0; $resWait = 0;
        try {
            $resPren = (int) $pdo->query("SELECT COUNT(*) FROM reservations WHERE usage_date = CURDATE() AND status = 'confermata'")->fetchColumn();
            $resWait = (int) $pdo->query("SELECT COUNT(*) FROM reservations WHERE usage_date = CURDATE() AND status = 'in attesa'")->fetchColumn();
        } catch (Throwable $ignored) {}
        $barS = $pdo->query("SELECT COUNT(*) AS orders, SUM(agg.dept_tot) AS total, SUM(CASE WHEN c.current_balance > 0.001 THEN agg.dept_tot ELSE 0 END) AS open FROM (SELECT card_id, SUM(total_amount) dept_tot FROM card_movements WHERE department='bar' AND movement_type='charge' AND status<>'cancelled' AND DATE(created_at)=CURDATE() GROUP BY card_id) agg JOIN cards c ON c.id = agg.card_id")->fetch(PDO::FETCH_ASSOC);
        $barOrders = (int)   ($barS['orders'] ?? 0);
        $barTotal  = (float) ($barS['total']  ?? 0);
        $barOpen   = (float) ($barS['open']   ?? 0);
        $ristoS = $pdo->query("SELECT COUNT(*) AS orders, SUM(agg.dept_tot) AS total, SUM(CASE WHEN c.current_balance > 0.001 THEN agg.dept_tot ELSE 0 END) AS open FROM (SELECT card_id, SUM(total_amount) dept_tot FROM card_movements WHERE department='ristorante' AND movement_type='charge' AND status<>'cancelled' AND DATE(created_at)=CURDATE() GROUP BY card_id) agg JOIN cards c ON c.id = agg.card_id")->fetch(PDO::FETCH_ASSOC);
        $ristoOrders = (int)   ($ristoS['orders'] ?? 0);
        $ristoTotal  = (float) ($ristoS['total']  ?? 0);
        $ristoOpen   = (float) ($ristoS['open']   ?? 0);
        $latest = $pdo->query("SELECT m.*, c.card_code, cu.first_name, cu.last_name FROM card_movements m LEFT JOIN cards c ON c.id = m.card_id LEFT JOIN customers cu ON cu.id = m.customer_id WHERE DATE(m.created_at) = CURDATE() ORDER BY m.created_at DESC LIMIT 30")->fetchAll();
        $stats = compact('inside', 'resPren', 'resWait', 'barOrders', 'barTotal', 'barOpen', 'ristoOrders', 'ristoTotal', 'ristoOpen');
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
        // Detail view when ?id= is present
        $customerId = (int) request_input('id', 0);
        if ($customerId > 0) {
            $pdo = db();
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            if (!$customer) redirect('/customers');
            $stmt = $pdo->prepare("SELECT * FROM cards WHERE customer_id = ? ORDER BY created_at DESC");
            $stmt->execute([$customerId]);
            $cards = $stmt->fetchAll();
            $selectedCard = null;
            $movements = [];
            $cardId = (int) request_input('card', 0);
            if ($cardId > 0) {
                foreach ($cards as $c) {
                    if ((int) $c['id'] === $cardId) { $selectedCard = $c; break; }
                }
                if ($selectedCard) {
                    $movements = (new BalanceService())->movements($cardId);
                }
            }
            render('customers/index', compact('customer', 'cards', 'selectedCard', 'movements'));
            exit;
        }
        $sel  = "SELECT cu.*, COALESCE(SUM(c.current_balance),0) AS total_balance, COUNT(DISTINCT c.id) AS active_cards FROM customers cu LEFT JOIN cards c ON c.customer_id = cu.id AND c.status = 'attiva'";
        if ($q !== '') {
            $like = '%' . $q . '%';
            $stmt = db()->prepare($sel . " WHERE (cu.first_name LIKE ? OR cu.last_name LIKE ? OR cu.phone LIKE ? OR cu.email LIKE ? OR CONCAT(cu.last_name,' ',cu.first_name) LIKE ?) GROUP BY cu.id ORDER BY cu.last_name, cu.first_name LIMIT 100");
            $stmt->execute([$like, $like, $like, $like, $like]);
        } else {
            $stmt = db()->prepare($sel . " GROUP BY cu.id ORDER BY cu.created_at DESC LIMIT 100");
            $stmt->execute();
        }
        $customers = $stmt->fetchAll();
        render('customers/index', compact('customers', 'q'));
        exit;
    }

    if ($path === '/cards') {
        require_role(['admin', 'reception', 'bar', 'ristorazione', 'cassa']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_role(['admin', 'reception']);
            $stmt = db()->prepare('INSERT INTO cards (card_code, customer_id, card_type, status, expires_at, notes) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([request_input('card_code'), request_input('customer_id'), request_input('card_type', 'nominale'), request_input('status', 'attiva'), request_input('expires_at') ?: null, request_input('notes')]);
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
                redirect('/entries?message=' . urlencode('Ingresso registrato.'));
            } catch (Throwable $e) {
                $pdo->rollBack();
                $error = $e->getMessage();
            }
        }
        if ($error === null && !empty($_GET['error'])) $error = $_GET['error'];
        $flash   = !empty($_GET['message']) ? $_GET['message'] : null;
        $places  = db()->query("SELECT p.*, a.name area_name FROM pool_places p JOIN pool_areas a ON a.id = p.area_id WHERE p.status = 'disponibile' ORDER BY a.name, p.code")->fetchAll();
        $entries = db()->query("SELECT e.*, c.card_code, CONCAT(cu.first_name, ' ', cu.last_name) customer_name FROM entries e JOIN cards c ON c.id = e.card_id JOIN customers cu ON cu.id = e.customer_id WHERE e.entry_date = CURDATE() ORDER BY e.created_at DESC")->fetchAll();
        render('entries/index', compact('places', 'entries', 'error', 'flash'));
        exit;
    }

    if ($path === '/checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_role(['admin', 'reception', 'cassa']);
        $card = (new CardService())->findByCode((string) request_input('card_code'));
        if (!$card) redirect('/cashdesk?error=' . urlencode('Card non trovata.'));
        $redirectTo = (string) request_input('redirect_to', 'cashdesk');
        try {
            $force  = !empty($_POST['force_checkout']) && $_POST['force_checkout'] === '1';
            $result = (new EntryService())->checkout((int) $card['id'], current_user()['id'], $force);
            $param  = $result['success'] ? 'message' : 'error';
            if ($redirectTo === 'entries') redirect('/entries?' . $param . '=' . urlencode($result['message']));
            redirect('/cashdesk?code=' . urlencode($card['card_code']) . '&' . $param . '=' . urlencode($result['message']));
        } catch (Throwable $e) {
            if ($redirectTo === 'entries') redirect('/entries?error=' . urlencode($e->getMessage()));
            redirect('/cashdesk?code=' . urlencode($card['card_code']) . '&error=' . urlencode($e->getMessage()));
        }
    }

    if ($path === '/bar') {
        require_role(['admin', 'bar', 'ristorazione']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $card = (new CardService())->findByCode((string) request_input('card_code'));
            if (!$card) throw new RuntimeException('Card non trovata.');
            $items = (array) (request_input('items') ?? []);
            if (empty($items)) throw new RuntimeException('Nessun prodotto selezionato.');
            $pdo = db();
            $pdo->beginTransaction();
            try {
                foreach ($items as $item) {
                    $productId = (int) ($item['product_id'] ?? 0);
                    $qty       = max(1, (int) ($item['quantity'] ?? 1));
                    $pStmt = $pdo->prepare('SELECT p.*, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id WHERE p.id = ? AND p.active = 1');
                    $pStmt->execute([$productId]);
                    $product = $pStmt->fetch();
                    if (!$product) continue;
                    $total = round($qty * (float) $product['price'], 2);
                    $pdo->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, product_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id) VALUES (?, ?, ?, ?, 'charge', ?, ?, ?, ?, ?, 'open', ?)")->execute([(int) $card['id'], (int) $card['customer_id'], (int) $card['active_entry_id'], $productId, $product['department'], $product['name'], $qty, (float) $product['price'], $total, current_user()['id']]);
                }
                (new BalanceService())->calculate((int) $card['id']);
                audit_log('bar_order', 'card', (int) $card['id'], ['items' => count($items)]);
                $pdo->commit();
            } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
            redirect('/bar?code=' . urlencode($card['card_code']) . '&message=' . urlencode('Ordine registrato.'));
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
            if (!$card) throw new RuntimeException('Card non trovata.');
            (new PaymentService())->pay((int) $card['id'], (float) request_input('amount'), (string) request_input('payment_method'), (string) request_input('reason', 'saldo finale'), current_user()['id'], request_input('notes'));
            redirect('/cashdesk?code=' . urlencode($card['card_code']) . '&message=' . urlencode('Pagamento registrato.'));
        }
        $pdo  = db();
        $code  = trim((string) request_input('code', ''));
        $card  = $code !== '' ? (new CardService())->findByCode($code) : null;
        $flash = !empty($_GET['message']) ? $_GET['message'] : null;
        $error = !empty($_GET['error'])   ? $_GET['error']   : null;
        $kpiCount   = (int)   $pdo->query("SELECT COUNT(*) FROM cards WHERE current_balance > 0.001")->fetchColumn();
        $kpiOpen    = (float) $pdo->query("SELECT COALESCE(SUM(current_balance),0) FROM cards WHERE current_balance > 0.001")->fetchColumn();
        $kpiPaid    = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $kpiSaldati = (int)   $pdo->query("SELECT COUNT(DISTINCT card_id) FROM payments WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $openAccounts = $pdo->query("
            SELECT c.card_code, c.is_inside, c.current_balance AS open_charges, cu.photo_path,
                   CONCAT(cu.first_name,' ',cu.last_name) AS customer_name, cu.phone,
                   e.checkin_at, e.status AS entry_status,
                   COALESCE(SUM(CASE WHEN m.department='bar'        AND m.movement_type='charge' AND m.status='open' THEN m.total_amount ELSE 0 END),0) AS bar_tot,
                   COALESCE(SUM(CASE WHEN m.department='ristorante' AND m.movement_type='charge' AND m.status='open' THEN m.total_amount ELSE 0 END),0) AS risto_tot
            FROM cards c
            JOIN customers cu ON cu.id = c.customer_id
            LEFT JOIN entries e ON e.card_id = c.id AND e.entry_date = CURDATE() AND e.status IN ('dentro','bloccato','uscito')
            LEFT JOIN card_movements m ON m.card_id = c.id AND m.status = 'open'
            WHERE c.status = 'attiva' AND cu.status = 'attivo'
              AND (e.id IS NOT NULL OR c.current_balance > 0.001)
            GROUP BY c.id, c.card_code, c.is_inside, c.current_balance, cu.photo_path, cu.first_name, cu.last_name, cu.phone, e.checkin_at, e.status
            ORDER BY c.current_balance DESC, c.is_inside DESC, cu.last_name, cu.first_name
            LIMIT 200
        ")->fetchAll();
        $movements  = $card ? (new BalanceService())->movements((int) $card['id']) : [];
        $due        = $card ? (float) $card['current_balance'] : 0;
        $isInside   = $card ? (bool)  $card['is_inside'] : false;
        render('cashdesk/index', compact('code', 'card', 'movements', 'due', 'isInside', 'flash', 'error', 'openAccounts', 'kpiCount', 'kpiOpen', 'kpiPaid', 'kpiSaldati'));
        exit;
    }

    if ($path === '/places/layout') {
        require_role(['admin', 'reception']);
        $date = trim((string) request_input('date', date('Y-m-d')));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');

        $layoutStmt = db()->prepare("
            SELECT p.id, p.code, p.row_label, p.number, p.type, p.base_price,
                   p.pos_row, p.pos_col, a.name area_name,
                   CASE
                     WHEN ep_a.place_id IS NOT NULL THEN 'occupato'
                     WHEN rp_a.place_id IS NOT NULL THEN 'prenotato-confermato'
                     WHEN p.status IN ('manutenzione','bloccato') THEN p.status
                     ELSE 'disponibile'
                   END day_status
            FROM pool_places p
            JOIN pool_areas a ON a.id = p.area_id
            LEFT JOIN (
              SELECT ep.place_id FROM entry_places ep
              JOIN entries e ON e.id = ep.entry_id
              WHERE ep.status='assegnato' AND e.entry_date=? AND e.status='dentro'
              GROUP BY ep.place_id
            ) ep_a ON ep_a.place_id = p.id
            LEFT JOIN (
              SELECT rp.place_id FROM reservation_places rp
              JOIN reservations r ON r.id = rp.reservation_id
              WHERE rp.status='prenotato' AND r.usage_date=? AND r.status IN ('confermata','in attesa')
              GROUP BY rp.place_id
            ) rp_a ON rp_a.place_id = p.id
            ORDER BY p.pos_row, p.pos_col, p.row_label, p.number
        ");
        $layoutStmt->execute([$date, $date]);
        $layoutPlaces = $layoutStmt->fetchAll();
        render('places/layout', compact('layoutPlaces', 'date'));
        exit;
    }

    if ($path === '/places') {
        require_role(['admin', 'reception']);

        // Creazione prenotazione (senza posto specifico — il posto viene abbinato via API /api/places/book.php)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && request_input('_action') === 'new_reservation') {
            $usageDate = request_input('usage_date', date('Y-m-d'));
            $usageDateTo = request_input('usage_date_to', '');
            $notes = trim((string) request_input('notes', ''));
            if ($usageDateTo && $usageDateTo !== $usageDate) {
                $notes = trim(($notes ? $notes . ' | ' : '') . 'Periodo: ' . $usageDate . ' → ' . $usageDateTo);
            }
            $code = 'PRE' . date('YmdHis') . rand(10, 99);
            $stmt = db()->prepare("INSERT INTO reservations (reservation_code, customer_id, reservation_date, usage_date, time_slot, people_count, status, deposit_amount, total_amount, paid_amount, payment_method, notes, created_by) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$code, request_input('customer_id'), $usageDate, request_input('time_slot'), request_input('people_count') ?: 1, request_input('res_status') ?: 'confermata', request_input('deposit_amount') ?: 0, request_input('total_amount') ?: 0, request_input('paid_amount') ?: 0, request_input('payment_method') ?: null, $notes, current_user()['id']]);
            audit_log('create', 'reservation', (int) db()->lastInsertId(), $_POST);
            json_response(['success' => true, 'date' => $usageDate]);
        }

        $date = trim((string) request_input('date', date('Y-m-d')));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        $stmt = db()->prepare("
            SELECT
                p.id, p.area_id, p.code, p.row_label, p.number, p.type, p.base_price, p.status,
                p.pos_row, p.pos_col,
                a.name area_name,
                e.id entry_id, e.checkin_at,
                CONCAT(ec.first_name,' ',ec.last_name) entry_customer_name,
                ec.phone entry_customer_phone,
                ecard.card_code entry_card_code,
                r.id reservation_id, r.reservation_code, r.time_slot, r.status res_status,
                CONCAT(rc.first_name,' ',rc.last_name) res_customer_name,
                rc.phone res_customer_phone,
                rcard.card_code res_card_code,
                CASE
                    WHEN ep_a.place_id IS NOT NULL THEN
                        CASE entry_res.time_slot
                            WHEN 'mattina'    THEN 'occupato-mattina'
                            WHEN 'pomeriggio' THEN 'occupato-pomeriggio'
                            ELSE 'occupato'
                        END
                    WHEN rp_a.place_id IS NOT NULL THEN
                        CASE
                            WHEN r.status = 'confermata' AND r.time_slot = 'mattina'    THEN 'prenotato-confermato-mattina'
                            WHEN r.status = 'confermata' AND r.time_slot = 'pomeriggio' THEN 'prenotato-confermato-pomeriggio'
                            WHEN r.status = 'confermata'                                THEN 'prenotato-confermato'
                            WHEN r.status = 'in attesa'  AND r.time_slot = 'mattina'    THEN 'prenotato-attesa-mattina'
                            WHEN r.status = 'in attesa'  AND r.time_slot = 'pomeriggio' THEN 'prenotato-attesa-pomeriggio'
                            ELSE 'prenotato-attesa'
                        END
                    WHEN p.status IN ('manutenzione','bloccato') THEN p.status
                    ELSE 'disponibile'
                END day_status
            FROM pool_places p
            JOIN pool_areas a ON a.id = p.area_id
            LEFT JOIN (
                SELECT ep.place_id, MIN(ep.entry_id) entry_id
                FROM entry_places ep
                JOIN entries e ON e.id = ep.entry_id
                WHERE ep.status = 'assegnato' AND e.entry_date = ? AND e.status = 'dentro'
                GROUP BY ep.place_id
            ) ep_a ON ep_a.place_id = p.id
            LEFT JOIN entries e ON e.id = ep_a.entry_id
            LEFT JOIN reservations entry_res ON entry_res.id = e.reservation_id
            LEFT JOIN customers ec ON ec.id = e.customer_id
            LEFT JOIN cards ecard ON ecard.id = e.card_id
            LEFT JOIN (
                SELECT rp.place_id, MIN(rp.reservation_id) reservation_id
                FROM reservation_places rp
                JOIN reservations r ON r.id = rp.reservation_id
                WHERE rp.status = 'prenotato' AND r.usage_date = ? AND r.status IN ('confermata','in attesa')
                GROUP BY rp.place_id
            ) rp_a ON rp_a.place_id = p.id
            LEFT JOIN reservations r ON r.id = rp_a.reservation_id
            LEFT JOIN customers rc ON rc.id = r.customer_id
            LEFT JOIN (SELECT customer_id, MIN(card_code) card_code FROM cards WHERE status='attiva' GROUP BY customer_id) rcard ON rcard.customer_id = rc.id
            ORDER BY a.id, p.row_label, p.number
        ");
        $stmt->execute([$date, $date]);
        $places = $stmt->fetchAll();
        $areas = [];
        foreach ($places as $p) {
            $aid = (int) $p['area_id'];
            if (!isset($areas[$aid])) {
                $areas[$aid] = ['name' => $p['area_name'], 'places' => []];
            }
            $areas[$aid]['places'][$p['row_label']][] = $p;
        }
        $customers = db()->query("SELECT id, CONCAT(first_name,' ',last_name) name, phone FROM customers WHERE status='attivo' ORDER BY last_name, first_name LIMIT 500")->fetchAll();
        $poolAreas = db()->query("SELECT id, name FROM pool_areas WHERE active = 1 ORDER BY id")->fetchAll();
        $rStmt = db()->prepare("
            SELECT r.id, r.reservation_code, r.usage_date, r.time_slot, r.people_count,
                   r.total_amount, r.status, r.customer_id, r.notes,
                   CONCAT(c.first_name,' ',c.last_name) customer_name,
                   GROUP_CONCAT(pp.code ORDER BY pp.code SEPARATOR ', ') places_codes,
                   GROUP_CONCAT(pp.id   ORDER BY pp.code SEPARATOR ',')  places_ids
            FROM reservations r
            JOIN customers c ON c.id = r.customer_id
            LEFT JOIN reservation_places rp ON rp.reservation_id = r.id AND rp.status = 'prenotato'
            LEFT JOIN pool_places pp ON pp.id = rp.place_id
            WHERE r.usage_date = ? AND r.status NOT IN ('cancellata','no-show')
            GROUP BY r.id
            ORDER BY r.id DESC
        ");
        $rStmt->execute([$date]);
        $reservations = $rStmt->fetchAll();
        $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
        $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
        render('places/index', compact('places', 'areas', 'date', 'prevDate', 'nextDate', 'customers', 'reservations', 'poolAreas'));
        exit;
    }

    if ($path === '/reservations') {
        redirect('/places');
    }

    if ($path === '/products') {
        require_role(['admin', 'reception']);
        $pdo        = db();
        $categories = $pdo->query("SELECT * FROM product_categories ORDER BY department, name")->fetchAll();
        $products   = $pdo->query("SELECT p.*, pc.name category_name, pc.department FROM products p JOIN product_categories pc ON pc.id = p.category_id ORDER BY pc.department, pc.name, p.name")->fetchAll();
        render('products/index', compact('categories', 'products'));
        exit;
    }

    if ($path === '/scheda') {
        require_role(['admin', 'bar', 'ristorazione', 'reception', 'cassa']);
        $activeCards = db()->query("SELECT c.card_code, c.current_balance AS balance, c.is_inside, cu.first_name, cu.last_name, cu.phone FROM cards c JOIN customers cu ON cu.id = c.customer_id WHERE c.status = 'attiva' AND (c.is_inside = 1 OR c.current_balance > 0) ORDER BY c.is_inside DESC, cu.last_name, cu.first_name")->fetchAll();
        render('scheda/index', compact('activeCards'));
        exit;
    }

    if ($path === '/reports') {
        require_role(['admin', 'cassa']);
        $pdo      = db();
        $fromDate = trim((string) request_input('from', date('Y-m-d')));
        $toDate   = trim((string) request_input('to',   date('Y-m-d')));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) $fromDate = date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate))   $toDate   = date('Y-m-d');
        if ($toDate < $fromDate) $toDate = $fromDate;
        $s = $pdo->prepare("SELECT COUNT(*) cnt, COALESCE(SUM(entry_fee),0) fee, COALESCE(SUM(people_count),0) people FROM entries WHERE entry_date BETWEEN ? AND ?");
        $s->execute([$fromDate, $toDate]); $entriesKpi = $s->fetch();
        $s = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE(created_at) BETWEEN ? AND ?");
        $s->execute([$fromDate, $toDate]); $paymentsTotal = (float) $s->fetchColumn();
        $cardBalance = (float) $pdo->query("SELECT COALESCE(SUM(current_balance),0) FROM cards WHERE current_balance > 0")->fetchColumn();
        $cardsOpen   = (int)   $pdo->query("SELECT COUNT(*) FROM cards WHERE current_balance > 0")->fetchColumn();
        $s = $pdo->prepare("SELECT department, COUNT(*) cnt, COALESCE(SUM(total_amount),0) total FROM card_movements WHERE movement_type='charge' AND status<>'cancelled' AND DATE(created_at) BETWEEN ? AND ? GROUP BY department ORDER BY total DESC");
        $s->execute([$fromDate, $toDate]); $deptCharges = $s->fetchAll();
        $s = $pdo->prepare("SELECT payment_method, COUNT(*) cnt, COALESCE(SUM(amount),0) total FROM payments WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY payment_method ORDER BY total DESC");
        $s->execute([$fromDate, $toDate]); $paymentMethods = $s->fetchAll();
        $s = $pdo->prepare("SELECT p.name prod_name, pc.name cat_name, pc.department, COUNT(*) orders_cnt, COALESCE(SUM(m.quantity),0) qty_total, COALESCE(SUM(m.total_amount),0) revenue FROM card_movements m JOIN products p ON p.id=m.product_id JOIN product_categories pc ON pc.id=p.category_id WHERE m.movement_type='charge' AND m.status<>'cancelled' AND DATE(m.created_at) BETWEEN ? AND ? GROUP BY m.product_id, p.name, pc.name, pc.department ORDER BY revenue DESC LIMIT 15");
        $s->execute([$fromDate, $toDate]); $topProducts = $s->fetchAll();
        $diffDays = max(1, (int) round((strtotime($toDate) - strtotime($fromDate)) / 86400) + 1);
        $trendData = [];
        if ($diffDays <= 93) {
            $s = $pdo->prepare("SELECT entry_date dk, COUNT(*) entries, COALESCE(SUM(entry_fee),0) fees FROM entries WHERE entry_date BETWEEN ? AND ? GROUP BY entry_date");
            $s->execute([$fromDate, $toDate]); $entryMap = array_column($s->fetchAll(PDO::FETCH_ASSOC), null, 'dk');
            $s = $pdo->prepare("SELECT DATE(created_at) dk, COALESCE(SUM(amount),0) paid FROM payments WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at)");
            $s->execute([$fromDate, $toDate]); $paidMap = array_column($s->fetchAll(PDO::FETCH_ASSOC), 'paid', 'dk');
            $s = $pdo->prepare("SELECT DATE(created_at) dk, COALESCE(SUM(total_amount),0) charges FROM card_movements WHERE movement_type='charge' AND status<>'cancelled' AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at)");
            $s->execute([$fromDate, $toDate]); $chargeMap = array_column($s->fetchAll(PDO::FETCH_ASSOC), 'charges', 'dk');
            $cur = strtotime($fromDate); $end = strtotime($toDate);
            while ($cur <= $end) { $dk = date('Y-m-d', $cur); $trendData[] = ['label' => date('d/m', $cur), 'entries' => (int) ($entryMap[$dk]['entries'] ?? 0), 'paid' => (float) ($paidMap[$dk] ?? 0), 'charges' => (float) ($chargeMap[$dk] ?? 0)]; $cur += 86400; }
        }
        $s = $pdo->prepare("SELECT e.entry_date, COUNT(*) cnt, COALESCE(SUM(e.entry_fee),0) fees, COALESCE(SUM(e.people_count),0) people FROM entries e WHERE e.entry_date BETWEEN ? AND ? GROUP BY e.entry_date ORDER BY e.entry_date DESC LIMIT 60");
        $s->execute([$fromDate, $toDate]); $entryDetail = $s->fetchAll();
        render('reports/index', compact('fromDate', 'toDate', 'diffDays', 'entriesKpi', 'paymentsTotal', 'cardBalance', 'cardsOpen', 'deptCharges', 'paymentMethods', 'topProducts', 'trendData', 'entryDetail'));
        exit;
    }

    // ── API: customers search (Reception) ────────────────────────────────────
    if ($path === '/api/customers/search') {
        require_role(['admin', 'reception', 'cassa']);
        $q   = trim((string) request_input('q', ''));
        $pdo = db();
        $sel  = "SELECT cu.id AS customer_id, cu.first_name, cu.last_name, cu.phone, c.card_code, c.card_type, c.current_balance AS balance, c.is_inside";
        $from = " FROM customers cu LEFT JOIN cards c ON c.customer_id = cu.id AND c.status = 'attiva' WHERE cu.status = 'attivo'";
        if ($q === '') {
            $stmt = $pdo->prepare($sel . $from . " ORDER BY cu.last_name, cu.first_name LIMIT 60");
            $stmt->execute();
        } else {
            $like = '%' . $q . '%';
            $stmt = $pdo->prepare($sel . $from . " AND (c.card_code LIKE ? OR cu.last_name LIKE ? OR cu.first_name LIKE ? OR cu.phone LIKE ? OR CONCAT(cu.last_name,' ',cu.first_name) LIKE ? OR CONCAT(cu.first_name,' ',cu.last_name) LIKE ?) ORDER BY cu.last_name, cu.first_name LIMIT 40");
            $stmt->execute([$like, $like, $like, $like, $like, $like]);
        }
        $results = [];
        foreach ($stmt->fetchAll() as $r) {
            $results[] = ['card_code' => $r['card_code'], 'card_type' => $r['card_type'], 'customer_id' => (int) $r['customer_id'], 'customer_name' => trim($r['last_name'] . ' ' . $r['first_name']), 'phone' => $r['phone'] ?? '', 'balance' => (float) ($r['balance'] ?? 0), 'is_inside' => (bool) ($r['is_inside'] ?? false), 'photo_url' => null];
        }
        json_response(['results' => $results]);
    }

    // ── API: bar search card (solo ospiti con ingresso oggi) ─────────────────
    if ($path === '/api/bar/search-card') {
        require_role(['admin', 'bar', 'ristorazione', 'reception', 'cassa']);
        $q   = trim((string) request_input('q', ''));
        $pdo = db();
        $sel  = "SELECT c.card_code, c.card_type, c.current_balance AS balance, c.is_inside, cu.id AS customer_id, cu.first_name, cu.last_name, cu.phone";
        $from = " FROM cards c JOIN customers cu ON cu.id = c.customer_id INNER JOIN entries e ON e.card_id = c.id AND e.entry_date = CURDATE() AND e.status IN ('dentro','bloccato') WHERE c.status = 'attiva' AND cu.status = 'attivo'";
        if ($q === '') {
            $stmt = $pdo->prepare($sel . $from . " ORDER BY c.is_inside DESC, cu.last_name, cu.first_name LIMIT 40");
            $stmt->execute();
        } else {
            $like = '%' . $q . '%';
            $stmt = $pdo->prepare($sel . $from . " AND (c.card_code LIKE ? OR cu.last_name LIKE ? OR cu.first_name LIKE ? OR cu.phone LIKE ? OR CONCAT(cu.last_name,' ',cu.first_name) LIKE ?) ORDER BY c.is_inside DESC, cu.last_name, cu.first_name LIMIT 20");
            $stmt->execute([$like, $like, $like, $like, $like]);
        }
        $results = [];
        foreach ($stmt->fetchAll() as $r) {
            $results[] = ['card_code' => $r['card_code'], 'card_type' => $r['card_type'], 'customer_id' => (int) $r['customer_id'], 'customer_name' => trim($r['last_name'] . ' ' . $r['first_name']), 'phone' => $r['phone'] ?? '', 'balance' => (float) $r['balance'], 'is_inside' => (bool) $r['is_inside'], 'photo_url' => null];
        }
        json_response(['results' => $results]);
    }

    // ── API: cancel movement ─────────────────────────────────────────────────
    if ($path === '/api/cashdesk/cancel-movement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_role(['admin', 'cassa', 'reception']);
        header('Content-Type: application/json');
        $id  = (int) request_input('movement_id');
        $pdo = db();
        $mov = $pdo->prepare('SELECT * FROM card_movements WHERE id = ?');
        $mov->execute([$id]);
        $mov = $mov->fetch();
        if (!$mov) { echo json_encode(['ok' => false, 'error' => 'Movimento non trovato']); exit; }
        $pdo->prepare("UPDATE card_movements SET status='cancelled' WHERE id=?")->execute([$id]);
        (new BalanceService())->calculate((int) $mov['card_id']);
        audit_log('cancel_movement', 'card_movement', $id);
        echo json_encode(['ok' => true]); exit;
    }

    // ── API: edit movement ───────────────────────────────────────────────────
    if ($path === '/api/cashdesk/edit-movement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_role(['admin', 'cassa', 'reception']);
        header('Content-Type: application/json');
        $id     = (int)   request_input('movement_id');
        $amount = (float) request_input('amount');
        if ($amount <= 0) { echo json_encode(['ok' => false, 'error' => 'Importo non valido']); exit; }
        $pdo = db();
        $mov = $pdo->prepare("SELECT * FROM card_movements WHERE id = ? AND movement_type = 'charge'");
        $mov->execute([$id]);
        $mov = $mov->fetch();
        if (!$mov) { echo json_encode(['ok' => false, 'error' => 'Movimento non trovato']); exit; }
        $pdo->prepare("UPDATE card_movements SET total_amount=?, unit_price=? WHERE id=?")->execute([$amount, $amount, $id]);
        (new BalanceService())->calculate((int) $mov['card_id']);
        audit_log('edit_movement', 'card_movement', $id, ['amount' => $amount]);
        echo json_encode(['ok' => true]); exit;
    }

    // ── Customers Register ───────────────────────────────────────────────────
    if ($path === '/customers/register') {
        require_role(['admin', 'reception']);
        render('customers/register');
        exit;
    }

    // ── Storico ingressi ─────────────────────────────────────────────────────
    if ($path === '/storico/ingressi') {
        require_role(['admin', 'cassa', 'reception']);
        $pdo  = db();
        $from = trim((string) request_input('from', date('Y-m-d', strtotime('-30 days'))));
        $to   = trim((string) request_input('to',   date('Y-m-d')));
        $q    = trim((string) request_input('q', ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');
        if ($to < $from) $to = $from;
        $params = [$from, $to]; $where = 'e.entry_date BETWEEN ? AND ?';
        if ($q !== '') { $where .= ' AND (cu.first_name LIKE ? OR cu.last_name LIKE ? OR c.card_code LIKE ?)'; $params = array_merge($params, ["%$q%", "%$q%", "%$q%"]); }
        $s = $pdo->prepare("SELECT e.id, e.entry_date, e.checkin_at, e.checkout_at, e.status, e.people_count, e.entry_fee, e.paid_amount, e.notes, c.card_code, cu.first_name, cu.last_name FROM entries e JOIN cards c ON c.id = e.card_id JOIN customers cu ON cu.id = e.customer_id WHERE $where ORDER BY e.entry_date DESC, e.checkin_at DESC LIMIT 500");
        $s->execute($params); $entries = $s->fetchAll();
        render('storico/ingressi', compact('entries', 'from', 'to', 'q'));
        exit;
    }

    // ── Storico pagamenti ────────────────────────────────────────────────────
    if ($path === '/storico/pagamenti') {
        require_role(['admin', 'cassa', 'reception']);
        $pdo  = db();
        $from = trim((string) request_input('from', date('Y-m-d', strtotime('-30 days'))));
        $to   = trim((string) request_input('to',   date('Y-m-d')));
        $q    = trim((string) request_input('q', ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');
        if ($to < $from) $to = $from;
        $params = [$from, $to]; $where = 'DATE(m.created_at) BETWEEN ? AND ?';
        if ($q !== '') { $where .= ' AND (cu.first_name LIKE ? OR cu.last_name LIKE ? OR c.card_code LIKE ?)'; $params = array_merge($params, ["%$q%", "%$q%", "%$q%"]); }
        $s = $pdo->prepare("SELECT m.id, m.created_at, m.movement_type, m.department, m.description, m.total_amount, m.quantity, m.unit_price, m.status, m.notes, c.card_code, cu.first_name, cu.last_name FROM card_movements m JOIN cards c ON c.id = m.card_id JOIN customers cu ON cu.id = c.customer_id WHERE $where ORDER BY m.created_at DESC LIMIT 1000");
        $s->execute($params); $movements = $s->fetchAll();
        render('storico/pagamenti', compact('movements', 'from', 'to', 'q'));
        exit;
    }

    http_response_code(404);
    render('errors/404');
} catch (Throwable $e) {
    http_response_code(500);
    render('errors/500', ['error' => app_config('debug') ? $e->getMessage() : 'Errore applicativo.']);
}
