<?php
$user    = current_user();
$reqPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$base    = rtrim(app_config('base_url', ''), '/');
if ($base && str_starts_with($reqPath, $base)) {
    $reqPath = '/' . ltrim(substr($reqPath, strlen($base)), '/');
}

/* ── Struttura navigazione ────────────────────────────────── */
$navItems = [
    ['href' => '/',          'label' => 'Dashboard', 'section' => 'dashboard'],
    ['href' => '/customers', 'label' => 'Clienti',   'section' => 'clienti'],
    [
        'label'    => 'Piscina',
        'href'     => '/places',
        'main'     => true,
        'children' => [
            ['href' => '/entries', 'label' => 'Reception',     'icon' => '🚪', 'section' => 'reception'],
            ['href' => '/places',  'label' => 'Mappa Piscina', 'icon' => '🏊', 'section' => 'piscina'],
            ['href' => '/tariffe', 'label' => 'Tariffe',       'icon' => '💶', 'section' => 'tariffe'],
        ],
    ],
    [
        'label'    => 'Food',
        'href'     => '/bar',
        'main'     => true,
        'children' => [
            ['href' => '/bar',    'label' => 'Consumazioni',  'icon' => '🍹', 'section' => 'food'],
        ],
    ],
    ['href' => '/cashdesk', 'label' => 'Cassa', 'main' => true, 'section' => 'cassa'],
    ['href' => '/products', 'label' => 'Prodotti', 'section' => 'prodotti'],
    ['label' => 'Report', 'children' => [
        ['href' => '/reports',           'label' => 'Riepilogo',          'icon' => '📊', 'section' => 'report'],
        ['href' => '/storico/ingressi',  'label' => 'Storico ingressi',   'icon' => '🚪', 'section' => 'report'],
        ['href' => '/storico/pagamenti', 'label' => 'Storico pagamenti',  'icon' => '💳', 'section' => 'report'],
    ]],
];
if (has_role('admin')) {
    $navItems[] = ['href' => '/utenti', 'label' => 'Utenti'];
}

/* Nasconde le voci di menu (e i gruppi rimasti senza figlie) per le sezioni
   che l'admin non ha assegnato a questo operatore. */
function filter_nav_items(array $items): array {
    $out = [];
    foreach ($items as $item) {
        if (isset($item['children'])) {
            $item['children'] = array_values(array_filter(
                $item['children'],
                fn($child) => !isset($child['section']) || has_section($child['section'])
            ));
            if ($item['children']) $out[] = $item;
        } elseif (!isset($item['section']) || has_section($item['section'])) {
            $out[] = $item;
        }
    }
    return $out;
}
$navItems = filter_nav_items($navItems);

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

/* Render a single top-nav item (simple link or dropdown group) */
function render_nav_item(array $item, string $reqPath): void {
    $isActive = nav_active($reqPath, $item);
    if (isset($item['href']) && empty($item['children'])) { ?>
        <a class="<?= $isActive ? 'active' : '' ?>" href="<?= url($item['href']) ?>"><?= e($item['label']) ?></a>
    <?php return; }
    ?>
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
    <?php
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
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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
  /* invisible bridge over the 6px gap so hover doesn't drop while moving
     from the toggle down to the dropdown */
  .topnav-dd::before {
    content: ''; position: absolute; top: -8px; left: 0; right: 0; height: 8px;
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

  /* ── Main sections moved to the right, next to the theme switch ── */
  .topnav-main { display: flex; align-items: center; gap: 2px; width: auto; flex-wrap: nowrap; order: 0; overflow: visible; }
  .topnav-main .topnav-dd { left: auto; right: 0; }   /* keep dropdown on-screen */
  .topnav-sep { width: 1px; height: 22px; background: var(--border); margin: 0 8px; display: inline-block; }

  /* ── Tom Select (searchable dropdowns) — match app theme ───── */
  .ts-wrapper { min-width: 0; }
  .ts-wrapper.single .ts-control,
  .ts-wrapper.single.input-active .ts-control,
  .ts-wrapper.single.dropdown-active .ts-control,
  .ts-control {
    background: var(--surface) !important; border: 1px solid var(--border);
    border-radius: 8px; color: var(--text); min-height: 36px;
    box-shadow: none; font-size: 13px;
  }
  .ts-control, .ts-control input, .ts-control input:focus { background: transparent !important; color: var(--text) !important; }
  .ts-wrapper.form-control, .ts-wrapper.form-select { padding: 0; border: none; background: none; }
  .ts-control input, .ts-control input::placeholder { color: var(--text); }
  .ts-wrapper.single .ts-control::after { border-color: var(--muted-2) transparent transparent; }
  .ts-wrapper.focus .ts-control { border-color: var(--accent); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--accent) 20%, transparent); }
  .ts-dropdown {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; color: var(--text); box-shadow: 0 8px 32px rgba(0,0,0,.18);
    margin-top: 4px; z-index: 3000;
  }
  .ts-dropdown .option, .ts-dropdown .no-results, .ts-dropdown .create {
    color: var(--text); padding: 8px 11px; border-radius: 6px;
  }
  .ts-dropdown .option:hover,
  .ts-dropdown .option.active { background: color-mix(in srgb, var(--accent) 16%, transparent); color: var(--text); }
  .ts-dropdown .ts-dropdown-content { padding: 4px; }
  .ts-dropdown-input {
    background: var(--surface-2); border: 1px solid var(--border) !important;
    color: var(--text); border-radius: 7px; margin: 4px;
  }
  .ts-control .item { color: var(--text); }

  /* ── Floating label support for Tom Select ─────────────────── */
  .form-floating > .ts-wrapper { height: calc(3.5rem + 2px); }
  .form-floating > .ts-wrapper .ts-control {
    height: calc(3.5rem + 2px); min-height: calc(3.5rem + 2px);
    padding: 1.625rem .75rem .625rem; border-radius: 8px;
    display: flex; align-items: center;
  }
  .form-floating > .ts-wrapper .ts-control > .item { line-height: 1.2; }
  /* label always floated for selects (a value/placeholder is always shown) */
  .form-floating > .ts-wrapper ~ label {
    opacity: .65; transform: scale(.85) translateY(-.5rem) translateX(.15rem);
    z-index: 3; padding-top: .75rem;
  }
  .form-floating:focus-within > .ts-wrapper ~ label { color: var(--accent); }
  .form-floating > .ts-wrapper .ts-control::after { margin-top: 0; }

  /* Floating label theming (avoid light notch strip on dark surfaces) */
  .form-floating > label { color: var(--muted); padding: .85rem .75rem; }
  .form-floating > label::after { background-color: transparent !important; }
  .form-floating > .form-control:focus ~ label,
  .form-floating:focus-within > label { color: var(--accent); }

  /* Tighter spacing between floated label and the entered value */
  .form-floating > .form-control,
  .form-floating > .form-control:focus {
    padding-top: 1.35rem; padding-bottom: .35rem;
  }
  .form-floating > textarea.form-control { padding-top: 1.5rem; }
  .form-floating > .form-control:focus ~ label,
  .form-floating > .form-control:not(:placeholder-shown) ~ label,
  .form-floating > .form-select ~ label,
  .form-floating > .ts-wrapper ~ label {
    transform: scale(.82) translateY(-.4rem) translateX(.12rem);
  }
  .form-floating > .ts-wrapper .ts-control { padding-top: 1.35rem; padding-bottom: .35rem; }
  </style>
