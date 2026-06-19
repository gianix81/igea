<!doctype html>
<html lang="it" <?php try { echo 'data-theme="' . (isset($_SESSION) ? (isset($_SESSION['igea_theme']) ? htmlspecialchars($_SESSION['igea_theme']) : 'dark') : 'dark') . '"'; } catch(Throwable $e) { echo 'data-theme="dark"'; } ?>>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registrazione Cliente — Igea Club</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%2317b3c4'/></svg>">
<script>
(function(){
  try { var t=localStorage.getItem('igea-theme')||'dark'; document.documentElement.setAttribute('data-theme',t); }
  catch(e){ document.documentElement.setAttribute('data-theme','dark'); }
})();
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
<style>
/* ── Registration Wizard ─────────────────────────────── */
.reg-page {
  min-height: 100dvh;
  display: flex; flex-direction: column;
  background: var(--bg);
}
.reg-header {
  position: sticky; top: 0; z-index: 50;
  background: var(--topbar);
  border-bottom: 1px solid var(--border);
  padding: 14px 24px;
  display: flex; align-items: center; gap: 20px;
}
.reg-brand {
  display: flex; align-items: center; gap: 10px;
  font-family: 'Bricolage Grotesque', sans-serif;
  font-weight: 700; font-size: 17px; color: var(--text); text-decoration: none;
}
.reg-brand .mark {
  width: 32px; height: 32px; border-radius: 50%;
  background: radial-gradient(circle at 35% 30%, #7fe7e0, var(--accent));
}
.reg-title {
  font-size: 14px; font-weight: 600; color: var(--muted);
  margin-left: 6px;
}

/* Progress stepper */
.reg-steps {
  display: flex; align-items: center; gap: 0;
  margin-left: auto;
}
.reg-step {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: var(--muted-2);
  padding: 6px 16px; border-radius: 24px;
  transition: all .2s;
}
.reg-step .step-num {
  width: 28px; height: 28px; border-radius: 50%;
  display: grid; place-items: center;
  font-size: 12px; font-weight: 800;
  border: 2px solid var(--muted-2); color: var(--muted-2);
  transition: all .2s;
}
.reg-step.active { color: var(--text); }
.reg-step.active .step-num { border-color: var(--accent); background: var(--accent); color: #fff; }
.reg-step.done .step-num { border-color: var(--good); background: var(--good); color: #fff; }
.reg-step.done { color: var(--good); }
.step-sep { width: 40px; height: 2px; background: var(--border); border-radius: 1px; }
.step-sep.done { background: var(--good); }

.reg-body { flex: 1; padding: 28px 24px; max-width: 1100px; margin: 0 auto; width: 100%; }

/* Step panes */
.step-pane { display: none; }
.step-pane.active { display: block; }

/* Section heading */
.reg-section-title {
  font-size: 11px; font-weight: 800; letter-spacing: .1em;
  text-transform: uppercase; color: var(--muted);
  margin-bottom: 14px; margin-top: 4px;
  display: flex; align-items: center; gap: 8px;
}
.reg-section-title::after { content:''; flex:1; height:1px; background: var(--border); }

/* Card containers */
.reg-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 24px;
  box-shadow: var(--shadow);
}

/* Bigger touch inputs */
.reg-input {
  height: 52px !important;
  font-size: 16px !important;
  border-radius: 10px !important;
  background: var(--surface-2) !important;
  border-color: var(--border) !important;
  color: var(--text) !important;
}
.reg-input:focus {
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px rgba(23,179,196,.15) !important;
  background: var(--surface) !important;
}
.reg-select { height: 52px !important; font-size: 16px !important; border-radius: 10px !important; }
.reg-label { font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }

/* Webcam */
.webcam-box {
  background: #000; border-radius: 14px; overflow: hidden;
  aspect-ratio: 4/3; position: relative;
  display: flex; align-items: center; justify-content: center;
}
.webcam-box video, .webcam-box canvas { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; }
.webcam-box canvas { display: none; }
.webcam-overlay {
  position: absolute; inset: 0; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 10px;
  background: rgba(0,0,0,.55); color: #fff; font-size: 14px;
  border-radius: 14px; text-align: center; padding: 16px;
}
.photo-ok-badge {
  position: absolute; top: 10px; right: 10px;
  background: var(--good); color: #fff; border-radius: 20px;
  font-size: 12px; font-weight: 700; padding: 4px 10px;
  display: none;
}

/* Signature pad */
.sig-wrap {
  border: 2px dashed var(--border); border-radius: 12px;
  background: var(--surface-2); position: relative;
  overflow: hidden; touch-action: none;
}
.sig-wrap canvas { display: block; width: 100%; cursor: crosshair; touch-action: none; }
.sig-placeholder {
  position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
  color: var(--muted-2); font-size: 14px; pointer-events: none; user-select: none;
}
.sig-has-content .sig-placeholder { display: none; }

/* Privacy text */
.privacy-text {
  background: var(--surface-2); border-radius: 10px;
  padding: 16px; font-size: 13px; line-height: 1.7;
  color: var(--muted); max-height: 220px; overflow-y: auto;
  border: 1px solid var(--border);
}

/* Step 3 preview card */
.customer-preview {
  display: flex; align-items: center; gap: 20px;
  padding: 20px; background: var(--surface-2);
  border-radius: 14px; border: 1px solid var(--border);
}
.customer-preview .preview-photo {
  width: 80px; height: 80px; border-radius: 50%;
  object-fit: cover; border: 3px solid var(--accent);
  flex-shrink: 0; background: var(--border);
}
.customer-preview .preview-photo-placeholder {
  width: 80px; height: 80px; border-radius: 50%;
  background: var(--surface-2); border: 3px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; flex-shrink: 0; color: var(--muted-2);
}

/* Card preview */
.card-visual {
  background: linear-gradient(135deg, var(--accent), #0e8a99);
  border-radius: 16px; padding: 24px 28px; color: #fff;
  position: relative; overflow: hidden;
}
.card-visual::before {
  content: ''; position: absolute; top: -30px; right: -30px;
  width: 120px; height: 120px; border-radius: 50%;
  background: rgba(255,255,255,.1);
}
.card-visual::after {
  content: ''; position: absolute; bottom: -40px; left: -20px;
  width: 150px; height: 150px; border-radius: 50%;
  background: rgba(255,255,255,.06);
}
.card-visual .card-code {
  font-family: 'Bricolage Grotesque', monospace;
  font-size: 26px; font-weight: 800; letter-spacing: 2px;
  margin-bottom: 6px;
}
.card-visual .card-label { font-size: 11px; opacity: .7; text-transform: uppercase; letter-spacing: .1em; }
.card-visual .card-name { font-size: 16px; font-weight: 600; margin-top: 12px; }
.nfc-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: rgba(255,255,255,.2); border-radius: 20px;
  padding: 4px 12px; font-size: 12px; font-weight: 700;
  margin-top: 10px; position: relative; z-index: 1;
}

/* Nav buttons */
.reg-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px;
  border-top: 1px solid var(--border);
  background: var(--topbar);
  position: sticky; bottom: 0; z-index: 40;
}
.btn-reg-back {
  height: 52px; padding: 0 28px; border-radius: 12px;
  font-size: 15px; font-weight: 700;
  background: var(--surface-2); border: 1px solid var(--border); color: var(--text);
  transition: all .15s; cursor: pointer;
}
.btn-reg-back:hover { background: var(--border); }
.btn-reg-next {
  height: 52px; padding: 0 36px; border-radius: 12px;
  font-size: 15px; font-weight: 700;
  background: var(--accent); border: none; color: #fff;
  transition: all .15s; cursor: pointer;
}
.btn-reg-next:hover { opacity: .88; }
.btn-reg-complete {
  height: 52px; padding: 0 36px; border-radius: 12px;
  font-size: 15px; font-weight: 700;
  background: var(--good); border: none; color: #fff;
  transition: all .15s; cursor: pointer;
}
.btn-reg-complete:hover { opacity: .88; }
.btn-reg-complete:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 768px) {
  .reg-header { padding: 12px 16px; flex-wrap: wrap; gap: 12px; }
  .reg-title { display: none; }
  .reg-steps { margin-left: 0; width: 100%; justify-content: center; order: 3; }
  .reg-body { padding: 16px 14px; }
  .reg-card { padding: 16px; }
  .reg-footer { padding: 14px 16px; }
}
</style>
</head>
<body>

