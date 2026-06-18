<div class="login-wrap">
  <form method="post" class="login-box">
    <?= csrf_field() ?>
    <h1>Igea Club Pool Manager</h1>
    <p class="text-muted">Accesso operatori</p>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <label class="form-label">Email</label>
    <input class="form-control form-control-lg" name="email" type="email" required autofocus>
    <label class="form-label mt-3">Password</label>
    <input class="form-control form-control-lg" name="password" type="password" required>
    <button class="btn btn-primary btn-lg w-100 mt-4">Entra</button>
    <div class="small text-muted mt-3">Demo: admin@igeaclub.it / IgeaDemo2026!</div>
  </form>
</div>
