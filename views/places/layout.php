<?php
/* Editor schema lettini — griglia drag-and-drop */

// Costruisce griglia [row][col] = place
$grid        = [];
$unpositioned = [];
$maxRow = 11;
$maxCol = 15;

foreach ($layoutPlaces as $p) {
    if ($p['pos_row'] !== null && $p['pos_col'] !== null) {
        $r = (int) $p['pos_row'];
        $c = (int) $p['pos_col'];
        $grid[$r][$c] = $p;
        $maxRow = max($maxRow, $r);
        $maxCol = max($maxCol, $c);
    } else {
        $unpositioned[] = $p;
    }
}

$N_ROWS = $maxRow + 1;
$N_COLS = $maxCol + 1;

$statusColor = [
    'libero'      => '#1a9e6c',
    'riservato'   => '#e8a020',
    'pagato'      => '#17b3c4',
    'manutenzione' => '#888',
    'bloccato'    => '#444',
];

// Colore stabile per area (stesso id → stesso colore ad ogni caricamento),
// usato per distinguere a colpo d'occhio le aree nella griglia.
$areaPalette = ['#7c5cff', '#e8547c', '#2f9e72', '#e8a020', '#17b3c4', '#c0392b', '#8e6b3f', '#5470c6'];
$areaColor   = [];
foreach ($poolAreas as $a) {
    $areaColor[(int) $a['id']] = $areaPalette[(int) $a['id'] % count($areaPalette)];
}
?>

