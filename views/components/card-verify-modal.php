<!-- ═══════════════════════════════════════════════════════════════
     POPUP VERIFICA IDENTITÀ — includi una sola volta nel layout
     JS API:  CardVerify.show(cardData, onConfirm, onCancel?)
════════════════════════════════════════════════════════════════ -->
<style>
.cv-overlay {
  display: none; position: fixed; inset: 0; z-index: 1900;
  background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
  align-items: center; justify-content: center;
}
.cv-overlay.open { display: flex; }

.cv-modal {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 20px; width: 340px; max-width: 94vw;
  box-shadow: 0 24px 64px rgba(0,0,0,.35);
  animation: cv-pop .18s ease;
  overflow: hidden;
}
@keyframes cv-pop {
  from { transform: scale(.9); opacity: 0; }
  to   { transform: scale(1);  opacity: 1; }
}

.cv-header {
  background: color-mix(in srgb, var(--accent) 12%, var(--surface));
  border-bottom: 1px solid var(--border);
  padding: 12px 16px;
  display: flex; align-items: center; gap: 8px;
}
.cv-header-icon { font-size: 16px; }
.cv-header-title {
  font-family: 'Poppins', sans-serif;
  font-size: 13px; font-weight: 800; color: var(--text);
  text-transform: uppercase; letter-spacing: .06em;
}

.cv-body { padding: 24px 20px; text-align: center; }

