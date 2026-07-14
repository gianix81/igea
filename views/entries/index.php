<style>
/* ── Reception / Ingressi ────────────────────────────────── */
.en-layout {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 18px;
  padding: 20px 22px;
}
@media (max-width: 900px) { .en-layout { grid-template-columns: 1fr; padding: 14px; } }
@media (max-width: 560px) {
  .en-table th, .en-table td { white-space: nowrap; }
  .en-row-2 { grid-template-columns: 1fr 1fr; }
}

/* Form panel */
.en-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; padding: 20px;
}
.en-panel-title {
  font-family: 'Poppins', sans-serif;
  font-size: 20px; font-weight: 800; color: var(--text);
  margin-bottom: 18px;
}

/* Inputs */
.en-input {
  width: 100%; padding: 11px 14px; border-radius: 10px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 14px; font-family: inherit;
  outline: none; transition: border-color .15s; box-sizing: border-box;
}
.en-input:focus { border-color: var(--accent); }
.en-input-lg { font-size: 16px; padding: 13px 16px; border-radius: 11px; }
.en-input::placeholder { color: var(--muted-2); }

.en-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.en-form-group { margin-bottom: 10px; }
.en-form-label {
  display: block; font-size: 10px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .07em;
  color: var(--muted-2); margin-bottom: 4px;
}

/* Multiselect */
.en-multiselect {
  width: 100%; border-radius: 10px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 12.5px; font-family: inherit;
  outline: none; padding: 4px;
}
.en-multiselect:focus { border-color: var(--accent); }
.en-multiselect option { padding: 5px 8px; border-radius: 6px; }
.en-multiselect option:checked { background: var(--accent); color: var(--accent-ink); }

.en-child-rate-btn {
  width: 100%; padding: 7px 10px; border-radius: 8px;
  border: 1.5px dashed var(--border); background: transparent;
  color: var(--muted); font-size: 11.5px; font-weight: 700; cursor: pointer;
  font-family: inherit; transition: border-color .15s, color .15s;
}
.en-child-rate-btn:hover { border-color: var(--accent); color: var(--accent); }

/* Submit */
.en-submit {
  width: 100%; padding: 13px; border-radius: 11px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 14px; font-weight: 800; cursor: pointer;
  font-family: inherit; margin-top: 4px; transition: opacity .15s;
}
.en-submit:hover { opacity: .88; }

/* Error */
.en-error {
  padding: 10px 13px; border-radius: 9px; margin-bottom: 14px;
  background: color-mix(in srgb, var(--bad) 12%, transparent);
  border: 1px solid color-mix(in srgb, var(--bad) 30%, transparent);
  font-size: 12px; color: var(--bad);
}

/* Client picker */
.en-picker-wrap { position: relative; margin-bottom: 10px; }
.en-picker-input {
  width: 100%; padding: 11px 36px 11px 14px; border-radius: 10px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--text); font-size: 14px; font-family: inherit;
  outline: none; transition: border-color .15s; box-sizing: border-box;
  cursor: pointer;
}
.en-picker-input:focus { border-color: var(--accent); }
.en-picker-input::placeholder { color: var(--muted-2); }
.en-picker-arrow {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  color: var(--muted-2); pointer-events: none; font-size: 11px;
}
.en-picker-dd {
  position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 120;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 11px; box-shadow: 0 8px 28px rgba(0,0,0,.18);
  max-height: 240px; overflow-y: auto; scrollbar-width: none;
  display: none;
}
.en-picker-dd::-webkit-scrollbar { display: none; }
.en-picker-dd.open { display: block; }
.en-picker-opt {
  display: flex; align-items: center; justify-content: space-between;
  padding: 9px 13px; cursor: pointer; gap: 10px;
  border-bottom: 1px solid var(--border); transition: background .1s;
}
.en-picker-opt:last-child { border-bottom: none; }
.en-picker-opt:hover { background: var(--surface-2); }
.en-picker-opt.selected { background: color-mix(in srgb, var(--accent) 10%, transparent); }
.en-picker-name { font-size: 13px; font-weight: 700; color: var(--text); }
.en-picker-meta { font-size: 10.5px; color: var(--muted); white-space: nowrap; }
.en-picker-code { font-family: monospace; font-size: 11px; color: var(--muted-2); }
.en-picker-empty { padding: 14px; text-align: center; color: var(--muted-2); font-size: 12px; }
.en-picker-inside { color: var(--good); font-size: 10px; font-weight: 700; }