<style>
:root {
  --lc: 60px;
  --lr: 36px;
  --lgap: 4px;
}
.le-page {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  background: var(--bg);
}
.le-topbar {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: 6px 14px;
  background: var(--nav-bg);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  flex-wrap: wrap;
}
.le-title {
  font-size: .85rem;
  font-weight: 700;
  color: var(--text);
  white-space: nowrap;
}
.le-back {
  font-size: .78rem;
  color: var(--accent);
  text-decoration: none;
  padding: 3px 8px;
  border: 1px solid var(--accent);
  border-radius: 4px;
}
.le-back:hover { background: var(--accent); color:#fff; }
.le-legend {
  display: flex;
  align-items: center;
  gap: .4rem;
  font-size: .72rem;
  color: var(--text-muted);
  flex-wrap: wrap;
}
.le-dot { display:inline-block; width:10px; height:10px; border-radius:2px; }
.le-legend-lbl { font-weight:700; color: var(--text); margin-right: 2px; }
.le-size-ctrl {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: auto;
}
.le-sz-lbl { font-size:.72rem; color: var(--text-muted); }
.le-sz-btn {
  background: var(--nav-bg);
  border: 1px solid var(--border);
  color: var(--text);
  width: 24px; height: 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: .8rem;
  line-height: 1;
}
.le-sz-btn:hover { background: var(--accent); color:#fff; border-color: var(--accent); }
.le-sz-val { font-size: .75rem; color: var(--text); min-width: 28px; text-align: center; }
.le-hint {
  font-size: .72rem;
  color: var(--text-muted);
  background: var(--card-bg);
  border: 1px solid var(--border);
  padding: 3px 8px;
  border-radius: 4px;
}

.le-body {
  flex: 1;
  overflow: auto;
  padding: 12px 16px;
}

.le-grid-wrap {
  display: inline-block;
  min-width: 100%;
}

.le-grid {
  display: grid;
  grid-template-columns: repeat(var(--grid-cols), var(--lc));
  grid-template-rows: repeat(var(--grid-rows), var(--lr));
  gap: var(--lgap);
}

.le-cell {
  width: var(--lc);
  height: var(--lr);
  border-radius: 4px;
  border: 1.5px dashed transparent;
  transition: border-color .15s, background .15s;
  position: relative;
}
.le-cell.empty {
  border-color: var(--border);
  background: var(--card-bg);
  opacity: .45;
}
.le-cell.empty.drag-over {
  border-color: var(--accent);
  background: color-mix(in srgb, var(--accent) 12%, transparent);
  opacity: 1;
}
.le-cell.empty { cursor: pointer; }
.le-cell.empty:hover {
  border-color: var(--accent);
  opacity: 1;
}
.le-cell.empty::after {
  content: '+';
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 700; color: var(--accent);
  opacity: 0;
}
.le-cell.empty:hover::after { opacity: .8; }
.le-cell.has-seat { border-color: transparent; }

.le-seat {
  width: 100%;
  height: 100%;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .68rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  color: #fff;
  cursor: grab;
  user-select: none;
  border: none;
  outline: none;
  transition: opacity .15s, box-shadow .15s;
  position: relative;
  overflow: hidden;
  white-space: nowrap;
}
.le-seat:active { cursor: grabbing; }
.le-seat.dragging { opacity: .35; }
.le-seat:hover { box-shadow: 0 2px 8px rgba(0,0,0,.35); z-index: 2; }
.le-seat .le-seat-lbl {
  pointer-events: none;
  font-size: .68rem;
  line-height: 1;
  text-align: center;
}
.le-del-btn, .le-edit-btn {
  position: absolute;
  top: -5px;
  width: 15px; height: 15px;
  border-radius: 50%;
  color: #fff;
  border: none;
  font-size: 9px;
  line-height: 1;
  cursor: pointer;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 10;
  padding: 0;
  font-weight: 700;
}
.le-del-btn  { right: -5px; background: #d9534f; }
.le-edit-btn { left: -5px; background: #2f6fd9; }
.le-seat:hover .le-del-btn, .le-seat:hover .le-edit-btn,
.le-unpos-seat:hover .le-del-btn, .le-unpos-seat:hover .le-edit-btn { display: flex; }

/* Unpositioned pool */
.le-unpos-zone {
  margin-top: 16px;
  padding: 10px 12px;
  background: var(--card-bg);
  border: 1.5px dashed var(--border);
  border-radius: 8px;
}
.le-unpos-title {
  font-size: .78rem;
  font-weight: 600;
  color: var(--text-muted);
  margin-bottom: 8px;
}
.le-unpos-seats {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.le-unpos-seat {
  width: var(--lc);
  height: var(--lr);
  border-radius: 4px;
  background: #1a9e6c;
  color: #fff;
  font-size: .68rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  border: none;
}

/* Modal aggiungi/modifica lettino */
.le-modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.45);
  display: flex; align-items: center; justify-content: center; z-index: 500;
}
.le-modal {
  background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
  width: 320px; max-width: 92vw; box-shadow: 0 12px 40px rgba(0,0,0,.3);
}
.le-modal-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px; border-bottom: 1px solid var(--border);
  font-weight: 700; font-size: .9rem; color: var(--text);
}
.le-modal-close { background: none; border: none; font-size: 18px; color: var(--muted-2); cursor: pointer; line-height: 1; }
.le-modal form { padding: 14px 16px; }
.le-modal-field { margin-bottom: 10px; }
.le-modal-field label { display: block; font-size: .72rem; font-weight: 700; color: var(--muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: .04em; }
.le-modal-field input, .le-modal-field select {
  width: 100%; padding: 7px 9px; border-radius: 7px; border: 1px solid var(--border);
  background: var(--surface-2); color: var(--text); font-size: .85rem; font-family: inherit; box-sizing: border-box;
}
.le-modal-err { color: var(--bad, #d9534f); font-size: .78rem; margin-bottom: 8px; }
.le-modal-actions { display: flex; gap: 8px; margin-top: 4px; }
.le-modal-actions button { flex: 1; padding: 8px; border-radius: 8px; border: none; font-weight: 700; font-size: .82rem; cursor: pointer; font-family: inherit; }
.le-modal-cancel { background: var(--surface-2); color: var(--text); }
.le-modal-save   { background: var(--accent); color: var(--accent-ink); }
.le-modal-delete { background: transparent; color: var(--bad, #d9534f); border: 1.5px solid var(--bad, #d9534f) !important; }

/* Toast feedback */
.le-toast {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%) translateY(60px);
  background: var(--accent);
  color: #fff;
  font-size: .8rem;
  padding: 6px 18px;
  border-radius: 20px;
  transition: transform .2s;
  pointer-events: none;
  z-index: 9999;
}
.le-toast.show { transform: translateX(-50%) translateY(0); }
.le-toast.err { background: var(--bad, #d9534f); }

/* Nota: il posizionamento drag&drop resta pensato per mouse — su touch i lettini
   restano visibili e selezionabili (elimina, dimensione) ma non trascinabili. */
@media (max-width: 860px) {
  .le-sz-btn { width: 40px; height: 40px; font-size: 1.1rem; }
  .le-del-btn { width: 26px; height: 26px; font-size: 15px; }
}
</style>

<div class="le-page">

  <div class="le-topbar">
    <a href="<?= url('/places?date=' . urlencode($date)) ?>" class="le-back">← Mappa</a>
    <span class="le-title">Editor Schema Lettini</span>

    <div class="le-legend">
      <span class="le-dot" style="background:#1a9e6c"></span>Libero
      <span class="le-dot" style="background:#e8a020"></span>Riservato
      <span class="le-dot" style="background:#17b3c4"></span>Pagato
      <span class="le-dot" style="background:#888"></span>Manut./Bloccato
    </div>

    <div class="le-legend le-legend-areas">
      <span class="le-legend-lbl">Aree:</span>
      <?php foreach ($poolAreas as $a): ?>
        <span class="le-dot" style="background:<?= $areaColor[(int) $a['id']] ?>"></span><?= e($a['name']) ?>
      <?php endforeach; ?>
    </div>

    <span class="le-hint">⟵ trascina per spostare · clicca un lettino per modificarlo · clicca una cella vuota per aggiungerne uno</span>

    <div class="le-size-ctrl">
      <span class="le-sz-lbl">Largh.</span>
      <button class="le-sz-btn" onclick="changeSize(-4)">−</button>
      <span class="le-sz-val" id="szVal">60</span>
      <button class="le-sz-btn" onclick="changeSize(4)">+</button>
    </div>
  </div>

  <div class="le-body">
    <div class="le-grid-wrap">

      <!-- Griglia principale -->
      <div class="le-grid" id="layoutGrid"
           style="--grid-cols:<?= $N_COLS ?>;--grid-rows:<?= $N_ROWS ?>">

        <?php for ($r = 0; $r < $N_ROWS; $r++): ?>
          <?php for ($c = 0; $c < $N_COLS; $c++): ?>
            <?php $seat = $grid[$r][$c] ?? null; ?>
            <div class="le-cell <?= $seat ? 'has-seat' : 'empty' ?>"
                 data-row="<?= $r ?>" data-col="<?= $c ?>"
                 <?= $seat ? '' : 'onclick="openAddModal(' . $r . ',' . $c . ')"' ?>>
              <?php if ($seat):
                $ds = $seat['day_status'] ?? 'libero';
                $bg = $statusColor[$ds] ?? '#1a9e6c';
                $ac = $areaColor[(int) $seat['area_id']] ?? '#666';
                $lbl = strlen($seat['code']) > 1 ? substr($seat['code'], 1) : $seat['code'];
              ?>
              <div class="le-seat"
                   draggable="true"
                   data-id="<?= (int)$seat['id'] ?>"
                   data-code="<?= e($seat['code']) ?>"
                   data-area-id="<?= (int)$seat['area_id'] ?>"
                   data-type="<?= e($seat['type']) ?>"
                   data-price="<?= (float)$seat['base_price'] ?>"
                   data-notes="<?= e($seat['notes'] ?? '') ?>"
                   style="background:<?= $bg ?>;box-shadow:inset 0 3px 0 <?= $ac ?>"
                   title="<?= e($seat['code']) ?> · <?= e($seat['area_name']) ?>"
                   onclick="handleSeatClick(event, this)">
                <span class="le-seat-lbl"><?= e($lbl) ?></span>
                <button class="le-edit-btn" title="Modifica" onclick="event.stopPropagation();openEditModalFromEl(this.closest('.le-seat'))">✏</button>
                <button class="le-del-btn" title="Elimina" onclick="event.stopPropagation();deleteSeat(<?= (int)$seat['id'] ?>,'<?= e($seat['code']) ?>')">×</button>
              </div>
              <?php endif; ?>
            </div>
          <?php endfor; ?>
        <?php endfor; ?>

      </div><!-- /le-grid -->

      <?php if (!empty($unpositioned)): ?>
      <div class="le-unpos-zone">
        <div class="le-unpos-title">⚠ Lettini senza posizione — trascinali nella griglia</div>
        <div class="le-unpos-seats" id="unposPool">
          <?php foreach ($unpositioned as $p):
            $lbl = strlen($p['code']) > 1 ? substr($p['code'], 1) : $p['code'];
            $ac  = $areaColor[(int) $p['area_id']] ?? '#666';
          ?>
          <div class="le-unpos-seat"
               draggable="true"
               data-id="<?= (int)$p['id'] ?>"
               data-code="<?= e($p['code']) ?>"
               data-area-id="<?= (int)$p['area_id'] ?>"
               data-type="<?= e($p['type']) ?>"
               data-price="<?= (float)$p['base_price'] ?>"
               data-notes="<?= e($p['notes'] ?? '') ?>"
               title="<?= e($p['code']) ?> · <?= e($p['area_name']) ?>"
               style="position:relative;box-shadow:inset 0 3px 0 <?= $ac ?>"
               onclick="handleSeatClick(event, this)">
            <?= e($lbl) ?>
            <button class="le-edit-btn" title="Modifica" onclick="event.stopPropagation();openEditModalFromEl(this.closest('.le-unpos-seat'))">✏</button>
            <button class="le-del-btn" title="Elimina" onclick="event.stopPropagation();deleteSeat(<?= (int)$p['id'] ?>,'<?= e($p['code']) ?>')">×</button>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- /le-grid-wrap -->
  </div><!-- /le-body -->

</div><!-- /le-page -->

<div class="le-modal-overlay" id="leModalOverlay" style="display:none" onclick="if(event.target===this)closeModal()">
  <div class="le-modal">
    <div class="le-modal-head">
      <span id="leModalTitle">Nuovo lettino</span>
      <button type="button" class="le-modal-close" onclick="closeModal()">×</button>
    </div>
    <form id="leForm" onsubmit="return submitLeForm(event)">
      <input type="hidden" id="lf_id">
      <input type="hidden" id="lf_row">
      <input type="hidden" id="lf_col">
      <div class="le-modal-field">
        <label>Codice</label>
        <input type="text" id="lf_code" maxlength="32" required pattern="[A-Za-z0-9_\-]+"
               style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      </div>
      <div class="le-modal-field">
        <label>Area</label>
        <select id="lf_area">
          <?php foreach ($poolAreas as $a): ?>
          <option value="<?= (int) $a['id'] ?>"><?= e($a['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="le-modal-field">
        <label>Tipo</label>
        <select id="lf_type">
          <option value="lettino">Lettino</option>
          <option value="sdraio">Sdraio</option>
          <option value="ombrellone">Ombrellone</option>
          <option value="tavolo">Tavolo</option>
          <option value="cabana">Cabana</option>
        </select>
      </div>
      <div class="le-modal-field">
        <label>Prezzo base (€)</label>
        <input type="number" id="lf_price" min="0" step="0.50" value="0">
      </div>
      <div class="le-modal-field">
        <label>Note</label>
        <input type="text" id="lf_notes">
      </div>
      <div class="le-modal-err" id="lf_err" style="display:none"></div>
      <div class="le-modal-actions">
        <button type="button" class="le-modal-cancel" onclick="closeModal()">Annulla</button>
        <button type="button" class="le-modal-delete d-none" id="lf_delete_btn" onclick="deleteFromModal()">Elimina</button>
        <button type="submit" class="le-modal-save" id="lf_submit">Salva</button>
      </div>
    </form>
  </div>
</div>

<div class="le-toast" id="leToast"></div>

<script>
(function () {
  'use strict';

  const CSRF    = <?= json_encode(csrf_token(), JSON_THROW_ON_ERROR) ?>;
  const POS_URL    = <?= json_encode(url('/api/places/update-position.php'), JSON_THROW_ON_ERROR) ?>;
  const DEL_URL    = <?= json_encode(url('/api/places/delete-place.php'), JSON_THROW_ON_ERROR) ?>;
  const CREATE_URL = <?= json_encode(url('/api/places/create-extra.php'), JSON_THROW_ON_ERROR) ?>;
  const UPDATE_URL = <?= json_encode(url('/api/places/update-place.php'), JSON_THROW_ON_ERROR) ?>;

  let _dragId = null;
  let _dragEl = null;
  let cellW   = 60;

  // ── Drag start ──
  document.addEventListener('dragstart', function (e) {
    // Non avviare drag se si clicca il pulsante elimina
    if (e.target.closest('.le-del-btn')) { e.preventDefault(); return; }
    const seat = e.target.closest('[draggable="true"]');
    if (!seat || !seat.dataset.id) return;
    _dragId = seat.dataset.id;
    _dragEl = seat;
    seat.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', _dragId);
  });

  document.addEventListener('dragend', function () {
    document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    _dragId = null;
    _dragEl = null;
  });

  // ── Drag over/leave su celle ──
  document.addEventListener('dragover', function (e) {
    const cell = e.target.closest('.le-cell.empty');
    if (!cell) return;
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    cell.classList.add('drag-over');
  });

  document.addEventListener('dragleave', function (e) {
    const cell = e.target.closest('.le-cell');
    if (cell && !cell.contains(e.relatedTarget)) {
      cell.classList.remove('drag-over');
    }
  });

  // ── Drop ──
  document.addEventListener('drop', function (e) {
    const cell = e.target.closest('.le-cell.empty');
    if (!cell) return;
    e.preventDefault();
    cell.classList.remove('drag-over');

    // Cattura subito — dragend arriva prima che la fetch risolva
    const capturedId = _dragId;
    const capturedEl = _dragEl;
    if (!capturedId || !capturedEl) return;

    const row = parseInt(cell.dataset.row, 10);
    const col = parseInt(cell.dataset.col, 10);

    // Feedback visivo immediato: sposta il lettino
    const srcCell = capturedEl.closest('.le-cell');
    if (srcCell) {
      srcCell.classList.remove('has-seat');
      srcCell.classList.add('empty');
    }
    capturedEl.classList.remove('dragging');
    cell.classList.remove('empty');
    cell.classList.add('has-seat');
    cell.appendChild(capturedEl);

    // Salva su DB
    fetch(POS_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ place_id: capturedId, pos_row: row, pos_col: col, _csrf: CSRF }).toString()
    })
    .then(r => r.text())
    .then(text => {
      let resp;
      try { resp = JSON.parse(text); }
      catch (_) { throw new Error('Risposta non valida: ' + text.slice(0, 80)); }
      if (resp.success) {
        toast('✓ Salvato');
      } else {
        // Rollback visivo
        if (srcCell) {
          srcCell.classList.add('has-seat');
          srcCell.classList.remove('empty');
          srcCell.appendChild(capturedEl);
          cell.classList.remove('has-seat');
          cell.classList.add('empty');
        }
        toast(resp.error ?? resp.message ?? 'Errore', true);
      }
    })
    .catch(ex => toast(ex.message || 'Errore di rete', true));
  });

  // ── Elimina lettino ──
  window.deleteSeat = function (id, code) {
    // Blocca l'avvio del drag quando si clicca ×
    event.stopPropagation();
    if (!confirm('Eliminare il lettino ' + code + '?\nQuesta azione non può essere annullata.')) return;
    fetch(DEL_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ place_id: id, _csrf: CSRF }).toString()
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        const seatEl = document.querySelector(`.le-seat[data-id="${id}"], .le-unpos-seat[data-id="${id}"]`);
        if (seatEl) {
          const cell = seatEl.closest('.le-cell');
          if (cell) { cell.classList.remove('has-seat'); cell.classList.add('empty'); }
          seatEl.remove();
        }
        toast('✓ ' + code + ' eliminato');
      } else {
        toast(resp.error ?? 'Errore', true);
      }
    })
    .catch(() => toast('Errore di rete', true));
  };

  // ── Dimensione celle ──
  function changeSize(delta) {
    cellW = Math.max(36, Math.min(120, cellW + delta));
    document.documentElement.style.setProperty('--lc', cellW + 'px');
    document.documentElement.style.setProperty('--lr', Math.round(cellW * 0.58) + 'px');
    document.getElementById('szVal').textContent = cellW;
  }
  window.changeSize = changeSize;

  // ── Toast ──
  let _toastTimer = null;
  function toast(msg, isErr = false) {
    const el = document.getElementById('leToast');
    el.textContent = msg;
    el.className = 'le-toast' + (isErr ? ' err' : '');
    void el.offsetWidth;
    el.classList.add('show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('show'), 2200);
  }

  // ── Modal aggiungi/modifica lettino ──
  window.openAddModal = function (row, col) {
    document.getElementById('leModalTitle').textContent = 'Nuovo lettino';
    document.getElementById('lf_id').value    = '';
    document.getElementById('lf_row').value   = row;
    document.getElementById('lf_col').value   = col;
    setSelectValue('lf_type', 'lettino');
    document.getElementById('lf_price').value = 0;
    document.getElementById('lf_notes').value = '';
    document.getElementById('lf_err').style.display = 'none';
    document.getElementById('lf_delete_btn').classList.add('d-none');
    // Suggerisce il prossimo codice EX libero
    const existing = new Set([...document.querySelectorAll('[data-code]')].map(el => el.dataset.code));
    let n = 1;
    while (existing.has('EX' + String(n).padStart(2, '0'))) n++;
    document.getElementById('lf_code').value = 'EX' + String(n).padStart(2, '0');
    document.getElementById('leModalOverlay').style.display = 'flex';
  };

  window.openEditModalFromEl = function (el) {
    document.getElementById('leModalTitle').textContent = 'Modifica lettino';
    document.getElementById('lf_id').value    = el.dataset.id;
    document.getElementById('lf_row').value   = '';
    document.getElementById('lf_col').value   = '';
    document.getElementById('lf_code').value  = el.dataset.code;
    setSelectValue('lf_area', el.dataset.areaId);
    setSelectValue('lf_type', el.dataset.type || 'lettino');
    document.getElementById('lf_price').value = el.dataset.price || 0;
    document.getElementById('lf_notes').value = el.dataset.notes || '';
    document.getElementById('lf_err').style.display = 'none';
    document.getElementById('lf_delete_btn').classList.remove('d-none');
    document.getElementById('leModalOverlay').style.display = 'flex';
  };

  window.handleSeatClick = function (event, el) {
    openEditModalFromEl(el);
  };

  window.closeModal = function () {
    document.getElementById('leModalOverlay').style.display = 'none';
  };

  window.submitLeForm = function (event) {
    event.preventDefault();
    const id  = document.getElementById('lf_id').value;
    const err = document.getElementById('lf_err');
    const btn = document.getElementById('lf_submit');
    err.style.display = 'none';
    btn.disabled = true; btn.textContent = '…';

    const params = {
      _csrf:       CSRF,
      code:        document.getElementById('lf_code').value,
      area_id:     document.getElementById('lf_area').value,
      type:        document.getElementById('lf_type').value,
      base_price:  document.getElementById('lf_price').value,
      notes:       document.getElementById('lf_notes').value,
    };
    let targetUrl;
    if (id) {
      params.place_id = id;
      targetUrl = UPDATE_URL;
    } else {
      params.pos_row = document.getElementById('lf_row').value;
      params.pos_col = document.getElementById('lf_col').value;
      targetUrl = CREATE_URL;
    }

    fetch(targetUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(params).toString()
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        window.location.reload();
      } else {
        err.textContent = resp.error ?? 'Errore.';
        err.style.display = 'block';
        btn.disabled = false; btn.textContent = 'Salva';
      }
    })
    .catch(() => {
      err.textContent = 'Errore di rete.';
      err.style.display = 'block';
      btn.disabled = false; btn.textContent = 'Salva';
    });
    return false;
  };

  window.deleteFromModal = function () {
    const id   = document.getElementById('lf_id').value;
    const code = document.getElementById('lf_code').value;
    if (!id) return;
    if (!confirm('Eliminare il lettino ' + code + '?\nQuesta azione non può essere annullata.')) return;
    fetch(DEL_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ place_id: id, _csrf: CSRF }).toString()
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        closeModal();
        window.location.reload();
      } else {
        toast(resp.error ?? 'Errore', true);
      }
    })
    .catch(() => toast('Errore di rete', true));
  };

})();
</script>
