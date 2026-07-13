<style>
/* ── Cassa: full-viewport ────────────────────────────────── */
body.ca-active { overflow: hidden !important; }
body.ca-active .topbar { position: relative !important; }
body.ca-active main.app {
  display: flex; flex-direction: column;
  height: calc(100dvh - 57px); overflow: hidden;
}

.ca-wrap {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
  padding: 12px 16px 10px; gap: 10px;
}

/* KPI row */
.ca-kpi-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; flex-shrink: 0; }
.ca-kpi {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 12px; padding: 11px 14px;
}
.ca-kpi-label { font-size: 9px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .07em; color: var(--muted-2); margin-bottom: 3px; }
.ca-kpi-value { font-family: 'Poppins', sans-serif;
  font-size: 20px; font-weight: 800; color: var(--text); }
.ca-kpi-value.warn { color: var(--warn); }
.ca-kpi-value.good { color: var(--good); }

/* Main split */
.ca-main {
  flex: 1; min-height: 0;
  display: grid; grid-template-columns: 40% 1fr; gap: 10px;
}

/* Left panel */
.ca-list-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; display: flex; flex-direction: column; overflow: hidden;
}
.ca-list-head { padding: 12px 12px 8px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
.ca-list-title { font-family: 'Poppins', sans-serif;
  font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 7px; }
.ca-search {
  width: 100%; padding: 7px 11px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 12px; font-family: inherit;
  outline: none; box-sizing: border-box; transition: border-color .15s;
}
.ca-search:focus { border-color: var(--accent); }
.ca-search::placeholder { color: var(--muted-2); }

.ca-list { flex: 1; min-height: 0; overflow-y: auto; scrollbar-width: none; }
.ca-list::-webkit-scrollbar { display: none; }

.ca-account {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px; border-bottom: 1px solid var(--border);
  cursor: pointer; transition: background .1s; text-decoration: none;
}
.ca-account:last-child { border-bottom: none; }
.ca-account:hover { background: var(--surface-2); }
.ca-account.active { background: color-mix(in srgb, var(--accent) 8%, var(--surface)); }
.ca-account.active .ca-acc-name { color: var(--accent); }

.ca-acc-avatar {
  width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Poppins', sans-serif; font-size: 12px;
  font-weight: 800; color: #fff; overflow: hidden;
}
.ca-acc-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ca-acc-info { flex: 1; min-width: 0; }
.ca-acc-name { font-size: 12.5px; font-weight: 700; color: var(--text);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ca-acc-sub  { font-size: 10px; color: var(--muted); margin-top: 1px; }
.ca-acc-bal  { font-family: 'Poppins', sans-serif;
  font-size: 14px; font-weight: 800; flex-shrink: 0; }
.ca-acc-bal.open { color: var(--warn); }
.ca-acc-bal.zero { color: var(--good); }
.ca-list-empty { text-align: center; padding: 32px 12px;
  color: var(--muted-2); font-size: 12px; }

/* Right panel */
.ca-detail-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; display: flex; flex-direction: column; overflow: hidden;
}
.ca-detail-empty {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 8px;
  color: var(--muted-2); font-size: 13px;
}
.ca-detail-empty-icon { font-size: 40px; }

/* Detail header */
.ca-det-head {
  padding: 12px 16px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: 11px; flex-shrink: 0;
}
.ca-det-avatar {
  width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Poppins', sans-serif; font-size: 16px;
  font-weight: 800; color: #fff; overflow: hidden;
}
.ca-det-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ca-det-name { font-family: 'Poppins', sans-serif;
  font-size: 18px; font-weight: 800; color: var(--text); line-height: 1.15; }
.ca-det-meta { font-size: 11.5px; color: var(--muted); }
.ca-det-code { font-family: monospace; font-size: 11px;
  background: color-mix(in srgb, var(--accent) 12%, transparent);
  color: var(--accent); padding: 1px 7px; border-radius: 5px; margin-left: 5px; }
.ca-status { display: inline-flex; align-items: center; gap: 4px;
  padding: 2px 8px; border-radius: 20px; font-size: 9.5px; font-weight: 800; margin-left: 7px; }
.ca-status::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
.ca-status.dentro { background:color-mix(in srgb,var(--good) 15%,transparent); color:var(--good); }
.ca-status.fuori  { background:color-mix(in srgb,var(--muted) 15%,transparent); color:var(--muted); }

/* KPI strip */
.ca-det-kpi { display: grid; grid-template-columns: repeat(4,1fr);
  border-bottom: 1px solid var(--border); flex-shrink: 0; }
.ca-det-kpi-cell { padding: 9px 14px; border-right: 1px solid var(--border); }
.ca-det-kpi-cell:last-child { border-right: none; }
.ca-det-kpi-label { font-size: 8.5px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .07em; color: var(--muted-2); margin-bottom: 2px; }
.ca-det-kpi-value { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 800; }
.ca-det-kpi-value.bar   { color: #17b3c4; }
.ca-det-kpi-value.risto { color: #e8a020; }
.ca-det-kpi-value.paid  { color: var(--muted); }
.ca-det-kpi-value.due   { color: var(--warn); }
.ca-det-kpi-value.zero  { color: var(--good); }

/* Scrollable movements area */
.ca-det-scroll {
  flex: 1; min-height: 0; overflow-y: auto; scrollbar-width: none;
  padding: 10px 16px;
}
.ca-det-scroll::-webkit-scrollbar { display: none; }

.ca-section-lbl { font-size: 9px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .09em; color: var(--muted); margin-bottom: 6px;
  display: flex; align-items: center; gap: 7px; }
.ca-section-lbl::after { content:''; flex:1; height:1px; background:var(--border); }

.ca-mov-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 4px; }
.ca-mov-table td { padding: 5px 0; border-bottom: 1px solid var(--border); color: var(--text); }
.ca-mov-table tr:last-child td { border-bottom: none; }
.dept-chip { display:inline-block; padding:1px 6px; border-radius:7px;
  font-size:9px; font-weight:800; text-transform:uppercase; }
.dept-chip.bar   { background:color-mix(in srgb,#17b3c4 18%,transparent); color:#17b3c4; }
.dept-chip.risto { background:color-mix(in srgb,#e8a020 18%,transparent); color:#e8a020; }
.dept-chip.pay   { background:color-mix(in srgb,var(--good) 18%,transparent); color:var(--good); }

/* Fixed payment footer */
.ca-det-foot {
  flex-shrink: 0; padding: 12px 16px;
  border-top: 1px solid var(--border); background: var(--surface);
}
.ca-pay-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px; }
.ca-pay-label { font-size: 9.5px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; display: block; }
.ca-pay-input {
  width: 100%; padding: 8px 11px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 13px; font-family: inherit;
  outline: none; transition: border-color .15s; box-sizing: border-box;
}
.ca-pay-input:focus { border-color: var(--accent); }
.ca-pay-actions { display: flex; gap: 7px; margin-top: 4px; }
.ca-btn {
  flex: 1; padding: 9px; border-radius: 9px; border: none;
  font-size: 12px; font-weight: 800; cursor: pointer;
  font-family: inherit; transition: opacity .12s; white-space: nowrap;
}
.ca-btn:hover { opacity: .85; }
.ca-btn.pay           { background: var(--accent); color: var(--accent-ink); }
.ca-btn.checkout      { background: var(--good);  color: #fff; }
.ca-btn.checkout-warn { background: var(--warn);  color: #fff; }

/* Alerts */
.ca-flash { padding: 8px 12px; border-radius: 8px; font-size: 11.5px; margin: 10px 16px 0; flex-shrink: 0; }
.ca-flash.ok   { background:color-mix(in srgb,var(--good) 10%,transparent); border:1px solid color-mix(in srgb,var(--good) 25%,transparent); color:var(--good); }
.ca-flash.err  { background:color-mix(in srgb,var(--bad) 10%,transparent);  border:1px solid color-mix(in srgb,var(--bad) 25%,transparent);  color:var(--bad); }
</style>

<script>document.body.classList.add('ca-active');</script>

<?php
function caInitials(string $name): string {
    $pts = preg_split('/\s+/', trim($name));
    return strtoupper(mb_substr($pts[0] ?? '', 0, 1) . mb_substr($pts[1] ?? '', 0, 1));
}
$avatarPalette = ['#17b3c4','#2f9e72','#7a6bb0','#e8624a','#b07d11','#0b5e74','#c44a8a'];
function caColor(string $name, array $pal): string {
    $h = 0;
    foreach (str_split($name) as $c) $h = ($h * 31 + ord($c)) & 0x7fffffff;
    return $pal[$h % count($pal)];
}
?>

<div class="ca-wrap">

  <?php if (!empty($_GET['message'])): ?>
  <div class="ca-flash ok"><?= e($_GET['message']) ?></div>
  <?php endif; ?>
  <?php if (!empty($_GET['error'])): ?>
  <div class="ca-flash err"><?= e($_GET['error']) ?></div>
  <?php endif; ?>

  <!-- KPI -->
  <div class="ca-kpi-row">
    <div class="ca-kpi">
      <div class="ca-kpi-label">Conti aperti</div>
      <div class="ca-kpi-value"><?= $kpiCount ?></div>
    </div>
    <div class="ca-kpi">
      <div class="ca-kpi-label">Da incassare</div>
      <div class="ca-kpi-value <?= $kpiOpen > 0 ? 'warn' : 'good' ?>">€ <?= number_format($kpiOpen, 2, ',', '.') ?></div>
    </div>
    <div class="ca-kpi">
      <div class="ca-kpi-label">Incassato oggi</div>
      <div class="ca-kpi-value good">€ <?= number_format($kpiPaid, 2, ',', '.') ?></div>
    </div>
    <div class="ca-kpi">
      <div class="ca-kpi-label">Già saldati</div>
      <div class="ca-kpi-value"><?= $kpiSaldati ?></div>
    </div>
  </div>

  <!-- Main -->
  <div class="ca-main">

    <!-- LEFT -->
    <div class="ca-list-panel">
      <div class="ca-list-head">
        <div class="ca-list-title">Conti attivi</div>
        <input type="text" class="ca-search" id="caSearch" placeholder="Cerca cliente o card…">
      </div>
      <div class="ca-list" id="caList">
        <?php if (empty($openAccounts)): ?>
        <div class="ca-list-empty">Nessuna pendenza aperta</div>
        <?php else: foreach ($openAccounts as $acc):
          $initials = caInitials($acc['customer_name']);
          $color    = caColor($acc['customer_name'], $avatarPalette);
          $isActive = ($code === $acc['card_code']);
          $inside   = (bool)$acc['is_inside'];
          $entSt    = $acc['entry_status'] ?? null;
        ?>
        <a class="ca-account <?= $isActive ? 'active' : '' ?>"
           href="<?= url('/cashdesk?code=' . urlencode($acc['card_code'])) ?>"
           data-name="<?= e(strtolower($acc['customer_name'])) ?>"
           data-code="<?= e(strtolower($acc['card_code'])) ?>"
           data-full-name="<?= e($acc['customer_name']) ?>"
           data-card-code="<?= e($acc['card_code']) ?>"
           data-is-inside="<?= $inside ? '1' : '0' ?>"
           data-photo="<?= $acc['photo_path'] ? e(url('/assets/uploads/customers/' . basename($acc['photo_path']))) : '' ?>">
          <div class="ca-acc-avatar" style="background:<?= $color ?>">
            <?php if ($acc['photo_path']): ?>
            <img src="<?= url('/assets/uploads/customers/' . basename($acc['photo_path'])) ?>" alt="">
            <?php else: ?><?= $initials ?><?php endif; ?>
          </div>
          <div class="ca-acc-info">
            <div class="ca-acc-name">
              <?= e($acc['customer_name']) ?>
              <?php if ($inside): ?>
                <span style="font-size:8px;background:color-mix(in srgb,var(--good) 15%,transparent);color:var(--good);padding:1px 5px;border-radius:8px;font-weight:800;margin-left:4px">DENTRO</span>
              <?php else: ?>
                <span style="font-size:8px;background:color-mix(in srgb,var(--muted) 15%,transparent);color:var(--muted);padding:1px 5px;border-radius:8px;font-weight:800;margin-left:4px">FUORI</span>
              <?php endif; ?>
            </div>
            <div class="ca-acc-sub">
              <?= e($acc['card_code']) ?>
              <?php if ($acc['checkin_at']): ?> · <?= date('H:i', strtotime($acc['checkin_at'])) ?><?php endif; ?>
              <?php if ($acc['bar_tot'] > 0): ?> · 🍹<?= number_format($acc['bar_tot'],2,',','.') ?><?php endif; ?>
              <?php if ($acc['risto_tot'] > 0): ?> · 🍽<?= number_format($acc['risto_tot'],2,',','.') ?><?php endif; ?>
            </div>
          </div>
          <div class="ca-acc-bal open">
            € <?= number_format((float)$acc['open_charges'], 2, ',', '.') ?>
          </div>
        </a>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="ca-detail-panel">
      <?php if (!$card): ?>
      <div class="ca-detail-empty">
        <div class="ca-detail-empty-icon">💰</div>
        <div>Seleziona un conto dalla lista</div>
      </div>
      <?php else:
        $barTot = $ristoTot = $recepTot = $paidTot = 0;
        foreach ($movements as $m) {
            if ($m['movement_type'] === 'charge' && $m['status'] !== 'cancelled') {
                $dept = $m['department'] ?? '';
                if ($dept === 'bar')            $barTot   += (float)$m['total_amount'];
                elseif ($dept === 'ristorante') $ristoTot += (float)$m['total_amount'];
                else                            $recepTot += (float)$m['total_amount'];
            }
            if (in_array($m['movement_type'], ['payment','refund'], true)) {
                $paidTot += (float)$m['total_amount'];
            }
        }
        $due = max(0, round((float)$card['balance'], 2));
        $initials = caInitials($card['customer_name']);
        $color    = caColor($card['customer_name'], $avatarPalette);
        $isInside = (bool)$card['is_inside'];
      ?>

      <!-- Header -->
      <div class="ca-det-head">
        <div class="ca-det-avatar" style="background:<?= $color ?>">
          <?php if ($card['photo_path'] ?? null): ?>
          <img src="<?= url('/assets/uploads/customers/' . basename($card['photo_path'])) ?>" alt="">
          <?php else: ?><?= $initials ?><?php endif; ?>
        </div>
        <div>
          <div class="ca-det-name">
            <?= e($card['customer_name']) ?>
            <span class="ca-status <?= $isInside ? 'dentro' : 'fuori' ?>"><?= $isInside ? 'Dentro' : 'Fuori' ?></span>
          </div>
          <div class="ca-det-meta">
            <?= e($card['card_type'] ?? '') ?><span class="ca-det-code"><?= e($card['card_code']) ?></span>
            <?php if ($card['phone'] ?? null): ?> · <?= e($card['phone']) ?><?php endif; ?>
          </div>
        </div>
      </div>

      <!-- KPI strip -->
      <div class="ca-det-kpi">
        <div class="ca-det-kpi-cell">
          <div class="ca-det-kpi-label">Bar</div>
          <div class="ca-det-kpi-value bar">€ <?= number_format($barTot, 2, ',', '.') ?></div>
        </div>
        <div class="ca-det-kpi-cell">
          <div class="ca-det-kpi-label">Ristorante</div>
          <div class="ca-det-kpi-value risto">€ <?= number_format($ristoTot, 2, ',', '.') ?></div>
        </div>
        <div class="ca-det-kpi-cell">
          <div class="ca-det-kpi-label">Acconti</div>
          <div class="ca-det-kpi-value paid">€ <?= number_format($paidTot, 2, ',', '.') ?></div>
        </div>
        <div class="ca-det-kpi-cell">
          <div class="ca-det-kpi-label">Da pagare</div>
          <div class="ca-det-kpi-value <?= $due > 0 ? 'due' : 'zero' ?>">
            <?= $due > 0 ? '€ ' . number_format($due, 2, ',', '.') : '✓ Saldato' ?>
          </div>
        </div>
      </div>

      <!-- Scrollable movements -->
      <div class="ca-det-scroll">
        <?php if (!empty($movements)): ?>
        <div class="ca-section-lbl">Movimenti</div>
        <table class="ca-mov-table">
          <?php foreach (array_reverse($movements) as $m):
            $cancelled = $m['status'] === 'cancelled';
            $isPay     = $m['movement_type'] === 'payment';
            $dept      = $m['department'] ?? '';
          ?>
          <tr style="<?= $cancelled ? 'opacity:.35;text-decoration:line-through' : '' ?>">
            <td style="color:var(--muted-2);width:34px;font-size:10px"><?= date('H:i', strtotime($m['created_at'])) ?></td>
            <td style="width:58px">
              <?php if ($isPay): ?>
              <span class="dept-chip pay">Pagam.</span>
              <?php elseif ($dept): ?>
              <span class="dept-chip <?= $dept === 'bar' ? 'bar' : 'risto' ?>"><?= $dept === 'bar' ? 'Bar' : 'Risto' ?></span>
              <?php endif; ?>
            </td>
            <td><?= e($m['description'] ?? '') ?></td>
            <td style="text-align:right;font-weight:700;color:<?= $isPay ? 'var(--good)' : 'var(--text)' ?>;white-space:nowrap">
              <?= $isPay ? '+' : '' ?>€ <?= number_format((float)$m['total_amount'], 2, ',', '.') ?>
            </td>
            <td style="width:52px;text-align:right;white-space:nowrap">
              <?php if (!$cancelled): ?>
              <?php if (!$isPay): ?>
              <button type="button" class="ca-mov-edit"
                      data-id="<?= $m['id'] ?>"
                      data-desc="<?= e($m['description'] ?? '') ?>"
                      data-amount="<?= (float)$m['total_amount'] ?>"
                      title="Modifica"
                      style="border:none;background:transparent;color:var(--muted-2);cursor:pointer;font-size:12px;padding:2px 3px">✏</button>
              <?php endif; ?>
              <button type="button" class="ca-mov-cancel"
                      data-id="<?= $m['id'] ?>"
                      data-desc="<?= e($m['description'] ?? ($isPay ? 'Pagamento' : '')) ?>"
                      data-amount="<?= number_format((float)$m['total_amount'],2,',','.') ?>"
                      title="Annulla"
                      style="border:none;background:transparent;color:var(--bad);cursor:pointer;font-size:13px;padding:2px 3px">✕</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
        <?php else: ?>
        <div style="text-align:center;padding:20px;color:var(--muted-2);font-size:12px">Nessun movimento</div>
        <?php endif; ?>
      </div>

      <!-- Fixed footer: payment form -->
      <div class="ca-det-foot">
        <form method="post" action="<?= url('/cashdesk') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="card_code" value="<?= e($card['card_code']) ?>">
          <div class="ca-pay-row">
            <div>
              <label class="ca-pay-label">Importo €</label>
              <input class="ca-pay-input" name="amount" type="number"
                     step="0.01" min="0.01" value="<?= e((string) max(0, $due)) ?>" required>
            </div>
            <div>
              <label class="ca-pay-label">Metodo</label>
              <select class="ca-pay-input" name="payment_method" required>
                <option>contanti</option><option>carta</option>
                <option>satispay</option><option>bonifico</option><option>altro</option>
              </select>
            </div>
          </div>
          <div class="ca-pay-actions">
            <button type="submit" class="ca-btn pay">💳 Registra pagamento</button>
            <?php if ($isInside): ?>
            <button type="button" class="ca-btn <?= $due > 0 ? 'checkout-warn' : 'checkout' ?>" id="caCheckoutBtn">
              <?= $due > 0 ? '⚠ Check-out' : '✓ Check-out' ?>
            </button>
            <?php endif; ?>
          </div>
        </form>
        <?php if ($isInside): ?>
        <form method="post" action="<?= url('/checkout') ?>" id="caCheckoutForm" style="display:none">
          <?= csrf_field() ?>
          <input type="hidden" name="card_code" value="<?= e($card['card_code']) ?>">
          <input type="hidden" name="force_checkout" id="caForceCheckout" value="0">
        </form>
        <?php endif; ?>
      </div>

      <?php endif; ?>
    </div>

  </div>
</div>

<script>
/* ── Account list click → CardVerify ───────────────────── */
document.querySelectorAll('.ca-account').forEach(function(el) {
  el.addEventListener('click', function(e) {
    e.preventDefault();
    var dest = el.href;
    var d = el.dataset;
    CardVerify.show({
      customer_name: d.fullName || d.name,
      card_code:     d.cardCode || d.code,
      photo_url:     d.photo || null,
      is_inside:     d.isInside === '1',
      card_type:     d.cardStatus || ''
    }, function() {
      window.location.href = dest;
    });
  });
});

/* ── Search filter ──────────────────────────────────────── */
(function() {
  var inp = document.getElementById('caSearch');
  if (!inp) return;
  inp.addEventListener('input', function() {
    var q = inp.value.toLowerCase().trim();
    document.querySelectorAll('#caList .ca-account').forEach(function(el) {
      var match = !q || el.dataset.name.includes(q) || el.dataset.code.includes(q);
      el.style.display = match ? '' : 'none';
    });
  });
}());

window.addEventListener('pagehide', function() {
  document.body.classList.remove('ca-active');
});

/* ── Check-out button ───────────────────────────────────── */
var coBtn  = document.getElementById('caCheckoutBtn');
var coForm = document.getElementById('caCheckoutForm');
if (coBtn && coForm) {
  coBtn.addEventListener('click', function() {
    if (coBtn.classList.contains('checkout-warn')) {
      if (!confirm('Attenzione: il cliente ha un saldo aperto.\nIl debito rimarrà sulla card per recupero successivo.\nVuoi effettuare il check-out comunque?')) return;
      document.getElementById('caForceCheckout').value = '1';
    }
    coForm.submit();
  });
}

/* ── Cancel / Edit movement popups ─────────────────────── */
var CA_CANCEL_URL = '<?= url('/api/cashdesk/cancel-movement') ?>';
var CA_EDIT_URL   = '<?= url('/api/cashdesk/edit-movement') ?>';
var CA_CARD_CODE  = '<?= e($card['card_code'] ?? '') ?>';
var CA_CSRF       = '<?= csrf_token() ?>';

/* --- Cancel --- */
document.querySelectorAll('.ca-mov-cancel').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var id     = btn.getAttribute('data-id');
    var desc   = btn.getAttribute('data-desc') || 'questo movimento';
    var amount = btn.getAttribute('data-amount');
    if (!confirm('Annullare "' + desc + '" (€ ' + amount + ')?\nL\'operazione aggiornerà il saldo automaticamente.')) return;
    btn.disabled = true;
    fetch(CA_CANCEL_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'movement_id=' + encodeURIComponent(id) + '&_csrf=' + encodeURIComponent(CA_CSRF)
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.ok) {
        window.location.href = '<?= url('/cashdesk') ?>?code=' + encodeURIComponent(CA_CARD_CODE) + '&message=' + encodeURIComponent('Movimento annullato.');
      } else {
        alert('Errore: ' + (d.error || 'Operazione fallita'));
        btn.disabled = false;
      }
    })
    .catch(function(){ btn.disabled = false; alert('Errore di rete.'); });
  });
});

/* --- Edit amount --- */
document.querySelectorAll('.ca-mov-edit').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var id       = btn.getAttribute('data-id');
    var desc     = btn.getAttribute('data-desc') || 'movimento';
    var oldAmt   = btn.getAttribute('data-amount');
    var newAmtStr = prompt('Modifica importo per "' + desc + '"\nImporto attuale: € ' + parseFloat(oldAmt).toFixed(2).replace('.',',') + '\n\nNuovo importo €:', oldAmt);
    if (newAmtStr === null) return;
    var newAmt = parseFloat(newAmtStr.replace(',', '.'));
    if (isNaN(newAmt) || newAmt <= 0) { alert('Importo non valido.'); return; }
    btn.disabled = true;
    fetch(CA_EDIT_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'movement_id=' + encodeURIComponent(id) + '&amount=' + encodeURIComponent(newAmt.toFixed(2)) + '&_csrf=' + encodeURIComponent(CA_CSRF)
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.ok) {
        window.location.href = '<?= url('/cashdesk') ?>?code=' + encodeURIComponent(CA_CARD_CODE) + '&message=' + encodeURIComponent('Movimento aggiornato.');
      } else {
        alert('Errore: ' + (d.error || 'Operazione fallita'));
        btn.disabled = false;
      }
    })
    .catch(function(){ btn.disabled = false; alert('Errore di rete.'); });
  });
});
</script>