/* Table panel */
.en-table-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; padding: 20px; display: flex; flex-direction: column;
}
.en-table-title {
  font-family: 'Poppins', sans-serif;
  font-size: 18px; font-weight: 800; color: var(--text);
  margin-bottom: 14px;
}
.en-table-wrap { border: 1px solid var(--border); border-radius: 11px; overflow-x: auto; }
.en-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.en-table thead th {
  padding: 9px 13px; background: var(--surface-2);
  font-size: 10px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .07em; color: var(--muted); border-bottom: 1px solid var(--border);
}
.en-table tbody td {
  padding: 10px 13px; border-bottom: 1px solid var(--border);
  color: var(--text); vertical-align: middle;
}
.en-table tbody tr:last-child td { border-bottom: none; }
.en-table tbody tr:hover td { background: var(--surface-2); }

/* Status badge */
.en-status {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 9px; border-radius: 20px; font-size: 10px; font-weight: 800;
}
.en-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.en-status.dentro  { background: color-mix(in srgb, var(--good) 15%, transparent); color: var(--good); }
.en-status.uscito  { background: color-mix(in srgb, var(--muted) 15%, transparent); color: var(--muted); }
.en-status.bloccato { background: color-mix(in srgb, var(--bad) 15%, transparent); color: var(--bad); }

.en-co-btn {
  border: none; border-radius: 6px; padding: 3px 10px; font-size: 11px; font-weight: 700;
  cursor: pointer; font-family: inherit; transition: opacity .12s;
}
.en-co-btn:hover { opacity: .8; }
.en-co-btn.co-normal { background: color-mix(in srgb, var(--good) 15%, transparent); color: var(--good); border: 1px solid color-mix(in srgb, var(--good) 35%, transparent); }
.en-co-btn.co-force  { background: color-mix(in srgb, var(--warn) 15%, transparent); color: var(--warn); border: 1px solid color-mix(in srgb, var(--warn) 35%, transparent); }

.en-card-link {
  font-family: monospace; font-size: 11.5px; color: var(--accent);
  background: color-mix(in srgb, var(--accent) 10%, transparent);
  padding: 2px 7px; border-radius: 5px; text-decoration: none;
}
.en-card-link:hover { text-decoration: underline; color: var(--accent); }

.en-empty {
  text-align: center; padding: 40px 20px; color: var(--muted-2); font-size: 13px;
}
</style>

<?php if (!empty($flash)): ?>
<div class="en-error" style="margin:16px 22px 0;background:color-mix(in srgb,var(--good) 12%,transparent);border-color:color-mix(in srgb,var(--good) 30%,transparent);color:var(--good)"><?= e($flash) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="en-error" style="margin:16px 22px 0"><?= e($error) ?></div>
<?php endif; ?>

