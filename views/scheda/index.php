<?php
$deptColor = ['bar' => 'primary', 'ristorante' => 'success'];
$deptLabel = ['bar' => 'Bar', 'ristorante' => 'Ristorante'];
$consTot   = ($barTotal ?? 0) + ($ristoTotal ?? 0);
$saldo     = max(0, $consTot - ($paidTotal ?? 0));
?>
<style>
/* ── Schede Consumazioni ─────────────────────────────────── */
.sc-wrap { padding: 20px 22px; }

.sc-page-head  { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.sc-page-title { font-family:'Poppins',sans-serif; font-size:22px; font-weight:800; color:var(--text); }
.sc-count-badge {
  padding:3px 10px; border-radius:20px;
  background:color-mix(in srgb, var(--accent) 15%, transparent);
  color:var(--accent); font-size:11px; font-weight:800;
}

/* Search */
.sc-search { display:flex; gap:8px; margin-bottom:18px; }
.sc-search-input {
  flex:1; padding:9px 14px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface);
  color:var(--text); font-size:13px; font-family:inherit; outline:none;
  transition:border-color .15s;
}
.sc-search-input:focus { border-color:var(--accent); }
.sc-search-btn {
  padding:9px 18px; border-radius:10px; border:none;
  background:var(--accent); color:var(--accent-ink);
  font-size:13px; font-weight:700; cursor:pointer; font-family:inherit;
  transition:opacity .12s;
}
.sc-search-btn:hover { opacity:.88; }
.sc-search-clear {
  padding:9px 12px; border-radius:10px; border:1px solid var(--border);
  background:transparent; color:var(--muted); cursor:pointer; font-size:14px;
  font-family:inherit; text-decoration:none; display:flex; align-items:center;
}

/* Table */
.sc-table-wrap { border:1px solid var(--border); border-radius:13px; overflow:hidden; }
.sc-table { width:100%; border-collapse:collapse; font-size:13px; }
.sc-table thead th {
  padding:9px 13px; background:var(--surface-2);
  font-size:10px; font-weight:800; text-transform:uppercase;
  letter-spacing:.07em; color:var(--muted); border-bottom:1px solid var(--border);
}
.sc-table tbody td {
  padding:10px 13px; border-bottom:1px solid var(--border);
  color:var(--text); vertical-align:middle;
}
.sc-table tbody tr:last-child td { border-bottom:none; }
.sc-table tbody tr:hover td { background:var(--surface-2); }
.sc-table tfoot td {
  padding:9px 13px; background:var(--surface-2);
  border-top:2px solid var(--border); font-weight:700; color:var(--text);
}

