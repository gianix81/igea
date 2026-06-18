<div class="row g-4">
  <div class="col-lg-4">
    <form class="panel" method="get">
      <h1 class="h4">Bar/Ristorazione</h1>
      <input class="form-control form-control-lg" name="code" value="<?= e($code) ?>" placeholder="Scansiona o cerca card" autofocus>
      <button class="btn btn-primary btn-lg w-100 mt-3">Carica card</button>
    </form>
    <?php if ($card): ?>
      <div class="panel mt-3">
        <div class="h5"><?= e($card['customer_name']) ?></div>
        <div class="d-flex gap-2 mb-2">
          <span class="badge text-bg-<?= $card['is_inside'] ? 'success' : 'danger' ?>"><?= $card['is_inside'] ? 'DENTRO' : 'FUORI' ?></span>
          <span class="badge text-bg-<?= $card['balance'] > 0 ? 'danger' : 'success' ?>"><?= $card['balance'] > 0 ? 'SALDO APERTO' : 'SALDATO' ?></span>
        </div>
        <div class="balance <?= $card['balance'] > 0 ? 'open' : '' ?>"><?= money($card['balance']) ?></div>
      </div>
    <?php elseif ($code !== ''): ?><div class="alert alert-warning mt-3">Card non trovata.</div><?php endif; ?>
  </div>
  <div class="col-lg-8">
    <div class="panel">
      <h2 class="h5">Prodotti</h2>
      <?php if (!$card): ?><div class="alert alert-info">Cerca una card prima di registrare consumazioni.</div><?php endif; ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <form method="post" class="product-button">
            <?= csrf_field() ?>
            <input type="hidden" name="card_code" value="<?= e($card['card_code'] ?? $code) ?>">
            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
            <strong><?= e($p['name']) ?></strong>
            <span><?= e($p['category_name']) ?> · <?= money($p['price']) ?></span>
            <input class="form-control form-control-sm mt-2" name="quantity" type="number" min="1" step="1" value="1" <?= $card ? '' : 'disabled' ?>>
            <button class="btn btn-outline-primary w-100 mt-2" <?= $card ? '' : 'disabled' ?>>Addebita</button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
