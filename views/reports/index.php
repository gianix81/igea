<?php
$deptLabel = ['bar'=>'Bar','ristorante'=>'Ristorante','reception'=>'Reception','cassa'=>'Cassa','extra'=>'Extra','piscina'=>'Piscina'];
$deptColor = ['bar'=>'#17b3c4','ristorante'=>'#e8a020','reception'=>'#0b5e74','cassa'=>'#2f9e72','extra'=>'#7a6bb0','piscina'=>'#0d9488'];
$pmLabel   = ['contanti'=>'Contanti','carta'=>'Carta/POS','bonifico'=>'Bonifico','altro'=>'Altro'];
$deptFilterLabel = ['bar'=>'Bar','ristorante'=>'Ristorante','piscina'=>'Piscina'];

// Totale di quanto mostrato nel grafico "Incassi per reparto" (già filtrato per reparto se attivo)
$totalCharges = array_sum(array_column($deptCharges, 'total'));
// "Consumazioni" (bar+ristorante) resta un dato a sé, indipendente dal filtro reparto:
// esclude sempre piscina/cassa/reception/extra, per non confondersi con l'incasso ingressi.
$consDeptCharges = array_filter($deptCharges, fn($r) => in_array($r['department'], ['bar', 'ristorante'], true));
$consumptionsTotal = array_sum(array_column($consDeptCharges, 'total'));
if ($deptFilter === 'bar' || $deptFilter === 'ristorante') {
    $consumptionsLabel = $deptFilterLabel[$deptFilter];
} else {
    $consumptionsLabel = 'Bar + Ristorante';
}
$showPoolTiles = ($deptFilter === '' || $deptFilter === 'piscina');
$showConsumptionsTile = ($deptFilter !== 'piscina');
$showDeptDoughnut = ($deptFilter === '');
$avgEntry     = $entriesKpi['cnt'] > 0 ? $paymentsTotal / $entriesKpi['cnt'] : 0;

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
/* ── Single-screen Report Dashboard ───────────────────── */
.rp-page {
  height: calc(100dvh - 60px);
  display: flex; flex-direction: column; gap: 8px;
  padding: 10px 16px; overflow: hidden; box-sizing: border-box;
}

/* Period bar */
.rp-period {
  display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;
  padding: 7px 12px; background: var(--surface); border: 1px solid var(--border);
  border-radius: 11px; flex-shrink: 0;
}
.rp-period-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-right: 2px; white-space: nowrap; }
.rp-quick { display: flex; gap: 3px; }
.rp-q-btn {
  padding: 4px 10px; border-radius: 7px; border: 1px solid var(--border);
  background: var(--surface-2); color: var(--muted); font-size: 11px; font-weight: 700;
  cursor: pointer; font-family: inherit; transition: all .12s; white-space: nowrap;
}
.rp-q-btn:hover  { border-color: var(--accent); color: var(--accent); }
.rp-q-btn.active { background: var(--accent); border-color: var(--accent); color: var(--accent-ink); }
.rp-sep { color: var(--border); margin: 0 2px; }
.rp-date-pair { display: flex; align-items: center; gap: 5px; }
.rp-date-input {
  height: 28px; padding: 0 8px; border-radius: 7px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 11px; font-family: inherit; outline: none;
}
.rp-date-input:focus { border-color: var(--accent); }
.rp-date-sep { color: var(--muted); font-size: 11px; }
.rp-go-btn {
  height: 28px; padding: 0 12px; border-radius: 7px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 11px; font-weight: 800; cursor: pointer; font-family: inherit;
}
.rp-go-btn:hover { filter: brightness(1.08); }
.rp-period-info { margin-left: auto; font-size: 10px; color: var(--muted); white-space: nowrap; }
.rp-period-info strong { color: var(--text); }

/* KPI row */
.rp-kpi-grid {
  display: grid; grid-template-columns: repeat(6, 1fr); gap: 6px; flex-shrink: 0;
}
@media(max-width:1000px){ .rp-kpi-grid{ grid-template-columns:repeat(3,1fr); } }
.rp-kpi {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 11px; padding: 8px 12px;
  display: flex; flex-direction: column; gap: 1px;
}
.rp-kpi-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); }
.rp-kpi-value { font-family:'Poppins',sans-serif; font-size: 20px; font-weight: 800; color: var(--text); line-height: 1.1; }
.rp-kpi-value.accent { color: var(--accent); }
.rp-kpi-value.good   { color: var(--good); }
.rp-kpi-value.warn   { color: var(--warn); }
.rp-kpi-sub { font-size: 9.5px; color: var(--muted-2); }