/* Dept chips */
.dept-chip { display:inline-block; padding:2px 9px; border-radius:10px; font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; }
.dept-chip.bar   { background:color-mix(in srgb,#17b3c4 18%,transparent); color:#17b3c4; }
.dept-chip.risto { background:color-mix(in srgb,#e8a020 18%,transparent); color:#e8a020; }

/* Status */
.sc-status { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:800; }
.sc-status::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
.sc-status.dentro  { background:color-mix(in srgb, var(--good) 15%, transparent); color:var(--good); }
.sc-status.uscito  { background:color-mix(in srgb, var(--muted) 15%, transparent); color:var(--muted); }
.sc-status.bloccato { background:color-mix(in srgb, var(--bad) 15%, transparent); color:var(--bad); }

/* Saldo */
.sc-saldo-open { color:var(--warn); font-weight:800; }
.sc-saldo-ok   { color:var(--good); font-weight:700; }

/* Open btn */
.sc-btn-open {
  display:inline-flex; align-items:center; gap:5px;
  padding:5px 13px; border-radius:8px; border:none;
  background:var(--accent); color:var(--accent-ink);
  font-size:11px; font-weight:700; text-decoration:none; cursor:pointer;
  transition:opacity .12s;
}
.sc-btn-open:hover { opacity:.85; color:var(--accent-ink); }

/* Empty */
.sc-empty { text-align:center; padding:52px 20px; color:var(--muted-2); }
.sc-empty-icon { font-size:44px; margin-bottom:10px; line-height:1; }
.sc-empty-msg  { font-size:13px; }

/* card code */
.sc-code { font-family:monospace; font-size:11.5px; color:var(--muted); background:var(--surface-2); padding:2px 7px; border-radius:5px; }

/* ── Detail ──────────────────────────────────────────────── */
.sc-back {
  display:inline-flex; align-items:center; gap:6px;
  padding:7px 13px; border-radius:9px; border:1px solid var(--border);
  background:transparent; color:var(--muted); font-size:12px; font-weight:700;
  text-decoration:none; margin-bottom:14px; transition:border-color .12s, color .12s;
}
.sc-back:hover { border-color:var(--accent); color:var(--accent); }

.sc-detail-head { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
.sc-detail-name { font-family:'Poppins',sans-serif; font-size:22px; font-weight:800; color:var(--text); }
.sc-detail-meta { font-size:12px; color:var(--muted); margin-left:auto; }

/* KPI */
.sc-kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
.sc-kpi { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:12px 14px; }
.sc-kpi-label { font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-2); margin-bottom:5px; }
.sc-kpi-value { font-family:'Poppins',sans-serif; font-size:20px; font-weight:800; }
.sc-kpi.kpi-bar    .sc-kpi-value { color:#17b3c4; }
.sc-kpi.kpi-risto  .sc-kpi-value { color:#e8a020; }
.sc-kpi.kpi-acconti .sc-kpi-value { color:var(--text); }
.sc-kpi.kpi-saldo-open .sc-kpi-value { color:var(--warn); }
.sc-kpi.kpi-saldo-ok   .sc-kpi-value { color:var(--good); }

/* Action buttons */
.sc-actions { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
.sc-act-btn {
  flex:1; min-width:110px; padding:12px 16px; border-radius:11px; border:none;
  font-size:13px; font-weight:800; cursor:pointer; font-family:inherit;
  transition:opacity .12s, transform .1s;
}
.sc-act-btn:hover { opacity:.88; transform:translateY(-1px); }
.sc-act-btn.bar     { background:#17b3c4; color:#fff; }
.sc-act-btn.risto   { background:#e8a020; color:#fff; }
.sc-act-btn.acconto { background:var(--surface-2); border:1px solid var(--border); color:var(--text); }
.sc-act-btn.chiudi  { background:var(--bad); color:#fff; }

/* Section label */
.sc-section-lbl {
  font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.09em;
  color:var(--muted); margin-bottom:8px;
  display:flex; align-items:center; gap:8px;
}
.sc-section-lbl::after { content:''; flex:1; height:1px; background:var(--border); }
.sc-section-badge {
  background:var(--surface-2); border:1px solid var(--border);
  color:var(--muted-2); font-size:9px; font-weight:800;
  padding:1px 7px; border-radius:8px;
}

/* Storno button */
.sc-btn-storna {
  padding:4px 10px; border-radius:7px; border:1px solid var(--bad);
  background:transparent; color:var(--bad); font-size:10px; font-weight:700;
  cursor:pointer; font-family:inherit; transition:background .12s;
}
.sc-btn-storna:hover { background:color-mix(in srgb, var(--bad) 12%, transparent); }
.sc-cancelled { opacity:.4; }
.sc-cancelled td { text-decoration:line-through; }

/* Modals */
.modal-content {
  background:var(--surface); border:1px solid var(--border);
  border-radius:16px; overflow:hidden;
}
.modal-header {
  border-bottom:1px solid var(--border); padding:14px 18px;
  background:var(--surface);
}
.modal-header.bar-hd   { background:color-mix(in srgb,#17b3c4 15%,var(--surface)); }
.modal-header.risto-hd { background:color-mix(in srgb,#e8a020 15%,var(--surface)); }
.modal-header.danger-hd { background:color-mix(in srgb, var(--bad) 15%, var(--surface)); }
.modal-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:var(--text); }
.modal-body   { padding:18px; background:var(--surface); }
.modal-footer { border-top:1px solid var(--border); padding:12px 18px; background:var(--surface); gap:8px; }
.btn-close { filter:var(--btn-close-filter, none); }
[data-theme="dark"] .btn-close { filter:invert(1) brightness(1); }

.sc-form-row { margin-bottom:14px; }
.sc-form-label { font-size:10.5px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-bottom:5px; display:block; }
.sc-input {
  width:100%; padding:10px 13px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface-2);
  color:var(--text); font-size:14px; font-family:inherit; outline:none;
  transition:border-color .15s; box-sizing:border-box;
}
.sc-input:focus { border-color:var(--accent); }

.sc-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
.sc-total-block { display:flex; flex-direction:column; justify-content:flex-end; }
.sc-total-preview { font-family:'Poppins',sans-serif; font-size:26px; font-weight:800; color:var(--accent); }
.sc-total-lbl { font-size:10px; color:var(--muted-2); margin-bottom:2px; }

.sc-prod-btns { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:6px; }
.sc-prod-btn {
  padding:5px 12px; border-radius:20px; border:1px solid var(--border);
  background:var(--surface-2); color:var(--text); font-size:11px; font-weight:700;
  cursor:pointer; font-family:inherit; transition:border-color .12s, color .12s;
}
.sc-prod-btn:hover { border-color:var(--accent); color:var(--accent); }
.sc-no-products { font-size:11px; color:var(--muted-2); font-style:italic; }

.sc-modal-actions { display:flex; gap:8px; }
.sc-modal-btn {
  padding:10px 22px; border-radius:10px; border:none; font-size:13px;
  font-weight:800; cursor:pointer; font-family:inherit; transition:opacity .12s;
}
.sc-modal-btn:hover { opacity:.85; }
.sc-modal-btn.cancel { background:var(--surface-2); border:1px solid var(--border); color:var(--text); }
.sc-modal-btn.bar    { background:#17b3c4; color:#fff; }
.sc-modal-btn.risto  { background:#e8a020; color:#fff; }
.sc-modal-btn.green  { background:var(--good); color:#fff; }
.sc-modal-btn.danger { background:var(--bad); color:#fff; }

.sc-chiudi-summary {
  display:grid; grid-template-columns:repeat(3,1fr); gap:10px;
  background:var(--surface-2); border:1px solid var(--border);
  border-radius:12px; padding:14px; margin-bottom:16px; text-align:center;
}
.sc-chiudi-summary-lbl { font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--muted-2); margin-bottom:4px; }
.sc-chiudi-summary-val { font-family:'Poppins',sans-serif; font-size:18px; font-weight:800; color:var(--text); }
.sc-chiudi-summary-val.warn { color:var(--warn); }

.sc-warn-box {
  padding:10px 13px; border-radius:9px; font-size:11px; margin-top:12px;
  background:color-mix(in srgb, var(--warn) 12%, transparent);
  border:1px solid color-mix(in srgb, var(--warn) 30%, transparent);
  color:var(--warn);
}

.sc-err {
  display:none; padding:9px 13px; border-radius:9px; margin-top:10px;
  background:color-mix(in srgb, var(--bad) 12%, transparent);
  border:1px solid color-mix(in srgb, var(--bad) 30%, transparent);
  font-size:12px; color:var(--bad);
}
.sc-err.visible { display:block; }

/* Toast */
#sc-toast {
  position:fixed; bottom:1.5rem; left:50%; transform:translateX(-50%) translateY(80px);
  background:var(--good); color:#fff; padding:.55rem 1.4rem; border-radius:8px;
  font-size:13px; font-weight:700; z-index:9999; transition:transform .25s;
  pointer-events:none; white-space:nowrap; box-shadow:0 4px 16px rgba(0,0,0,.22);
}

@media (max-width:600px) {
  .sc-kpi-grid { grid-template-columns:repeat(2,1fr); }
  .sc-row-3 { grid-template-columns:1fr; }
}
</style>

<div class="sc-wrap">

<?php if (!$entry): ?>
<!-- ═══════════════════════════════════════════
     LIST
═══════════════════════════════════════════ -->
<div class="sc-page-head">
  <h1 class="sc-page-title">Schede Consumazioni</h1>
  <span class="sc-count-badge"><?= count($activeEntries) ?> presenti</span>
</div>

<form method="GET" action="<?= url('/scheda') ?>">
  <div class="sc-search">
    <input type="text" name="q" class="sc-search-input"
           placeholder="Cerca per nome o codice card…"
           value="<?= e($q ?? '') ?>">
    <button class="sc-search-btn" type="submit">Cerca</button>
    <?php if (!empty($q)): ?>
    <a class="sc-search-clear" href="<?= url('/scheda') ?>">✕</a>
    <?php endif; ?>
  </div>
</form>

<?php if (empty($activeEntries)): ?>
<div class="sc-empty">
  <div class="sc-empty-icon">🏊</div>
  <div class="sc-empty-msg">Nessun cliente presente in questo momento.</div>
</div>
<?php else: ?>
<div class="sc-table-wrap">
  <table class="sc-table">
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Card</th>
        <th>Lettini</th>
        <th class="text-end" style="color:#17b3c4">Bar</th>
        <th class="text-end" style="color:#e8a020">Ristorante</th>
        <th class="text-end">Totale</th>
        <th class="text-end">Pagato</th>
        <th class="text-end">Saldo</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($activeEntries as $ae):
        $tot  = (float)$ae['bar_total'] + (float)$ae['risto_total'];
        $paid = (float)$ae['paid_total'];
        $sal  = max(0, $tot - $paid);
      ?>
      <tr>
        <td>
          <strong><?= e($ae['customer_name']) ?></strong>
          <?php if ($ae['phone']): ?>
          <div style="font-size:11px;color:var(--muted)"><?= e($ae['phone']) ?></div>
          <?php endif; ?>
        </td>
        <td><span class="sc-code"><?= e($ae['card_code']) ?></span></td>
        <td style="color:var(--muted);font-size:12px"><?= e($ae['places'] ?? '—') ?></td>
        <td class="text-end" style="color:#17b3c4;font-size:12px">€ <?= number_format((float)$ae['bar_total'], 2, ',', '.') ?></td>
        <td class="text-end" style="color:#e8a020;font-size:12px">€ <?= number_format((float)$ae['risto_total'], 2, ',', '.') ?></td>
        <td class="text-end" style="font-weight:700">€ <?= number_format($tot, 2, ',', '.') ?></td>
        <td class="text-end" style="color:var(--muted);font-size:12px">€ <?= number_format($paid, 2, ',', '.') ?></td>
        <td class="text-end">
          <?php if ($sal > 0): ?>
          <span class="sc-saldo-open">€ <?= number_format($sal, 2, ',', '.') ?></span>
          <?php else: ?>
          <span class="sc-saldo-ok">✓ Saldato</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="<?= url('/scheda?entry=' . (int)$ae['id']) ?>" class="sc-btn-open">Apri ↗</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php else: ?>
<!-- ═══════════════════════════════════════════
     DETAIL
═══════════════════════════════════════════ -->

<a class="sc-back" href="<?= url('/scheda') ?>">← Schede</a>

<div class="sc-detail-head">
  <span class="sc-detail-name"><?= e($entry['customer_name']) ?></span>
  <?php
    $stClass = match($entry['status']) { 'dentro'=>'dentro','bloccato'=>'bloccato', default=>'uscito' };
    $stLabel = match($entry['status']) { 'dentro'=>'Dentro','bloccato'=>'Bloccato', default=>ucfirst($entry['status']) };
  ?>
  <span class="sc-status <?= $stClass ?>"><?= $stLabel ?></span>
  <?php if ($entry['places']): ?>
  <span class="sc-status uscito">Lettini: <?= e($entry['places']) ?></span>
  <?php endif; ?>
  <?php if ($entry['phone']): ?>
  <span style="font-size:12px;color:var(--muted)">📞 <?= e($entry['phone']) ?></span>
  <?php endif; ?>
  <span class="sc-code sc-detail-meta"><?= e($entry['card_code']) ?></span>
</div>

<!-- KPI -->
<div class="sc-kpi-grid">
  <div class="sc-kpi kpi-bar">
    <div class="sc-kpi-label">Bar</div>
    <div class="sc-kpi-value">€ <?= number_format($barTotal, 2, ',', '.') ?></div>
  </div>
  <div class="sc-kpi kpi-risto">
    <div class="sc-kpi-label">Ristorante</div>
    <div class="sc-kpi-value">€ <?= number_format($ristoTotal, 2, ',', '.') ?></div>
  </div>
  <div class="sc-kpi kpi-acconti">
    <div class="sc-kpi-label">Acconti</div>
    <div class="sc-kpi-value">€ <?= number_format($paidTotal, 2, ',', '.') ?></div>
  </div>
  <div class="sc-kpi <?= $saldo > 0 ? 'kpi-saldo-open' : 'kpi-saldo-ok' ?>">
    <div class="sc-kpi-label">Da pagare</div>
    <div class="sc-kpi-value">€ <?= number_format($saldo, 2, ',', '.') ?></div>
  </div>
</div>

<!-- Actions -->
<?php if ($entry['status'] === 'dentro'): ?>
<div class="sc-actions">
  <button class="sc-act-btn bar"     onclick="openConsModal('bar')">+ Bar</button>
  <button class="sc-act-btn risto"   onclick="openConsModal('ristorante')">+ Ristorante</button>
  <button class="sc-act-btn acconto" onclick="openAccontoModal()">+ Acconto</button>
  <button class="sc-act-btn chiudi"  onclick="openChiudiModal()">Chiudi conto</button>
</div>
<?php endif; ?>

<!-- Consumazioni -->
<?php
  $nActive    = count(array_filter($movements, fn($m) => $m['status'] !== 'cancelled'));
  $nCancelled = count($movements) - $nActive;
?>
<div class="sc-section-lbl">
  Consumazioni
  <span class="sc-section-badge"><?= $nActive ?></span>
  <?php if ($nCancelled): ?>
  <span class="sc-section-badge"><?= $nCancelled ?> stornati</span>
  <?php endif; ?>
</div>

<?php if (empty($movements)): ?>
<div class="sc-empty" style="padding:28px 0">
  <div class="sc-empty-msg">Nessuna consumazione registrata.</div>
</div>
<?php else: ?>
<div class="sc-table-wrap">
  <table class="sc-table">
    <thead>
      <tr>
        <th style="width:50px">Ora</th>
        <th style="width:90px">Reparto</th>
        <th>Descrizione</th>
        <th class="text-center" style="width:50px">Qtà</th>
        <th class="text-end" style="width:80px">P.U.</th>
        <th class="text-end" style="width:90px">Totale</th>
        <th style="width:70px"></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($movements as $m):
        $cancelled = ($m['status'] === 'cancelled');
      ?>
      <tr class="<?= $cancelled ? 'sc-cancelled' : '' ?>">
        <td style="color:var(--muted-2);font-size:11px"><?= date('H:i', strtotime($m['created_at'])) ?></td>
        <td>
          <span class="dept-chip <?= $m['department'] === 'bar' ? 'bar' : 'risto' ?>">
            <?= e($deptLabel[$m['department']] ?? $m['department']) ?>
          </span>
        </td>
        <td><?= e($m['description']) ?></td>
        <td class="text-center" style="font-size:12px;color:var(--muted)"><?= rtrim(rtrim(number_format((float)$m['quantity'], 2), '0'), '.') ?></td>
        <td class="text-end" style="font-size:12px;color:var(--muted)">€ <?= number_format((float)$m['unit_price'], 2, ',', '.') ?></td>
        <td class="text-end" style="font-weight:700">€ <?= number_format((float)$m['total_amount'], 2, ',', '.') ?></td>
        <td class="text-end">
          <?php if (!$cancelled && $entry['status'] === 'dentro'): ?>
          <button class="sc-btn-storna"
                  onclick="stornaConsumazione(<?= (int)$m['id'] ?>, <?= json_encode($m['description']) ?>)">
            Storna
          </button>
          <?php elseif ($cancelled): ?>
          <span style="font-size:10px;color:var(--muted-2)">stornato</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <?php if ($nActive > 0): ?>
    <tfoot>
      <tr>
        <td colspan="5" class="text-end" style="color:var(--muted);font-size:11px">Totale consumazioni</td>
        <td class="text-end">€ <?= number_format($consTot, 2, ',', '.') ?></td>
        <td></td>
      </tr>
    </tfoot>
    <?php endif; ?>
  </table>
</div>
<?php endif; ?>

<?php endif; /* entry detail */ ?>
</div><!-- /sc-wrap -->


<!-- ═══════════════════════════════════════════
     MODALS
═══════════════════════════════════════════ -->

<!-- Consumazione Modal -->
<div class="modal fade" id="consModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="consModalHeader">
        <h5 class="modal-title" id="consModalTitle">Aggiungi consumazione</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="sc-prod-btns" id="cons_product_btns"></div>
        <div id="cons_no_products" class="sc-no-products" style="display:none;margin-bottom:12px">
          Nessun prodotto configurato per questo reparto.
        </div>
        <hr style="border-color:var(--border);margin:10px 0 14px">
        <div class="sc-form-row">
          <label class="sc-form-label">Descrizione *</label>
          <input type="text" class="sc-input" id="cons_desc" maxlength="150"
                 placeholder="Es. Acqua naturale + Gelato artigianale">
        </div>
        <div class="sc-row-3">
          <div class="sc-form-row" style="margin:0">
            <label class="sc-form-label">Quantità</label>
            <input type="number" class="sc-input" id="cons_qty" min="0.5" step="0.5" value="1">
          </div>
          <div class="sc-form-row" style="margin:0">
            <label class="sc-form-label">Prezzo unitario (€) *</label>
            <input type="number" class="sc-input" id="cons_price" min="0.01" step="0.01" placeholder="0,00">
          </div>
          <div class="sc-total-block">
            <div class="sc-total-lbl">Totale</div>
            <div class="sc-total-preview" id="cons_total_preview">€ 0,00</div>
          </div>
        </div>
        <div class="sc-err" id="cons_error"></div>
      </div>
      <div class="modal-footer">
        <div class="sc-modal-actions" style="width:100%;justify-content:flex-end">
          <button type="button" class="sc-modal-btn cancel" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="sc-modal-btn" id="cons_submit_btn" onclick="submitConsumazione()">Registra</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Acconto Modal -->
<div class="modal fade" id="accontoModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registra acconto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="sc-form-row">
          <label class="sc-form-label">Importo (€) *</label>
          <input type="number" class="sc-input" id="acc_amount" min="0.01" step="0.01" placeholder="0,00">
        </div>
        <div class="sc-form-row">
          <label class="sc-form-label">Metodo di pagamento</label>
          <select class="sc-input sc-select" id="acc_method">
            <option value="contanti">Contanti</option>
            <option value="carta">Carta / POS</option>
            <option value="satispay">Satispay</option>
            <option value="bonifico">Bonifico</option>
            <option value="altro">Altro</option>
          </select>
        </div>
        <div class="sc-form-row">
          <label class="sc-form-label">Note (opzionale)</label>
          <input type="text" class="sc-input" id="acc_note" maxlength="200">
        </div>
        <div class="sc-err" id="acc_error"></div>
      </div>
      <div class="modal-footer">
        <div class="sc-modal-actions" style="width:100%;justify-content:flex-end">
          <button type="button" class="sc-modal-btn cancel" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="sc-modal-btn green" onclick="submitAcconto()">Registra acconto</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chiudi Conto Modal -->
<div class="modal fade" id="chiudiModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header danger-hd">
        <h5 class="modal-title">Chiudi conto &amp; Check-out</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="sc-chiudi-summary">
          <div>
            <div class="sc-chiudi-summary-lbl">Consumazioni</div>
            <div class="sc-chiudi-summary-val" id="chiudi_tot">€ 0,00</div>
          </div>
          <div>
            <div class="sc-chiudi-summary-lbl">Acconti</div>
            <div class="sc-chiudi-summary-val" id="chiudi_acconti">€ 0,00</div>
          </div>
          <div>
            <div class="sc-chiudi-summary-lbl">Residuo</div>
            <div class="sc-chiudi-summary-val warn" id="chiudi_residuo">€ 0,00</div>
          </div>
        </div>
        <div class="sc-form-row">
          <label class="sc-form-label">Importo da incassare ora (€)</label>
          <input type="number" class="sc-input" id="chiudi_amount" min="0" step="0.01">
          <div style="font-size:10.5px;color:var(--muted-2);margin-top:4px">Lascia 0 se il saldo è già coperto dagli acconti.</div>
        </div>
        <div class="sc-form-row">
          <label class="sc-form-label">Metodo di pagamento</label>
          <select class="sc-input sc-select" id="chiudi_method">
            <option value="contanti">Contanti</option>
            <option value="carta">Carta / POS</option>
            <option value="satispay">Satispay</option>
            <option value="bonifico">Bonifico</option>
          </select>
        </div>
        <div class="sc-form-row">
          <label class="sc-form-label">Note (opzionale)</label>
          <input type="text" class="sc-input" id="chiudi_note" maxlength="200">
        </div>
        <div class="sc-warn-box">⚠ Questa operazione registra il pagamento e effettua il <strong>check-out</strong> del cliente.</div>
        <div class="sc-err" id="chiudi_error"></div>
      </div>
      <div class="modal-footer">
        <div class="sc-modal-actions" style="width:100%;justify-content:flex-end">
          <button type="button" class="sc-modal-btn cancel" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="sc-modal-btn danger" id="chiudi_submit_btn" onclick="submitChiudi()">
            Chiudi conto e Check-out
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="sc-toast"></div>

<script>
const ENTRY_ID  = <?= $entry ? (int)$entry['id']      : 'null' ?>;
const CARD_ID   = <?= $entry ? (int)$entry['card_id'] : 'null' ?>;
const CSRF      = <?= json_encode(csrf_token()) ?>;
const BAR_TOTAL   = <?= number_format($barTotal  ?? 0, 4, '.', '') ?>;
const RISTO_TOTAL = <?= number_format($ristoTotal ?? 0, 4, '.', '') ?>;
const PAID_TOTAL  = <?= number_format($paidTotal  ?? 0, 4, '.', '') ?>;
const PRODUCTS    = <?= json_encode($products ?? [], JSON_UNESCAPED_UNICODE) ?>;

const ADD_URL    = '<?= url('/api/scheda/add-consumazione.php') ?>';
const STORNA_URL = '<?= url('/api/scheda/storna-consumazione.php') ?>';
const ACC_URL    = '<?= url('/api/scheda/add-acconto.php') ?>';
const CHIUDI_URL = '<?= url('/api/scheda/chiudi-conto.php') ?>';

// ── Toast ─────────────────────────────────────────────────
var _toTimer;
function toast(msg, isErr) {
  var el = document.getElementById('sc-toast');
  el.textContent = msg;
  el.style.background = isErr ? 'var(--bad)' : 'var(--good)';
  el.style.transform  = 'translateX(-50%) translateY(0)';
  clearTimeout(_toTimer);
  _toTimer = setTimeout(function() { el.style.transform = 'translateX(-50%) translateY(80px)'; }, 2800);
}

// ── Consumazione modal ────────────────────────────────────
var _consDept = 'bar';
function openConsModal(dept) {
  _consDept = dept;
  var isBar = dept === 'bar';
  document.getElementById('consModalTitle').textContent  = isBar ? 'Aggiungi — Bar' : 'Aggiungi — Ristorante';
  document.getElementById('consModalHeader').className   = 'modal-header ' + (isBar ? 'bar-hd' : 'risto-hd');
  document.getElementById('cons_submit_btn').className   = 'sc-modal-btn ' + (isBar ? 'bar' : 'risto');
  document.getElementById('cons_desc').value  = '';
  document.getElementById('cons_qty').value   = '1';
  document.getElementById('cons_price').value = '';
  document.getElementById('cons_total_preview').textContent = '€ 0,00';
  var errEl = document.getElementById('cons_error');
  errEl.classList.remove('visible'); errEl.textContent = '';

  var btns     = document.getElementById('cons_product_btns');
  var none     = document.getElementById('cons_no_products');
  var filtered = PRODUCTS.filter(function(p) { return p.department === dept; });
  btns.innerHTML = '';
  filtered.forEach(function(p) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'sc-prod-btn';
    b.textContent = p.name + '  €' + parseFloat(p.price).toFixed(2).replace('.', ',');
    b.onclick = function() {
      document.getElementById('cons_desc').value  = p.name;
      document.getElementById('cons_price').value = p.price;
      updateConsTotal();
    };
    btns.appendChild(b);
  });
  none.style.display = filtered.length ? 'none' : '';

  new bootstrap.Modal(document.getElementById('consModal')).show();
  setTimeout(function() { document.getElementById('cons_desc').focus(); }, 350);
}

function updateConsTotal() {
  var qty   = parseFloat(document.getElementById('cons_qty').value)   || 0;
  var price = parseFloat(document.getElementById('cons_price').value) || 0;
  document.getElementById('cons_total_preview').textContent =
    '€ ' + (qty * price).toFixed(2).replace('.', ',');
}
document.getElementById('cons_qty').addEventListener('input', updateConsTotal);
document.getElementById('cons_price').addEventListener('input', updateConsTotal);

function submitConsumazione() {
  var desc  = document.getElementById('cons_desc').value.trim();
  var qty   = document.getElementById('cons_qty').value;
  var price = document.getElementById('cons_price').value;
  var errEl = document.getElementById('cons_error');
  if (!desc || !price || parseFloat(price) <= 0) {
    errEl.textContent = 'Compila descrizione e prezzo.';
    errEl.classList.add('visible');
    return;
  }
  var btn = document.getElementById('cons_submit_btn');
  btn.disabled = true;
  fetch(ADD_URL, { method:'POST', body: new URLSearchParams({
    _csrf: CSRF, entry_id: ENTRY_ID, card_id: CARD_ID,
    department: _consDept, description: desc, quantity: qty, price: price,
  })}).then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('consModal')).hide();
      toast('✓ Consumazione registrata');
      setTimeout(function() { location.reload(); }, 600);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.add('visible');
      btn.disabled = false;
    }
  }).catch(function() {
    errEl.textContent = 'Errore di rete.';
    errEl.classList.add('visible');
    btn.disabled = false;
  });
}

// ── Storna ───────────────────────────────────────────────
function stornaConsumazione(movId, desc) {
  if (!confirm('Stornare "' + desc + '"?')) return;
  fetch(STORNA_URL, { method:'POST', body: new URLSearchParams({ _csrf: CSRF, movement_id: movId }) })
  .then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      toast('✓ Consumazione stornata');
      setTimeout(function() { location.reload(); }, 600);
    } else {
      toast('Errore: ' + (resp.error || 'sconosciuto'), true);
    }
  });
}

// ── Acconto modal ─────────────────────────────────────────
function openAccontoModal() {
  document.getElementById('acc_amount').value = '';
  setSelectValue('acc_method', 'contanti');
  document.getElementById('acc_note').value   = '';
  var errEl = document.getElementById('acc_error');
  errEl.classList.remove('visible'); errEl.textContent = '';
  new bootstrap.Modal(document.getElementById('accontoModal')).show();
  setTimeout(function() { document.getElementById('acc_amount').focus(); }, 350);
}

function submitAcconto() {
  var amount = document.getElementById('acc_amount').value;
  var errEl  = document.getElementById('acc_error');
  if (!amount || parseFloat(amount) <= 0) {
    errEl.textContent = 'Inserisci un importo valido.';
    errEl.classList.add('visible');
    return;
  }
  fetch(ACC_URL, { method:'POST', body: new URLSearchParams({
    _csrf: CSRF, entry_id: ENTRY_ID, card_id: CARD_ID,
    amount: amount,
    payment_method: document.getElementById('acc_method').value,
    note: document.getElementById('acc_note').value,
  })}).then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('accontoModal')).hide();
      toast('✓ Acconto registrato');
      setTimeout(function() { location.reload(); }, 600);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.add('visible');
    }
  }).catch(function() {
    errEl.textContent = 'Errore di rete.';
    errEl.classList.add('visible');
  });
}

// ── Chiudi conto modal ────────────────────────────────────
function openChiudiModal() {
  var tot   = BAR_TOTAL + RISTO_TOTAL;
  var saldo = Math.max(0, tot - PAID_TOTAL);
  document.getElementById('chiudi_tot').textContent     = '€ ' + tot.toFixed(2).replace('.', ',');
  document.getElementById('chiudi_acconti').textContent = '€ ' + PAID_TOTAL.toFixed(2).replace('.', ',');
  document.getElementById('chiudi_residuo').textContent = '€ ' + saldo.toFixed(2).replace('.', ',');
  document.getElementById('chiudi_amount').value        = saldo > 0 ? saldo.toFixed(2) : '0';
  setSelectValue('chiudi_method', 'contanti');
  document.getElementById('chiudi_note').value          = '';
  var errEl = document.getElementById('chiudi_error');
  errEl.classList.remove('visible'); errEl.textContent = '';
  document.getElementById('chiudi_submit_btn').disabled = false;
  new bootstrap.Modal(document.getElementById('chiudiModal')).show();
}

function submitChiudi() {
  var errEl = document.getElementById('chiudi_error');
  var btn   = document.getElementById('chiudi_submit_btn');
  btn.disabled = true;
  btn.textContent = 'Elaborazione…';
  fetch(CHIUDI_URL, { method:'POST', body: new URLSearchParams({
    _csrf: CSRF, entry_id: ENTRY_ID, card_id: CARD_ID,
    amount: document.getElementById('chiudi_amount').value || '0',
    payment_method: document.getElementById('chiudi_method').value,
    note: document.getElementById('chiudi_note').value,
  })}).then(function(r) { return r.json(); }).then(function(resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('chiudiModal')).hide();
      toast('✓ Conto chiuso — check-out effettuato');
      setTimeout(function() { location.href = '<?= url('/scheda') ?>'; }, 1200);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.add('visible');
      btn.disabled = false;
      btn.textContent = 'Chiudi conto e Check-out';
    }
  }).catch(function() {
    errEl.textContent = 'Errore di rete.';
    errEl.classList.add('visible');
    btn.disabled = false;
    btn.textContent = 'Chiudi conto e Check-out';
  });
}
</script>
