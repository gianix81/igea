<style>
.ut-page { padding: 20px 24px; max-width: 820px; }
.ut-head { display: flex; align-items: center; gap: 12px; margin-bottom: 4px; }
.ut-title { font-family:'Poppins',sans-serif; font-size: 22px; font-weight: 800; color: var(--text); margin: 0; flex: 1; }
.ut-sub { font-size: 13px; color: var(--muted); margin-bottom: 18px; }
.ut-add-btn {
  display: inline-flex; align-items: center; gap: 6px;
  height: 36px; padding: 0 16px; border-radius: 9px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit;
}
.ut-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.ut-table { width: 100%; border-collapse: collapse; }
.ut-table th {
  padding: 10px 14px; font-size: 10.5px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .06em; color: var(--muted); border-bottom: 1px solid var(--border); background: var(--surface-2);
}
.ut-table td { padding: 11px 14px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); vertical-align: middle; }
.ut-table tbody tr:last-child td { border-bottom: none; }
.ut-table tbody tr { cursor: pointer; transition: background .1s; }
.ut-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 4%, var(--surface)); }
.ut-role-chip {
  display: inline-block; font-size: 10px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .05em; padding: 3px 9px; border-radius: 8px;
}
.ut-role-admin       { background: color-mix(in srgb, var(--bad) 15%, transparent);   color: var(--bad); }
.ut-role-gestore     { background: color-mix(in srgb, var(--accent) 15%, transparent); color: var(--accent); }
.ut-role-reception   { background: color-mix(in srgb, #0b5e74 15%, transparent); color: #0b5e74; }
.ut-role-bar         { background: color-mix(in srgb, #17b3c4 15%, transparent); color: #17b3c4; }
.ut-role-ristorazione{ background: color-mix(in srgb, #e8a020 18%, transparent); color: #7a5200; }
.ut-role-cassa       { background: color-mix(in srgb, var(--good) 15%, transparent);  color: var(--good); }
.ut-active-yes { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:var(--good); }
.ut-active-no  { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:var(--bad); }
.ut-active-yes::before, .ut-active-no::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
#ut-toast {
  position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
  background: var(--good); color: #fff; padding: .55rem 1.4rem; border-radius: 10px;
  font-size: 13px; font-weight: 700; z-index: 9999; transition: transform .25s; pointer-events: none; white-space: nowrap;
}
#ut-toast.show { transform: translateX(-50%) translateY(0); }
#ut-toast.err { background: var(--bad); }

@media (max-width: 560px) {
  .ut-page { padding: 14px 12px; }
  .ut-table th, .ut-table td { white-space: nowrap; }
}
</style>

<div class="ut-page">
  <div class="ut-head">
    <h1 class="ut-title">Utenti</h1>
    <button class="ut-add-btn" onclick="openUserModal()">+ Nuovo utente</button>
  </div>
  <div class="ut-sub">Il "gestore" ha accesso operativo pieno come l'amministratore, tranne qui e le eliminazioni permanenti.</div>

  <div class="ut-panel" style="overflow-x:auto">
    <table class="ut-table">
      <thead>
        <tr><th>Nome</th><th>Email</th><th>Ruolo</th><th>Stato</th></tr>
      </thead>
      <tbody>
        <?php foreach ($roleUsers as $u): ?>
        <tr onclick='editUser(<?= json_encode($u, JSON_THROW_ON_ERROR) ?>)'>
          <td style="font-weight:700"><?= e($u['name']) ?></td>
          <td style="color:var(--muted)"><?= e($u['email']) ?></td>
          <td><span class="ut-role-chip ut-role-<?= e($u['role']) ?>"><?= e(ucfirst($u['role'])) ?></span></td>
          <td>
            <?php if ($u['active']): ?>
              <span class="ut-active-yes">Attivo</span>
            <?php else: ?>
              <span class="ut-active-no">Disattivo</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal utente -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalTitle">Nuovo utente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="um_id">
        <div class="mb-3"><label class="form-label">Nome *</label>
          <input type="text" class="form-control" id="um_name" maxlength="120"></div>
        <div class="mb-3"><label class="form-label">Email *</label>
          <input type="email" class="form-control" id="um_email" maxlength="190"></div>
        <div class="mb-3"><label class="form-label">Ruolo *</label>
          <select class="form-select" id="um_role">
            <option value="admin">Admin</option>
            <option value="gestore">Gestore</option>
            <option value="reception">Reception</option>
            <option value="bar">Bar</option>
            <option value="ristorazione">Ristorazione</option>
            <option value="cassa">Cassa</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password <span id="um_pw_hint" class="text-muted small">(lascia vuoto per non cambiarla)</span></label>
          <input type="password" class="form-control" id="um_password" autocomplete="new-password" placeholder="Almeno 8 caratteri">
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="um_active">
          <label class="form-check-label" for="um_active">Attivo</label>
        </div>
        <div id="um_error" class="alert alert-danger mt-3 d-none" style="font-size:13px;border-radius:9px"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary ms-auto" onclick="saveUser()">Salva</button>
      </div>
    </div>
  </div>
</div>

<div id="ut-toast"></div>

<script>
const SAVE_USER_URL = <?= json_encode(url('/api/utenti/save.php'), JSON_THROW_ON_ERROR) ?>;
const UT_CSRF = <?= json_encode(csrf_token(), JSON_THROW_ON_ERROR) ?>;

function openUserModal(data) {
  document.getElementById('um_id').value       = data ? data.id : '';
  document.getElementById('um_name').value     = data ? data.name : '';
  document.getElementById('um_email').value    = data ? data.email : '';
  document.getElementById('um_role').value     = data ? data.role : 'reception';
  document.getElementById('um_active').checked = data ? !!parseInt(data.active) : true;
  document.getElementById('um_password').value = '';
  document.getElementById('um_pw_hint').style.display = data ? '' : 'none';
  document.getElementById('um_error').classList.add('d-none');
  document.getElementById('userModalTitle').textContent = data ? 'Modifica utente' : 'Nuovo utente';
  new bootstrap.Modal(document.getElementById('userModal')).show();
}
function editUser(data) { openUserModal(data); }

function saveUser() {
  const errEl = document.getElementById('um_error');
  const name  = document.getElementById('um_name').value.trim();
  const email = document.getElementById('um_email').value.trim();
  if (!name || !email) {
    errEl.textContent = 'Nome ed email sono obbligatori.'; errEl.classList.remove('d-none'); return;
  }
  fetch(SAVE_USER_URL, {
    method: 'POST',
    body: new URLSearchParams({
      _csrf: UT_CSRF,
      id:       document.getElementById('um_id').value,
      name, email,
      role:     document.getElementById('um_role').value,
      password: document.getElementById('um_password').value,
      active:   document.getElementById('um_active').checked ? '1' : '0',
    })
  }).then(r => r.json()).then(function (resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
      toast('Utente salvato');
      setTimeout(function () { location.reload(); }, 500);
    } else {
      errEl.textContent = resp.error || 'Errore sconosciuto.';
      errEl.classList.remove('d-none');
    }
  }).catch(function () {
    errEl.textContent = 'Errore di rete.';
    errEl.classList.remove('d-none');
  });
}

(function () {
  var _t;
  window.toast = function (msg, isErr) {
    var el = document.getElementById('ut-toast');
    el.textContent = msg;
    el.classList.toggle('err', !!isErr);
    el.classList.add('show');
    clearTimeout(_t);
    _t = setTimeout(function () { el.classList.remove('show'); }, 2800);
  };
}());
</script>
