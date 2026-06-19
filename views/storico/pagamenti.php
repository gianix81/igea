<?php
$deptLabels = ['bar'=>'Bar','ristorante'=>'Risto','reception'=>'Reception','extra'=>'Extra','piscina'=>'Piscina'];
$typeLabels = ['charge'=>'Addebito','payment'=>'Pagamento','refund'=>'Rimborso'];
$typeColors = ['charge'=>'var(--text)','payment'=>'var(--good)','refund'=>'var(--accent)'];
?>
<style>
.sto-wrap { padding: 22px 28px; max-width: 1280px; margin: 0 auto; }
.sto-head { display:flex; align-items:center; gap:14px; margin-bottom:20px; flex-wrap:wrap; }
.sto-title { font-family:'Bricolage Grotesque',sans-serif; font-size:20px; font-weight:800; color:var(--text); flex:1; }
.sto-filters { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.sto-filter-lbl { font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
.sto-input {
  border: 1px solid var(--border); border-radius: 7px; background: var(--surface-2);
  color: var(--text); font-size: 12.5px; padding: 6px 10px; font-family: inherit;
  outline: none; transition: border-color .15s;
}
.sto-input:focus { border-color: var(--accent); }
.sto-btn {
  background: var(--accent); color: var(--accent-ink); border: none; border-radius: 7px;
  padding: 7px 16px; font-size: 12.5px; font-weight: 700; cursor: pointer; font-family: inherit;
}
.sto-kpi-bar { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
.sto-kpi {
  background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px;
  padding: 12px 18px; min-width: 130px;
}
.sto-kpi-v { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
.sto-kpi-l { font-size: 10.5px; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-top: 3px; }
.sto-table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
.sto-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.sto-table thead th {
  background: var(--surface-2); color: var(--muted); font-size: 10px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .06em; padding: 9px 12px; white-space: nowrap;
  border-bottom: 1px solid var(--border); text-align: left;
}
.sto-table tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
.sto-table tbody tr:last-child { border-bottom: none; }
.sto-table tbody tr:hover { background: var(--surface-2); }
.sto-table tbody tr.cancelled { opacity: .45; text-decoration: line-through; }
.sto-table td { padding: 9px 12px; color: var(--text); white-space: nowrap; }
.sto-dept {
  display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 800;
  background: color-mix(in srgb, var(--accent) 12%, transparent); color: var(--accent);
}
.sto-dept.bar       { background:color-mix(in srgb,var(--c-bar)   12%,transparent);color:var(--c-bar); }
.sto-dept.ristorante{ background:color-mix(in srgb,var(--c-risto) 12%,transparent);color:var(--c-risto); }
.sto-dept.reception { background:color-mix(in srgb,var(--c-recep) 12%,transparent);color:var(--c-recep); }
.sto-dept.extra     { background:color-mix(in srgb,var(--c-extra) 12%,transparent);color:var(--c-extra); }
.sto-card-link {
  font-family: monospace; font-size: 11px; color: var(--accent);
  background: color-mix(in srgb, var(--accent) 10%, transparent);
  padding: 2px 7px; border-radius: 5px; text-decoration: none;
}
.sto-card-link:hover { text-decoration: underline; }
.sto-empty { padding: 48px; text-align: center; color: var(--muted); font-size: 13px; }
</style>

<div class="sto-wrap">
  <div class="sto-head">
    <div class="sto-title">💳 Storico Pagamenti e Movimenti</div>
    <form method="get" action="<?= url('/storico/pagamenti') ?>" class="sto-filters">
      <span class="sto-filter-lbl">Dal</span>
      <input type="date" name="from" value="<?= e($from) ?>" class="sto-input">
      <span class="sto-filter-lbl">Al</span>
      <input type="date" name="to"   value="<?= e($to)   ?>" class="sto-input">
      <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cerca cliente o card…" class="sto-input" style="width:190px">
      <button type="submit" class="sto-btn">Filtra</button>
      <?php if ($q): ?>
      <a href="<?= url('/storico/pagamenti?from='.urlencode($from).'&to='.urlencode($to)) ?>" style="font-size:12px;color:var(--muted);text-decoration:none">✕ Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <?php
    $charges  = array_filter($movements, fn($m) => $m['movement_type']==='charge'  && $m['status']!=='cancelled');
    $payments = array_filter($movements, fn($m) => $m['movement_type']==='payment' && $m['status']!=='cancelled');
    $refunds  = array_filter($movements, fn($m) => $m['movement_type']==='refund'  && $m['status']!=='cancelled');
    $totCharge  = array_sum(array_column(array_values($charges),  'total_amount'));
    $totPayment = array_sum(array_column(array_values($payments), 'total_amount'));
    $totRefund  = array_sum(array_column(array_values($refunds),  'total_amount'));
  ?>
  <div class="sto-kpi-bar">
    <div class="sto-kpi">
      <div class="sto-kpi-v"><?= count($charges) ?></div>
      <div class="sto-kpi-l">Addebiti</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v">€ <?= number_format($totCharge, 2, ',', '.') ?></div>
      <div class="sto-kpi-l">Tot. addebitato</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v" style="color:var(--good)"><?= count($payments) ?></div>
      <div class="sto-kpi-l">Pagamenti</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v" style="color:var(--good)">€ <?= number_format($totPayment, 2, ',', '.') ?></div>
      <div class="sto-kpi-l">Tot. incassato</div>
    </div>
    <?php if ($totRefund > 0): ?>
    <div class="sto-kpi">
      <div class="sto-kpi-v" style="color:var(--accent)">€ <?= number_format($totRefund, 2, ',', '.') ?></div>
      <div class="sto-kpi-l">Rimborsi</div>
    </div>
    <?php endif; ?>
    <div class="sto-kpi">
      <div class="sto-kpi-v" style="color:<?= ($totCharge - $totPayment) > 0.01 ? 'var(--warn)' : 'var(--good)' ?>">
        € <?= number_format(max(0, $totCharge - $totPayment), 2, ',', '.') ?>
      </div>
      <div class="sto-kpi-l">Da incassare</div>
    </div>
  </div>

  <?php if (empty($movements)): ?>
  <div class="sto-empty">Nessun movimento trovato per il periodo selezionato.</div>
  <?php else: ?>
  <div class="sto-table-wrap">
    <table class="sto-table">
      <thead>
        <tr>
          <th>Data / Ora</th>
          <th>Cliente</th>
          <th>Card</th>
          <th>Tipo</th>
          <th>Reparto</th>
          <th>Descrizione</th>
          <th class="text-center">Qtà</th>
          <th class="text-end">Importo</th>
          <th>Metodo</th>
          <th>Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($movements as $m): ?>
        <?php
          $isCancelled = $m['status'] === 'cancelled';
          $isPay  = in_array($m['movement_type'], ['payment','refund'], true);
          $typeClr = $typeColors[$m['movement_type']] ?? 'var(--text)';
          $typeLbl = $typeLabels[$m['movement_type']] ?? $m['movement_type'];
          $dept    = $m['department'] ?? '';
          $deptLbl = $deptLabels[$dept] ?? ucfirst($dept);
        ?>
        <tr class="<?= $isCancelled ? 'cancelled' : '' ?>">
          <td style="color:var(--muted-2);font-size:11px">
            <?= date('d/m/Y', strtotime($m['created_at'])) ?><br>
            <span style="font-size:10px"><?= date('H:i', strtotime($m['created_at'])) ?></span>
          </td>
          <td style="font-weight:600"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></td>
          <td>
            <a class="sto-card-link" href="<?= url('/cashdesk?code=' . urlencode($m['card_code'])) ?>"><?= e($m['card_code']) ?></a>
          </td>
          <td style="color:<?= $typeClr ?>;font-weight:700"><?= $typeLbl ?></td>
          <td>
            <?php if ($dept): ?>
            <span class="sto-dept <?= e($dept) ?>"><?= $deptLbl ?></span>
            <?php endif; ?>
          </td>
          <td style="color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis"><?= e($m['description'] ?? '') ?></td>
          <td style="text-align:center;color:var(--muted)"><?= $m['quantity'] !== null ? (int)$m['quantity'] : '—' ?></td>
          <td style="text-align:right;font-weight:700;color:<?= $isPay ? 'var(--good)' : 'var(--text)' ?>">
            <?= $isPay ? '+' : '' ?>€ <?= number_format((float)$m['total_amount'], 2, ',', '.') ?>
          </td>
          <td style="color:var(--muted)"><?= e($m['payment_method'] ?? '—') ?></td>
          <td style="color:<?= $isCancelled ? 'var(--bad)' : 'var(--muted)' ?>;font-size:11px">
            <?= $isCancelled ? 'Annullato' : 'OK' ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (count($movements) >= 1000): ?>
  <div style="padding:10px 14px;font-size:11px;color:var(--muted)">Mostrati i 1000 risultati più recenti. Restringi il periodo per vedere tutto.</div>
  <?php endif; ?>
  <?php endif; ?>
</div>