/* Foto / Avatar */
.cv-photo-wrap {
  width: 110px; height: 110px; border-radius: 50%;
  margin: 0 auto 16px;
  border: 3px solid var(--border);
  overflow: hidden; background: var(--surface-2);
  display: flex; align-items: center; justify-content: center;
  position: relative;
}
.cv-photo { width: 100%; height: 100%; object-fit: cover; }
.cv-initials {
  font-family: 'Poppins', sans-serif;
  font-size: 36px; font-weight: 800;
  background: linear-gradient(135deg, var(--accent), #0b3e50);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}

.cv-name {
  font-family: 'Poppins', sans-serif;
  font-size: 22px; font-weight: 800; color: var(--text);
  margin-bottom: 6px; line-height: 1.15;
}
.cv-meta { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
.cv-code {
  display: inline-block;
  font-family: monospace; font-size: 13px;
  background: color-mix(in srgb, var(--accent) 12%, transparent);
  color: var(--accent); padding: 3px 11px; border-radius: 8px;
  margin-bottom: 10px; font-weight: 700;
}
.cv-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 800;
  margin-left: 6px;
}
.cv-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
.cv-badge.dentro  { background:color-mix(in srgb, var(--good) 15%, transparent); color:var(--good); }
.cv-badge.fuori   { background:color-mix(in srgb, var(--muted) 15%, transparent); color:var(--muted); }

.cv-warn {
  background: color-mix(in srgb, var(--warn) 12%, transparent);
  border: 1px solid color-mix(in srgb, var(--warn) 30%, transparent);
  border-radius: 9px; padding: 8px 12px; font-size: 11.5px;
  color: var(--warn); margin-top: 10px; text-align: left;
}

.cv-footer {
  display: flex; gap: 8px; padding: 14px 20px;
  border-top: 1px solid var(--border);
  background: var(--surface);
}
.cv-btn {
  flex: 1; padding: 11px; border-radius: 10px; border: none;
  font-size: 13px; font-weight: 800; cursor: pointer;
  font-family: inherit; transition: opacity .12s;
}
.cv-btn:hover { opacity: .85; }
.cv-btn.cancel { background: var(--surface-2); border: 1px solid var(--border); color: var(--text); }
.cv-btn.confirm { background: var(--accent); color: var(--accent-ink); }
</style>

<div class="cv-overlay" id="cv_overlay">
  <div class="cv-modal" role="dialog" aria-modal="true">
    <div class="cv-header">
      <span class="cv-header-icon">🪪</span>
      <span class="cv-header-title">Verifica identità</span>
    </div>
    <div class="cv-body">
      <div class="cv-photo-wrap">
        <img class="cv-photo" id="cv_photo" src="" alt="" style="display:none">
        <span class="cv-initials" id="cv_initials"></span>
      </div>
      <div class="cv-name" id="cv_name"></div>
      <div class="cv-meta" id="cv_meta"></div>
      <div>
        <span class="cv-code" id="cv_code"></span>
        <span class="cv-badge" id="cv_inside_badge"></span>
      </div>
      <div class="cv-warn" id="cv_inside_warn" style="display:none">
        ⚠ Cliente già risulta <strong>dentro</strong> — verifica prima di procedere.
      </div>
    </div>
    <div class="cv-footer">
      <button class="cv-btn cancel" id="cv_cancel">Annulla</button>
      <button class="cv-btn confirm" id="cv_confirm">✓ Confermo l'identità</button>
    </div>
  </div>
</div>

<script>
var CardVerify = (function () {
  var overlay    = document.getElementById('cv_overlay');
  var photoEl    = document.getElementById('cv_photo');
  var initialsEl = document.getElementById('cv_initials');
  var nameEl     = document.getElementById('cv_name');
  var metaEl     = document.getElementById('cv_meta');
  var codeEl     = document.getElementById('cv_code');
  var badgeEl    = document.getElementById('cv_inside_badge');
  var warnEl     = document.getElementById('cv_inside_warn');
  var confirmBtn = document.getElementById('cv_confirm');
  var cancelBtn  = document.getElementById('cv_cancel');

  var _onConfirm = null;
  var _onCancel  = null;

  function getInitials(name) {
    return name.trim().split(/\s+/).map(function(w){ return w[0] || ''; }).join('').toUpperCase().slice(0,2);
  }
  function titleCase(name) {
    return (name || '').toLowerCase().replace(/(^|[\s'’-])([\p{L}])/gu, function(_, sep, ch){ return sep + ch.toUpperCase(); });
  }

  function show(data, onConfirm, onCancel) {
    _onConfirm = onConfirm || null;
    _onCancel  = onCancel  || null;

    /* Photo or initials */
    if (data.photo_url) {
      photoEl.src = data.photo_url;
      photoEl.style.display = '';
      initialsEl.style.display = 'none';
    } else {
      photoEl.style.display = 'none';
      initialsEl.style.display = '';
      initialsEl.textContent = getInitials(data.customer_name || '');
    }

    nameEl.textContent = titleCase(data.customer_name || '');
    metaEl.textContent = [data.card_type, data.phone].filter(Boolean).join(' · ');
    codeEl.textContent = data.card_code || '';

    if (data.is_inside) {
      badgeEl.className = 'cv-badge dentro';
      badgeEl.textContent = 'Dentro';
      warnEl.style.display = data._custom_warn ? '' : '';
      warnEl.textContent = data._custom_warn || '⚠ Cliente già risulta dentro — verifica prima di procedere.';
    } else {
      badgeEl.className = 'cv-badge fuori';
      badgeEl.textContent = 'Fuori';
      warnEl.style.display = data._custom_warn ? '' : 'none';
      if (data._custom_warn) warnEl.textContent = data._custom_warn;
    }

    overlay.classList.add('open');
    confirmBtn.focus();
  }

  function close() { overlay.classList.remove('open'); }

  confirmBtn.addEventListener('click', function () {
    close();
    if (_onConfirm) _onConfirm();
  });
  cancelBtn.addEventListener('click', function () {
    close();
    if (_onCancel) _onCancel();
  });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) { close(); if (_onCancel) _onCancel(); }
  });
  document.addEventListener('keydown', function (e) {
    if (overlay.classList.contains('open') && e.key === 'Escape') {
      close(); if (_onCancel) _onCancel();
    }
  });

  return { show: show, close: close };
}());
</script>
