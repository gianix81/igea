<style>
.tf-page { padding: 20px 24px; max-width: 760px; }
.tf-head { display: flex; align-items: baseline; gap: 10px; margin-bottom: 4px; }
.tf-title { font-family:'Poppins',sans-serif; font-size: 22px; font-weight: 800; color: var(--text); margin: 0; }
.tf-sub { font-size: 13px; color: var(--muted); margin-bottom: 18px; }
.tf-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden;
}
.tf-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 18px; border-bottom: 1px solid var(--border);
}
.tf-row:last-child { border-bottom: none; }
.tf-row-main { flex: 1; min-width: 0; }
.tf-row-label { font-weight: 700; color: var(--text); font-size: 14px; }
.tf-row-code { font-size: 11px; color: var(--muted-2); font-family: monospace; margin-top: 1px; }
.tf-row-updated { font-size: 11px; color: var(--muted-2); margin-top: 2px; }
.tf-price-wrap { display: flex; align-items: center; gap: 4px; }
.tf-price-wrap span { color: var(--muted); font-size: 13px; }
.tf-price-input {
  width: 90px; height: 34px; padding: 0 8px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 14px; font-weight: 700; font-family: inherit; outline: none;
}
.tf-price-input:focus { border-color: var(--accent); }
.tf-active-toggle { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--muted); white-space: nowrap; }
.tf-active-toggle input { width: 15px; height: 15px; accent-color: var(--accent); cursor: pointer; }
.tf-save-btn {
  height: 34px; padding: 0 14px; border-radius: 8px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 12.5px; font-weight: 700; cursor: pointer; font-family: inherit;
  transition: filter .15s; white-space: nowrap;
}
.tf-save-btn:hover { filter: brightness(1.08); }
.tf-save-btn:disabled { opacity: .5; cursor: default; }
.tf-note {
  margin-top: 14px; font-size: 12px; color: var(--muted); background: var(--surface);
  border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px;
}
#tf-toast {
  position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
  background: var(--good); color: #fff; padding: .55rem 1.4rem; border-radius: 10px;
  font-size: 13px; font-weight: 700; z-index: 9999; transition: transform .25s;
  pointer-events: none; white-space: nowrap;
}
#tf-toast.show { transform: translateX(-50%) translateY(0); }
#tf-toast.err { background: var(--bad); }
</style>

<div class="tf-page">
  <div class="tf-head"><h1 class="tf-title">Listino tariffe</h1></div>
  <div class="tf-sub">Lettini, ombrelloni e ingresso ridotto bimbi. Modificabile in qualsiasi momento: le nuove prenotazioni useranno subito il prezzo aggiornato.</div>

  <div class="tf-panel">
    <?php foreach ($rates as $r): ?>
    <div class="tf-row" data-id="<?= (int)$r['id'] ?>">
      <div class="tf-row-main">
        <div class="tf-row-label"><?= e($r['label']) ?></div>
        <div class="tf-row-code"><?= e($r['code']) ?></div>
        <div class="tf-row-updated">Aggiornato il <?= date('d/m/Y H:i', strtotime($r['updated_at'])) ?></div>
      </div>
      <label class="tf-active-toggle">
        <input type="checkbox" class="tf-active-input" <?= $r['active'] ? 'checked' : '' ?>>
        Attiva
      </label>
      <div class="tf-price-wrap">
        <span>€</span>
        <input type="number" class="tf-price-input" step="0.01" min="0" value="<?= e((string)$r['price']) ?>">
      </div>
      <button type="button" class="tf-save-btn" onclick="tfSave(this)">Salva</button>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="tf-note">
    L'ingresso ridotto bimbi si applica manualmente in Reception al check-in (pulsante "Ridotto bimbi").
    Le tariffe lettino feriale/festivo si applicano automaticamente in base al giorno della prenotazione.
  </div>
</div>

<div id="tf-toast"></div>

<script>
const TF_SAVE_URL = <?= json_encode(url('/api/tariffe/save.php'), JSON_THROW_ON_ERROR) ?>;
const TF_CSRF = <?= json_encode(csrf_token(), JSON_THROW_ON_ERROR) ?>;

function tfToast(msg, isErr) {
  const el = document.getElementById('tf-toast');
  el.textContent = msg;
  el.classList.toggle('err', !!isErr);
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2600);
}

function tfSave(btn) {
  const row   = btn.closest('.tf-row');
  const id    = row.dataset.id;
  const price = row.querySelector('.tf-price-input').value;
  const active = row.querySelector('.tf-active-input').checked ? '1' : '0';
  btn.disabled = true; btn.textContent = '…';
  fetch(TF_SAVE_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ id, price, active, _csrf: TF_CSRF }).toString()
  })
  .then(r => r.json())
  .then(d => {
    btn.disabled = false; btn.textContent = 'Salva';
    if (d.success) tfToast('Tariffa aggiornata');
    else tfToast(d.error || 'Errore', true);
  })
  .catch(() => {
    btn.disabled = false; btn.textContent = 'Salva';
    tfToast('Errore di rete', true);
  });
}
</script>