/* Main body */
.rp-body {
  flex: 1; min-height: 0;
  display: grid; grid-template-columns: 1fr 255px; gap: 8px;
}
@media(max-width:900px){ .rp-body{ grid-template-columns: 1fr; } }

/* Left column */
.rp-col-l { display: flex; flex-direction: column; gap: 8px; min-height: 0; }

/* Right column */
.rp-col-r { display: flex; flex-direction: column; gap: 8px; min-height: 0; }

/* Generic chart/panel box */
.rp-box {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 12px; overflow: hidden; flex-shrink: 0;
}
.rp-box.grow { flex: 1; min-height: 0; display: flex; flex-direction: column; }
.rp-box-hd {
  padding: 8px 13px; font-family:'Poppins',sans-serif;
  font-size: 12px; font-weight: 800; color: var(--text);
  border-bottom: 1px solid var(--border); background: var(--surface-2);
  flex-shrink: 0;
}
.rp-chart-wrap {
  flex: 1; min-height: 0; position: relative; padding: 8px 10px 10px;
}
.rp-chart-wrap canvas {
  position: absolute; top: 8px; left: 10px;
  width: calc(100% - 20px) !important;
  height: calc(100% - 18px) !important;
}

/* Dept section: doughnut + list side by side */
.rp-dept-inner { display: flex; align-items: center; gap: 10px; padding: 10px 12px; }
.rp-dept-donut { width: 84px; height: 84px; position: relative; flex-shrink: 0; }
.rp-dept-donut canvas { position: absolute; top:0; left:0; width:100%!important; height:100%!important; }
.rp-dept-list { flex: 1; display: flex; flex-direction: column; gap: 5px; min-width: 0; }
.rp-dept-row { display: flex; align-items: center; gap: 5px; font-size: 10.5px; min-width: 0; }
.rp-dept-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.rp-dept-name { flex: 1; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.rp-dept-val { font-weight: 800; color: var(--text); white-space: nowrap; }
.rp-dept-pct { color: var(--muted-2); font-size: 9px; white-space: nowrap; }

/* Tables */
.rp-table-wrap { overflow-y: auto; overflow-x: auto; flex: 1; min-height: 0; }
.rp-tbl { width: 100%; border-collapse: collapse; font-size: 11px; }
.rp-tbl th { padding: 5px 10px; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); border-bottom: 1px solid var(--border); white-space: nowrap; position: sticky; top: 0; background: var(--surface-2); }
.rp-tbl td { padding: 6px 10px; color: var(--text); border-bottom: 1px solid var(--border); }
.rp-tbl tbody tr:last-child td { border-bottom: none; }
.rp-tbl tbody tr:hover td { background: color-mix(in srgb,var(--accent) 4%,var(--surface)); }
.rp-tbl tfoot td { font-weight: 800; border-top: 2px solid var(--border); color: var(--accent); font-size: 10.5px; background: var(--surface); }
.rp-tbl .num { text-align: right; font-weight: 700; font-family:'Bricolage Grotesque',sans-serif; }
.rp-tbl .bold { font-weight: 700; }
.dept-pill {
  display: inline-block; font-size: 9px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .04em;
  padding: 1px 5px; border-radius: 5px;
}
.rp-empty { text-align: center; padding: 18px; color: var(--muted-2); font-size: 11.5px; }

/* Bar inline for products */
.bar-track-sm { flex: 1; height: 4px; border-radius: 2px; background: var(--surface-2); overflow: hidden; max-width: 60px; }
.bar-fill-sm  { height: 100%; border-radius: 2px; }