<div class="reg-page">

  <!-- Header / stepper -->
  <header class="reg-header">
    <a class="reg-brand" href="<?= url('/customers') ?>">
      <span class="mark"></span>Igea Club
    </a>
    <span class="reg-title">Nuova Registrazione</span>

    <div class="reg-steps">
      <div class="reg-step active" id="stepper-1">
        <div class="step-num">1</div>
        <span class="d-none d-md-inline">Anagrafica</span>
      </div>
      <div class="step-sep" id="sep-1-2"></div>
      <div class="reg-step" id="stepper-2">
        <div class="step-num">2</div>
        <span class="d-none d-md-inline">Foto & Privacy</span>
      </div>
      <div class="step-sep" id="sep-2-3"></div>
      <div class="reg-step" id="stepper-3">
        <div class="step-num">3</div>
        <span class="d-none d-md-inline">Card</span>
      </div>
    </div>
  </header>

  <!-- Main body -->
  <div class="reg-body">

    <!-- ═══════════════════════════════════════════════
         STEP 1 — Anagrafica + Documento
    ════════════════════════════════════════════════ -->
    <div class="step-pane active" id="step-1">
      <div class="row g-4">

        <!-- Dati personali -->
        <div class="col-lg-7">
          <div class="reg-card h-100">
            <div class="reg-section-title">Dati personali</div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="reg-label">Nome *</label>
                <input class="form-control reg-input" id="f_first_name" placeholder="Mario" required>
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Cognome *</label>
                <input class="form-control reg-input" id="f_last_name" placeholder="Rossi" required>
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Data di nascita</label>
                <input class="form-control reg-input" type="date" id="f_birth_date">
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Codice fiscale</label>
                <input class="form-control reg-input" id="f_fiscal_code" placeholder="RSSMRA80A01H501Z" maxlength="16" style="text-transform:uppercase">
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Telefono *</label>
                <input class="form-control reg-input" type="tel" id="f_phone" placeholder="333 111 2222" required>
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Email</label>
                <input class="form-control reg-input" type="email" id="f_email" placeholder="mario@esempio.it">
              </div>
              <div class="col-12">
                <label class="reg-label">Indirizzo</label>
                <input class="form-control reg-input" id="f_address" placeholder="Via Roma 1, 00100 Roma">
              </div>
              <div class="col-12">
                <label class="reg-label">Note interne</label>
                <textarea class="form-control reg-input" id="f_notes" style="height:64px!important" placeholder="Allergie, preferenze, note operative…"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Documento -->
        <div class="col-lg-5">
          <div class="reg-card h-100">
            <div class="reg-section-title">Documento d'identità</div>
            <div class="row g-3">
              <div class="col-12">
                <label class="reg-label">Tipo documento</label>
                <select class="form-select reg-input reg-select" id="f_doc_type">
                  <option value="">— seleziona —</option>
                  <option value="carta_identita">Carta d'Identità (CIE)</option>
                  <option value="passaporto">Passaporto</option>
                  <option value="patente">Patente di Guida</option>
                  <option value="permesso_soggiorno">Permesso di Soggiorno</option>
                </select>
              </div>
              <div class="col-12">
                <label class="reg-label">Numero documento</label>
                <input class="form-control reg-input" id="f_doc_number" placeholder="AB1234567" maxlength="30" style="text-transform:uppercase">
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Data scadenza</label>
                <input class="form-control reg-input" type="date" id="f_doc_expiry">
              </div>
              <div class="col-sm-6">
                <label class="reg-label">Rilasciato da</label>
                <input class="form-control reg-input" id="f_doc_issuer" placeholder="Comune di Roma">
              </div>
            </div>

            <div class="mt-4 p-3 rounded-3" style="background:var(--surface-2);border:1px solid var(--border)">
              <div class="d-flex align-items-center gap-2 mb-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em">NFC Card</span>
              </div>
              <p style="font-size:12px;color:var(--muted);margin-bottom:10px">
                Inserisci l'UID del chip NFC se già disponibile. Può essere aggiunto in seguito.
              </p>
              <input class="form-control reg-input" id="f_nfc_uid" placeholder="Es. 04:A3:2B:11:5C:7D:80" style="font-family:monospace;font-size:14px!important;height:46px!important">
              <p style="font-size:11px;color:var(--muted-2);margin-top:6px;margin-bottom:0">
                🔒 Il chip NFC è crittografato e non modificabile da sistemi esterni.
              </p>
            </div>
          </div>
        </div>

      </div>

      <div id="step1_error" class="alert alert-danger mt-3 d-none"></div>
    </div>

    <!-- ═══════════════════════════════════════════════
         STEP 2 — Foto + Privacy + Firma
    ════════════════════════════════════════════════ -->
    <div class="step-pane" id="step-2">
      <div class="row g-4">

        <!-- Webcam -->
        <div class="col-lg-5">
          <div class="reg-card h-100">
            <div class="reg-section-title">Foto identificativa</div>
            <p class="text-muted small mb-3">La foto è usata dagli operatori per verificare l'identità al momento del pagamento.</p>

            <div class="webcam-box mb-3" id="webcam_box">
              <video id="webcam_video" autoplay muted playsinline></video>
              <canvas id="webcam_canvas"></canvas>
              <div class="webcam-overlay" id="webcam_overlay">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M20.94 11A8.994 8.994 0 0 0 12 3a8.994 8.994 0 0 0-8.94 8M12 21a8.994 8.994 0 0 0 8.94-8"/><path d="M5 3L3 5M19 3l2 2"/></svg>
                <div>Clicca per attivare la webcam</div>
              </div>
              <div class="photo-ok-badge" id="photo_ok_badge">✓ Foto acquisita</div>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-primary flex-fill" style="height:48px;font-size:15px;font-weight:700;border-radius:10px" id="btn_start_cam" onclick="startCamera()">
                📷 Attiva webcam
              </button>
              <button type="button" class="btn btn-outline-secondary flex-fill" style="height:48px;font-size:15px;font-weight:700;border-radius:10px" id="btn_capture" onclick="capturePhoto()" disabled>
                📸 Scatta
              </button>
            </div>
            <button type="button" class="btn btn-sm btn-link text-muted mt-2 w-100" id="btn_retake" onclick="retakePhoto()" style="display:none">
              ↺ Ripeti foto
            </button>

            <div id="photo_preview_wrap" class="mt-3" style="display:none">
              <img id="photo_preview" src="" class="rounded-3 w-100" style="border:3px solid var(--good)">
            </div>
          </div>
        </div>

        <!-- Privacy + Firma -->
        <div class="col-lg-7">
          <div class="reg-card h-100 d-flex flex-column">
            <div class="reg-section-title">Informativa privacy & Firma digitale</div>

            <div class="privacy-text mb-4">
              <strong>INFORMATIVA SUL TRATTAMENTO DEI DATI PERSONALI</strong><br>
              ai sensi dell'art. 13 del Regolamento UE 2016/679 (GDPR)<br><br>
              <strong>Titolare del trattamento:</strong> Igea Club, nella persona del legale rappresentante.<br><br>
              <strong>Dati trattati:</strong> nome, cognome, data di nascita, codice fiscale, documento d'identità, recapiti, fotografia.<br><br>
              <strong>Finalità:</strong> gestione degli accessi alla struttura, identificazione del titolare della card, comunicazioni operative e di servizio.<br><br>
              <strong>Base giuridica:</strong> esecuzione di un contratto; legittimo interesse per la sicurezza della struttura (art. 6, par. 1, lett. b e f GDPR).<br><br>
              <strong>Conservazione:</strong> i dati sono conservati per tutta la durata del rapporto e per i successivi 5 anni ai fini fiscali.<br><br>
              <strong>Diritti dell'interessato:</strong> diritto di accesso, rettifica, cancellazione, limitazione, portabilità e opposizione, esercitabili scrivendo al Titolare.<br><br>
              <strong>La foto</strong> è trattata esclusivamente per la verifica dell'identità del titolare al momento del pagamento e dell'accesso ai servizi.
            </div>

            <div class="reg-section-title">Firma del cliente</div>
            <p class="text-muted small mb-2">Il cliente firma qui sotto per accettare l'informativa privacy.</p>

            <div class="sig-wrap flex-fill" id="sig_wrap" style="min-height:160px">
              <canvas id="sig_canvas"></canvas>
              <div class="sig-placeholder">✍ Firma qui</div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSignature()">✕ Cancella firma</button>
              <span class="small text-muted" id="sig_status">Non firmato</span>
            </div>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" id="privacy_accept">
              <label class="form-check-label" for="privacy_accept" style="font-size:14px;font-weight:600">
                Il cliente ha letto e accetta l'informativa privacy
              </label>
            </div>
          </div>
        </div>

      </div>

      <div id="step2_error" class="alert alert-danger mt-3 d-none"></div>
    </div>

    <!-- ═══════════════════════════════════════════════
         STEP 3 — Card + Riepilogo
    ════════════════════════════════════════════════ -->
    <div class="step-pane" id="step-3">
      <div class="row g-4">

        <!-- Riepilogo cliente -->
        <div class="col-lg-5">
          <div class="reg-card">
            <div class="reg-section-title">Riepilogo cliente</div>
            <div class="customer-preview">
              <img id="preview_photo" src="" class="preview-photo" style="display:none">
              <div class="preview-photo-placeholder" id="preview_photo_placeholder">👤</div>
              <div>
                <div style="font-size:20px;font-weight:800;color:var(--text)" id="preview_name">—</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px" id="preview_birth"></div>
                <div style="font-size:13px;color:var(--muted)" id="preview_phone"></div>
                <div style="font-size:13px;color:var(--muted)" id="preview_doc"></div>
              </div>
            </div>
          </div>

          <div class="reg-card mt-4">
            <div class="reg-section-title">Tipo card</div>
            <div class="row g-3">
              <div class="col-12">
                <label class="reg-label">Tipo di card</label>
                <select class="form-select reg-input reg-select" id="f_card_type">
                  <option value="nominale">Nominale</option>
                  <option value="abbonamento">Abbonamento</option>
                  <option value="ospite">Ospite</option>
                  <option value="staff">Staff</option>
                </select>
              </div>
              <div class="col-12">
                <label class="reg-label">Note card</label>
                <input class="form-control reg-input" id="f_card_notes" placeholder="opzionale">
              </div>
            </div>
          </div>
        </div>

        <!-- Card visuale -->
        <div class="col-lg-7">
          <div class="reg-card">
            <div class="reg-section-title">Card assegnata</div>

            <div class="card-visual mb-4">
              <div class="card-label">Igea Club</div>
              <div class="card-code" id="preview_card_code">IGA-<?= date('Y') ?>-????</div>
              <div class="card-name" id="preview_card_name">—</div>
              <div class="nfc-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12h8M12 8v8"/></svg>
                NFC Encrypted
              </div>
            </div>

            <div class="p-3 rounded-3" style="background:var(--surface-2);border:1px solid var(--border)">
              <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">
                Informazioni sicurezza
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <div style="font-size:12px;color:var(--muted)">Codice generato</div>
                  <div style="font-size:13px;font-weight:600;color:var(--text)" id="info_code">automatico</div>
                </div>
                <div class="col-6">
                  <div style="font-size:12px;color:var(--muted)">Chip NFC</div>
                  <div style="font-size:13px;font-weight:600;color:var(--text)" id="info_nfc">—</div>
                </div>
                <div class="col-12">
                  <div style="font-size:11px;color:var(--muted-2);line-height:1.5;margin-top:4px">
                    🔒 Il chip NFC è protetto crittograficamente e non può essere duplicato o modificato da sistemi di terze parti. L'UID è univoco per ogni card.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div id="step3_error" class="alert alert-danger mt-3 d-none"></div>
    </div>

  </div><!-- /reg-body -->

  <!-- Footer navigation -->
  <footer class="reg-footer">
    <button type="button" class="btn-reg-back" id="btn_back" onclick="prevStep()" style="display:none">
      ← Indietro
    </button>
    <div id="footer_left_empty"></div>

    <div class="d-flex align-items-center gap-3">
      <a href="<?= url('/customers') ?>" class="text-muted small" style="text-decoration:none">Annulla</a>
      <button type="button" class="btn-reg-next" id="btn_next" onclick="nextStep()">
        Avanti →
      </button>
      <button type="button" class="btn-reg-complete" id="btn_complete" onclick="submitRegistration()" style="display:none" disabled>
        ✓ Completa registrazione
      </button>
    </div>
  </footer>

