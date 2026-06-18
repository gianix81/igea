<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Dashboard operativa</h1>
  <form class="quick-card" method="get" action="<?= url('/cards') ?>">
    <input class="form-control" name="code" placeholder="Cerca card">
  </form>
</div>
<div class="row g-3">
  <div class="col-6 col-xl-2"><div class="metric"><span>Dentro</span><strong><?= (int) $stats['inside'] ?></strong></div></div>
  <div class="col-6 col-xl-2"><div class="metric"><span>Card attive</span><strong><?= (int) $stats['active_cards'] ?></strong></div></div>
  <div class="col-6 col-xl-2"><div class="metric danger"><span>Saldi aperti</span><strong><?= (int) $stats['open_cards'] ?></strong></div></div>
  <div class="col-6 col-xl-2"><div class="metric"><span>Consumazioni oggi</span><strong><?= money($stats['charges']) ?></strong></div></div>
  <div class="col-6 col-xl-2"><div class="metric ok"><span>Incassato oggi</span><strong><?= money($stats['payments']) ?></strong></div></div>
  <div class="col-6 col-xl-2"><div class="metric warn"><span>Prenotazioni</span><strong><?= (int) $stats['reservations'] ?></strong></div></div>
</div>
<section class="panel mt-4">
  <h2 class="h5">Ultimi movimenti</h2>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr><th>Ora</th><th>Card</th><th>Reparto</th><th>Descrizione</th><th class="text-end">Totale</th><th>Stato</th></tr></thead>
      <tbody>
      <?php foreach ($latest as $row): ?>
        <tr>
          <td><?= e($row['created_at']) ?></td><td><?= e($row['card_code']) ?></td><td><?= e($row['department']) ?></td>
          <td><?= e($row['description']) ?></td><td class="text-end"><?= money($row['total_amount']) ?></td><td><span class="badge text-bg-secondary"><?= e($row['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
