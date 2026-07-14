<?php
$statusColor = [
    'attivo'    => 'success',
    'sospeso'   => 'warning',
    'blacklist' => 'danger',
];
$cardStatusColor = [
    'attiva'   => 'success',
    'chiusa'   => 'secondary',
    'bloccata' => 'danger',
    'smarrita' => 'warning',
];
$cardTypeLabel = [
    'nominale'    => 'Nominale',
    'abbonamento' => 'Abbonamento',
    'ospite'      => 'Ospite',
    'staff'       => 'Staff',
];
$movType = [
    'charge'     => ['label' => 'Addebito',  'color' => 'danger'],
    'payment'    => ['label' => 'Pagamento', 'color' => 'success'],
    'refund'     => ['label' => 'Rimborso',  'color' => 'info'],
    'adjustment' => ['label' => 'Rettifica', 'color' => 'warning'],
];
$deptLabel = [
    'bar'        => 'Bar',
    'ristorante' => 'Ristorante',
    'reception'  => 'Reception',
    'cassa'      => 'Cassa',
    'extra'      => 'Extra',
];

// Avatar color from name
function cu_avatar_color(string $name): string {
    $palette = ['#17b3c4','#2f9e72','#7a6bb0','#e8624a','#b07d11','#0b5e74','#e8a020'];
    return $palette[abs(crc32($name)) % count($palette)];
}
function cu_initials(string $first, string $last): string {
    return mb_strtoupper(mb_substr($last, 0, 1) . mb_substr($first, 0, 1));
}
?>
<style>
/* ── Customers & Card — rich design ─────────────────────────── */
.cu-page { padding: 24px; }

