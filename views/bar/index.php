<style>
/* ── Bar / Ristorazione ─────────────────────────────── */
.bar-layout { display:grid; grid-template-columns:320px 1fr; gap:20px; padding:20px; }
@media(max-width:900px){ .bar-layout{grid-template-columns:1fr;} }

/* Left panel */
.bar-panel-title {
  font-family:'Poppins',sans-serif;
  font-size:20px; font-weight:800; color:var(--text); margin-bottom:18px;
}

/* Search */
.bar-search-wrap { position:relative; margin-bottom:10px; }
.bar-search-input {
  width:100%; height:50px; padding:0 44px 0 16px;
  border:2px solid var(--border); border-radius:12px;
  background:var(--surface-2); color:var(--text); font-size:15px;
  outline:none; transition:border-color .15s;
}
.bar-search-input:focus { border-color:var(--accent); background:var(--surface); }
.bar-search-icon {
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  color:var(--muted-2); pointer-events:none;
}
.bar-search-btn {
  width:100%; height:50px; border-radius:12px;
  background:var(--accent); color:#fff; border:none;
  font-size:15px; font-weight:700; cursor:pointer; transition:opacity .15s;
}
.bar-search-btn:hover { opacity:.88; }

/* Dropdown */
.bar-dropdown {
  position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:300;
  background:var(--surface); border:1.5px solid var(--border);
  border-radius:14px; box-shadow:0 8px 40px rgba(0,0,0,.22);
  max-height:420px; overflow-y:auto; display:none;
}
.bar-dropdown.open { display:block; }
.bar-dd-header {
  padding:8px 14px 6px; font-size:10px; font-weight:800; text-transform:uppercase;
  letter-spacing:.1em; color:var(--muted-2); border-bottom:1px solid var(--border);
  position:sticky; top:0; background:var(--surface); z-index:1;
}
.bar-dd-item {
  display:flex; align-items:center; gap:12px;
  padding:11px 14px; cursor:pointer; transition:background .1s; border:none;
  background:transparent; width:100%; text-align:left;
  border-bottom:1px solid var(--border);
}
.bar-dd-item:last-child { border-bottom:none; }
.bar-dd-item:hover, .bar-dd-item:focus { background:var(--surface-2); outline:none; }
.bar-dd-avatar {
  width:36px; height:36px; border-radius:50%; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-size:12px; font-weight:800; color:#fff;
}
.bar-dd-name  { font-size:14px; font-weight:700; color:var(--text); }
.bar-dd-sub   { font-size:11px; color:var(--muted); margin-top:1px; }
.bar-dd-right { margin-left:auto; text-align:right; flex-shrink:0; }
.bar-dd-code  { font-family:monospace; font-size:12px; color:var(--accent); font-weight:700; }
.bar-dd-inside  { font-size:10px; color:var(--good); font-weight:800; }
.bar-dd-outside { font-size:10px; color:var(--muted-2); font-weight:700; }
.bar-dd-bal-open { color:var(--bad); font-size:12px; font-weight:800; }
.bar-dd-bal-ok   { color:var(--good); font-size:11px; }
.bar-dd-empty    { padding:18px; text-align:center; color:var(--muted-2); font-size:13px; }
.bar-dd-loading  { padding:14px; text-align:center; color:var(--muted-2); font-size:12px; }