</div><!-- /reg-page -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF         = <?= json_encode(csrf_token()) ?>;
const REGISTER_URL = '<?= url('/api/customers/register.php') ?>';
const CUSTOMERS_URL = '<?= url('/customers') ?>';

var currentStep  = 1;
var photoData    = null;
var sigData      = null;
var sigDrawing   = false;
var sigHasContent = false;
var cameraStream = null;
var cardCode     = 'IGA-<?= date('Y') ?>-' + '????';

// ── STEP NAVIGATION ───────────────────────────────────────────────────────────
function showStep(n) {
  document.querySelectorAll('.step-pane').forEach(function(p) { p.classList.remove('active'); });
  document.getElementById('step-' + n).classList.add('active');

  for (var i = 1; i <= 3; i++) {
    var s = document.getElementById('stepper-' + i);
    s.classList.remove('active', 'done');
    if (i < n) s.classList.add('done');
    else if (i === n) s.classList.add('active');
  }
  ['1-2', '2-3'].forEach(function(pair) {
    var sep = document.getElementById('sep-' + pair);
    var done = parseInt(pair[0]) < n;
    sep.classList.toggle('done', done);
  });

  document.getElementById('btn_back').style.display = n > 1 ? '' : 'none';
  document.getElementById('footer_left_empty').style.display = n > 1 ? 'none' : '';
  document.getElementById('btn_next').style.display = n < 3 ? '' : 'none';
  document.getElementById('btn_complete').style.display = n === 3 ? '' : 'none';

  if (n === 3) populateStep3();
  currentStep = n;
}

