<div class="panel">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Report giornaliero</h1>
    <form class="d-flex gap-2" method="get"><input class="form-control" type="date" name="date" value="<?= e($date) ?>"><button class="btn btn-outline-primary">Filtra</button></form>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="metric"><span>Ingressi</span><strong><?= (int) $summary['entries'] ?></strong></div></div>
    <div class="col-md-3"><div class="metric"><span>Tariffe ingresso</span><strong><?= money($summary['entry_total']) ?></strong></div></div>
  </div>
  <div class="row g-4">
    <div class="col-lg-6">
      <h2 class="h5">Consumazioni</h2>
      <table class="table table-sm"><thead><tr><th>Reparto</th><th>Righe</th><th class="text-end">Totale</th></tr></thead><tbody><?php foreach ($charges as $c): ?><tr><td><?= e($c['department']) ?></td><td><?= (int) $c['rows_count'] ?></td><td class="text-end"><?= money($c['total']) ?></td></tr><?php endforeach; ?></tbody></table>
    </div>
    <div class="col-lg-6">
      <h2 class="h5">Pagamenti</h2>
      <table class="table table-sm"><thead><tr><th>Metodo</th><th class="text-end">Totale</th></tr></thead><tbody><?php foreach ($payments as $p): ?><tr><td><?= e($p['payment_method']) ?></td><td class="text-end"><?= money($p['total']) ?></td></tr><?php endforeach; ?></tbody></table>
    </div>
  </div>
</div>