</head>
<body>
<?php if ($user): ?>
<header class="topbar">
  <a class="brand brand-logo-link" href="<?= url('/') ?>">
    <img src="<?= url('/assets/images/logo.png') ?>" alt="iGEA Club" class="brand-img">
  </a>

  <nav class="topnav">
    <?php foreach ($navItems as $item): ?>
      <?php if (empty($item['main'])) render_nav_item($item, $reqPath); ?>
    <?php endforeach; ?>
  </nav>

  <div class="spacer"></div>
  <div class="tools">
    <nav class="topnav topnav-main">
      <?php foreach ($navItems as $item): ?>
        <?php if (!empty($item['main'])) render_nav_item($item, $reqPath); ?>
      <?php endforeach; ?>
    </nav>
    <span class="topnav-sep" aria-hidden="true"></span>
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
  /* ── Searchable dropdowns: turn every <select> into a click-to-open,
        searchable combobox. Opt out with data-no-search on the <select>. ── */
  function initSearchableSelects(root) {
    (root || document).querySelectorAll('select:not([data-no-search])').forEach(function (sel) {
      if (sel.tomselect) return;
      var selectOnly = sel.hasAttribute('data-select-only');
      new TomSelect(sel, {
        create: false,
        allowEmptyOption: true,
        maxOptions: null,
        controlInput: selectOnly ? null : undefined,   // null → no search box, choice only
        placeholder: sel.querySelector('option[value=""]') ? sel.querySelector('option[value=""]').textContent : '',
        onDropdownOpen: function () { this.clearActiveOption(); }
      });
      /* Auto-width for inline selects (filters): slightly wider than the
         longest option. Full-width form selects are left untouched. */
      if (!sel.classList.contains('form-select') && !sel.closest('.form-floating')) {
        autosizeTomSelect(sel);
      }
    });
  }
  function autosizeTomSelect(sel) {
    var ts = sel.tomselect; if (!ts) return;
    var cs = getComputedStyle(ts.control);
    var span = document.createElement('span');
    span.style.cssText = 'position:absolute;visibility:hidden;white-space:nowrap;top:-9999px;left:-9999px';
    span.style.fontFamily = cs.fontFamily;
    span.style.fontSize   = cs.fontSize;
    span.style.fontWeight  = cs.fontWeight;
    span.style.letterSpacing = cs.letterSpacing;
    document.body.appendChild(span);
    var max = 0;
    Array.prototype.forEach.call(sel.options, function (o) {
      span.textContent = o.textContent.trim();
      if (span.offsetWidth > max) max = span.offsetWidth;
    });
    document.body.removeChild(span);
    /* text + horizontal padding + caret + a little extra */
    ts.wrapper.style.width    = 'auto';
    ts.wrapper.style.minWidth = (max + 54) + 'px';
    ts.wrapper.style.maxWidth = '100%';
  }
  /* Helper to set a select's value that works with Tom Select too. */
  function setSelectValue(id, val) {
    var el = document.getElementById(id);
    if (!el) return;
    if (el.tomselect) el.tomselect.setValue(val == null ? '' : String(val), true);
    else el.value = val == null ? '' : val;
  }
  document.addEventListener('DOMContentLoaded', function () { initSearchableSelects(document); });
</script>
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