/* ── Mobile: pagina scrollabile, grafici con altezza propria invece di flex:1 su un contenitore a 0 ── */
@media (max-width: 900px) {
  .rp-page { height: auto; overflow: visible; }
  .rp-period { flex-wrap: wrap; row-gap: 6px; }
  .rp-period-info { margin-left: 0; width: 100%; }
  .rp-box.grow { flex: none !important; }
  .rp-chart-wrap { height: 260px; }
  .rp-table-wrap { max-height: 320px; }
}
@media (max-width: 640px) {
  .rp-kpi-grid { grid-template-columns: repeat(2,1fr); }
}
</style>

<div class="rp-page">

  <!-- ── Period + reparto selector ── -->
  <form method="get" action="<?= url('/reports') ?>" id="rpForm">
    <input type="hidden" id="rpDept" name="dept" value="<?= e($deptFilter) ?>">
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
      <span class="rp-sep">|</span>
      <span class="rp-period-label">Reparto</span>
      <div class="rp-quick">
        <button type="button" class="rp-q-btn <?= $deptFilter===''?'active':'' ?>" onclick="setDept('')">Tutti</button>
        <?php foreach ($deptFilterLabel as $dv => $dLbl): ?>
        <button type="button" class="rp-q-btn <?= $deptFilter===$dv?'active':'' ?>" onclick="setDept('<?= $dv ?>')"><?= e($dLbl) ?></button>
        <?php endforeach; ?>
      </div>
      <span class="rp-period-info">
        <strong><?= $diffDays ?></strong> <?= $diffDays===1?'giorno':'giorni' ?> analizzati
      </span>
    </div>
  </form>

  <!-- ── KPI tiles ── -->
  <div class="rp-kpi-grid">
    <?php if ($showPoolTiles): ?>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Ingressi</span>
      <span class="rp-kpi-value accent"><?= number_format((int)$entriesKpi['cnt']) ?></span>
      <span class="rp-kpi-sub"><?= number_format((int)$entriesKpi['people']) ?> persone</span>
    </div>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Tariffe ingresso</span>
      <span class="rp-kpi-value">€&thinsp;<?= number_format((float)$entriesKpi['fee'],2,',','.') ?></span>
      <span class="rp-kpi-sub">Totale periodo</span>
    </div>
    <?php endif; ?>
    <?php if ($showConsumptionsTile): ?>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Consumazioni</span>
      <span class="rp-kpi-value">€&thinsp;<?= number_format($consumptionsTotal,2,',','.') ?></span>
      <span class="rp-kpi-sub"><?= e($consumptionsLabel) ?></span>
    </div>
    <?php endif; ?>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Incassato</span>
      <span class="rp-kpi-value good">€&thinsp;<?= number_format($paymentsTotal,2,',','.') ?></span>
      <span class="rp-kpi-sub">Pagamenti registrati</span>
    </div>
    <?php if ($showPoolTiles): ?>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Media / ingresso</span>
      <span class="rp-kpi-value">€&thinsp;<?= number_format($avgEntry,2,',','.') ?></span>
      <span class="rp-kpi-sub">Incasso medio</span>
    </div>
    <?php endif; ?>
    <div class="rp-kpi">
      <span class="rp-kpi-label">Saldi aperti</span>
      <span class="rp-kpi-value warn">€&thinsp;<?= number_format($cardBalance,2,',','.') ?></span>
      <span class="rp-kpi-sub"><?= $cardsOpen ?> card con saldo</span>
    </div>
  </div>

  <!-- ── Main body ── -->
  <div class="rp-body">

    <!-- LEFT: trend + top prodotti -->
    <div class="rp-col-l">

      <!-- Trend giornaliero -->
      <div class="rp-box grow" style="flex:2">
        <div class="rp-box-hd">Andamento giornaliero</div>
        <?php if (empty($trendData)): ?>
          <div class="rp-empty">Periodo troppo lungo per il grafico (max 93 giorni)</div>
        <?php else: ?>
          <div class="rp-chart-wrap"><canvas id="trendChart"></canvas></div>
        <?php endif; ?>
      </div>

      <!-- Top prodotti -->
      <div class="rp-box grow" style="flex:1">
        <div class="rp-box-hd">Top prodotti per fatturato</div>
        <?php if (empty($topProducts)): ?>
          <div class="rp-empty">Nessuna consumazione nel periodo</div>
        <?php else: ?>
          <div class="rp-chart-wrap"><canvas id="prodChart"></canvas></div>
        <?php endif; ?>
      </div>

    </div>

    <!-- RIGHT: dept + payments + ingressi -->
    <div class="rp-col-r">

      <!-- Incassi per reparto -->
      <?php if ($showDeptDoughnut): ?>
      <div class="rp-box">
        <div class="rp-box-hd">Incassi per reparto</div>
        <?php if (empty($deptCharges)): ?>
          <div class="rp-empty">Nessun dato</div>
        <?php else: ?>
          <div class="rp-dept-inner">
            <div class="rp-dept-donut"><canvas id="deptChart"></canvas></div>
            <div class="rp-dept-list">
              <?php foreach ($deptCharges as $dc):
                $pct = $totalCharges > 0 ? ($dc['total'] / $totalCharges * 100) : 0;
                $col = $deptColor[$dc['department']] ?? '#999';
              ?>
              <div class="rp-dept-row">
                <span class="rp-dept-dot" style="background:<?= $col ?>"></span>
                <span class="rp-dept-name"><?= e($deptLabel[$dc['department']] ?? $dc['department']) ?></span>
                <span class="rp-dept-val">€&thinsp;<?= number_format((float)$dc['total'],2,',','.') ?></span>
                <span class="rp-dept-pct"><?= number_format($pct,0) ?>%</span>
              </div>
              <?php endforeach; ?>
              <div class="rp-dept-row" style="border-top:1px solid var(--border);padding-top:4px;margin-top:1px">
                <span class="rp-dept-dot" style="background:transparent"></span>
                <span class="rp-dept-name" style="color:var(--text);font-weight:700">Totale</span>
                <span class="rp-dept-val" style="color:var(--accent)">€&thinsp;<?= number_format($totalCharges,2,',','.') ?></span>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Metodi di pagamento -->
      <div class="rp-box">
        <div class="rp-box-hd">Metodi di pagamento</div>
        <?php if (empty($paymentMethods)): ?>
          <div class="rp-empty">Nessun pagamento</div>
        <?php else: ?>
          <table class="rp-tbl">
            <thead><tr><th>Metodo</th><th class="num">N°</th><th class="num">Totale</th></tr></thead>
            <tbody>
              <?php foreach ($paymentMethods as $pm): ?>
              <tr>
                <td class="bold"><?= e($pmLabel[$pm['payment_method']] ?? ucfirst($pm['payment_method'])) ?></td>
                <td class="num"><?= (int)$pm['cnt'] ?></td>
                <td class="num">€&thinsp;<?= number_format((float)$pm['total'],2,',','.') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot><tr>
              <td colspan="2">Totale</td>
              <td class="num">€&thinsp;<?= number_format($paymentsTotal,2,',','.') ?></td>
            </tr></tfoot>
          </table>
        <?php endif; ?>
      </div>

      <!-- Dettaglio ingressi per giorno -->
      <?php if ($showPoolTiles): ?>
      <div class="rp-box grow">
        <div class="rp-box-hd">Ingressi giorno per giorno</div>
        <?php if (empty($entryDetail)): ?>
          <div class="rp-empty">Nessun dato</div>
        <?php else: ?>
          <div class="rp-table-wrap">
            <table class="rp-tbl">
              <thead><tr>
                <th>Data</th>
                <th class="num">Ing.</th>
                <th class="num">Pers.</th>
                <th class="num">Tariffe</th>
              </tr></thead>
              <tbody>
                <?php
                $itDow = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
                foreach ($entryDetail as $row):
                  $ts  = strtotime($row['entry_date']);
                  $dow = $itDow[(int)date('w',$ts)];
                ?>
                <tr>
                  <td><span style="color:var(--muted-2);font-size:9px;margin-right:3px"><?= $dow ?></span><?= date('d/m',$ts) ?></td>
                  <td class="num"><?= (int)$row['cnt'] ?></td>
                  <td class="num"><?= (int)$row['people'] ?></td>
                  <td class="num">€&thinsp;<?= number_format((float)$row['fees'],2,',','.') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot><tr>
                <td>Totale</td>
                <td class="num"><?= number_format((int)$entriesKpi['cnt']) ?></td>
                <td class="num"><?= number_format((int)$entriesKpi['people']) ?></td>
                <td class="num">€&thinsp;<?= number_format((float)$entriesKpi['fee'],2,',','.') ?></td>
              </tr></tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div><!-- /rp-col-r -->
  </div><!-- /rp-body -->

