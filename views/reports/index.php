<?php
$deptLabel = ['bar'=>'Bar','ristorante'=>'Ristorante','reception'=>'Reception','cassa'=>'Cassa','extra'=>'Extra'];
$deptColor = ['bar'=>'#17b3c4','ristorante'=>'#e8a020','reception'=>'#0b5e74','cassa'=>'#2f9e72','extra'=>'#7a6bb0'];
$pmLabel   = ['contanti'=>'Contanti','carta'=>'Carta/POS','bonifico'=>'Bonifico','altro'=>'Altro'];

$totalCharges = array_sum(array_column($deptCharges, 'total'));
$avgEntry     = $entriesKpi['cnt'] > 0 ? $paymentsTotal / $entriesKpi['cnt'] : 0;

// JSON for charts
$trendLabels   = json_encode(array_column($trendData, 'label'));
$trendEntries  = json_encode(array_column($trendData, 'entries'));
$trendPaid     = json_encode(array_map(fn($r) => round($r['paid'],2),    $trendData));
$trendCharges  = json_encode(array_map(fn($r) => round($r['charges'],2), $trendData));

$deptChartLabels = json_encode(array_map(fn($r) => $deptLabel[$r['department']] ?? $r['department'], $deptCharges));
$deptChartData   = json_encode(array_map(fn($r) => round((float)$r['total'],2), $deptCharges));
$deptChartColors = json_encode(array_map(fn($r) => $deptColor[$r['department']] ?? '#999', $deptCharges));

$prodLabels  = json_encode(array_column($topProducts, 'prod_name'));
$prodRevenue = json_encode(array_map(fn($r) => round((float)$r['revenue'],2), $topProducts));
$prodColors  = json_encode(array_map(fn($r) => $deptColor[$r['department']] ?? '#999', $topProducts));
?>
<style>
/* ── Report page ─────────────────────────────────────────── */
.rp-page { padding: 20px 24px; display: flex; flex-direction: column; gap: 20px; }

/* Period selector */
.rp-period {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  padding: 12px 16px; background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px;
}
.rp-period-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-right: 4px; }
.rp-quick { display: flex; gap: 4px; flex-wrap: wrap; }
.rp-q-btn {
  padding: 5px 12px; border-radius: 8px; border: 1px solid var(--border);
  background: var(--surface-2); color: var(--muted); font-size: 12px; font-weight: 700;
  cursor: pointer; font-family: inherit; transition: all .12s; white-space: nowrap;
}
.rp-q-btn:hover  { border-color: var(--accent); color: var(--accent); }
.rp-q-btn.active { background: var(--accent); border-color: var(--accent); color: var(--accent-ink); }
.rp-sep { color: var(--border); margin: 0 4px; }
.rp-date-pair { display: flex; align-items: center; gap: 6px; }
.rp-date-input {
  height: 32px; padding: 0 10px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 12px; font-family: inherit; outline: none;
}
.rp-date-input:focus { border-color: var(--accent); }
.rp-date-sep { color: var(--muted); font-size: 12px; }
.rp-go-btn {
  height: 32px; padding: 0 16px; border-radius: 8px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 12px; font-weight: 800; cursor: pointer; font-family: inherit;
  transition: filter .12s;
}
.rp-go-btn:hover { filter: brightness(1.08); }

.rp-period-info { margin-left: auto; font-size: 11px; color: var(--muted); }
.rp-period-info strong { color: var(--text); }

/* KPI tiles */
.rp-kpi-grid {
  display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;
}
@media(max-width:1100px){ .rp-kpi-grid{ grid-template-columns:repeat(3,1fr); } }
@media(max-width:650px) { .rp-kpi-grid{ grid-template-columns:repeat(2,1fr); } }

