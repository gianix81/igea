<?php $user = current_user(); ?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(app_config('name')) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
<?php if ($user): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-igea sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-semibold" href="<?= url('/') ?>">Gestionale Piscina Igea Club</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/customers') ?>">Clienti</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/cards') ?>">Card</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/entries') ?>">Reception</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/bar') ?>">Bar</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/cashdesk') ?>">Cassa</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/reservations') ?>">Prenotazioni</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/places') ?>">Posti</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/reports') ?>">Report</a></li>
      </ul>
      <span class="navbar-text me-3"><?= e($user['name']) ?> · <?= e($user['role']) ?></span>
      <a class="btn btn-outline-light btn-sm" href="<?= url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>
<?php endif; ?>
<main class="container-fluid py-4">
  <?= $content ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
