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
.le-del-btn {
  position: absolute;
  top: -5px; right: -5px;
  width: 15px; height: 15px;
  border-radius: 50%;
  background: #d9534f;
  color: #fff;
  border: none;
  font-size: 10px;
  line-height: 1;
  cursor: pointer;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 10;
  padding: 0;
  font-weight: 700;
}
.le-seat:hover .le-del-btn { display: flex; }

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

    <span class="le-hint">⟵ trascina i lettini per riposizionarli</span>

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
                 data-row="<?= $r ?>" data-col="<?= $c ?>">
              <?php if ($seat):
                $ds = $seat['day_status'] ?? 'libero';
                $bg = $statusColor[$ds] ?? '#1a9e6c';
                $lbl = strlen($seat['code']) > 1 ? substr($seat['code'], 1) : $seat['code'];
              ?>
              <div class="le-seat"
                   draggable="true"
                   data-id="<?= (int)$seat['id'] ?>"
                   data-code="<?= e($seat['code']) ?>"
                   style="background:<?= $bg ?>"
                   title="<?= e($seat['code']) ?> · <?= e($seat['area_name']) ?>">
                <span class="le-seat-lbl"><?= e($lbl) ?></span>
                <button class="le-del-btn" title="Elimina" onclick="deleteSeat(<?= (int)$seat['id'] ?>,'<?= e($seat['code']) ?>')">×</button>
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
          ?>
          <div class="le-unpos-seat"
               draggable="true"
               data-id="<?= (int)$p['id'] ?>"
               data-code="<?= e($p['code']) ?>"
               title="<?= e($p['code']) ?>"
               style="position:relative">
            <?= e($lbl) ?>
            <button class="le-del-btn" title="Elimina" onclick="deleteSeat(<?= (int)$p['id'] ?>,'<?= e($p['code']) ?>')">×</button>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- /le-grid-wrap -->
  </div><!-- /le-body -->

</div><!-- /le-page -->

<div class="le-toast" id="leToast"></div>

<script>
(function () {
  'use strict';

  const CSRF    = <?= json_encode(csrf_token(), JSON_THROW_ON_ERROR) ?>;
  const POS_URL = <?= json_encode(url('/api/places/update-position.php'), JSON_THROW_ON_ERROR) ?>;
  const DEL_URL = <?= json_encode(url('/api/places/delete-place.php'), JSON_THROW_ON_ERROR) ?>;

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

})();
</script>