/* Card info */
.bar-card-banner {
  background:linear-gradient(135deg,#0b3e50,#0e6276);
  border-radius:14px; padding:18px 20px; margin-top:14px; color:#fff;
}
.bar-card-name   { font-size:17px; font-weight:800; margin-bottom:5px; }
.bar-card-code   { font-family:monospace; font-size:12px; opacity:.65; margin-bottom:10px; }
.bar-card-badges { display:flex; gap:6px; flex-wrap:wrap; }
.bar-cbadge {
  padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:.05em;
}
.bar-cbadge-green  { background:rgba(95,208,163,.25); color:#b7f5d8; }
.bar-cbadge-red    { background:rgba(255,142,118,.25); color:#ffb3a7; }
.bar-cbadge-ghost  { background:rgba(255,255,255,.12); color:rgba(255,255,255,.75); }
.bar-balance-box   { text-align:center; padding:12px 0 4px; }
.bar-balance-label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:2px; }
.bar-balance-num   { font-family:'Poppins',sans-serif; font-size:30px; font-weight:800; }
.bar-balance-num.open { color:var(--bad); }
.bar-balance-num.ok   { color:var(--good); }

/* Dept legend */
.bar-legend { display:flex; gap:12px; flex-wrap:wrap; margin-top:14px; font-size:11px; font-weight:600; color:var(--muted); }
.bar-leg-dot { display:inline-block; width:7px; height:7px; border-radius:50%; margin-right:4px; }

/* Right */
.bar-products-panel { padding:22px; }
.bar-products-top { display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.bar-products-title { font-family:'Poppins',sans-serif; font-size:18px; font-weight:800; }
.bar-dept-pills { display:flex; gap:6px; flex-wrap:wrap; }
.bar-dept-pill {
  padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700;
  border:1.5px solid var(--border); color:var(--muted); background:var(--surface-2);
  cursor:pointer; transition:all .15s; white-space:nowrap; user-select:none;
}
.bar-dept-pill.active { background:var(--accent); border-color:var(--accent); color:#fff; }
.bar-prod-filter {
  width:100%; height:40px; padding:0 14px; border:1.5px solid var(--border);
  border-radius:10px; background:var(--surface-2); color:var(--text);
  font-size:14px; outline:none; margin-bottom:16px; transition:border-color .15s;
}
.bar-prod-filter:focus { border-color:var(--accent); }

/* ── Department color palette ───────────────────────────── */
:root {
  --c-bar:   #17b3c4;
  --c-risto: #e8a020;
  --c-recep: #0b5e74;
  --c-extra: #7a6bb0;
}

/* Card tint + top stripe per reparto */
.product-button { cursor: default; overflow: hidden; position: relative; }
.product-button.dept-bar        { border-top: 4px solid var(--c-bar);   background: color-mix(in srgb, var(--c-bar)   9%, var(--surface)); }
.product-button.dept-ristorante { border-top: 4px solid var(--c-risto); background: color-mix(in srgb, var(--c-risto) 9%, var(--surface)); }
.product-button.dept-reception  { border-top: 4px solid var(--c-recep); background: color-mix(in srgb, var(--c-recep) 9%, var(--surface)); }
.product-button.dept-extra      { border-top: 4px solid var(--c-extra); background: color-mix(in srgb, var(--c-extra) 9%, var(--surface)); }
.product-button.dept-altro      { border-top: 4px solid var(--muted-2); }

/* Foto d'esempio prodotto */
.pb-thumb { width: 100%; height: 64px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; display: block; }

/* Dept badge (small pill at top of card) */
.pb-badge {
  display: inline-block;
  font-size: 8px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .09em; padding: 2px 8px; border-radius: 5px;
  margin-bottom: 8px;
}
.dept-bar .pb-badge        { background: color-mix(in srgb, var(--c-bar)   18%, transparent); color: #0b6e7e; }
.dept-ristorante .pb-badge { background: color-mix(in srgb, var(--c-risto) 18%, transparent); color: #7a5200; }
.dept-reception .pb-badge  { background: color-mix(in srgb, var(--c-recep) 18%, transparent); color: var(--c-recep); }
.dept-extra .pb-badge      { background: color-mix(in srgb, var(--c-extra) 18%, transparent); color: var(--c-extra); }

/* Card content */
.pb-name  { font-size: 14px; font-weight: 800; color: var(--text); display: block; line-height: 1.2; margin-bottom: 2px; }
.pb-cat   { font-size: 10px; color: var(--muted); margin-bottom: 10px; }
.pb-price {
  font-family: 'Poppins', sans-serif;
  font-size: 24px; font-weight: 800; line-height: 1; margin-bottom: 10px;
}
.dept-bar .pb-price        { color: var(--c-bar);   }
.dept-ristorante .pb-price { color: var(--c-risto); }
.dept-reception .pb-price  { color: var(--c-recep); }
.dept-extra .pb-price      { color: var(--c-extra); }

/* Add button — replaces Bootstrap outline-primary */
.bar-add-btn {
  width: 100%; padding: 9px 0; border-radius: 9px;
  font-size: 12.5px; font-weight: 800; cursor: pointer;
  font-family: inherit; transition: background .15s, color .15s;
  background: transparent;
}
.dept-bar .bar-add-btn              { border: 2px solid var(--c-bar);   color: var(--c-bar); }
.dept-bar .bar-add-btn:not(:disabled):hover { background: var(--c-bar);   color: var(--accent-ink); }
.dept-ristorante .bar-add-btn       { border: 2px solid var(--c-risto); color: var(--c-risto); }
.dept-ristorante .bar-add-btn:not(:disabled):hover { background: var(--c-risto); color: #fff; }
.dept-reception .bar-add-btn        { border: 2px solid var(--c-recep); color: var(--c-recep); }
.dept-reception .bar-add-btn:not(:disabled):hover  { background: var(--c-recep); color: #fff; }
.dept-extra .bar-add-btn            { border: 2px solid var(--c-extra); color: var(--c-extra); }
.dept-extra .bar-add-btn:not(:disabled):hover      { background: var(--c-extra); color: #fff; }
.dept-altro .bar-add-btn            { border: 2px solid var(--muted); color: var(--muted); }
.bar-add-btn:disabled { opacity: .35; cursor: not-allowed; }

.bar-no-card {
  text-align:center; padding:48px 20px; color:var(--muted-2);
}
.bar-no-card-icon { font-size:48px; opacity:.4; margin-bottom:12px; }
</style>

<?php
$cardTypes = [
  'nominale'    => 'Nominale',
  'abbonamento' => 'Abbonamento',
  'ospite'      => 'Ospite',
  'staff'       => 'Staff',
];
$deptLabels = [
  'bar'        => 'Bar',
  'ristorante' => 'Ristorante',
  'reception'  => 'Reception',
  'extra'      => 'Extra',
];
$depts = [];
foreach ($products as $p) {
    $d = $p['department'] ?? 'altro';
    $depts[$d] = true;
}
$deptKeys = array_keys($depts);
?>

<div class="bar-layout">

  <!-- ── LEFT ──────────────────────────────────────────────── -->
  <div>
    <div class="panel" style="padding:20px">
      <div class="bar-panel-title">Seleziona un cliente</div>

      <form method="get" id="cardForm" autocomplete="off">
        <div class="bar-search-wrap">
          <input
            class="bar-search-input"
            type="text"
            name="code"
            id="cardSearch"
            value="<?= e($code) ?>"
            placeholder="Codice card, nome, telefono…"
            autofocus
            autocomplete="off"
          >
          <svg class="bar-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <div class="bar-dropdown" id="barDropdown"></div>
        </div>
        <button type="submit" class="bar-search-btn">Carica card</button>
      </form>

      <div class="bar-legend">
        <span><span class="bar-leg-dot" style="background:var(--accent)"></span>Bar</span>
        <span><span class="bar-leg-dot" style="background:#e8a020"></span>Ristorazione</span>
        <span><span class="bar-leg-dot" style="background:var(--good)"></span>Snack</span>
        <span><span class="bar-leg-dot" style="background:#b07d11"></span>Caffetteria</span>
      </div>
    </div>

    <?php if ($card): ?>
    <div class="bar-card-banner mt-3">
      <div class="bar-card-name"><?= e($card['customer_name']) ?></div>
      <div class="bar-card-code"><?= e($card['card_code']) ?></div>
      <div class="bar-card-badges">
        <?php if ($card['is_inside']): ?>
          <span class="bar-cbadge bar-cbadge-green">● Dentro</span>
        <?php else: ?>
          <span class="bar-cbadge bar-cbadge-red">○ Fuori</span>
        <?php endif; ?>
        <?php if ($card['balance'] > 0): ?>
          <span class="bar-cbadge bar-cbadge-red">Saldo aperto</span>
        <?php else: ?>
          <span class="bar-cbadge bar-cbadge-green">Saldato</span>
        <?php endif; ?>
        <?php if (!empty($card['card_type'])): ?>
          <span class="bar-cbadge bar-cbadge-ghost"><?= e($cardTypes[$card['card_type']] ?? $card['card_type']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Carrello -->
    <div class="panel mt-2" id="cartPanel" style="padding:14px 16px">
      <div style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);margin-bottom:8px;display:flex;align-items:center;gap:6px">
        Ordine in corso
        <span id="cartCount" style="background:var(--accent);color:var(--accent-ink);border-radius:10px;padding:1px 6px;font-size:9px">0</span>
      </div>
      <div id="cartItems" style="display:flex;flex-direction:column;gap:4px;min-height:36px">
        <div id="cartEmpty" style="color:var(--muted-2);font-size:12px;text-align:center;padding:8px 0">Nessun prodotto aggiunto</div>
      </div>
      <div id="cartFooter" style="display:none;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <span style="font-size:11px;font-weight:700;color:var(--muted)">Totale ordine</span>
          <span id="cartTotal" style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:800;color:var(--accent)">€ 0,00</span>
        </div>
        <button type="button" id="cartSendBtn" style="width:100%;padding:11px;border-radius:10px;border:none;background:var(--accent);color:var(--accent-ink);font-size:13px;font-weight:800;cursor:pointer;font-family:inherit">
          Invia ordine →
        </button>
      </div>
    </div>

    <?php elseif ($code !== ''): ?>
    <div class="alert alert-warning mt-3 rounded-3">Card <strong><?= e($code) ?></strong> non trovata.</div>
    <?php endif; ?>
  </div>

  <!-- ── RIGHT ─────────────────────────────────────────────── -->
  <div class="panel">
    <div class="bar-products-top">
      <span class="bar-products-title">Prodotti</span>
      <?php if (!empty($deptKeys)): ?>
      <div class="bar-dept-pills" id="deptPills">
        <span class="bar-dept-pill active" data-dept="tutti">Tutti</span>
        <?php foreach ($deptKeys as $d): ?>
          <span class="bar-dept-pill" data-dept="<?= e($d) ?>"><?= e(ucfirst($d)) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <input type="text" class="bar-prod-filter" id="prodFilter" placeholder="🔍 Filtra prodotti…">

    <?php if (!$card): ?>
    <div class="alert alert-info rounded-3 mb-3" style="font-size:13px">
      💳 Cerca e seleziona una card a sinistra per abilitare gli addebiti.
    </div>
    <?php endif; ?>

    <div class="product-grid" id="productGrid">
      <?php foreach ($products as $p):
        $dept      = $p['department'] ?? 'altro';
        $deptClass = 'dept-' . $dept;
        $deptLabel = $deptLabels[$dept] ?? ucfirst($dept);
      ?>
      <div class="product-button <?= $deptClass ?>"
           data-id="<?= (int)$p['id'] ?>"
           data-name="<?= strtolower(e($p['name'])) ?>"
           data-cat="<?= strtolower(e($p['category_name'])) ?>"
           data-dept="<?= e($dept) ?>"
           data-prod-name="<?= e($p['name']) ?>"
           data-prod-price="<?= (float)$p['price'] ?>"
           <?= !$card ? 'style="opacity:.4;pointer-events:none"' : '' ?>>
        <?php if (!empty($p['image_path'])): ?>
          <img class="pb-thumb" src="<?= url('/' . e($p['image_path'])) ?>" alt="">
        <?php endif; ?>
        <div class="pb-badge"><?= e($deptLabel) ?></div>
        <div class="pb-name"><?= e($p['name']) ?></div>
        <div class="pb-cat"><?= e($p['category_name']) ?></div>
        <div class="pb-price">€&nbsp;<?= number_format((float)$p['price'], 2, ',', '.') ?></div>
        <button type="button" class="bar-add-btn" <?= !$card ? 'disabled' : '' ?>>+ Aggiungi</button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Form nascosto per l'invio batch -->
<form id="barOrderForm" method="post" style="display:none">
  <?= csrf_field() ?>
  <input type="hidden" name="card_code" value="<?= e($card['card_code'] ?? '') ?>">
  <input type="hidden" name="force_charge" value="0" id="barForceCharge">
</form>

<!-- Popup conferma ordine completo -->
<div id="barConfirmOverlay" style="display:none;position:fixed;inset:0;z-index:1800;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center">
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;width:400px;max-width:94vw;max-height:90vh;box-shadow:0 24px 64px rgba(0,0,0,.35);overflow:hidden;display:flex;flex-direction:column">
    <div style="background:color-mix(in srgb,var(--accent) 12%,var(--surface));border-bottom:1px solid var(--border);padding:13px 18px;display:flex;align-items:center;gap:8px;flex-shrink:0">
      <span style="font-size:16px">🧾</span>
      <span style="font-family:'Poppins',sans-serif;font-size:13px;font-weight:800;color:var(--text);text-transform:uppercase;letter-spacing:.06em">Conferma ordine</span>
    </div>
    <div style="padding:16px 20px;overflow-y:auto;flex:1">
      <div id="bco_customer" style="font-size:13px;font-weight:700;color:var(--muted);margin-bottom:12px"></div>
      <div id="bco_items" style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px"></div>
      <div style="border-top:2px solid var(--border);padding-top:10px;display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">Totale</span>
        <span id="bco_total" style="font-family:'Poppins',sans-serif;font-size:24px;font-weight:800;color:var(--accent)"></span>
      </div>
      <div id="bco_warn" style="display:none;margin-top:10px;background:color-mix(in srgb,var(--warn) 12%,transparent);border:1px solid color-mix(in srgb,var(--warn) 30%,transparent);border-radius:9px;padding:9px 13px;font-size:12px;color:var(--warn)">
        ⚠ <strong>Cliente non in struttura.</strong> L'ordine verrà registrato ugualmente.
      </div>
    </div>
    <div style="display:flex;gap:8px;padding:14px 20px;border-top:1px solid var(--border);flex-shrink:0">
      <button id="bco_cancel" style="flex:1;padding:11px;border-radius:10px;border:1px solid var(--border);background:var(--surface-2);color:var(--text);font-size:13px;font-weight:800;cursor:pointer;font-family:inherit">Annulla</button>
      <button id="bco_confirm" style="flex:2;padding:11px;border-radius:10px;border:none;background:var(--accent);color:var(--accent-ink);font-size:13px;font-weight:800;cursor:pointer;font-family:inherit">✓ Addebita tutto</button>
    </div>
  </div>
</div>

<script>
const SEARCH_URL = '<?= url('/api/bar/search-card.php') ?>';

/* ── Avatar helpers ──────────────────────────────────────── */
function avatarColor(name) {
  var p = ['#17b3c4','#2f9e72','#7a6bb0','#e8624a','#b07d11','#0b5e74'];
  var h = 0; for (var i=0;i<name.length;i++) h=(h*31+name.charCodeAt(i))&0xffffffff;
  return p[Math.abs(h)%p.length];
}
function initials(name) {
  var pts = name.trim().split(/\s+/);
  return ((pts[0]||'')[0]||'').toUpperCase() + ((pts[1]||'')[0]||'').toUpperCase();
}

/* ── Dropdown search ─────────────────────────────────────── */
var ddEl       = document.getElementById('barDropdown');
var inpEl      = document.getElementById('cardSearch');
var _timer     = null;
var _ddResults = [];

function closeDd() { ddEl.classList.remove('open'); }

function renderDd(results, label) {
  _ddResults = results || [];
  if (!results || results.length === 0) {
    ddEl.innerHTML = '<div class="bar-dd-empty">Nessun risultato</div>';
    ddEl.classList.add('open'); return;
  }
  var html = '<div class="bar-dd-header">' + label + '</div>';
  results.forEach(function(r) {
    var col = avatarColor(r.customer_name);
    var ini = initials(r.customer_name);
    var avatarHtml = r.photo_url
      ? '<div class="bar-dd-avatar" style="padding:0;overflow:hidden"><img src="' + r.photo_url + '" alt="" style="width:100%;height:100%;object-fit:cover"></div>'
      : '<div class="bar-dd-avatar" style="background:' + col + '">' + ini + '</div>';
    var balHtml = r.balance > 0
      ? '<span class="bar-dd-bal-open">€ ' + r.balance.toFixed(2).replace('.',',') + '</span>'
      : '<span class="bar-dd-bal-ok">✓ Saldato</span>';
    var inHtml = r.is_inside
      ? '<span class="bar-dd-inside">● DENTRO</span>'
      : '<span class="bar-dd-outside">○ fuori</span>';
    html += '<button type="button" class="bar-dd-item" data-code="' + r.card_code + '">'
      + avatarHtml
      + '<div style="flex:1;min-width:0">'
      + '<div class="bar-dd-name">' + r.customer_name + '</div>'
      + '<div class="bar-dd-sub">' + (r.phone ? '📞 ' + r.phone + ' · ' : '') + r.card_code + '</div>'
      + '</div>'
      + '<div class="bar-dd-right">' + inHtml + '<br>' + balHtml + '</div>'
      + '</button>';
  });
  ddEl.innerHTML = html;
  ddEl.classList.add('open');
  ddEl.querySelectorAll('.bar-dd-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var code   = btn.getAttribute('data-code');
      var record = _ddResults.find(function(r){ return r.card_code === code; }) || {};
      closeDd();
      CardVerify.show(record, function() {
        inpEl.value = code;
        document.getElementById('cardForm').submit();
      });
    });
  });
}

function doSearch(q) {
  ddEl.innerHTML = '<div class="bar-dd-loading">Ricerca…</div>';
  ddEl.classList.add('open');
  fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
    .then(function(r){ return r.json(); })
    .then(function(d){ renderDd(d.results, q === '' ? 'Ospiti registrati oggi' : 'Risultati per "' + q + '"'); })
    .catch(closeDd);
}

inpEl.addEventListener('focus', function() { doSearch(inpEl.value.trim()); });
inpEl.addEventListener('input', function() {
  clearTimeout(_timer);
  _timer = setTimeout(function(){ doSearch(inpEl.value.trim()); }, 260);
});
document.addEventListener('click', function(e) {
  if (!inpEl.contains(e.target) && !ddEl.contains(e.target)) closeDd();
});
inpEl.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { closeDd(); inpEl.blur(); }
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    var items = ddEl.querySelectorAll('.bar-dd-item');
    if (items.length) items[0].focus();
  }
});
ddEl.addEventListener('keydown', function(e) {
  var items = Array.from(ddEl.querySelectorAll('.bar-dd-item'));
  var idx   = items.indexOf(document.activeElement);
  if (e.key === 'ArrowDown' && idx < items.length-1) { e.preventDefault(); items[idx+1].focus(); }
  if (e.key === 'ArrowUp') {
    e.preventDefault();
    if (idx > 0) items[idx-1].focus(); else inpEl.focus();
  }
  if (e.key === 'Escape') { closeDd(); inpEl.focus(); }
});

/* ── Dept pills + text filter ────────────────────────────── */
document.querySelectorAll('.bar-dept-pill').forEach(function(pill) {
  pill.addEventListener('click', function() {
    document.querySelectorAll('.bar-dept-pill').forEach(function(p){ p.classList.remove('active'); });
    pill.classList.add('active');
    filterProducts();
  });
});
var prodFilter = document.getElementById('prodFilter');
if (prodFilter) prodFilter.addEventListener('input', filterProducts);

function filterProducts() {
  var dept = (document.querySelector('.bar-dept-pill.active')||{getAttribute:function(){return'tutti';}}).getAttribute('data-dept');
  var q    = prodFilter ? prodFilter.value.trim().toLowerCase() : '';
  document.querySelectorAll('#productGrid .product-button').forEach(function(card) {
    var okDept = dept === 'tutti' || card.getAttribute('data-dept') === dept;
    var okQ    = !q || card.getAttribute('data-name').includes(q) || card.getAttribute('data-cat').includes(q);
    card.style.display = (okDept && okQ) ? '' : 'none';
  });
}

/* ── Cart state ──────────────────────────────────────────── */
var BAR_CARD_INSIDE = <?= json_encode((bool)($card['is_inside'] ?? true)) ?>;
var BAR_CARD_NAME   = <?= json_encode($card['customer_name'] ?? '') ?>;
var BAR_CARD_CODE   = <?= json_encode($card['card_code'] ?? '') ?>;
var cart = [];

function fmtMoney(val) { return '€ ' + val.toFixed(2).replace('.', ','); }

function renderCart() {
  var count    = cart.reduce(function(s,i){ return s+i.qty; }, 0);
  var total    = cart.reduce(function(s,i){ return s+i.qty*i.price; }, 0);
  var countEl  = document.getElementById('cartCount');
  var itemsEl  = document.getElementById('cartItems');
  var emptyEl  = document.getElementById('cartEmpty');
  var footerEl = document.getElementById('cartFooter');
  var totalEl  = document.getElementById('cartTotal');

  if (countEl) countEl.textContent = count;
  itemsEl.querySelectorAll('.cart-row').forEach(function(r){ r.remove(); });

  if (cart.length === 0) {
    if (emptyEl) emptyEl.style.display = '';
    if (footerEl) footerEl.style.display = 'none';
    return;
  }
  if (emptyEl) emptyEl.style.display = 'none';
  if (footerEl) footerEl.style.display = '';
  if (totalEl) totalEl.textContent = fmtMoney(total);

  cart.forEach(function(item, idx) {
    var row = document.createElement('div');
    row.className = 'cart-row';
    row.style.cssText = 'display:flex;align-items:center;gap:6px;padding:5px 0;border-bottom:1px solid var(--border)';
    row.innerHTML = '<div style="flex:1;min-width:0">'
      + '<div style="font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + item.name + '</div>'
      + '<div style="font-size:10px;color:var(--muted)">' + fmtMoney(item.price) + ' × ' + item.qty + '</div>'
      + '</div>'
      + '<span style="font-size:12px;font-weight:800;color:var(--accent);white-space:nowrap">' + fmtMoney(item.price*item.qty) + '</span>'
      + '<button type="button" data-idx="' + idx + '" style="border:none;background:transparent;color:var(--bad);font-size:14px;cursor:pointer;padding:0 4px;line-height:1;flex-shrink:0">✕</button>';
    itemsEl.appendChild(row);
  });

  itemsEl.querySelectorAll('[data-idx]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      cart.splice(parseInt(btn.getAttribute('data-idx')), 1);
      renderCart();
    });
  });
}

