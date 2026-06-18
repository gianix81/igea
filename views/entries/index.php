<?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-4">
    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h1 class="h4">Nuovo ingresso</h1>
      <input class="form-control form-control-lg mb-2" name="card_code" placeholder="Codice card" required>
      <div class="row g-2">
        <div class="col-6"><input class="form-control" name="people_count" type="number" min="1" value="1" placeholder="Persone"></div>
        <div class="col-6"><input class="form-control" name="entry_fee" type="number" step="0.01" min="0" value="0" placeholder="Tariffa"></div>
        <div class="col-6"><input class="form-control" name="paid_amount" type="number" step="0.01" min="0" value="0" placeholder="Pagato"></div>
        <div class="col-6"><select class="form-select" name="payment_method"><option value="">Metodo</option><option>contanti</option><option>carta</option><option>bonifico</option><option>satispay</option><option>altro</option></select></div>
      </div>
      <label class="form-label mt-3">Lettini/posti disponibili</label>
      <select class="form-select" name="place_ids[]" multiple size="8">
        <?php foreach ($places as $p): ?><option value="<?= (int) $p['id'] ?>"><?= e($p['area_name'] . ' · ' . $p['code'] . ' · ' . $p['type']) ?></option><?php endforeach; ?>
      </select>
      <textarea class="form-control mt-2" name="notes" placeholder="Note"></textarea>
      <button class="btn btn-primary btn-lg w-100 mt-3">Registra ingresso</button>
    </form>
  </div>
  <div class="col-lg-8">
    <div class="panel">
      <h2 class="h5">Ingressi di oggi</h2>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Ora</th><th>Cliente</th><th>Card</th><th>Persone</th><th>Tariffa</th><th>Pagato</th><th>Stato</th></tr></thead>
          <tbody><?php foreach ($entries as $e): ?><tr><td><?= e($e['checkin_at']) ?></td><td><?= e($e['customer_name']) ?></td><td><a href="<?= url('/cards?code=' . urlencode($e['card_code'])) ?>"><?= e($e['card_code']) ?></a></td><td><?= (int) $e['people_count'] ?></td><td><?= money($e['entry_fee']) ?></td><td><?= money($e['paid_amount']) ?></td><td><span class="badge text-bg-<?= $e['status'] === 'dentro' ? 'success' : ($e['status'] === 'bloccato' ? 'danger' : 'secondary') ?>"><?= e(strtoupper($e['status'])) ?></span></td></tr><?php endforeach; ?></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
