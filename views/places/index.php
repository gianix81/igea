<div class="panel">
  <h1 class="h4">Mappa posti piscina</h1>
  <div class="places-grid">
    <?php foreach ($places as $p): ?>
      <div class="place <?= e($p['status']) ?>">
        <strong><?= e($p['code']) ?></strong>
        <span><?= e($p['area_name']) ?></span>
        <small><?= e($p['type']) ?> · <?= money($p['base_price']) ?></small>
        <em><?= e($p['status']) ?></em>
      </div>
    <?php endforeach; ?>
  </div>
</div>