.rp-kpi {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; padding: 14px 16px;
  display: flex; flex-direction: column; gap: 4px;
}
.rp-kpi-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); }
.rp-kpi-value { font-family:'Poppins',sans-serif; font-size: 24px; font-weight: 800; color: var(--text); line-height: 1; }
.rp-kpi-value.accent { color: var(--accent); }
.rp-kpi-value.good   { color: var(--good); }
.rp-kpi-value.warn   { color: var(--warn); }
.rp-kpi-sub  { font-size: 10.5px; color: var(--muted); }

/* Charts row */
.rp-charts { display: grid; grid-template-columns: 1fr 300px; gap: 12px; }
@media(max-width:900px){ .rp-charts{ grid-template-columns: 1fr; } }

.rp-chart-box {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; padding: 16px 18px;
}
.rp-chart-title {
  font-family:'Poppins',sans-serif;
  font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 14px;
}
.rp-chart-canvas { width: 100% !important; }

/* Bottom tables grid */
.rp-tables { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
@media(max-width:1000px){ .rp-tables{ grid-template-columns: 1fr 1fr; } }
@media(max-width:650px) { .rp-tables{ grid-template-columns: 1fr; } }

/* Products chart box spans 2 cols */
.rp-prod-box {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; padding: 16px 18px; grid-column: span 2;
}
@media(max-width:1000px){ .rp-prod-box{ grid-column: span 1; } }

/* Generic table */
.rp-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 13px; overflow: hidden;
}
.rp-panel-hd {
  padding: 12px 14px; font-family:'Poppins',sans-serif;
  font-size: 13px; font-weight: 800; color: var(--text);
  border-bottom: 1px solid var(--border); background: var(--surface-2);
  display: flex; align-items: center; gap: 8px;
}
.rp-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.rp-table th { padding: 9px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); border-bottom: 1px solid var(--border); white-space: nowrap; }
.rp-table td { padding: 10px 12px; color: var(--text); border-bottom: 1px solid var(--border); }
.rp-table tbody tr:last-child td { border-bottom: none; }
.rp-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 4%, var(--surface)); }
.rp-table .num  { text-align: right; font-weight: 700; font-family:'Poppins',sans-serif; }
.rp-table .bold { font-weight: 700; }
.rp-table tfoot td { font-weight: 800; border-top: 2px solid var(--border); color: var(--accent); font-family:'Poppins',sans-serif; }

.dept-pill {
  display: inline-block; font-size: 9.5px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .05em;
  padding: 2px 7px; border-radius: 7px;
}
.rp-empty { text-align: center; padding: 28px; color: var(--muted-2); font-size: 13px; }

/* bar-inline per top products */
.bar-inline { display: flex; align-items: center; gap: 8px; }
.bar-track-sm { flex: 1; height: 5px; border-radius: 3px; background: var(--surface-2); overflow: hidden; max-width: 80px; }
.bar-fill-sm  { height: 100%; border-radius: 3px; }

.rp-date-detail { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; overflow: hidden; }
</style>

