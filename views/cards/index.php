<div class="row g-4">
  <div class="col-xl-8">
    <div class="panel">
      <form class="d-flex gap-2 mb-3" method="get">
        <input class="form-control form-control-lg" name="code" value="<?= e($code) ?>" placeholder="Codice card">
        <button class="btn btn-primary btn-lg">Cerca</button>
      </form>
      <?php if ($card): ?>
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
          <h1 class="h4 mb-0"><?= e($card['card_code']) ?> · <?= e($card['customer_name']) ?></h1>
          <span class="badge text-bg-<?= $card['is_inside'] ? 'success' : 'secondary' ?>"><?= $card['is_inside'] ? 'DENTRO' : 'FUORI' ?></span>
          <span class="badge text-bg-<?= $card['balance'] > 0 ? 'danger' : 'success' ?>"><?= $card['balance'] > 0 ? 'SALDO APERTO' : 'SALDATO' ?></span>
        </div>
        <div class="balance <?= $card['balance'] > 0 ? 'open' : '' ?>"><?= money($card['balance']) ?></div>
        <div class="table-responsive mt-3">
          <table class="table table-sm">
            <thead><tr><th>Data</th><th>Tipo</th><th>Reparto</th><th>Descrizione</th><th class="text-end">Totale</th><th>Stato</th></tr></thead>
            <tbody><?php foreach ($movements as $m): ?><tr><td><?= e($m['created_at']) ?></td><td><?= e($m['movement_type']) ?></td><td><?= e($m['department']) ?></td><td><?= e($m['description']) ?></td><td class="text-end"><?= money($m['total_amount']) ?></td><td><?= e($m['status']) ?></td></tr><?php endforeach; ?></tbody>
          </table>
        </div>
      <?php elseif ($code !== ''): ?>
        <div class="alert alert-warning">Card non trovata.</div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-xl-4">
    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h2 class="h5">Nuova card</h2>
      <input class="form-control mb-2" name="card_code" placeholder="Codice card" required>
      <select class="form-select mb-2" name="customer_id" required><option value="">Cliente</option><?php foreach ($customers as $c): ?><option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select>
      <select class="form-select mb-2" name="card_type"><option>nominale</option><option>abbonamento</option><option>ospite</option><option>staff</option></select>
      <select class="form-select mb-2" name="status"><option>attiva</option><option>chiusa</option><option>bloccata</option><option>smarrita</option></select>
      <input class="form-control mb-2" name="expires_at" type="datetime-local">
      <textarea class="form-control" name="notes" placeholder="Note"></textarea>
      <button class="btn btn-primary w-100 mt-3">Crea card</button>
    </form>
  </div>
</div>