<div class="en-layout">

  <!-- LEFT: form nuovo ingresso -->
  <div class="en-panel">
    <div class="en-panel-title">Nuovo ingresso</div>
    <form method="post">
      <?= csrf_field() ?>

      <!-- Client picker -->
      <div class="en-form-group">
        <label class="en-form-label">Cerca cliente</label>
        <div class="en-picker-wrap">
          <input type="text" id="en_picker_search" class="en-picker-input"
                 placeholder="Cognome, nome o codice card…" autocomplete="off">
          <span class="en-picker-arrow">▾</span>
          <div class="en-picker-dd" id="en_picker_dd"></div>
        </div>
      </div>

      <div class="en-form-group">
        <label class="en-form-label">Codice card</label>
        <input class="en-input en-input-lg" name="card_code" id="en_card_code"
               placeholder="Codice card" required autocomplete="off">
      </div>

      <div class="en-row-2" style="margin-bottom:10px">
        <div>
          <label class="en-form-label">Persone</label>
          <input class="en-input" name="people_count" type="number" min="1" value="1">
        </div>
        <div>
          <label class="en-form-label">Tariffa €</label>
          <input class="en-input" name="entry_fee" id="en_entry_fee" type="number" step="0.01" min="0" value="0">
        </div>
      </div>

      <?php if ($childEntryRate !== null): ?>
      <div class="en-form-group" style="margin-top:-4px">
        <button type="button" class="en-child-rate-btn" onclick="document.getElementById('en_entry_fee').value = '<?= e((string)$childEntryRate) ?>'">
          👶 Ridotto bimbi (≤5 anni) — € <?= e((string)$childEntryRate) ?>
        </button>
      </div>
      <?php endif; ?>

      <div class="en-row-2" style="margin-bottom:10px">
        <div>
          <label class="en-form-label">Pagato €</label>
          <input class="en-input" name="paid_amount" type="number" step="0.01" min="0" value="0">
        </div>
        <div>
          <label class="en-form-label">Metodo</label>
          <select class="en-input" name="payment_method">
            <option value="">—</option>
            <option>contanti</option>
            <option>carta</option>
            <option>bonifico</option>
            <option>satispay</option>
            <option>altro</option>
          </select>
        </div>
      </div>

      <div class="en-form-group">
        <label class="en-form-label">Lettini / posti</label>
        <select class="en-multiselect" name="place_ids[]" multiple size="7">
          <?php foreach ($places as $p): ?>
          <option value="<?= (int)$p['id'] ?>">
            <?= e($p['area_name'] . ' · ' . $p['code'] . ' · ' . $p['type']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="en-form-group">
        <label class="en-form-label">Note</label>
        <textarea class="en-input" name="notes" rows="2"
                  style="resize:vertical;min-height:60px" placeholder="Note opzionali…"></textarea>
      </div>

      <button class="en-submit" type="submit">Registra ingresso</button>
    </form>
  </div>

  <!-- RIGHT: lista ingressi di oggi -->
  <div class="en-table-panel">
    <div class="en-table-title">Ingressi di oggi</div>
    <?php if (empty($entries)): ?>
    <div class="en-empty">Nessun ingresso registrato oggi.</div>
    <?php else: ?>
    <div class="en-table-wrap">
      <table class="en-table">
        <thead>
          <tr>
            <th>Ora</th>
            <th>Cliente</th>
            <th>Card</th>
            <th class="text-center">Persone</th>
            <th class="text-end">Tariffa</th>
            <th class="text-end">Pagato</th>
            <th>Stato</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($entries as $e): ?>
          <tr>
            <td style="color:var(--muted-2);font-size:11px;white-space:nowrap">
              <?= date('H:i', strtotime($e['checkin_at'])) ?>
            </td>
            <td style="font-weight:600"><?= e($e['customer_name']) ?></td>
            <td>
              <a class="en-card-link"
                 href="<?= url('/cards?code=' . urlencode($e['card_code'])) ?>">
                <?= e($e['card_code']) ?>
              </a>
            </td>
            <td class="text-center" style="color:var(--muted)"><?= (int)$e['people_count'] ?></td>
            <td class="text-end" style="font-size:12px"><?= money($e['entry_fee']) ?></td>
            <td class="text-end" style="font-size:12px"><?= money($e['paid_amount']) ?></td>
            <td>
              <?php
                $st = $e['status'];
                $cls = match($st) { 'dentro'=>'dentro','bloccato'=>'bloccato', default=>'uscito' };
                $lbl = match($st) { 'dentro'=>'Dentro','bloccato'=>'Bloccato', default=>ucfirst($st) };
              ?>
              <span class="en-status <?= $cls ?>"><?= $lbl ?></span>
            </td>
            <td style="white-space:nowrap">
              <?php if (in_array($st, ['dentro','bloccato'], true)): ?>
              <form method="post" action="<?= url('/checkout') ?>" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="card_code" value="<?= e($e['card_code']) ?>">
                <input type="hidden" name="redirect_to" value="entries">
                <button type="submit" class="en-co-btn co-normal">✓ Check-out</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  var searchEl = document.getElementById('en_picker_search');
  var ddEl     = document.getElementById('en_picker_dd');
  var codeEl   = document.getElementById('en_card_code');
  var _timer, _lastQ = null, _lastResults = [];

  function renderOptions(results) {
    _lastResults = results;
    if (!results.length) {
      ddEl.innerHTML = '<div class="en-picker-empty">Nessun cliente trovato</div>';
      return;
    }
    results.sort(function(a, b) {
      return a.customer_name.localeCompare(b.customer_name, 'it');
    });
    var html = '';
    results.forEach(function (r) {
      var hasCard    = r.card_code && r.card_code !== 'null';
      var insideMark = r.is_inside ? '<span class="en-picker-inside">● dentro</span>' : '';
      var noCardMark = !hasCard    ? '<span style="font-size:10px;color:var(--muted-2)">Senza card</span>' : '';
      var metaCode   = hasCard     ? '<span class="en-picker-code">' + r.card_code + '</span>' : '';
      html += '<div class="en-picker-opt" data-code="' + (hasCard ? r.card_code : '') + '" data-name="' + r.customer_name.replace(/"/g,'&quot;') + '">'
        + '<div>'
        + '<div class="en-picker-name">' + r.customer_name + '</div>'
        + '<div class="en-picker-meta">' + (r.phone ? r.phone + (metaCode ? ' · ' : '') : '') + metaCode + '</div>'
        + '</div>'
        + (insideMark || noCardMark)
        + '</div>';
    });
    ddEl.innerHTML = html;

    ddEl.querySelectorAll('.en-picker-opt').forEach(function (opt) {
      opt.addEventListener('mousedown', function (e) {
        e.preventDefault();
        ddEl.classList.remove('open');
        var code   = opt.getAttribute('data-code');
        var name   = opt.getAttribute('data-name');
        var record = (_lastResults || []).find(function(r){ return r.customer_name === name; }) || {};
        /* Seleziona sempre il nome; card code solo se disponibile */
        searchEl.value = name;
        codeEl.value   = code || '';
        ddEl.querySelectorAll('.en-picker-opt').forEach(function(o){ o.classList.remove('selected'); });
        opt.classList.add('selected');
        /* Mostra CardVerify solo se ha card */
        if (code && record.card_code) {
          CardVerify.show(record, function () {
            codeEl.value = code;
          }, function () {
            codeEl.value  = '';
            searchEl.value = '';
          });
        }
      });
    });
  }

  function doSearch(q) {
    if (q === _lastQ) return;
    _lastQ = q;
    fetch('<?= url('/api/customers/search.php') ?>?q=' + encodeURIComponent(q))
      .then(function(r){ return r.json(); })
      .then(function(d){ renderOptions(d.results || []); });
  }

  searchEl.addEventListener('focus', function () {
    ddEl.classList.add('open');
    doSearch(searchEl.value.trim());
  });

  searchEl.addEventListener('input', function () {
    ddEl.classList.add('open');
    clearTimeout(_timer);
    _timer = setTimeout(function () { doSearch(searchEl.value.trim()); }, 200);
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.en-picker-wrap')) ddEl.classList.remove('open');
  });

  searchEl.addEventListener('keydown', function (e) {
    var opts = Array.from(ddEl.querySelectorAll('.en-picker-opt'));
    var cur  = ddEl.querySelector('.en-picker-opt.selected');
    var idx  = opts.indexOf(cur);
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      var next = opts[idx + 1] || opts[0];
      if (next) { opts.forEach(function(o){o.classList.remove('selected');}); next.classList.add('selected'); next.scrollIntoView({block:'nearest'}); }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      var prev = opts[idx - 1] || opts[opts.length - 1];
      if (prev) { opts.forEach(function(o){o.classList.remove('selected');}); prev.classList.add('selected'); prev.scrollIntoView({block:'nearest'}); }
    } else if (e.key === 'Enter') {
      if (cur) { e.preventDefault(); cur.dispatchEvent(new MouseEvent('mousedown')); }
    } else if (e.key === 'Escape') {
      ddEl.classList.remove('open');
    }
  });
}());

</script>
