<?php
$user    = current_user();
$reqPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$base    = rtrim(app_config('base_url', ''), '/');
if ($base && str_starts_with($reqPath, $base)) {
    $reqPath = '/' . ltrim(substr($reqPath, strlen($base)), '/');
}

/* ── Struttura navigazione ────────────────────────────────── */
$navItems = [
    ['href' => '/',          'label' => 'Dashboard'],
    ['href' => '/customers', 'label' => 'Clienti & Card'],
    [
        'label'    => 'Piscina',
        'href'     => '/places',
        'children' => [
            ['href' => '/entries', 'label' => 'Reception',     'icon' => '🚪'],
            ['href' => '/places',  'label' => 'Mappa Piscina', 'icon' => '🏊'],
        ],
    ],
    [
        'label'    => 'Bar & Risto',
        'href'     => '/bar',
        'children' => [
            ['href' => '/bar',    'label' => 'Consumazioni',  'icon' => '🍹'],
            ['href' => '/scheda', 'label' => 'Schede attive', 'icon' => '📋'],
        ],
    ],
    ['href' => '/products', 'label' => 'Prodotti'],
    ['href' => '/cashdesk', 'label' => 'Cassa'],
    ['label' => 'Report', 'children' => [
        ['href' => '/reports',           'label' => 'Riepilogo',          'icon' => '📊'],
        ['href' => '/storico/ingressi',  'label' => 'Storico ingressi',   'icon' => '🚪'],
        ['href' => '/storico/pagamenti', 'label' => 'Storico pagamenti',  'icon' => '💳'],
    ]],
];

/* is-active helper: check item or any child */
function nav_active(string $reqPath, array $item): bool {
    if (isset($item['href'])) {
        return $item['href'] === '/'
            ? $reqPath === '/'
            : str_starts_with($reqPath, $item['href']);
    }
    foreach ($item['children'] ?? [] as $child) {
        if (nav_active($reqPath, $child)) return true;
    }
    return false;
}

