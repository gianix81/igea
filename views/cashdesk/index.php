<?php if (!empty($_GET['message'])): ?><div class="alert alert-info"><?= e($_GET['message']) ?></div><?php endif; ?>
<?php if (!empty($_GET['error'])): ?><div class="alert alert-danger"><?= e($_GET['error']) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-4">
    <form class="panel" method="get">
      <h1 class="h4">Cassa</h1>
      <input class="form-control form-control-lg" name="code" value="<?= e($code) ?>" placeholder="Codice card">
      <button class="btn btn-primary btn-lg w-100 mt-3">Apri conto</button>
    </form>
    <?php if ($card): ?>
      <form method="post" class="panel mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="card_code" value="<?= e($card['card_code']) ?>">
        <h2 class="h5">Pagamento</h2>
        <div class="balance <?= $card['balance'] > 0 ? 'open' : '' ?>"><?= money($card['balance']) ?></div>
        <input class="form-control mt-3" name="amount" type="number" step="0.01" min="0.01" value="<?= e((string) max(0, (float) $card['balance'])) ?>" required>
        <select class="form-select mt-2" name="payment_method" required><option>contanti</option><option>carta</option><option>bonifico</option><option>satispay</option><option>altro</option></select>
        <select class="form-select mt-2" name="reason"><option>saldo finale</option><option>consumazioni</option><option>ingresso</option><option>extra</option></select>
        <textarea class="form-control mt-2" name="notes" placeholder="Note"></textarea>
        <button class="btn btn-success btn-lg w-100 mt-3">Registra pagamento</button>
      </form>
      <form method="post" action="<?= url('/checkout') ?>" class="panel mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="card_code" value="<?= e($card['card_code']) ?>">
        <button class="btn btn-<?= $card['balance'] > 0 ? 'danger' : 'primary' ?> btn-lg w-100"><?= $card['balance'] > 0 ? 'Tenta uscita: saldo aperto' : 'Registra uscita' ?></button>
      </form>
    <?php endif; ?>
  </div>
  <div class="col-lg-8">
    <div class="panel">
      <h2 class="h5">Riepilogo conto</h2>
      <?php if ($card): ?>
        <p><strong><?= e($card['card_code']) ?></strong> · <?= e($card['customer_name']) ?> · <?= $card['is_inside'] ? 'DENTRO' : 'FUORI' ?></p>
        <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Data</th><th>Tipo</th><th>Descrizione</th><th class="text-end">Importo</th><th>Stato</th></tr></thead><tbody><?php foreach ($movements as $m): ?><tr><td><?= e($m['created_at']) ?></td><td><?= e($m['movement_type']) ?></td><td><?= e($m['description']) ?></td><td class="text-end"><?= money($m['total_amount']) ?></td><td><?= e($m['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>
      <?php else: ?>
        <div class="alert alert-info">Cerca una card per vedere saldo e movimenti.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