<div class="rp-page">

  <!-- Period selector -->
  <form method="get" action="<?= url('/reports') ?>" id="rpForm">
    <div class="rp-period">
      <span class="rp-period-label">Periodo</span>
      <div class="rp-quick">
        <?php
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $w0        = date('Y-m-d', strtotime('monday this week'));
        $m0        = date('Y-m-'.str_pad(1,2,'0',STR_PAD_LEFT));
        $m30       = date('Y-m-d', strtotime('-30 days'));
        $presets   = ['Oggi'=>[$today,$today],'Ieri'=>[$yesterday,$yesterday],'7 giorni'=>[$m30,$today],'Questa sett.'=>[$w0,$today],'Questo mese'=>[$m0,$today]];
        foreach ($presets as $lbl => [$f,$t]):
          $active = $fromDate===$f && $toDate===$t;
        ?>
        <button type="button" class="rp-q-btn <?= $active?'active':'' ?>"
                onclick="setRange('<?= $f ?>','<?= $t ?>')">
          <?= e($lbl) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <span class="rp-sep">|</span>
      <div class="rp-date-pair">
        <input type="date" class="rp-date-input" id="rpFrom" name="from" value="<?= e($fromDate) ?>">
        <span class="rp-date-sep">→</span>
        <input type="date" class="rp-date-input" id="rpTo"   name="to"   value="<?= e($toDate) ?>">
        <button type="submit" class="rp-go-btn">Aggiorna</button>
      </div>
      <span class="rp-period-info">
        <strong><?= $diffDays ?></strong> <?= $diffDays===1?'giorno':'giorni' ?> analizzati
      </span>
    </div>
  </form>

  <!-- KPI -->
  <div class="rp-kpi-grid">
    <div class="rp-kpi">
      <span class="rp-kpi-label">Ingressi</span>
      <span class="rp-kpi-value accent"><?= number_format((int)$entriesKpi['cnt']) ?></span>
      <span class="rp-kpi-sub"><?= number_format((int)$entriesKpi['people']) ?> persone</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Tariffe ingresso</span>
      <span class="rp-kpi-value">€&nbsp;<?= number_format((float)$entriesKpi['fee'],2,',','.') ?></span>
      <span class="rp-kpi-sub">Totale periodo</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Consumazioni</span>
      <span class="rp-kpi-value">€&nbsp;<?= number_format($totalCharges,2,',','.') ?></span>
      <span class="rp-kpi-sub">Bar + Ristorante</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Incassato</span>
      <span class="rp-kpi-value good">€&nbsp;<?= number_format($paymentsTotal,2,',','.') ?></span>
      <span class="rp-kpi-sub">Pagamenti registrati</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Media / ingresso</span>
      <span class="rp-kpi-value">€&nbsp;<?= number_format($avgEntry,2,',','.') ?></span>
      <span class="rp-kpi-sub">Incasso medio</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Saldi aperti</span>
      <span class="rp-kpi-value warn">€&nbsp;<?= number_format($cardBalance,2,',','.') ?></span>
      <span class="rp-kpi-sub"><?= $cardsOpen ?> card con saldo</span>
    </div>
  </div>

  <!-- Charts: trend + doughnut -->
  <div class="rp-charts">

    <div class="rp-chart-box">
      <div class="rp-chart-title">Andamento giornaliero</div>
      <?php if (empty($trendData)): ?>
        <div class="rp-empty">Periodo troppo lungo per il grafico giornaliero (max 93 giorni)</div>
      <?php else: ?>
        <canvas id="trendChart" class="rp-chart-canvas" height="200"></canvas>
      <?php endif; ?>
    </div>

    <div class="rp-chart-box">
      <div class="rp-chart-title">Incassi per reparto</div>
      <?php if (empty($deptCharges)): ?>
        <div class="rp-empty">Nessun dato</div>
      <?php else: ?>
        <canvas id="deptChart" class="rp-chart-canvas" height="200"></canvas>
        <div style="margin-top:12px;display:flex;flex-direction:column;gap:6px">
          <?php foreach ($deptCharges as $dc):
            $pct = $totalCharges > 0 ? ($dc['total'] / $totalCharges * 100) : 0;
            $col = $deptColor[$dc['department']] ?? '#999';
          ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:11.5px">
            <span style="width:10px;height:10px;border-radius:50%;background:<?= $col ?>;flex-shrink:0"></span>
            <span style="flex:1;color:var(--muted)"><?= e($deptLabel[$dc['department']] ?? $dc['department']) ?></span>
            <span style="font-weight:800;color:var(--text)">€&nbsp;<?= number_format((float)$dc['total'],2,',','.') ?></span>
            <span style="color:var(--muted-2);font-size:10px"><?= number_format($pct,1) ?>%</span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <!-- Bottom: top products + dept detail + payment methods -->
  <div class="rp-tables">

    <!-- Top prodotti (grafico orizzontale) -->
    <div class="rp-prod-box">
      <div class="rp-chart-title">Top prodotti per fatturato</div>
      <?php if (empty($topProducts)): ?>
        <div class="rp-empty">Nessuna consumazione nel periodo</div>
      <?php else: ?>
        <canvas id="prodChart" class="rp-chart-canvas" height="<?= min(count($topProducts) * 30 + 20, 350) ?>"></canvas>
      <?php endif; ?>
    </div>

    <!-- Dettaglio reparti -->
    <div class="rp-panel">
      <div class="rp-panel-hd">Consumazioni per reparto</div>
      <?php if (empty($deptCharges)): ?>
        <div class="rp-empty">Nessun dato</div>
      <?php else: ?>
        <table class="rp-table">
          <thead><tr><th>Reparto</th><th class="num">Ord.</th><th class="num">Totale</th></tr></thead>
          <tbody>
            <?php foreach ($deptCharges as $dc):
              $col = $deptColor[$dc['department']] ?? '#999';
            ?>
            <tr>
              <td><span class="dept-pill" style="background:<?= $col ?>22;color:<?= $col ?>"><?= e($deptLabel[$dc['department']] ?? $dc['department']) ?></span></td>
              <td class="num"><?= number_format((int)$dc['cnt']) ?></td>
              <td class="num">€&nbsp;<?= number_format((float)$dc['total'],2,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2">Totale</td>
              <td class="num">€&nbsp;<?= number_format($totalCharges,2,',','.') ?></td>
            </tr>
          </tfoot>
        </table>
      <?php endif; ?>
    </div>

    <!-- Metodi di pagamento -->
    <div class="rp-panel">
      <div class="rp-panel-hd">Metodi di pagamento</div>
      <?php if (empty($paymentMethods)): ?>
        <div class="rp-empty">Nessun pagamento</div>
      <?php else: ?>
        <table class="rp-table">
          <thead><tr><th>Metodo</th><th class="num">N°</th><th class="num">Totale</th></tr></thead>
          <tbody>
            <?php foreach ($paymentMethods as $pm): ?>
            <tr>
              <td class="bold"><?= e($pmLabel[$pm['payment_method']] ?? ucfirst($pm['payment_method'])) ?></td>
              <td class="num"><?= (int)$pm['cnt'] ?></td>
              <td class="num">€&nbsp;<?= number_format((float)$pm['total'],2,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2">Totale incassato</td>
              <td class="num">€&nbsp;<?= number_format($paymentsTotal,2,',','.') ?></td>
            </tr>
          </tfoot>
        </table>
      <?php endif; ?>
    </div>

  </div>

  <!-- Dettaglio ingressi per giorno -->
  <?php if (!empty($entryDetail)): ?>
  <div class="rp-date-detail">
    <div class="rp-panel-hd">Dettaglio ingressi giorno per giorno</div>
    <table class="rp-table">
      <thead>
        <tr>
          <th>Data</th>
          <th class="num">Ingressi</th>
          <th class="num">Persone</th>
          <th class="num">Tariffe</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $itDow = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
        foreach ($entryDetail as $row):
          $ts  = strtotime($row['entry_date']);
          $dow = $itDow[(int)date('w',$ts)];
        ?>
        <tr>
          <td><span style="color:var(--muted-2);font-size:10px;margin-right:5px"><?= $dow ?></span><?= date('d/m/Y',$ts) ?></td>
          <td class="num"><?= (int)$row['cnt'] ?></td>
          <td class="num"><?= (int)$row['people'] ?></td>
          <td class="num">€&nbsp;<?= number_format((float)$row['fees'],2,',','.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="1">Totale periodo</td>
          <td class="num"><?= number_format((int)$entriesKpi['cnt']) ?></td>
          <td class="num"><?= number_format((int)$entriesKpi['people']) ?></td>
          <td class="num">€&nbsp;<?= number_format((float)$entriesKpi['fee'],2,',','.') ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php endif; ?>

</div><!-- /rp-page -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ── Colori dal tema ─────────────────────────────────── */
var style   = getComputedStyle(document.documentElement);
var accent  = style.getPropertyValue('--accent').trim()  || '#17b3c4';
var good    = style.getPropertyValue('--good').trim()    || '#2f9e72';
var warn    = style.getPropertyValue('--warn').trim()    || '#e8a020';
var muted   = style.getPropertyValue('--muted').trim()   || '#6b828c';
var border  = style.getPropertyValue('--border').trim()  || '#dde8eb';
var textCol = style.getPropertyValue('--text').trim()    || '#0f2730';

Chart.defaults.color      = muted;
Chart.defaults.borderColor = border;
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size   = 11;

/* ── Trend giornaliero ───────────────────────────────── */
var trendEl = document.getElementById('trendChart');
if (trendEl) {
  new Chart(trendEl, {
    type: 'line',
    data: {
      labels: <?= $trendLabels ?>,
      datasets: [
        {
          label: 'Ingressi',
          data:  <?= $trendEntries ?>,
          borderColor: accent, backgroundColor: accent + '22',
          tension: .35, fill: true, pointRadius: 3, yAxisID: 'yLeft',
        },
        {
          label: 'Consumazioni €',
          data:  <?= $trendCharges ?>,
          borderColor: warn, backgroundColor: 'transparent',
          tension: .35, fill: false, pointRadius: 3, borderDash: [4,3], yAxisID: 'yRight',
        },
        {
          label: 'Incassato €',
          data:  <?= $trendPaid ?>,
          borderColor: good, backgroundColor: good + '22',
          tension: .35, fill: true, pointRadius: 3, yAxisID: 'yRight',
        },
      ]
    },
    options: {
      responsive: true, interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 12 } }, tooltip: { callbacks: {
        label: function(ctx) {
          if (ctx.datasetIndex === 0) return ' ' + ctx.raw + ' ingressi';
          return ' €' + ctx.raw.toFixed(2).replace('.',',');
        }
      }}},
      scales: {
        yLeft:  { type:'linear', position:'left',  grid: { color: border + '55' }, ticks: { precision: 0 } },
        yRight: { type:'linear', position:'right', grid: { drawOnChartArea: false }, ticks: { callback: function(v){ return '€'+v.toFixed(0); } } },
        x: { grid: { color: border + '33' } }
      }
    }
  });
}