function nextStep() {
  if (currentStep === 1 && !validateStep1()) return;
  if (currentStep === 2 && !validateStep2()) return;
  showStep(currentStep + 1);
  window.scrollTo(0, 0);
}
function prevStep() {
  showStep(currentStep - 1);
  window.scrollTo(0, 0);
}

function validateStep1() {
  var err = document.getElementById('step1_error');
  var fn = document.getElementById('f_first_name').value.trim();
  var ln = document.getElementById('f_last_name').value.trim();
  var ph = document.getElementById('f_phone').value.trim();
  if (!fn || !ln) { err.textContent = 'Nome e cognome sono obbligatori.'; err.classList.remove('d-none'); return false; }
  if (!ph)        { err.textContent = 'Il numero di telefono è obbligatorio.'; err.classList.remove('d-none'); return false; }
  err.classList.add('d-none'); return true;
}

function validateStep2() {
  var err = document.getElementById('step2_error');
  if (!document.getElementById('privacy_accept').checked) {
    err.textContent = 'Il cliente deve accettare l\'informativa privacy.';
    err.classList.remove('d-none'); return false;
  }
  err.classList.add('d-none'); return true;
}

// ── WEBCAM ────────────────────────────────────────────────────────────────────
function startCamera() {
  if (!navigator.mediaDevices) {
    alert('Webcam non disponibile in questo browser o contesto (serve HTTPS o localhost).');
    return;
  }
  navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
  .then(function(stream) {
    cameraStream = stream;
    var video = document.getElementById('webcam_video');
    video.srcObject = stream;
    video.play();
    document.getElementById('webcam_overlay').style.display = 'none';
    document.getElementById('btn_capture').disabled = false;
    document.getElementById('btn_start_cam').textContent = '✓ Webcam attiva';
    document.getElementById('btn_start_cam').disabled = true;
  }).catch(function(e) {
    alert('Impossibile accedere alla webcam: ' + e.message);
  });
}