</div><!-- /rp-page -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var style   = getComputedStyle(document.documentElement);
var accent  = style.getPropertyValue('--accent').trim()  || '#17b3c4';
var good    = style.getPropertyValue('--good').trim()    || '#2f9e72';
var warn    = style.getPropertyValue('--warn').trim()    || '#e8a020';
var muted   = style.getPropertyValue('--muted').trim()   || '#6b828c';
var border  = style.getPropertyValue('--border').trim()  || '#dde8eb';

Chart.defaults.color       = muted;
Chart.defaults.borderColor = border;
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size   = 10;

/* Trend */
var trendEl = document.getElementById('trendChart');
if (trendEl) {
  new Chart(trendEl, {
    type: 'line',
    data: {
      labels: <?= $trendLabels ?>,
      datasets: [
        { label:'Ingressi',       data:<?= $trendEntries ?>, borderColor:accent, backgroundColor:accent+'22', tension:.35, fill:true,  pointRadius:2, yAxisID:'yL' },
        { label:'Consumazioni €', data:<?= $trendCharges ?>, borderColor:warn,   backgroundColor:'transparent', tension:.35, fill:false, pointRadius:2, borderDash:[4,3], yAxisID:'yR' },
        { label:'Incassato €',    data:<?= $trendPaid ?>,    borderColor:good,   backgroundColor:good+'22',   tension:.35, fill:true,  pointRadius:2, yAxisID:'yR' },
      ]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      interaction:{ mode:'index', intersect:false },
      plugins:{
        legend:{ position:'top', labels:{ boxWidth:8, padding:10, font:{size:10} } },
        tooltip:{ callbacks:{ label:function(ctx){ return ctx.datasetIndex===0 ? ' '+ctx.raw+' ingressi' : ' €'+ctx.raw.toFixed(2).replace('.',','); } } }
      },
      scales:{
        yL:{ type:'linear', position:'left',  grid:{color:border+'44'}, ticks:{precision:0, font:{size:10}} },
        yR:{ type:'linear', position:'right', grid:{drawOnChartArea:false}, ticks:{callback:function(v){return '€'+v.toFixed(0);}, font:{size:10}} },
        x:{ grid:{color:border+'22'}, ticks:{font:{size:9}} }
      }
    }
  });
}

