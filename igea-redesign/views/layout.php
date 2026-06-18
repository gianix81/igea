<?php
$user = current_user();
$reqPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$base = rtrim(app_config('base_url', ''), '/');
if ($base && str_starts_with($reqPath, $base)) {
    $reqPath = '/' . ltrim(substr($reqPath, strlen($base)), '/');
}
$nav = [
    '/'             => 'Dashboard',
    '/customers'    => 'Clienti',
    '/cards'        => 'Card',
    '/entries'      => 'Reception',
    '/bar'          => 'Bar',
    '/cashdesk'     => 'Cassa',
    '/reservations' => 'Prenotazioni',
    '/places'       => 'Posti',
    '/reports'      => 'Report',
];
$isActive = function (string $href) use ($reqPath): bool {
    return $href === '/' ? $reqPath === '/' : str_starts_with($reqPath, $href);
};
$initials = '';
if ($user) {
    foreach (preg_split('/\s+/', trim($user['name'])) as $p) {
        if ($p !== '') { $initials .= mb_strtoupper(mb_substr($p, 0, 1)); }
    }
    $initials = mb_substr($initials, 0, 2) ?: 'U';
}
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(app_config('name')) ?></title>
  <script>
    /* applica il tema prima del paint per evitare il flash */
    (function () {
      try {
        var t = localStorage.getItem('igea-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) { document.documentElement.setAttribute('data-theme', 'dark'); }
    })();
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
<?php if ($user): ?>
<header class="topbar">
  <a class="brand" href="<?= url('/') ?>"><span class="mark"></span>Igea Club</a>
  <nav class="topnav">
    <?php foreach ($nav as $href => $label): ?>
      <a class="<?= $isActive($href) ? 'active' : '' ?>" href="<?= url($href) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="spacer"></div>
  <div class="tools">
    <div class="theme-toggle" role="group" aria-label="Tema chiaro o scuro">
      <button type="button" data-set="light" title="Tema chiaro" aria-label="Tema chiaro">
        <svg width="15" height="15" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="9" cy="9" r="3.4"/><line x1="9" y1="1.5" x2="9" y2="3"/><line x1="9" y1="15" x2="9" y2="16.5"/><line x1="1.5" y1="9" x2="3" y2="9"/><line x1="15" y1="9" x2="16.5" y2="9"/><line x1="3.8" y1="3.8" x2="4.9" y2="4.9"/><line x1="13.1" y1="13.1" x2="14.2" y2="14.2"/><line x1="3.8" y1="14.2" x2="4.9" y2="13.1"/><line x1="13.1" y1="4.9" x2="14.2" y2="3.8"/></svg>
      </button>
      <button type="button" data-set="dark" title="Tema scuro" aria-label="Tema scuro">
        <svg width="15" height="15" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M14.5 10.6A6 6 0 1 1 7.4 3.5a4.7 4.7 0 0 0 7.1 7.1z"/></svg>
      </button>
    </div>
    <span class="avatar" title="<?= e($user['name'] . ' · ' . $user['role']) ?>"><?= e($initials) ?></span>
    <a class="btn btn-sm btn-outline-secondary" href="<?= url('/logout') ?>">Esci</a>
  </div>
</header>
<?php endif; ?>
<main class="app">
  <?= $content ?>
</main>
<script>
  (function () {
    function setTheme(t) {
      document.documentElement.setAttribute('data-theme', t);
      try { localStorage.setItem('igea-theme', t); } catch (e) {}
    }
    document.querySelectorAll('.theme-toggle button[data-set]').forEach(function (b) {
      b.addEventListener('click', function () { setTheme(b.getAttribute('data-set')); });
    });
  })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