function capturePhoto() {
  var video  = document.getElementById('webcam_video');
  var canvas = document.getElementById('webcam_canvas');
  canvas.width  = video.videoWidth  || 640;
  canvas.height = video.videoHeight || 480;
  canvas.getContext('2d').drawImage(video, 0, 0);
  photoData = canvas.toDataURL('image/jpeg', 0.85);

  // Show preview
  document.getElementById('photo_preview').src = photoData;
  document.getElementById('photo_preview_wrap').style.display = '';
  document.getElementById('photo_ok_badge').style.display = '';
  document.getElementById('btn_retake').style.display = '';
  document.getElementById('btn_capture').disabled = true;

  // Stop camera
  if (cameraStream) cameraStream.getTracks().forEach(function(t) { t.stop(); });
}

function retakePhoto() {
  photoData = null;
  document.getElementById('photo_preview_wrap').style.display = 'none';
  document.getElementById('photo_ok_badge').style.display = 'none';
  document.getElementById('btn_retake').style.display = 'none';
  document.getElementById('webcam_overlay').style.display = '';
  document.getElementById('btn_start_cam').disabled = false;
  document.getElementById('btn_start_cam').textContent = '📷 Attiva webcam';
  document.getElementById('btn_capture').disabled = true;
}

// ── SIGNATURE PAD ─────────────────────────────────────────────────────────────
(function() {
  var canvas, ctx, rect;

  function init() {
    canvas = document.getElementById('sig_canvas');
    var wrap = document.getElementById('sig_wrap');
    canvas.width  = wrap.offsetWidth  || 500;
    canvas.height = 180;
    ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#0f2730';
    ctx.lineWidth   = 2.5;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    canvas.addEventListener('pointerdown', function(e) {
      e.preventDefault();
      sigDrawing = true;
      rect = canvas.getBoundingClientRect();
      ctx.beginPath();
      ctx.moveTo((e.clientX - rect.left) * (canvas.width / rect.width),
                 (e.clientY - rect.top)  * (canvas.height / rect.height));
      canvas.setPointerCapture(e.pointerId);
    });
    canvas.addEventListener('pointermove', function(e) {
      if (!sigDrawing) return;
      e.preventDefault();
      ctx.lineTo((e.clientX - rect.left) * (canvas.width / rect.width),
                 (e.clientY - rect.top)  * (canvas.height / rect.height));
      ctx.stroke();
      if (!sigHasContent) {
        sigHasContent = true;
        document.getElementById('sig_wrap').classList.add('sig-has-content');
        document.getElementById('sig_status').textContent = '✓ Firmato';
        document.getElementById('sig_status').style.color = 'var(--good)';
      }
    });
    canvas.addEventListener('pointerup', function(e) {
      sigDrawing = false;
      sigData = canvas.toDataURL('image/png');
    });
  }

  // Init after DOM ready
  document.addEventListener('DOMContentLoaded', function() {
    // Delay to let layout settle
    setTimeout(init, 100);
    window.addEventListener('resize', function() {
      if (!sigHasContent) { setTimeout(init, 50); }
    });
  });
})();