/* Doughnut reparti */
var deptEl = document.getElementById('deptChart');
if (deptEl) {
  new Chart(deptEl, {
    type:'doughnut',
    data:{ labels:<?= $deptChartLabels ?>, datasets:[{ data:<?= $deptChartData ?>, backgroundColor:<?= $deptChartColors ?>, borderWidth:2, borderColor:style.getPropertyValue('--surface').trim()||'#fff' }] },
    options:{
      responsive:true, maintainAspectRatio:false, cutout:'68%',
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:function(ctx){return ' €'+ctx.raw.toFixed(2).replace('.',',');}}} }
    }
  });
}

/* Top prodotti */
var prodEl = document.getElementById('prodChart');
if (prodEl) {
  new Chart(prodEl, {
    type:'bar',
    data:{ labels:<?= $prodLabels ?>, datasets:[{ label:'Fatturato €', data:<?= $prodRevenue ?>, backgroundColor:<?= $prodColors ?>, borderRadius:4 }] },
    options:{
      indexAxis:'y', responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:function(ctx){return ' €'+ctx.raw.toFixed(2).replace('.',',');}}} },
      scales:{
        x:{ grid:{color:border+'33'}, ticks:{callback:function(v){return '€'+v;}, font:{size:9}} },
        y:{ grid:{display:false}, ticks:{font:{size:9}} }
      }
    }
  });
}

function setRange(from, to) {
  document.getElementById('rpFrom').value = from;
  document.getElementById('rpTo').value   = to;
  document.getElementById('rpForm').submit();
}

function setDept(dept) {
  document.getElementById('rpDept').value = dept;
  document.getElementById('rpForm').submit();
}
</script>
