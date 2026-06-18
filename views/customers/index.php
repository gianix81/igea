<div class="row g-4">
  <div class="col-lg-8">
    <div class="panel">
      <div class="d-flex justify-content-between mb-3">
        <h1 class="h4">Clienti</h1>
        <form class="d-flex gap-2" method="get"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Nome, telefono, email"><button class="btn btn-outline-primary">Cerca</button></form>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Cliente</th><th>Telefono</th><th>Email</th><th>Privacy</th><th>Stato</th></tr></thead>
          <tbody>
          <?php foreach ($customers as $c): ?>
            <tr><td><?= e($c['last_name'] . ' ' . $c['first_name']) ?></td><td><?= e($c['phone']) ?></td><td><?= e($c['email']) ?></td><td><?= $c['privacy_consent'] ? 'Si' : 'No' ?></td><td><span class="badge text-bg-info"><?= e($c['status']) ?></span></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h2 class="h5">Nuovo cliente</h2>
      <div class="row g-2">
        <div class="col-6"><input class="form-control" name="first_name" placeholder="Nome" required></div>
        <div class="col-6"><input class="form-control" name="last_name" placeholder="Cognome" required></div>
        <div class="col-6"><input class="form-control" name="phone" placeholder="Telefono"></div>
        <div class="col-6"><input class="form-control" name="email" type="email" placeholder="Email"></div>
        <div class="col-6"><input class="form-control" name="fiscal_code" placeholder="Codice fiscale"></div>
        <div class="col-6"><input class="form-control" name="birth_date" type="date"></div>
        <div class="col-12"><input class="form-control" name="address" placeholder="Indirizzo"></div>
        <div class="col-12"><textarea class="form-control" name="notes" placeholder="Note"></textarea></div>
        <div class="col-6"><select class="form-select" name="status"><option>attivo</option><option>sospeso</option><option>blacklist</option></select></div>
        <div class="col-6 form-check pt-2"><input class="form-check-input" type="checkbox" name="privacy_consent" id="privacy"><label class="form-check-label" for="privacy">Privacy</label></div>
      </div>
      <button class="btn btn-primary w-100 mt-3">Crea cliente</button>
    </form>
  </div>
</div>