/* ── "Aggiungi" click on product cards ───────────────────── */
document.querySelectorAll('.bar-add-btn').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    var card   = btn.closest('.product-button');
    var id     = parseInt(card.getAttribute('data-id'));
    var name   = card.getAttribute('data-prod-name');
    var price  = parseFloat(card.getAttribute('data-prod-price'));
    var exists = cart.find(function(i){ return i.id === id; });
    if (exists) { exists.qty++; } else { cart.push({id:id,name:name,price:price,qty:1}); }
    renderCart();

    /* feedback visivo */
    btn.textContent = '✓ Aggiunto';
    btn.style.background = 'var(--good)';
    btn.style.color = '#fff';
    btn.style.border = 'none';
    setTimeout(function(){
      btn.textContent = '+ Aggiungi';
      btn.style.background = '';
      btn.style.color = '';
      btn.style.border = '';
    }, 800);
  });
});

/* ── "Invia ordine" → apri popup ─────────────────────────── */
var cartSendBtn = document.getElementById('cartSendBtn');
if (cartSendBtn) cartSendBtn.addEventListener('click', openOrderPopup);

function openOrderPopup() {
  if (cart.length === 0) return;
  var total   = cart.reduce(function(s,i){ return s+i.qty*i.price; }, 0);
  var custEl  = document.getElementById('bco_customer');
  var itemsEl = document.getElementById('bco_items');
  var totalEl = document.getElementById('bco_total');
  var warnEl  = document.getElementById('bco_warn');
  var overlay = document.getElementById('barConfirmOverlay');

  if (custEl) custEl.textContent = BAR_CARD_NAME + ' · ' + BAR_CARD_CODE;
  itemsEl.innerHTML = '';
  cart.forEach(function(item) {
    var row = document.createElement('div');
    row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:7px 12px;background:var(--surface-2);border-radius:8px';
    row.innerHTML = '<div>'
      + '<div style="font-size:13px;font-weight:700;color:var(--text)">' + item.name + '</div>'
      + '<div style="font-size:11px;color:var(--muted)">' + fmtMoney(item.price) + ' × ' + item.qty + '</div>'
      + '</div>'
      + '<div style="font-size:14px;font-weight:800;color:var(--text)">' + fmtMoney(item.price*item.qty) + '</div>';
    itemsEl.appendChild(row);
  });
  if (totalEl) totalEl.textContent = fmtMoney(total);
  if (warnEl)  warnEl.style.display = BAR_CARD_INSIDE ? 'none' : '';
  overlay.style.display = 'flex';
  document.getElementById('bco_confirm').focus();
}

function closeOrderPopup() {
  document.getElementById('barConfirmOverlay').style.display = 'none';
}

document.getElementById('bco_cancel').addEventListener('click', closeOrderPopup);
document.getElementById('barConfirmOverlay').addEventListener('click', function(e){
  if (e.target === this) closeOrderPopup();
});
document.addEventListener('keydown', function(e){
  var ov = document.getElementById('barConfirmOverlay');
  if (ov && ov.style.display === 'flex' && e.key === 'Escape') closeOrderPopup();
});

document.getElementById('bco_confirm').addEventListener('click', function() {
  if (cart.length === 0) return;
  var form = document.getElementById('barOrderForm');
  form.querySelectorAll('[name^="items"]').forEach(function(el){ el.remove(); });
  cart.forEach(function(item, idx) {
    var p = document.createElement('input'); p.type='hidden'; p.name='items['+idx+'][product_id]'; p.value=item.id; form.appendChild(p);
    var q = document.createElement('input'); q.type='hidden'; q.name='items['+idx+'][quantity]';   q.value=item.qty; form.appendChild(q);
  });
  if (!BAR_CARD_INSIDE) document.getElementById('barForceCharge').value = '1';
  closeOrderPopup();
  form.submit();
});
</script>