/* ── Doughnut reparti ────────────────────────────────── */
var deptEl = document.getElementById('deptChart');
if (deptEl) {
  new Chart(deptEl, {
    type: 'doughnut',
    data: {
      labels: <?= $deptChartLabels ?>,
      datasets: [{ data: <?= $deptChartData ?>, backgroundColor: <?= $deptChartColors ?>, borderWidth: 2, borderColor: style.getPropertyValue('--surface').trim() || '#fff' }]
    },
    options: {
      responsive: true, cutout: '65%',
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: function(ctx){ return ' €' + ctx.raw.toFixed(2).replace('.',','); } } }
      }
    }
  });
}

/* ── Top prodotti (bar orizzontale) ──────────────────── */
var prodEl = document.getElementById('prodChart');
if (prodEl) {
  new Chart(prodEl, {
    type: 'bar',
    data: {
      labels: <?= $prodLabels ?>,
      datasets: [{
        label: 'Fatturato €',
        data:  <?= $prodRevenue ?>,
        backgroundColor: <?= $prodColors ?>,
        borderRadius: 5,
      }]
    },
    options: {
      indexAxis: 'y', responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: function(ctx){ return ' €' + ctx.raw.toFixed(2).replace('.',','); } } }
      },
      scales: {
        x: { grid: { color: border + '44' }, ticks: { callback: function(v){ return '€'+v; } } },
        y: { grid: { display: false } }
      }
    }
  });
}

/* ── Quick period buttons ────────────────────────────── */
function setRange(from, to) {
  document.getElementById('rpFrom').value = from;
  document.getElementById('rpTo').value   = to;
  document.getElementById('rpForm').submit();
}
</script>