/* KPI tiles */
.cu-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px; }
.cu-kpi {
  background: var(--surface); border:1px solid var(--border);
  border-radius: 14px; padding:18px 20px;
  position: relative; overflow: hidden;
}
.cu-kpi::before {
  content:''; position:absolute; top:-18px; right:-18px;
  width:72px; height:72px; border-radius:50%; opacity:.12;
}
.cu-kpi.kpi-green::before  { background: var(--good); }
.cu-kpi.kpi-blue::before   { background: var(--accent); }
.cu-kpi.kpi-red::before    { background: var(--bad); }
.cu-kpi.kpi-purple::before { background: #7a6bb0; }
.cu-kpi-num {
  font-family:'Bricolage Grotesque',sans-serif; font-size:32px; font-weight:800;
  line-height:1; margin-bottom:4px;
}
.cu-kpi.kpi-green .cu-kpi-num  { color: var(--good); }
.cu-kpi.kpi-blue .cu-kpi-num   { color: var(--accent); }
.cu-kpi.kpi-red .cu-kpi-num    { color: var(--bad); }
.cu-kpi.kpi-purple .cu-kpi-num { color: #7a6bb0; }
.cu-kpi-label { font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; }
.cu-kpi-sub   { font-size:11px; color:var(--muted-2); margin-top:4px; }

/* Filter bar */
.cu-toolbar {
  display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;
}
.cu-pills { display:flex; gap:6px; flex-wrap:wrap; }
.cu-pill {
  display:inline-flex; align-items:center;
  padding:6px 14px; border-radius:20px; border:1.5px solid var(--border);
  font-size:13px; font-weight:600; color:var(--muted); background:var(--surface);
  cursor:pointer; text-decoration:none; transition:all .15s; white-space:nowrap;
  user-select:none;
}
.cu-pill:hover { border-color:var(--accent); color:var(--accent); }
.cu-pill.active { background:var(--accent); border-color:var(--accent); color:#fff; }
.cu-pill.pill-warn.active  { background:var(--warn); border-color:var(--warn); color:#fff; }
.cu-pill.pill-danger.active { background:var(--bad);  border-color:var(--bad);  color:#fff; }

.cu-search-wrap {
  flex:1; min-width:220px; max-width:360px;
  display:flex; align-items:center; gap:0;
  background:var(--surface); border:1.5px solid var(--border); border-radius:10px;
  overflow:hidden; transition:border-color .15s;
}
.cu-search-wrap:focus-within { border-color:var(--accent); }
.cu-search-wrap input {
  flex:1; border:none; background:transparent; padding:8px 14px;
  font-size:14px; color:var(--text); outline:none;
}
.cu-search-wrap button {
  background:var(--accent); border:none; color:#fff;
  padding:0 16px; height:40px; font-size:13px; font-weight:700; cursor:pointer;
  transition:opacity .15s;
}
.cu-search-wrap button:hover { opacity:.85; }

.cu-new-btn {
  display:inline-flex; align-items:center; gap:6px;
  height:40px; padding:0 20px; border-radius:10px;
  background:var(--accent); color:#fff; border:none;
  font-size:14px; font-weight:700; text-decoration:none;
  transition:opacity .15s; white-space:nowrap;
  margin-left:auto;
}
.cu-new-btn:hover { opacity:.88; color:#fff; }

/* Customer table */
.cu-table {
  width:100%; border-collapse:collapse;
}
.cu-table thead tr { background:var(--surface-2); }
.cu-table thead th {
  padding:10px 14px; font-size:11px; font-weight:800; text-transform:uppercase;
  letter-spacing:.08em; color:var(--muted); border-bottom:2px solid var(--border);
  white-space:nowrap;
}
.cu-table tbody tr {
  border-bottom:1px solid var(--border); cursor:pointer;
  transition:background .1s;
}
.cu-table tbody tr:hover { background:var(--surface-2); }
.cu-table tbody tr:last-child { border-bottom:none; }
.cu-table tbody td { padding:12px 14px; vertical-align:middle; }
.cu-table-wrap {
  background:var(--surface); border:1px solid var(--border);
  border-radius:14px; overflow:hidden;
}

/* Avatar circle */
.cu-avatar {
  width:40px; height:40px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:14px; font-weight:800; color:#fff; flex-shrink:0;
}
.cu-avatar-lg {
  width:72px; height:72px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:24px; font-weight:800; color:#fff; flex-shrink:0;
  border:3px solid rgba(255,255,255,.35);
}

/* Status badges (richer) */
.cu-badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:.05em; white-space:nowrap;
}
.cu-badge-green  { background:rgba(47,158,114,.15); color:var(--good); }
.cu-badge-yellow { background:rgba(176,125,17,.15);  color:var(--warn); }
.cu-badge-red    { background:rgba(232,98,74,.15);   color:var(--bad); }
.cu-badge-blue   { background:rgba(23,179,196,.15);  color:var(--accent); }
.cu-badge-gray   { background:rgba(155,179,188,.15); color:var(--muted); }
:root[data-theme="dark"] .cu-badge-green  { background:rgba(95,208,163,.15); }
:root[data-theme="dark"] .cu-badge-yellow { background:rgba(240,180,41,.15); }
:root[data-theme="dark"] .cu-badge-red    { background:rgba(255,142,118,.15); }

.cu-badge-dot { width:6px; height:6px; border-radius:50%; }
.cu-badge-green  .cu-badge-dot { background:var(--good); }
.cu-badge-yellow .cu-badge-dot { background:var(--warn); }
.cu-badge-red    .cu-badge-dot { background:var(--bad); }

/* Card count bubble */
.cu-card-count {
  display:inline-flex; align-items:center; justify-content:center;
  width:26px; height:26px; border-radius:50%;
  background:var(--accent); color:#fff; font-size:12px; font-weight:800;
}

/* Open button */
.cu-open-btn {
  display:inline-flex; align-items:center; gap:4px;
  padding:5px 14px; border-radius:8px; font-size:13px; font-weight:700;
  border:1.5px solid var(--accent); color:var(--accent); background:transparent;
  text-decoration:none; transition:all .15s;
}
.cu-open-btn:hover { background:var(--accent); color:#fff; }

/* ══ DETAIL ══════════════════════════════════════════════════ */

/* Profile banner */
.cu-profile {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
  margin-bottom: 24px;
}
.cu-profile-banner {
  background: linear-gradient(135deg, #0b3e50, #0e6276);
  padding: 28px 28px 24px;
  display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;
}
.cu-profile-main { flex:1; min-width:0; }
.cu-profile-name {
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:26px; font-weight:800; color:#fff; margin-bottom:8px;
}
.cu-profile-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
.cu-profile-chip {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 12px; border-radius:20px; font-size:13px; font-weight:500;
  background:rgba(255,255,255,.12); color:rgba(255,255,255,.88);
}
.cu-profile-actions { display:flex; gap:8px; margin-left:auto; align-items:flex-start; }
.cu-profile-body { padding:20px 28px; }

/* Profile stat tiles (inside banner) */
.cu-profile-stats { display:flex; gap:12px; flex-wrap:wrap; padding:0 28px 20px; }
.cu-pstat {
  flex:1; min-width:100px;
  background:var(--surface-2); border:1px solid var(--border); border-radius:12px;
  padding:14px 18px; text-align:center;
}
.cu-pstat-num { font-size:22px; font-weight:800; font-family:'Bricolage Grotesque',sans-serif; }
.cu-pstat-label { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:2px; }

/* Detail tabs */
.cu-tabs {
  display:flex; gap:0; border-bottom:2px solid var(--border); margin-bottom:20px;
}
.cu-tab {
  padding:10px 22px; font-size:14px; font-weight:700; color:var(--muted);
  cursor:pointer; border:none; background:none; position:relative; transition:color .15s;
}
.cu-tab::after {
  content:''; position:absolute; bottom:-2px; left:0; right:0; height:2px;
  background:var(--accent); border-radius:1px;
  transform:scaleX(0); transition:transform .2s;
}
.cu-tab.active { color:var(--text); }
.cu-tab.active::after { transform:scaleX(1); }
.cu-tab-badge {
  display:inline-flex; align-items:center; justify-content:center;
  width:20px; height:20px; border-radius:50%; font-size:11px;
  background:var(--accent); color:#fff; margin-left:6px;
}

/* Visual credit card */
.cu-card-visual {
  background: linear-gradient(135deg, #0e8a99, #17b3c4);
  border-radius: 16px; padding: 20px 22px; color:#fff;
  position:relative; overflow:hidden; transition:box-shadow .15s;
  cursor: pointer;
}
.cu-card-visual:hover { box-shadow:0 4px 24px rgba(23,179,196,.4); }
.cu-card-visual.selected { box-shadow: 0 0 0 3px var(--accent), 0 4px 20px rgba(23,179,196,.3); }
.cu-card-visual.status-chiusa   { background: linear-gradient(135deg, #4a5568, #718096); }
.cu-card-visual.status-bloccata { background: linear-gradient(135deg, #c53030, #e8624a); }
.cu-card-visual.status-smarrita { background: linear-gradient(135deg, #b07d11, #f0b429); }
.cu-card-visual::before {
  content:''; position:absolute; top:-25px; right:-25px;
  width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,.1);
}
.cu-card-visual::after {
  content:''; position:absolute; bottom:-35px; left:-20px;
  width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.07);
}
.cu-cv-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px; position:relative; z-index:1; }
.cu-cv-logo   { font-size:10px; font-weight:800; opacity:.7; letter-spacing:.1em; text-transform:uppercase; }
.cu-cv-type   { font-size:11px; font-weight:700; opacity:.7; background:rgba(255,255,255,.15); padding:2px 8px; border-radius:20px; }
.cu-cv-code   { font-family:'Bricolage Grotesque',monospace; font-size:19px; font-weight:800; letter-spacing:2px; margin-bottom:6px; position:relative; z-index:1; }
.cu-cv-name   { font-size:13px; font-weight:600; opacity:.85; position:relative; z-index:1; }
.cu-cv-footer { display:flex; align-items:center; justify-content:space-between; margin-top:14px; position:relative; z-index:1; }
.cu-cv-balance{ font-size:18px; font-weight:800; }
.cu-cv-balance.red { color:#ffb3a7; }
.cu-cv-balance.ok  { color:#b7f5d8; font-size:13px; opacity:.8; }
.cu-cv-chip   { display:flex; align-items:center; gap:4px; font-size:10px; font-weight:700; opacity:.65; }
.cu-cv-chip svg { flex-shrink:0; }

/* Inside badge on card */
.cu-inside-badge {
  display:inline-flex; align-items:center; gap:3px;
  font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.07em;
  background:rgba(255,255,255,.2); padding:2px 8px; border-radius:10px;
}

/* Card actions row */
.cu-card-actions { display:flex; gap:8px; padding: 12px 0 4px; }
.cu-card-actions .btn { font-size:12px; font-weight:700; }

/* Movements inline */
.cu-movements { margin-top:16px; }
.cu-mov-table { width:100%; border-collapse:collapse; font-size:13px; }
.cu-mov-table thead th {
  padding:7px 10px; font-size:10px; font-weight:800; text-transform:uppercase;
  letter-spacing:.07em; color:var(--muted); border-bottom:1px solid var(--border);
}
.cu-mov-table tbody tr { border-bottom:1px solid var(--border); }
.cu-mov-table tbody tr:last-child { border-bottom:none; }
.cu-mov-table tbody td { padding:8px 10px; vertical-align:middle; }
.cu-mov-table tbody tr.cancelled td { opacity:.4; text-decoration:line-through; }

/* Scheda personale doc section */
.cu-doc-card {
  background:var(--surface-2); border:1px solid var(--border); border-radius:12px; padding:16px 20px;
}
.cu-field-label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px; }
.cu-field-value { font-size:15px; color:var(--text); font-weight:500; }
.cu-field-empty { color:var(--muted-2); font-style:italic; font-size:13px; }

/* Signature/photo thumbnail */
.cu-sig-thumb {
  width:100%; height:80px; object-fit:contain;
  border:1px solid var(--border); border-radius:8px; background:var(--surface-2);
}
.cu-photo-thumb {
  width:100px; height:100px; object-fit:cover;
  border-radius:10px; border:2px solid var(--accent);
}

/* Empty state */
.cu-empty {
  text-align:center; padding:48px 20px; color:var(--muted-2);
}
.cu-empty-icon { font-size:48px; margin-bottom:12px; opacity:.5; }
.cu-empty-title { font-size:16px; font-weight:700; color:var(--muted); margin-bottom:6px; }

/* Registered flash */
.cu-flash-ok {
  display:flex; align-items:center; gap:12px;
  background:rgba(47,158,114,.12); border:1.5px solid var(--good);
  border-radius:12px; padding:14px 18px; margin-bottom:20px; color:var(--good);
  font-weight:600; font-size:14px;
}
.cu-flash-ok button { margin-left:auto; background:none; border:none; color:var(--good); font-size:18px; cursor:pointer; }

/* Back button */
.cu-back-btn {
  display:inline-flex; align-items:center; gap:6px; padding:7px 16px;
  border-radius:9px; border:1.5px solid var(--border); background:var(--surface);
  color:var(--muted); font-size:13px; font-weight:700; text-decoration:none;
  transition:all .15s;
}
.cu-back-btn:hover { border-color:var(--accent); color:var(--accent); }

/* Toast */
#cu-toast {
  position:fixed; bottom:1.5rem; left:50%; transform:translateX(-50%) translateY(80px);
  background:#198754; color:#fff; padding:.55rem 1.4rem; border-radius:8px;
  font-size:.9rem; z-index:9999; transition:transform .25s; pointer-events:none;
  font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,.2);
}
</style>

<div class="cu-page">

<?php if (!isset($customer)): ?>
<!-- ═══════════════════════════════════════════════════════════════
     LIST
═══════════════════════════════════════════════════════════════ -->
<?php
  // Aggregate stats from the customers array
  $stat_active   = 0; $stat_suspended = 0; $stat_blacklist = 0;
  $stat_balance  = 0.0; $stat_cards = 0;
  foreach (($customers ?? []) as $c) {
      if ($c['status'] === 'attivo')    $stat_active++;
      if ($c['status'] === 'sospeso')   $stat_suspended++;
      if ($c['status'] === 'blacklist') $stat_blacklist++;
      $stat_balance += (float)$c['total_balance'];
      $stat_cards   += (int)$c['active_cards'];
  }
?>

<!-- KPI tiles -->
<div class="cu-kpi-grid">
  <div class="cu-kpi kpi-green">
    <div class="cu-kpi-num"><?= $stat_active ?></div>
    <div class="cu-kpi-label">Clienti attivi</div>
    <?php if ($stat_suspended): ?>
      <div class="cu-kpi-sub"><?= $stat_suspended ?> sospesi</div>
    <?php endif; ?>
  </div>
  <div class="cu-kpi kpi-blue">
    <div class="cu-kpi-num"><?= $stat_cards ?></div>
    <div class="cu-kpi-label">Card attive</div>
  </div>
  <div class="cu-kpi kpi-red">
    <div class="cu-kpi-num">€&nbsp;<?= number_format($stat_balance, 2, ',', '.') ?></div>
    <div class="cu-kpi-label">Saldo aperto totale</div>
  </div>
  <?php if ($stat_blacklist): ?>
  <div class="cu-kpi kpi-purple">
    <div class="cu-kpi-num"><?= $stat_blacklist ?></div>
    <div class="cu-kpi-label">Blacklist</div>
  </div>
  <?php endif; ?>
</div>

<!-- Toolbar: pills + search + new button -->
<div class="cu-toolbar">
  <div class="cu-pills">
    <span class="cu-pill active" data-filter="tutti">Tutti (<?= count($customers ?? []) ?>)</span>
    <span class="cu-pill" data-filter="attivo">Attivi</span>
    <span class="cu-pill pill-warn" data-filter="sospeso">Sospesi</span>
    <span class="cu-pill pill-danger" data-filter="blacklist">Blacklist</span>
  </div>
  <form method="GET" action="<?= url('/customers') ?>" class="cu-search-wrap">
    <input type="text" name="q" placeholder="Cerca nome, telefono, email…" value="<?= e($q ?? '') ?>">
    <button type="submit">Cerca</button>
  </form>
  <a href="<?= url('/customers/register') ?>" class="cu-new-btn">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuovo cliente
  </a>
</div>

<?php if (empty($customers)): ?>
<div class="cu-empty">
  <div class="cu-empty-icon">👤</div>
  <div class="cu-empty-title">Nessun cliente trovato</div>
  <?php if (!empty($q)): ?>
    <p>Prova con un termine di ricerca diverso.</p>
    <a href="<?= url('/customers') ?>" class="btn btn-outline-secondary btn-sm">✕ Cancella ricerca</a>
  <?php else: ?>
    <a href="<?= url('/customers/register') ?>" class="cu-new-btn" style="margin:0 auto">+ Registra il primo cliente</a>
  <?php endif; ?>
</div>
<?php else: ?>

<div class="cu-table-wrap">
  <table class="cu-table" id="cuTable">
    <thead>
      <tr>
        <th style="width:48px"></th>
        <th>Cliente</th>
        <th>Contatti</th>
        <th class="text-center">Card</th>
        <th class="text-end">Saldo</th>
        <th class="text-center">Stato</th>
        <th style="width:100px"></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($customers as $c):
      $fullName = $c['last_name'] . ' ' . $c['first_name'];
      $initials = cu_initials($c['first_name'], $c['last_name']);
      $bgColor  = cu_avatar_color($fullName);
      $bal      = (float)$c['total_balance'];
      $statusBadgeClass = match($c['status']) {
          'attivo'    => 'cu-badge-green',
          'sospeso'   => 'cu-badge-yellow',
          'blacklist' => 'cu-badge-red',
          default     => 'cu-badge-gray',
      };
    ?>
    <tr data-status="<?= e($c['status']) ?>"
        onclick="location.href='<?= url('/customers?id=' . (int)$c['id']) ?>'">
      <td>
        <div class="cu-avatar" style="background:<?= $bgColor ?>"><?= $initials ?></div>
      </td>
      <td>
        <div style="font-weight:700;font-size:15px"><?= e($fullName) ?></div>
        <?php if ($c['fiscal_code']): ?>
          <div style="font-size:11px;color:var(--muted);font-family:monospace;margin-top:2px"><?= e($c['fiscal_code']) ?></div>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($c['phone']): ?><div style="font-size:13px;font-weight:600"><?= e($c['phone']) ?></div><?php endif; ?>
        <?php if ($c['email']): ?><div style="font-size:12px;color:var(--muted)"><?= e($c['email']) ?></div><?php endif; ?>
      </td>
      <td class="text-center">
        <?php if ((int)$c['active_cards'] > 0): ?>
          <span class="cu-card-count"><?= (int)$c['active_cards'] ?></span>
        <?php else: ?>
          <span style="color:var(--muted-2)">—</span>
        <?php endif; ?>
      </td>
      <td class="text-end">
        <?php if ($bal > 0): ?>
          <span style="color:var(--bad);font-weight:800;font-size:14px">€&nbsp;<?= number_format($bal, 2, ',', '.') ?></span>
        <?php else: ?>
          <span style="color:var(--muted-2)">—</span>
        <?php endif; ?>
      </td>
      <td class="text-center">
        <span class="cu-badge <?= $statusBadgeClass ?>">
          <span class="cu-badge-dot"></span>
          <?= e($c['status']) ?>
        </span>
      </td>
      <td onclick="event.stopPropagation()">
        <a href="<?= url('/customers?id=' . (int)$c['id']) ?>" class="cu-open-btn">
          Apri
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>


<?php else: ?>
<!-- ═══════════════════════════════════════════════════════════════
     DETAIL
═══════════════════════════════════════════════════════════════ -->
<?php
  $fullName  = $customer['last_name'] . ' ' . $customer['first_name'];
  $initials  = cu_initials($customer['first_name'], $customer['last_name']);
  $avatarBg  = cu_avatar_color($fullName);
  $totalBal  = array_sum(array_column($cards ?? [], 'current_balance'));
  $totalCards = count($cards ?? []);
  $activeCards = count(array_filter($cards ?? [], fn($c) => $c['status'] === 'attiva'));
?>

<?php if (!empty($_GET['registered'])): ?>
<div class="cu-flash-ok" role="alert">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  Registrazione completata! Cliente e card creati correttamente.
  <button onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>

<!-- Profile card -->
<div class="cu-profile">
  <div class="cu-profile-banner">
    <?php if (!empty($customer['photo_path'])): ?>
      <img src="<?= url('/' . e($customer['photo_path'])) ?>"
           alt="<?= e($customer['first_name']) ?>"
           style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.35);flex-shrink:0">
    <?php else: ?>
      <div class="cu-avatar-lg" style="background:<?= $avatarBg ?>"><?= $initials ?></div>
    <?php endif; ?>

    <div class="cu-profile-main">
      <div class="cu-profile-name"><?= e($fullName) ?></div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <?php $stBadge = match($customer['status']) {
          'attivo' => 'cu-badge-green', 'sospeso' => 'cu-badge-yellow',
          'blacklist' => 'cu-badge-red', default => 'cu-badge-gray'
        }; ?>
        <span class="cu-badge <?= $stBadge ?>" style="background:rgba(255,255,255,.15);color:#fff">
          <span class="cu-badge-dot" style="background:#fff"></span>
          <?= e($customer['status']) ?>
        </span>
        <?php if ($customer['privacy_consent']): ?>
          <span class="cu-badge" style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.8)">
            🔒 Privacy acquisita
          </span>
        <?php endif; ?>
      </div>
      <div class="cu-profile-chips">
        <?php if ($customer['phone']): ?>
          <span class="cu-profile-chip">📞 <?= e($customer['phone']) ?></span>
        <?php endif; ?>
        <?php if ($customer['email']): ?>
          <span class="cu-profile-chip">✉ <?= e($customer['email']) ?></span>
        <?php endif; ?>
        <?php if ($customer['fiscal_code']): ?>
          <span class="cu-profile-chip" style="font-family:monospace">CF: <?= e($customer['fiscal_code']) ?></span>
        <?php endif; ?>
        <?php if ($customer['birth_date']): ?>
          <span class="cu-profile-chip">🎂 <?= date('d/m/Y', strtotime($customer['birth_date'])) ?></span>
        <?php endif; ?>
        <?php if ($customer['address']): ?>
          <span class="cu-profile-chip">📍 <?= e($customer['address']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div class="cu-profile-actions">
      <a href="<?= url('/customers') ?>" class="cu-back-btn" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.8);background:rgba(255,255,255,.08)">
        ← Clienti
      </a>
      <button class="cu-open-btn" style="border-color:rgba(255,255,255,.3);color:#fff;background:rgba(255,255,255,.1)"
              data-bs-toggle="modal" data-bs-target="#editCustomerModal">
        ✏ Modifica
      </button>
    </div>
  </div>

  <!-- Mini stats strip -->
  <div class="cu-profile-stats">
    <div class="cu-pstat">
      <div class="cu-pstat-num" style="color:var(--accent)"><?= $totalCards ?></div>
      <div class="cu-pstat-label">Card totali</div>
    </div>
    <div class="cu-pstat">
      <div class="cu-pstat-num" style="color:var(--good)"><?= $activeCards ?></div>
      <div class="cu-pstat-label">Card attive</div>
    </div>
    <div class="cu-pstat">
      <?php $tbal = (float)$totalBal; ?>
      <div class="cu-pstat-num" style="color:<?= $tbal > 0 ? 'var(--bad)' : 'var(--good)' ?>">
        €&nbsp;<?= number_format($tbal, 2, ',', '.') ?>
      </div>
      <div class="cu-pstat-label">Saldo aperto</div>
    </div>
    <?php if ($customer['notes']): ?>
    <div class="cu-pstat" style="flex:3;text-align:left">
      <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px">Note</div>
      <div style="font-size:13px;color:var(--muted)"><?= e($customer['notes']) ?></div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Tabs -->
<div class="cu-tabs" id="cuTabs">
  <button class="cu-tab active" onclick="showDetailTab('cards')">
    Card & Movimenti
    <?php if ($totalCards): ?><span class="cu-tab-badge"><?= $totalCards ?></span><?php endif; ?>
  </button>
  <button class="cu-tab" onclick="showDetailTab('info')">Scheda personale</button>
</div>

<!-- ── TAB: Card & Movimenti ─────────────────────────────────── -->
<div id="pane-cards">

  <?php if (empty($cards)): ?>
  <div class="cu-empty">
    <div class="cu-empty-icon">💳</div>
    <div class="cu-empty-title">Nessuna card registrata</div>
    <p style="color:var(--muted-2);font-size:14px">Assegna una card per abilitare gli addebiti e i movimenti.</p>
  </div>
  <?php else: ?>
  <div class="row g-3 mb-4">
    <?php foreach ($cards as $card):
      $isSelected = isset($selectedCard) && (int)$selectedCard['id'] === (int)$card['id'];
      $bal = (float)$card['current_balance'];
      $cvClass = 'status-' . $card['status'];
    ?>
    <div class="col-sm-6 col-lg-4">
      <div class="cu-card-visual <?= $cvClass ?> <?= $isSelected ? 'selected' : '' ?>"
           onclick="location.href='<?= url('/customers?id=' . (int)$customer['id'] . '&card=' . (int)$card['id']) ?>'">
        <div class="cu-cv-header">
          <div class="cu-cv-logo">Igea Club</div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
            <span class="cu-cv-type"><?= e($cardTypeLabel[$card['card_type']] ?? $card['card_type']) ?></span>
            <?php if ($card['is_inside']): ?>
              <span class="cu-inside-badge">● Dentro</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="cu-cv-code"><?= e($card['card_code']) ?></div>
        <div class="cu-cv-name"><?= e($fullName) ?></div>
        <div class="cu-cv-footer">
          <?php if ($bal > 0): ?>
            <div class="cu-cv-balance red">€&nbsp;<?= number_format($bal, 2, ',', '.') ?></div>
          <?php else: ?>
            <div class="cu-cv-balance ok">✓ Saldato</div>
          <?php endif; ?>
          <?php if (!empty($card['nfc_uid'])): ?>
          <div class="cu-cv-chip">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="5" width="14" height="14" rx="2"/><path d="M9 9h6v6H9z"/></svg>
            NFC
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card actions -->
      <div class="cu-card-actions">
        <?php if ($isSelected): ?>
          <a href="<?= url('/customers?id=' . (int)$customer['id']) ?>" class="btn btn-sm btn-outline-secondary flex-fill">
            ✕ Chiudi movimenti
          </a>
        <?php else: ?>
          <a href="<?= url('/customers?id=' . (int)$customer['id'] . '&card=' . (int)$card['id']) ?>"
             class="btn btn-sm btn-outline-primary flex-fill">
            📋 Movimenti
          </a>
        <?php endif; ?>
        <button class="btn btn-sm btn-outline-secondary"
                onclick='event.stopPropagation();openEditCard(<?= json_encode(['id'=>$card['id'],'card_code'=>$card['card_code'],'status'=>$card['status'],'notes'=>$card['notes']??'']) ?>)'>
          ✏
        </button>
      </div>

      <!-- Card sub-info -->
      <div style="font-size:11px;color:var(--muted);padding:0 2px;display:flex;gap:12px;flex-wrap:wrap">
        <?php if ($card['expires_at']): ?>
          <span>Scade: <?= date('d/m/Y', strtotime($card['expires_at'])) ?></span>
        <?php endif; ?>
        <?php if ($card['last_movement']): ?>
          <span>Ult. mov.: <?= date('d/m H:i', strtotime($card['last_movement'])) ?></span>
        <?php endif; ?>
        <?php if ($card['notes']): ?>
          <span>📝 <?= e($card['notes']) ?></span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Movements inline (when card selected) -->
  <?php if (isset($selectedCard) && !empty($movements)): ?>
  <div class="cu-movements">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
      <div style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">
        Movimenti — <?= e($selectedCard['card_code']) ?>
      </div>
      <span class="cu-badge cu-badge-blue"><?= count($movements) ?></span>
    </div>
    <div class="cu-table-wrap">
      <table class="cu-mov-table">
        <thead>
          <tr>
            <th>Data/Ora</th>
            <th>Tipo</th>
            <th>Reparto</th>
            <th>Descrizione</th>
            <th class="text-end">Importo</th>
            <th class="text-center">Stato</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($movements as $m):
          $mt = $movType[$m['movement_type']] ?? ['label' => $m['movement_type'], 'color' => 'secondary'];
          $cancelled = $m['status'] === 'cancelled';
        ?>
        <tr class="<?= $cancelled ? 'cancelled' : '' ?>">
          <td style="font-size:12px;color:var(--muted);white-space:nowrap"><?= date('d/m H:i', strtotime($m['created_at'])) ?></td>
          <td><span class="badge bg-<?= $mt['color'] ?>"><?= $mt['label'] ?></span></td>
          <td style="font-size:12px;color:var(--muted)"><?= e($deptLabel[$m['department']] ?? $m['department']) ?></td>
          <td style="font-size:13px">
            <?= e($m['description']) ?>
            <?php if ((float)$m['quantity'] != 1): ?>
              <span style="color:var(--muted)"> ×<?= rtrim(rtrim(number_format((float)$m['quantity'],2),'0'),'.') ?></span>
            <?php endif; ?>
          </td>
          <td class="text-end" style="font-weight:700;font-size:14px;
            color:<?= $m['movement_type']==='charge'?'var(--bad)':'var(--good)' ?>">
            €&nbsp;<?= number_format((float)$m['total_amount'],2,',','.') ?>
          </td>
          <td class="text-center">
            <span class="cu-badge <?= $m['status']==='open'?'cu-badge-yellow':($m['status']==='paid'?'cu-badge-green':'cu-badge-gray') ?>">
              <?= e($m['status']) ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif (isset($selectedCard) && empty($movements)): ?>
  <div class="cu-empty" style="padding:24px">
    <div class="cu-empty-title" style="font-size:14px">Nessun movimento per questa card</div>
  </div>
  <?php endif; ?>

  <div class="mt-3">
    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newCardModal">
      + Nuova card
    </button>
  </div>
</div>

<!-- ── TAB: Scheda personale ─────────────────────────────────── -->
<div id="pane-info" style="display:none">
  <div class="row g-3">

    <?php if (!empty($customer['photo_path']) || !empty($customer['signature_path'])): ?>
    <div class="col-12">
      <div class="row g-3 mb-2">
        <?php if (!empty($customer['photo_path'])): ?>
        <div class="col-auto">
          <div class="cu-field-label">Foto cliente</div>
          <img src="<?= url('/' . e($customer['photo_path'])) ?>" class="cu-photo-thumb" alt="Foto">
        </div>
        <?php endif; ?>
        <?php if (!empty($customer['signature_path'])): ?>
        <div class="col">
          <div class="cu-field-label">Firma privacy</div>
          <img src="<?= url('/' . e($customer['signature_path'])) ?>" class="cu-sig-thumb" alt="Firma">
          <?php if (!empty($customer['privacy_signed_at'])): ?>
            <div style="font-size:11px;color:var(--muted);margin-top:4px">
              Firmato il <?= date('d/m/Y H:i', strtotime($customer['privacy_signed_at'])) ?>
            </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Dati anagrafici -->
    <div class="col-12">
      <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:10px">Dati personali</div>
      <div class="cu-doc-card">
        <div class="row g-3">
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Nome</div>
            <div class="cu-field-value"><?= e($customer['first_name'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Cognome</div>
            <div class="cu-field-value"><?= e($customer['last_name'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Data di nascita</div>
            <div class="cu-field-value"><?= $customer['birth_date'] ? date('d/m/Y', strtotime($customer['birth_date'])) : '<span class="cu-field-empty">—</span>' ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Codice fiscale</div>
            <div class="cu-field-value" style="font-family:monospace;font-size:13px"><?= e($customer['fiscal_code'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="cu-field-label">Telefono</div>
            <div class="cu-field-value"><?= e($customer['phone'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="cu-field-label">Email</div>
            <div class="cu-field-value"><?= e($customer['email'] ?: '—') ?></div>
          </div>
          <div class="col-md-4">
            <div class="cu-field-label">Indirizzo</div>
            <div class="cu-field-value"><?= e($customer['address'] ?: '—') ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Documento -->
    <?php
      $hasDoc = !empty($customer['doc_type']) || !empty($customer['doc_number']);
    ?>
    <div class="col-12">
      <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:10px">Documento d'identità</div>
      <div class="cu-doc-card">
        <?php if ($hasDoc): ?>
        <div class="row g-3">
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Tipo</div>
            <div class="cu-field-value"><?php
              $docLabels = ['carta_identita'=>"Carta d'Identità",'passaporto'=>'Passaporto','patente'=>'Patente','permesso_soggiorno'=>'Permesso di Soggiorno'];
              echo e($docLabels[$customer['doc_type'] ?? ''] ?? ($customer['doc_type'] ?: '—'));
            ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Numero</div>
            <div class="cu-field-value" style="font-family:monospace"><?= e($customer['doc_number'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Scadenza</div>
            <div class="cu-field-value"><?= !empty($customer['doc_expiry']) ? date('d/m/Y', strtotime($customer['doc_expiry'])) : '—' ?></div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="cu-field-label">Rilasciato da</div>
            <div class="cu-field-value"><?= e($customer['doc_issuer'] ?: '—') ?></div>
          </div>
        </div>
        <?php else: ?>
          <span class="cu-field-empty">Documento non registrato</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-12 mt-2">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCustomerModal">
        ✏ Modifica scheda
      </button>
    </div>
  </div>
</div>

<!-- ── Edit Customer Modal ─────────────────────────────────── -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifica cliente — <?= e($fullName) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-4">

          <!-- Colonna sinistra: foto -->
          <div class="col-lg-3 d-flex flex-column align-items-center gap-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);align-self:flex-start">Foto</div>
            <div id="ec_photo_wrap" style="width:120px;height:120px;border-radius:50%;overflow:hidden;border:3px solid var(--border);background:var(--surface-2);flex-shrink:0;display:flex;align-items:center;justify-content:center">
              <?php if (!empty($customer['photo_path'])): ?>
                <img id="ec_photo_img" src="<?= url('/' . e($customer['photo_path'])) ?>" style="width:100%;height:100%;object-fit:cover">
              <?php else: ?>
                <span id="ec_photo_img" style="font-size:40px;color:var(--muted-2)">👤</span>
              <?php endif; ?>
            </div>
            <div id="ec_webcam_box" style="display:none;width:100%;border-radius:10px;overflow:hidden;background:#000;aspect-ratio:4/3;position:relative">
              <video id="ec_webcam_video" autoplay muted playsinline style="width:100%;height:100%;object-fit:cover"></video>
            </div>
            <div class="d-flex flex-column gap-2 w-100">
              <button type="button" class="btn btn-sm btn-outline-primary" id="ec_btn_cam" onclick="ecStartCamera()">📷 Webcam</button>
              <button type="button" class="btn btn-sm btn-primary d-none" id="ec_btn_snap" onclick="ecSnapPhoto()">📸 Scatta</button>
              <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor:pointer">
                📁 Da file <input type="file" id="ec_photo_file" accept="image/*" style="display:none" onchange="ecLoadFile(this)">
              </label>
              <?php if (!empty($customer['photo_path'])): ?>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="ecClearPhoto()">✕ Rimuovi foto</button>
              <?php endif; ?>
            </div>
            <input type="hidden" id="ec_photo_data" value="">
            <input type="hidden" id="ec_clear_photo" value="0">
          </div>

          <!-- Colonna destra: campi -->
          <div class="col-lg-9">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Nome *</label>
                <input class="form-control" id="ec_first_name" value="<?= e($customer['first_name']) ?>" maxlength="80"></div>
              <div class="col-md-6"><label class="form-label">Cognome *</label>
                <input class="form-control" id="ec_last_name" value="<?= e($customer['last_name']) ?>" maxlength="80"></div>
              <div class="col-md-6"><label class="form-label">Telefono</label>
                <input class="form-control" id="ec_phone" value="<?= e($customer['phone'] ?? '') ?>" maxlength="30"></div>
              <div class="col-md-6"><label class="form-label">Email</label>
                <input class="form-control" type="email" id="ec_email" value="<?= e($customer['email'] ?? '') ?>" maxlength="120"></div>
              <div class="col-md-6"><label class="form-label">Codice fiscale</label>
                <input class="form-control" id="ec_fiscal" value="<?= e($customer['fiscal_code'] ?? '') ?>" maxlength="16" style="text-transform:uppercase"></div>
              <div class="col-md-6"><label class="form-label">Data di nascita</label>
                <input class="form-control" type="date" id="ec_birth" value="<?= $customer['birth_date'] ? date('Y-m-d', strtotime($customer['birth_date'])) : '' ?>"></div>
              <div class="col-12"><label class="form-label">Indirizzo</label>
                <input class="form-control" id="ec_address" value="<?= e($customer['address'] ?? '') ?>" maxlength="200"></div>
              <div class="col-12"><label class="form-label">Note</label>
                <textarea class="form-control" id="ec_notes" rows="2" maxlength="500"><?= e($customer['notes'] ?? '') ?></textarea></div>
              <div class="col-md-6"><label class="form-label">Stato</label>
                <select class="form-select" id="ec_status">
                  <option value="attivo"    <?= $customer['status']==='attivo'    ?'selected':'' ?>>Attivo</option>
                  <option value="sospeso"   <?= $customer['status']==='sospeso'   ?'selected':'' ?>>Sospeso</option>
                  <option value="blacklist" <?= $customer['status']==='blacklist' ?'selected':'' ?>>Blacklist</option>
                </select></div>
              <div class="col-md-6 d-flex align-items-end">
                <div class="form-check pb-1">
                  <input class="form-check-input" type="checkbox" id="ec_privacy" <?= $customer['privacy_consent'] ? 'checked' : '' ?>>
                  <label class="form-check-label" for="ec_privacy">Consenso privacy</label>
                </div>
              </div>

              <!-- Documento -->
              <div class="col-12"><hr style="border-color:var(--border);margin:4px 0">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Documento d'identità</div>
              </div>
              <div class="col-md-6"><label class="form-label">Tipo documento</label>
                <select class="form-select" id="ec_doc_type">
                  <option value="">— nessuno —</option>
                  <option value="carta_identita"   <?= ($customer['doc_type']??'')==='carta_identita'   ?'selected':'' ?>>Carta d'Identità</option>
                  <option value="passaporto"       <?= ($customer['doc_type']??'')==='passaporto'       ?'selected':'' ?>>Passaporto</option>
                  <option value="patente"          <?= ($customer['doc_type']??'')==='patente'          ?'selected':'' ?>>Patente</option>
                  <option value="permesso_soggiorno" <?= ($customer['doc_type']??'')==='permesso_soggiorno' ?'selected':'' ?>>Permesso di Soggiorno</option>
                </select></div>
              <div class="col-md-6"><label class="form-label">Numero documento</label>
                <input class="form-control" id="ec_doc_number" value="<?= e($customer['doc_number'] ?? '') ?>" maxlength="30" style="text-transform:uppercase"></div>
              <div class="col-md-6"><label class="form-label">Scadenza</label>
                <input class="form-control" type="date" id="ec_doc_expiry" value="<?= !empty($customer['doc_expiry']) ? date('Y-m-d', strtotime($customer['doc_expiry'])) : '' ?>"></div>
              <div class="col-md-6"><label class="form-label">Rilasciato da</label>
                <input class="form-control" id="ec_doc_issuer" value="<?= e($customer['doc_issuer'] ?? '') ?>" maxlength="100"></div>

              <!-- Firma privacy -->
              <div class="col-12"><hr style="border-color:var(--border);margin:4px 0">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Firma privacy</div>
              </div>
              <?php if (!empty($customer['signature_path'])): ?>
              <div class="col-12">
                <div class="cu-field-label" style="margin-bottom:6px">Firma attuale</div>
                <img src="<?= url('/' . e($customer['signature_path'])) ?>" class="cu-sig-thumb" alt="Firma">
              </div>
              <?php endif; ?>
              <div class="col-12">
                <div id="ec_sig_wrap" style="border:2px dashed var(--border);border-radius:10px;background:#fff;position:relative;cursor:crosshair">
                  <canvas id="ec_sig_canvas" style="display:block;width:100%;height:120px;touch-action:none"></canvas>
                  <div id="ec_sig_placeholder" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:var(--muted-2);font-size:14px;pointer-events:none">✍ Firma qui per aggiornare la firma privacy</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="ecClearSig()">✕ Rimuovi firma</button>
                  <span class="small text-muted" id="ec_sig_status"><?= !empty($customer['signature_path']) ? '✓ Firma presente' : 'Non firmato' ?></span>
                </div>
                <input type="hidden" id="ec_sig_data" value="">
                <input type="hidden" id="ec_clear_sig" value="0">
              </div>
            </div>
          </div>

        </div>
        <div id="ec_error" class="alert alert-danger mt-3 d-none"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteCustomer()">🗑 Elimina cliente</button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="btn btn-primary" onclick="saveCustomer()">Salva modifiche</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── New Card Modal ─────────────────────────────────────── -->
<div class="modal fade" id="newCardModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?= url('/customers?id=' . (int)$customer['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="new_card">
        <div class="modal-header">
          <h5 class="modal-title">Nuova card — <?= e($fullName) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Codice card *</label>
            <input class="form-control form-control-lg" name="card_code" placeholder="Es. IGA-2026-0042" required maxlength="64"></div>
          <div class="mb-3"><label class="form-label">Tipo</label>
            <select class="form-select" name="card_type">
              <option value="nominale">Nominale</option>
              <option value="abbonamento">Abbonamento</option>
              <option value="ospite">Ospite</option>
              <option value="staff">Staff</option>
            </select></div>
          <div class="mb-3"><label class="form-label">Stato</label>
            <select class="form-select" name="status">
              <option value="attiva">Attiva</option>
              <option value="chiusa">Chiusa</option>
              <option value="bloccata">Bloccata</option>
            </select></div>
          <div class="mb-3"><label class="form-label">Scadenza</label>
            <input class="form-control" type="datetime-local" name="expires_at"></div>
          <div class="mb-3"><label class="form-label">Note</label>
            <textarea class="form-control" name="notes" rows="2" maxlength="500"></textarea></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Crea card</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ── Edit Card Modal ────────────────────────────────────── -->
<div class="modal fade" id="editCardModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifica card <span id="editCard_code"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editCard_id">
        <div class="mb-3"><label class="form-label">Stato</label>
          <select class="form-select" id="editCard_status">
            <option value="attiva">Attiva</option>
            <option value="chiusa">Chiusa</option>
            <option value="bloccata">Bloccata</option>
            <option value="smarrita">Smarrita</option>
          </select></div>
        <div class="mb-3"><label class="form-label">Note</label>
          <textarea class="form-control" id="editCard_notes" rows="2" maxlength="500"></textarea></div>
        <div id="editCard_error" class="alert alert-danger d-none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" onclick="saveCard()">Salva</button>
      </div>
    </div>
  </div>
</div>

<?php endif; /* detail */ ?>
</div><!-- /cu-page -->

<div id="cu-toast"></div>

<script>
const CSRF              = <?= json_encode(csrf_token()) ?>;
const UPD_CUSTOMER_URL  = '<?= url('/api/customers/update.php') ?>';
const DEL_CUSTOMER_URL  = '<?= url('/api/customers/delete.php') ?>';
const UPD_CARD_URL      = '<?= url('/api/customers/update-card.php') ?>';
const CUSTOMER_ID       = <?= isset($customer) ? (int)$customer['id'] : 'null' ?>;
var _ecCameraStream     = null;

/* Toast */
var _toTimer;
function toast(msg, isErr) {
  var el = document.getElementById('cu-toast');
  el.textContent = msg;
  el.style.background = isErr ? 'var(--bad)' : 'var(--good)';
  el.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(_toTimer);
  _toTimer = setTimeout(function() { el.style.transform = 'translateX(-50%) translateY(80px)'; }, 2800);
}

/* Status filter pills (list view) */
document.querySelectorAll('.cu-pill[data-filter]').forEach(function(pill) {
  pill.addEventListener('click', function() {
    document.querySelectorAll('.cu-pill[data-filter]').forEach(function(p) { p.classList.remove('active'); });
    pill.classList.add('active');
    var f = pill.getAttribute('data-filter');
    document.querySelectorAll('#cuTable tbody tr').forEach(function(row) {
      row.style.display = (f === 'tutti' || row.getAttribute('data-status') === f) ? '' : 'none';
    });
  });
});

/* Detail tabs */
function showDetailTab(tab) {
  document.getElementById('pane-cards').style.display = tab === 'cards' ? '' : 'none';
  document.getElementById('pane-info').style.display  = tab === 'info'  ? '' : 'none';
  document.querySelectorAll('.cu-tab').forEach(function(t) { t.classList.remove('active'); });
  var idx = tab === 'cards' ? 0 : 1;
  document.querySelectorAll('.cu-tab')[idx].classList.add('active');
}

/* Save customer */
function saveCustomer() {
  var errEl = document.getElementById('ec_error');
  var fname = document.getElementById('ec_first_name').value.trim();
  var lname = document.getElementById('ec_last_name').value.trim();
  if (!fname || !lname) {
    errEl.textContent = 'Nome e cognome obbligatori.';
    errEl.classList.remove('d-none'); return;
  }
  ecStopCamera();
  var fd = new FormData();
  fd.append('_csrf', CSRF);
  fd.append('id', CUSTOMER_ID);
  fd.append('first_name', fname);
  fd.append('last_name', lname);
  fd.append('phone',        document.getElementById('ec_phone').value);
  fd.append('email',        document.getElementById('ec_email').value);
  fd.append('fiscal_code',  document.getElementById('ec_fiscal').value);
  fd.append('birth_date',   document.getElementById('ec_birth').value);
  fd.append('address',      document.getElementById('ec_address').value);
  fd.append('notes',        document.getElementById('ec_notes').value);
  fd.append('status',       document.getElementById('ec_status').value);
  fd.append('privacy_consent', document.getElementById('ec_privacy').checked ? '1' : '0');
  fd.append('doc_type',     document.getElementById('ec_doc_type').value);
  fd.append('doc_number',   document.getElementById('ec_doc_number').value);
  fd.append('doc_expiry',   document.getElementById('ec_doc_expiry').value);
  fd.append('doc_issuer',   document.getElementById('ec_doc_issuer').value);
  fd.append('photo_data',      document.getElementById('ec_photo_data').value);
  fd.append('clear_photo',     document.getElementById('ec_clear_photo').value);
  fd.append('signature_data',  document.getElementById('ec_sig_data').value);
  fd.append('clear_signature', document.getElementById('ec_clear_sig').value);
  fetch(UPD_CUSTOMER_URL, { method:'POST', body: fd })
  .then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('editCustomerModal')).hide();
      toast('✓ Cliente aggiornato');
      setTimeout(function() { location.reload(); }, 600);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.remove('d-none');
    }
  }).catch(function() { errEl.textContent = 'Errore di rete.'; errEl.classList.remove('d-none'); });
}

/* Delete customer */
function deleteCustomer() {
  if (!confirm('Eliminare definitivamente questo cliente?\nLe card verranno chiuse. Operazione non reversibile.')) return;
  fetch(DEL_CUSTOMER_URL, { method:'POST', body: new URLSearchParams({ _csrf: CSRF, id: CUSTOMER_ID }) })
  .then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      window.location.href = '<?= url('/customers') ?>';
    } else {
      alert('Impossibile eliminare: ' + (resp.error || 'Errore.'));
    }
  }).catch(function() { alert('Errore di rete.'); });
}

/* Webcam nel modal modifica */
function ecStartCamera() {
  if (!navigator.mediaDevices) { alert('Webcam non disponibile (serve HTTPS).'); return; }
  navigator.mediaDevices.getUserMedia({ video: { width:320, height:240, facingMode:'user' } })
  .then(function(stream) {
    _ecCameraStream = stream;
    var v = document.getElementById('ec_webcam_video');
    v.srcObject = stream; v.play();
    document.getElementById('ec_webcam_box').style.display = '';
    document.getElementById('ec_btn_cam').classList.add('d-none');
    document.getElementById('ec_btn_snap').classList.remove('d-none');
  }).catch(function(e) { alert('Webcam: ' + e.message); });
}

function ecSnapPhoto() {
  var v = document.getElementById('ec_webcam_video');
  var c = document.createElement('canvas');
  c.width = v.videoWidth || 320; c.height = v.videoHeight || 240;
  c.getContext('2d').drawImage(v, 0, 0);
  var data = c.toDataURL('image/jpeg', 0.85);
  document.getElementById('ec_photo_data').value = data;
  document.getElementById('ec_clear_photo').value = '0';
  var img = document.getElementById('ec_photo_img');
  img.src = data; img.style = 'width:100%;height:100%;object-fit:cover';
  document.getElementById('ec_webcam_box').style.display = 'none';
  document.getElementById('ec_btn_snap').classList.add('d-none');
  document.getElementById('ec_btn_cam').classList.remove('d-none');
  document.getElementById('ec_btn_cam').textContent = '↺ Riprendi';
  ecStopCamera();
}

function ecLoadFile(input) {
  var file = input.files[0]; if (!file) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var data = e.target.result;
    document.getElementById('ec_photo_data').value = data;
    document.getElementById('ec_clear_photo').value = '0';
    var img = document.getElementById('ec_photo_img');
    img.src = data; img.style = 'width:100%;height:100%;object-fit:cover';
  };
  reader.readAsDataURL(file);
}

function ecClearPhoto() {
  document.getElementById('ec_photo_data').value = '';
  document.getElementById('ec_clear_photo').value = '1';
  var img = document.getElementById('ec_photo_img');
  img.src = ''; img.style = 'font-size:40px;color:var(--muted-2)';
  img.textContent = '👤';
}

function ecStopCamera() {
  if (_ecCameraStream) { _ecCameraStream.getTracks().forEach(function(t){t.stop();}); _ecCameraStream = null; }
  document.getElementById('ec_webcam_box').style.display = 'none';
  document.getElementById('ec_btn_snap').classList.add('d-none');
  document.getElementById('ec_btn_cam').classList.remove('d-none');
}

/* Firma nel modal modifica */
(function() {
  var _ecSigCanvas, _ecSigCtx, _ecSigDrawing = false, _ecSigHasContent = false, _ecSigRect;
  function initEcSig() {
    _ecSigCanvas = document.getElementById('ec_sig_canvas');
    if (!_ecSigCanvas) return;
    var wrap = document.getElementById('ec_sig_wrap');
    _ecSigCanvas.width  = wrap.offsetWidth || 500;
    _ecSigCanvas.height = 120;
    _ecSigCtx = _ecSigCanvas.getContext('2d');
    _ecSigCtx.strokeStyle = '#0f2730';
    _ecSigCtx.lineWidth = 2.5;
    _ecSigCtx.lineCap = 'round';
    _ecSigCtx.lineJoin = 'round';
    _ecSigHasContent = false;
    document.getElementById('ec_sig_data').value = '';
    document.getElementById('ec_clear_sig').value = '0';
    document.getElementById('ec_sig_placeholder').style.display = '';
    _ecSigCanvas.addEventListener('pointerdown', function(e) {
      e.preventDefault();
      _ecSigDrawing = true;
      _ecSigRect = _ecSigCanvas.getBoundingClientRect();
      _ecSigCtx.beginPath();
      _ecSigCtx.moveTo((e.clientX - _ecSigRect.left) * (_ecSigCanvas.width / _ecSigRect.width),
                       (e.clientY - _ecSigRect.top)  * (_ecSigCanvas.height / _ecSigRect.height));
      _ecSigCanvas.setPointerCapture(e.pointerId);
    });
    _ecSigCanvas.addEventListener('pointermove', function(e) {
      if (!_ecSigDrawing) return;
      e.preventDefault();
      _ecSigCtx.lineTo((e.clientX - _ecSigRect.left) * (_ecSigCanvas.width / _ecSigRect.width),
                       (e.clientY - _ecSigRect.top)  * (_ecSigCanvas.height / _ecSigRect.height));
      _ecSigCtx.stroke();
      if (!_ecSigHasContent) {
        _ecSigHasContent = true;
        document.getElementById('ec_sig_placeholder').style.display = 'none';
        document.getElementById('ec_sig_status').textContent = '✓ Nuova firma';
        document.getElementById('ec_sig_status').style.color = 'var(--good)';
      }
    });
    _ecSigCanvas.addEventListener('pointerup', function() {
      _ecSigDrawing = false;
      if (_ecSigHasContent) document.getElementById('ec_sig_data').value = _ecSigCanvas.toDataURL('image/png');
    });
  }
  document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('editCustomerModal');
    if (modal) modal.addEventListener('shown.bs.modal', function() { setTimeout(initEcSig, 50); });
  });
  window.ecClearSig = function() {
    if (_ecSigCtx) _ecSigCtx.clearRect(0, 0, _ecSigCanvas.width, _ecSigCanvas.height);
    _ecSigHasContent = false;
    document.getElementById('ec_sig_data').value = '';
    document.getElementById('ec_clear_sig').value = '1';
    document.getElementById('ec_sig_placeholder').style.display = '';
    document.getElementById('ec_sig_status').textContent = 'Firma rimossa';
    document.getElementById('ec_sig_status').style.color = 'var(--bad)';
  };
})();

/* Edit card */
function openEditCard(data) {
  document.getElementById('editCard_id').value     = data.id;
  document.getElementById('editCard_code').textContent = data.card_code;
  document.getElementById('editCard_status').value = data.status;
  document.getElementById('editCard_notes').value  = data.notes || '';
  document.getElementById('editCard_error').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('editCardModal')).show();
}
function saveCard() {
  var errEl = document.getElementById('editCard_error');
  fetch(UPD_CARD_URL, { method:'POST', body: new URLSearchParams({
    _csrf:  CSRF,
    id:     document.getElementById('editCard_id').value,
    status: document.getElementById('editCard_status').value,
    notes:  document.getElementById('editCard_notes').value,
  })}).then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('editCardModal')).hide();
      toast('✓ Card aggiornata');
      setTimeout(function() { location.reload(); }, 600);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.remove('d-none');
    }
  }).catch(function() { errEl.textContent = 'Errore di rete.'; errEl.classList.remove('d-none'); });
}
</script>