window.clearSignature = function() {
  var canvas = document.getElementById('sig_canvas');
  var ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  sigHasContent = false; sigData = null;
  document.getElementById('sig_wrap').classList.remove('sig-has-content');
  document.getElementById('sig_status').textContent = 'Non firmato';
  document.getElementById('sig_status').style.color = '';
};

// ── STEP 3 POPULATE ───────────────────────────────────────────────────────────
function populateStep3() {
  var fn = document.getElementById('f_first_name').value.trim();
  var ln = document.getElementById('f_last_name').value.trim();
  var ph = document.getElementById('f_phone').value.trim();
  var bd = document.getElementById('f_birth_date').value;
  var dt = document.getElementById('f_doc_type');
  var dn = document.getElementById('f_doc_number').value.trim();
  var de = document.getElementById('f_doc_expiry').value;
  var nfc = document.getElementById('f_nfc_uid').value.trim();

  var fullName = ln + ' ' + fn;
  document.getElementById('preview_name').textContent    = fullName;
  document.getElementById('preview_card_name').textContent = fullName;
  document.getElementById('preview_phone').textContent   = ph ? '📞 ' + ph : '';
  document.getElementById('preview_birth').textContent   = bd ? '🎂 ' + formatDate(bd) : '';

  var docStr = '';
  if (dt.value) docStr = (dt.options[dt.selectedIndex].text) + (dn ? ' · ' + dn : '') + (de ? ' (scad. ' + formatDate(de) + ')' : '');
  document.getElementById('preview_doc').textContent = docStr ? '📄 ' + docStr : '';

  document.getElementById('info_nfc').textContent = nfc ? nfc : '— da assegnare';

  if (photoData) {
    document.getElementById('preview_photo').src = photoData;
    document.getElementById('preview_photo').style.display = '';
    document.getElementById('preview_photo_placeholder').style.display = 'none';
  }

  // Generate card code preview (actual is server-generated)
  document.getElementById('preview_card_code').textContent = 'IGA-<?= date('Y') ?>-XXXX';

  // Enable complete button
  document.getElementById('btn_complete').disabled = false;
}