/* initials */
$initials = '';
if ($user) {
    foreach (preg_split('/\s+/', trim($user['name'])) as $p) {
        if ($p !== '') $initials .= mb_strtoupper(mb_substr($p, 0, 1));
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
  <link rel="icon" type="image/png" href="<?= url('/assets/images/logo.png') ?>">
  <script>
    (function () {
      try { document.documentElement.setAttribute('data-theme', localStorage.getItem('igea-theme') || 'dark'); }
      catch (e) { document.documentElement.setAttribute('data-theme', 'dark'); }
    })();
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
  <style>
  /* ── Dropdown navigation ─────────────────────────────────── */
  .topnav-group { position: relative; }

  .topnav-toggle {
    display: flex; align-items: center; gap: 5px;
    padding: 7px 13px; border-radius: 9px; border: none; background: transparent;
    font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer;
    font-family: inherit; transition: background .12s, color .12s;
    white-space: nowrap;
  }
  .topnav-toggle:hover    { background: var(--surface-2); color: var(--text-2); }
  .topnav-toggle.active   { background: var(--accent); color: var(--accent-ink); }
  .topnav-caret {
    font-size: 9px; opacity: .6; transition: transform .18s;
    display: inline-block; margin-top: 1px;
  }
  .topnav-group.open .topnav-caret { transform: rotate(180deg); }

  .topnav-dd {
    position: absolute; top: calc(100% + 6px); left: 0;
    min-width: 200px;
    background: var(--topbar); border: 1px solid var(--border);
    border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,.14);
    padding: 5px; z-index: 200;
    opacity: 0; pointer-events: none;
    transform: translateY(-6px);
    transition: opacity .15s, transform .15s;
  }
  /* desktop: hover opens */
  @media (hover: hover) {
    .topnav-group:hover .topnav-dd { opacity: 1; pointer-events: auto; transform: none; }
  }
  /* always open on .open (click / touch) */
  .topnav-group.open .topnav-dd   { opacity: 1; pointer-events: auto; transform: none; }

  .topnav-dd a {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 11px; border-radius: 8px;
    font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; transition: background .1s, color .1s;
  }
  .topnav-dd a:hover  { background: var(--surface-2); color: var(--text); }
  .topnav-dd a.active { background: color-mix(in srgb, var(--accent) 14%, transparent); color: var(--accent); }
  .topnav-dd .dd-icon { font-size: 15px; width: 20px; text-align: center; flex-shrink: 0; }
  .topnav-dd hr { border-color: var(--border); margin: 4px 6px; }
  </style>
</head>
<body>
<?php if ($user): ?>
<header class="topbar">
  <a class="brand brand-logo-link" href="<?= url('/') ?>">
    <img src="<?= url('/assets/images/logo.png') ?>" alt="iGEA Club" class="brand-img">
  </a>

  <nav class="topnav">
    <?php foreach ($navItems as $item):
      $isActive = nav_active($reqPath, $item);
    ?>

      <?php if (isset($item['href'])): ?>
        <!-- Simple link -->
        <a class="<?= $isActive ? 'active' : '' ?>" href="<?= url($item['href']) ?>"><?= e($item['label']) ?></a>

      <?php else: ?>
        <!-- Dropdown group -->
        <div class="topnav-group">
          <?php if (isset($item['href'])): ?>
          <a class="topnav-toggle <?= $isActive ? 'active' : '' ?>"
             href="<?= url($item['href']) ?>" onclick="event.stopPropagation()">
            <?= e($item['label']) ?>
            <span class="topnav-caret" onclick="event.preventDefault();toggleNav(this.closest('.topnav-group').querySelector('.topnav-toggle'))">▾</span>
          </a>
          <?php else: ?>
          <button class="topnav-toggle <?= $isActive ? 'active' : '' ?>" type="button"
                  aria-expanded="false" onclick="toggleNav(this)">
            <?= e($item['label']) ?>
            <span class="topnav-caret">▾</span>
          </button>
          <?php endif; ?>
          <div class="topnav-dd">
            <?php foreach ($item['children'] as $child):
              $childActive = nav_active($reqPath, $child);
            ?>
              <a href="<?= url($child['href']) ?>" class="<?= $childActive ? 'active' : '' ?>">
                <?php if ($child['icon'] ?? null): ?>
                  <span class="dd-icon"><?= $child['icon'] ?></span>
                <?php endif; ?>
                <?= e($child['label']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php endforeach; ?>
  </nav>

  <div class="spacer"></div>
  <div class="tools">
    <div class="theme-toggle" role="group" aria-label="Tema">
      <button type="button" data-set="light" title="Chiaro">
        <svg width="15" height="15" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="9" cy="9" r="3.4"/><line x1="9" y1="1.5" x2="9" y2="3"/><line x1="9" y1="15" x2="9" y2="16.5"/><line x1="1.5" y1="9" x2="3" y2="9"/><line x1="15" y1="9" x2="16.5" y2="9"/><line x1="3.8" y1="3.8" x2="4.9" y2="4.9"/><line x1="13.1" y1="13.1" x2="14.2" y2="14.2"/><line x1="3.8" y1="14.2" x2="4.9" y2="13.1"/><line x1="13.1" y1="4.9" x2="14.2" y2="3.8"/></svg>
      </button>
      <button type="button" data-set="dark" title="Scuro">
        <svg width="15" height="15" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M14.5 10.6A6 6 0 1 1 7.4 3.5a4.7 4.7 0 0 0 7.1 7.1z"/></svg>
      </button>
    </div>
    <span class="avatar" title="<?= e($user['name'] . ' · ' . $user['role']) ?>"><?= e($initials) ?></span>
    <a class="btn btn-sm btn-outline-secondary" href="<?= url('/logout') ?>">Esci</a>
  </div>
</header>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
<script>
  /* Theme */
  (function () {
    function setTheme(t) {
      document.documentElement.setAttribute('data-theme', t);
      try { localStorage.setItem('igea-theme', t); } catch (e) {}
    }
    document.querySelectorAll('.theme-toggle button[data-set]').forEach(function (b) {
      b.addEventListener('click', function () { setTheme(b.getAttribute('data-set')); });
    });
  })();

  /* Dropdown: click / touch toggle */
  function toggleNav(el) {
    var group  = el.closest('.topnav-group');
    var isOpen = group.classList.contains('open');
    document.querySelectorAll('.topnav-group.open').forEach(function (g) { g.classList.remove('open'); });
    if (!isOpen) {
      group.classList.add('open');
      if (el.setAttribute) el.setAttribute('aria-expanded','true');
    } else {
      if (el.setAttribute) el.setAttribute('aria-expanded','false');
    }
  }
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.topnav-group')) {
      document.querySelectorAll('.topnav-group.open').forEach(function (g) { g.classList.remove('open'); });
    }
  });
</script>

<main class="app">
  <?= $content ?>
</main>
<?php include __DIR__ . '/components/card-verify-modal.php'; ?>
</body>
</html>
