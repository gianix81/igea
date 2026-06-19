<?php
$deptLabels = ['bar'=>'Bar','ristorante'=>'Risto','reception'=>'Reception','extra'=>'Extra','piscina'=>'Piscina'];
$stColors   = ['dentro'=>'var(--good)','uscito'=>'var(--muted)','bloccato'=>'var(--bad)'];
$stLabels   = ['dentro'=>'Dentro','uscito'=>'Uscito','bloccato'=>'Bloccato'];
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
.sto-kpi-bar {
  display: flex; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;
}
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
.sto-table td { padding: 9px 12px; color: var(--text); white-space: nowrap; }
.sto-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 800;
}
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
    <div class="sto-title">🚪 Storico Ingressi</div>
    <form method="get" action="<?= url('/storico/ingressi') ?>" class="sto-filters">
      <span class="sto-filter-lbl">Dal</span>
      <input type="date" name="from" value="<?= e($from) ?>" class="sto-input">
      <span class="sto-filter-lbl">Al</span>
      <input type="date" name="to"   value="<?= e($to)   ?>" class="sto-input">
      <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cerca cliente o card…" class="sto-input" style="width:190px">
      <button type="submit" class="sto-btn">Filtra</button>
      <?php if ($q): ?>
      <a href="<?= url('/storico/ingressi?from='.urlencode($from).'&to='.urlencode($to)) ?>" style="font-size:12px;color:var(--muted);text-decoration:none">✕ Reset ricerca</a>
      <?php endif; ?>
    </form>
  </div>

  <?php
    $totEntries = count($entries);
    $totPeople  = array_sum(array_column($entries, 'people_count'));
    $totFee     = array_sum(array_column($entries, 'entry_fee'));
    $totPaid    = array_sum(array_column($entries, 'paid_amount'));
  ?>
  <div class="sto-kpi-bar">
    <div class="sto-kpi">
      <div class="sto-kpi-v"><?= $totEntries ?></div>
      <div class="sto-kpi-l">Ingressi</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v"><?= $totPeople ?></div>
      <div class="sto-kpi-l">Persone</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v">€ <?= number_format($totFee, 2, ',', '.') ?></div>
      <div class="sto-kpi-l">Tariffe</div>
    </div>
    <div class="sto-kpi">
      <div class="sto-kpi-v">€ <?= number_format($totPaid, 2, ',', '.') ?></div>
      <div class="sto-kpi-l">Incassato ingresso</div>
    </div>
  </div>

  <?php if (empty($entries)): ?>
  <div class="sto-empty">Nessun ingresso trovato per il periodo selezionato.</div>
  <?php else: ?>
  <div class="sto-table-wrap">
    <table class="sto-table">
      <thead>
        <tr>
          <th>Data</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Cliente</th>
          <th>Card</th>
          <th class="text-center">Persone</th>
          <th class="text-end">Tariffa</th>
          <th class="text-end">Pagato</th>
          <th>Stato</th>
          <th>Note</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($entries as $e): ?>
        <?php
          $st  = $e['status'];
          $clr = $stColors[$st] ?? 'var(--muted)';
          $lbl = $stLabels[$st] ?? ucfirst($st);
        ?>
        <tr>
          <td style="color:var(--muted-2)"><?= date('d/m/Y', strtotime($e['entry_date'])) ?></td>
          <td style="color:var(--muted-2)"><?= $e['checkin_at'] ? date('H:i', strtotime($e['checkin_at'])) : '—' ?></td>
          <td style="color:var(--muted-2)"><?= $e['checkout_at'] ? date('H:i', strtotime($e['checkout_at'])) : '—' ?></td>
          <td style="font-weight:600"><?= e($e['first_name'] . ' ' . $e['last_name']) ?></td>
          <td>
            <a class="sto-card-link" href="<?= url('/cashdesk?code=' . urlencode($e['card_code'])) ?>"><?= e($e['card_code']) ?></a>
          </td>
          <td style="text-align:center;color:var(--muted)"><?= (int)$e['people_count'] ?></td>
          <td style="text-align:right">€ <?= number_format((float)$e['entry_fee'], 2, ',', '.') ?></td>
          <td style="text-align:right">€ <?= number_format((float)$e['paid_amount'], 2, ',', '.') ?></td>
          <td>
            <span class="sto-badge" style="background:color-mix(in srgb,<?= $clr ?> 14%,transparent);color:<?= $clr ?>">
              <?= $lbl ?>
            </span>
          </td>
          <td style="color:var(--muted-2);max-width:180px;overflow:hidden;text-overflow:ellipsis"><?= e($e['notes'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (count($entries) >= 500): ?>
  <div style="padding:10px 14px;font-size:11px;color:var(--muted)">Mostrati i 500 risultati più recenti. Restringi il periodo per vedere tutto.</div>
  <?php endif; ?>
  <?php endif; ?>
</div>
