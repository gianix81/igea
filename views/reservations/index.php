<div class="row g-4">
  <div class="col-lg-4">
    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h1 class="h4">Nuova prenotazione</h1>
      <select class="form-select mb-2" name="customer_id" required><option value="">Cliente</option><?php foreach ($customers as $c): ?><option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select>
      <input class="form-control mb-2" name="usage_date" type="date" value="<?= e(date('Y-m-d')) ?>" required>
      <select class="form-select mb-2" name="time_slot"><option>intera giornata</option><option>mattina</option><option>pomeriggio</option></select>
      <input class="form-control mb-2" name="people_count" type="number" min="1" value="1">
      <select class="form-select mb-2" name="status"><option>confermata</option><option>in attesa</option><option>cancellata</option><option>no-show</option><option>completata</option></select>
      <div class="row g-2">
        <div class="col-4"><input class="form-control" name="deposit_amount" type="number" step="0.01" value="0" placeholder="Acconto"></div>
        <div class="col-4"><input class="form-control" name="total_amount" type="number" step="0.01" value="0" placeholder="Totale"></div>
        <div class="col-4"><input class="form-control" name="paid_amount" type="number" step="0.01" value="0" placeholder="Pagato"></div>
      </div>
      <select class="form-select mt-2" name="payment_method"><option value="">Metodo</option><option>contanti</option><option>carta</option><option>bonifico</option><option>satispay</option><option>altro</option></select>
      <textarea class="form-control mt-2" name="notes" placeholder="Note"></textarea>
      <button class="btn btn-primary w-100 mt-3">Crea prenotazione</button>
    </form>
  </div>
  <div class="col-lg-8">
    <div class="panel">
      <h2 class="h5">Prenotazioni</h2>
      <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Codice</th><th>Data uso</th><th>Cliente</th><th>Fascia</th><th>Persone</th><th>Totale</th><th>Stato</th></tr></thead><tbody><?php foreach ($reservations as $r): ?><tr><td><?= e($r['reservation_code']) ?></td><td><?= e($r['usage_date']) ?></td><td><?= e($r['customer_name']) ?></td><td><?= e($r['time_slot']) ?></td><td><?= (int) $r['people_count'] ?></td><td><?= money($r['total_amount']) ?></td><td><span class="badge text-bg-warning"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
    </div>
  </div>
</div>
