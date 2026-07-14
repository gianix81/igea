<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prenota la piscina — Igea Club</title>
<meta name="robots" content="noindex">
<link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
<style>
  body { background: var(--bg, #f4f7f6); min-height: 100vh; margin: 0; }
  .pb-wrap { max-width: 560px; margin: 0 auto; padding: 40px 20px 60px; }
  .pb-header { text-align: center; margin-bottom: 26px; }
  .pb-logo { font-family:'Poppins',sans-serif; font-size: 22px; font-weight: 800; color: var(--text); }
  .pb-title { font-family:'Poppins',sans-serif; font-size: 20px; font-weight: 700; color: var(--muted); margin-top: 4px; }
  .pb-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 16px; padding: 26px 24px; box-shadow: 0 4px 20px rgba(0,0,0,.06);
  }
  .pb-field { margin-bottom: 14px; }
  .pb-label { display:block; font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 5px; }
  .pb-input {
    width: 100%; box-sizing: border-box; padding: 11px 13px; border-radius: 10px;
    border: 1px solid var(--border); background: var(--surface-2); color: var(--text);
    font-size: 14px; font-family: inherit; outline: none; transition: border-color .15s;
  }
  .pb-input:focus { border-color: var(--accent); }
  .pb-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .pb-hint { font-size: 11px; color: var(--muted-2); margin-top: 4px; }
  .pb-consent { display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: var(--muted); margin: 16px 0; }
  .pb-consent input { margin-top: 3px; }
  .pb-submit {
    width: 100%; padding: 13px; border-radius: 11px; border: none;
    background: var(--accent); color: var(--accent-ink);
    font-size: 15px; font-weight: 800; cursor: pointer; font-family: inherit;
  }
  .pb-submit:hover { opacity: .9; }
  .pb-errors {
    background: color-mix(in srgb, var(--bad) 10%, transparent);
    border: 1px solid color-mix(in srgb, var(--bad) 30%, transparent);
    color: var(--bad); border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: 13px;
  }
  .pb-errors ul { margin: 4px 0 0; padding-left: 18px; }
  .pb-success { text-align: center; padding: 20px 6px; }
  .pb-success-icon { font-size: 44px; margin-bottom: 10px; }
  .pb-success-title { font-family:'Poppins',sans-serif; font-size: 19px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
  .pb-success-text { font-size: 14px; color: var(--muted); line-height: 1.5; }
  .pb-hp { position: absolute; left: -9999px; top: -9999px; }
  .pb-footer { text-align: center; margin-top: 20px; font-size: 11.5px; color: var(--muted-2); }
</style>
</head>
<body>
<div class="pb-wrap">
  <div class="pb-header">
    <div class="pb-logo">Igea Club</div>
    <div class="pb-title">Prenota la piscina</div>
  </div>

  <div class="pb-card">
    <?php if ($success): ?>
      <div class="pb-success">
        <div class="pb-success-icon">✅</div>
        <div class="pb-success-title">Richiesta inviata</div>
        <div class="pb-success-text">
          Grazie! Abbiamo ricevuto la tua richiesta di prenotazione.<br>
          Ti contatteremo al numero indicato per confermare disponibilità e dettagli.
        </div>
      </div>
    <?php else: ?>

      <?php if (!empty($errors)): ?>
      <div class="pb-errors">
        <strong>Controlla questi punti:</strong>
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>

      <form method="post" action="<?= url('/prenota') ?>" autocomplete="off">
        <?= csrf_field() ?>
        <input type="text" name="website" class="pb-hp" tabindex="-1" autocomplete="off">

        <div class="pb-row-2">
          <div class="pb-field">
            <label class="pb-label">Nome *</label>
            <input class="pb-input" type="text" name="first_name" maxlength="80" required
                   value="<?= e((string) request_input('first_name', '')) ?>">
          </div>
          <div class="pb-field">
            <label class="pb-label">Cognome *</label>
            <input class="pb-input" type="text" name="last_name" maxlength="80" required
                   value="<?= e((string) request_input('last_name', '')) ?>">
          </div>
        </div>

        <div class="pb-row-2">
          <div class="pb-field">
            <label class="pb-label">Telefono *</label>
            <input class="pb-input" type="tel" name="phone" maxlength="30" required
                   value="<?= e((string) request_input('phone', '')) ?>">
          </div>
          <div class="pb-field">
            <label class="pb-label">Email</label>
            <input class="pb-input" type="email" name="email" maxlength="120"
                   value="<?= e((string) request_input('email', '')) ?>">
          </div>
        </div>

        <div class="pb-row-2">
          <div class="pb-field">
            <label class="pb-label">Data *</label>
            <input class="pb-input" type="date" name="usage_date" required
                   min="<?= e(date('Y-m-d')) ?>" max="<?= e(date('Y-m-d', strtotime('+90 days'))) ?>"
                   value="<?= e((string) request_input('usage_date', '')) ?>">
          </div>
          <div class="pb-field">
            <label class="pb-label">Fascia oraria</label>
            <select class="pb-input" name="time_slot">
              <option value="intera giornata">Intera giornata</option>
              <option value="pomeriggio">Pomeriggio</option>
            </select>
          </div>
        </div>

        <div class="pb-field" style="max-width:160px">
          <label class="pb-label">Persone</label>
          <input class="pb-input" type="number" name="people_count" min="1" max="20"
                 value="<?= e((string) request_input('people_count', '1')) ?>">
        </div>

        <div class="pb-field">
          <label class="pb-label">Note (facoltativo)</label>
          <textarea class="pb-input" name="notes" rows="2" maxlength="300" style="resize:vertical"><?= e((string) request_input('notes', '')) ?></textarea>
          <div class="pb-hint">Questa è una richiesta: la confermiamo noi telefonicamente in base alla disponibilità.</div>
        </div>

        <label class="pb-consent">
          <input type="checkbox" name="privacy_consent" value="1" required>
          <span>Acconsento al trattamento dei miei dati personali per la gestione della richiesta di prenotazione.</span>
        </label>

        <button type="submit" class="pb-submit">Invia richiesta</button>
      </form>
    <?php endif; ?>
  </div>

  <div class="pb-footer">Igea Club — piscina, bar e ristorazione</div>
</div>
</body>
</html>