function formatDate(d) {
  if (!d) return '';
  var p = d.split('-');
  return p[2] + '/' + p[1] + '/' + p[0];
}

// ── SUBMIT ────────────────────────────────────────────────────────────────────
function submitRegistration() {
  var btn = document.getElementById('btn_complete');
  var err = document.getElementById('step3_error');
  btn.disabled = true; btn.textContent = 'Salvataggio…';

  var body = new URLSearchParams({
    _csrf:          CSRF,
    first_name:     document.getElementById('f_first_name').value.trim(),
    last_name:      document.getElementById('f_last_name').value.trim(),
    phone:          document.getElementById('f_phone').value.trim(),
    email:          document.getElementById('f_email').value.trim(),
    fiscal_code:    document.getElementById('f_fiscal_code').value.trim(),
    birth_date:     document.getElementById('f_birth_date').value,
    address:        document.getElementById('f_address').value.trim(),
    notes:          document.getElementById('f_notes').value.trim(),
    doc_type:       document.getElementById('f_doc_type').value,
    doc_number:     document.getElementById('f_doc_number').value.trim(),
    doc_expiry:     document.getElementById('f_doc_expiry').value,
    doc_issuer:     document.getElementById('f_doc_issuer').value.trim(),
    card_type:      document.getElementById('f_card_type').value,
    card_notes:     document.getElementById('f_card_notes').value.trim(),
    nfc_uid:        document.getElementById('f_nfc_uid').value.trim(),
    photo_data:     photoData     || '',
    signature_data: sigData       || '',
  });

  fetch(REGISTER_URL, { method: 'POST', body: body })
  .then(function(r) { return r.json(); })
  .then(function(resp) {
    if (resp.success) {
      // Show success and redirect
      window.location.href = CUSTOMERS_URL + '?id=' + resp.customer_id + '&registered=1';
    } else {
      err.textContent = resp.error || 'Errore durante il salvataggio.';
      err.classList.remove('d-none');
      btn.disabled = false; btn.textContent = '✓ Completa registrazione';
    }
  }).catch(function() {
    err.textContent = 'Errore di rete. Riprovare.';
    err.classList.remove('d-none');
    btn.disabled = false; btn.textContent = '✓ Completa registrazione';
  });
}
</script>
</body>
</html>
