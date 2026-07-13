<?php
$deptLabels = ['bar' => 'Bar', 'ristorante' => 'Ristorante', 'reception' => 'Reception', 'extra' => 'Extra'];
$deptColors = [
  'bar'        => ['bg' => 'var(--chip-bar-bg)',   'ink' => 'var(--chip-bar-ink)'],
  'ristorante' => ['bg' => 'var(--chip-rist-bg)',  'ink' => 'var(--chip-rist-ink)'],
  'reception'  => ['bg' => 'var(--chip-recep-bg)', 'ink' => 'var(--chip-recep-ink)'],
  'extra'      => ['bg' => 'var(--chip-extra-bg)', 'ink' => 'var(--chip-extra-ink)'],
];

$catMap = [];
foreach ($categories as $c) {
    $catMap[(int)$c['id']] = $c;
}
?>
<style>
/* ── Catalogo layout ──────────────────────────────────────────── */
.cat-page { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }

.cat-head {
  display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.cat-head-title {
  font-family:'Poppins',sans-serif;
  font-size: 22px; font-weight: 800; color: var(--text); margin: 0;
}
.spacer { flex: 1; }

/* ── Pill tabs ────────────────────────────────────────────────── */
.cat-tabs {
  display: inline-flex; gap: 3px;
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: 10px; padding: 3px;
}
.cat-tab {
  padding: 6px 18px; border-radius: 8px; border: none; background: transparent;
  font-size: 13px; font-weight: 700; color: var(--muted); cursor: pointer;
  transition: background .15s, color .15s;
}
.cat-tab.active { background: var(--accent); color: #fff; }
.cat-tab:not(.active):hover { background: var(--border); color: var(--text); }

/* ── Filter bar ───────────────────────────────────────────────── */
.cat-filters {
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  margin-bottom: 16px;
}
.cat-select {
  height: 36px; padding: 0 10px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface);
  color: var(--text); font-size: 13px; font-family: inherit; outline: none;
  cursor: pointer; transition: border-color .15s;
}
.cat-select:focus { border-color: var(--accent); }

.cat-toggle {
  display: flex; align-items: center; gap: 7px;
  padding: 0 12px; height: 36px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface);
  cursor: pointer; font-size: 13px; font-weight: 600; color: var(--muted);
  user-select: none; transition: border-color .15s, color .15s;
}
.cat-toggle input { width: 14px; height: 14px; accent-color: var(--accent); cursor: pointer; }
.cat-toggle:hover { border-color: var(--accent); color: var(--text); }

.cat-search {
  display: flex; align-items: center; gap: 8px;
  height: 36px; padding: 0 12px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--surface);
  margin-left: auto;
}
.cat-search input {
  border: none; background: transparent; outline: none;
  color: var(--text); font: 13px/1 'Inter', sans-serif; width: 180px;
}
.cat-search input::placeholder { color: var(--muted-2); }
.cat-search:focus-within { border-color: var(--accent); }

/* ── Pulsanti azione ──────────────────────────────────────────── */
.btn-add {
  display: inline-flex; align-items: center; gap: 6px;
  height: 36px; padding: 0 16px; border-radius: 9px; border: none;
  background: var(--accent); color: var(--accent-ink);
  font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit;
  transition: filter .15s;
}
.btn-add:hover { filter: brightness(1.08); }

/* ── Panel / table ────────────────────────────────────────────── */
.cat-panel {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden;
}
.cat-table { width: 100%; border-collapse: collapse; }
.cat-table th {
  padding: 10px 14px; font-size: 10.5px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .06em; color: var(--muted);
  border-bottom: 1px solid var(--border); white-space: nowrap;
  background: var(--surface-2);
}
.cat-table td {
  padding: 11px 14px; font-size: 13px; color: var(--text);
  border-bottom: 1px solid var(--border); vertical-align: middle;
}
.cat-table tbody tr:last-child td { border-bottom: none; }
.cat-table tbody tr { transition: background .1s; }
.cat-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 4%, var(--surface)); }
.cat-table .text-end  { text-align: right; }
.cat-table .text-center { text-align: center; }

.cat-table .prod-name { font-weight: 700; color: var(--text); }
.cat-table .prod-note { font-size: 11px; color: var(--muted); margin-top: 1px; }

/* dept chip */
.dept-chip {
  display: inline-block; font-size: 10px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .05em;
  padding: 3px 8px; border-radius: 8px; white-space: nowrap;
}
/* active badge */
.active-yes {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; color: var(--good);
  background: color-mix(in srgb, var(--good) 14%, transparent);
  padding: 3px 9px; border-radius: 20px;
}
.active-no {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; color: var(--bad);
  background: color-mix(in srgb, var(--bad) 14%, transparent);
  padding: 3px 9px; border-radius: 20px;
}
.active-yes::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--good); }
.active-no::before  { content:''; width:6px; height:6px; border-radius:50%; background:var(--bad); }

/* stock badge */
.stock-badge {
  font-size: 11px; font-weight: 700;
  padding: 3px 8px; border-radius: 8px;
  background: color-mix(in srgb, var(--accent) 14%, transparent);
  color: var(--accent);
}

/* row actions */
.row-actions { display: flex; justify-content: flex-end; gap: 6px; }
.btn-edit, .btn-del {
  height: 28px; padding: 0 11px; border-radius: 7px;
  font-size: 11.5px; font-weight: 700; cursor: pointer; font-family: inherit;
  border: 1px solid; transition: background .12s, color .12s;
}
.btn-edit { border-color: var(--accent); color: var(--accent); background: transparent; }
.btn-edit:hover { background: var(--accent); color: var(--accent-ink); }
.btn-del  { border-color: var(--bad); color: var(--bad); background: transparent; }
.btn-del:hover { background: var(--bad); color: #fff; }

/* empty state */
.cat-empty {
  text-align: center; padding: 48px 24px;
  color: var(--muted-2); font-size: 14px;
}
.cat-empty .cat-empty-icon { font-size: 36px; margin-bottom: 8px; }

/* price */
.price-val { font-family:'Poppins',sans-serif; font-weight: 800; color: var(--text); }

/* ── Modal override ───────────────────────────────────────────── */
.modal-content {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
}
.modal-header {
  background: var(--surface-2); border-bottom: 1px solid var(--border);
  padding: 16px 20px;
}
.modal-title {
  font-family:'Poppins',sans-serif;
  font-size: 17px; font-weight: 800; color: var(--text);
}
.modal-body { padding: 20px; }
.modal-footer { border-top: 1px solid var(--border); padding: 14px 20px; background: var(--surface-2); }
.form-label { font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 5px; }
.form-control, .form-select {
  background: var(--surface-2) !important; border: 1px solid var(--border) !important;
  color: var(--text) !important; border-radius: 8px !important;
  font-size: 14px !important; padding: 8px 12px !important;
}
.form-control:focus, .form-select:focus {
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent) !important;
}
.btn-close { filter: var(--btn-close-filter, none); }
:root[data-theme="dark"] { --btn-close-filter: invert(1); }

/* Toast */
#pg-toast {
  position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
  background: var(--good); color: #fff;
  padding: .55rem 1.4rem; border-radius: 10px;
  font-size: 13px; font-weight: 700; z-index: 9999;
  transition: transform .25s; pointer-events: none; white-space: nowrap;
}
#pg-toast.show { transform: translateX(-50%) translateY(0); }
#pg-toast.err  { background: var(--bad); }
</style>

<div class="cat-page">

  <!-- Header -->
  <div class="cat-head">
    <h1 class="cat-head-title">Catalogo prodotti</h1>
    <div class="spacer"></div>
    <div class="cat-tabs">
      <button class="cat-tab active" id="tab-products"   onclick="showTab('products')">Prodotti</button>
      <button class="cat-tab"        id="tab-categories" onclick="showTab('categories')">Categorie</button>
    </div>
    <button class="btn-add" id="btnAddProduct" onclick="openProductModal()">+ Prodotto</button>
    <button class="btn-add" id="btnAddCategory" onclick="openCategoryModal()" style="display:none">+ Categoria</button>
  </div>

  <!-- ══ TAB PRODOTTI ══════════════════════════════════════════ -->
  <div id="pane-products">

    <!-- Filter bar -->
    <div class="cat-filters">
      <select class="cat-select" id="filterCat" data-select-only onchange="filterProducts()">
        <option value="">Tutte le categorie</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select class="cat-select" id="filterDept" data-select-only onchange="filterProducts()">
        <option value="">Tutti i reparti</option>
        <?php foreach ($deptLabels as $k => $v): ?>
          <option value="<?= e($k) ?>"><?= e($v) ?></option>
        <?php endforeach; ?>
      </select>

      <label class="cat-toggle">
        <input type="checkbox" id="filterActive" checked onchange="filterProducts()">
        Solo attivi
      </label>

      <div class="cat-search">
        <svg width="14" height="14" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" style="color:var(--muted-2);flex-shrink:0">
          <circle cx="7.5" cy="7.5" r="5.5"/><line x1="11.5" y1="11.5" x2="16" y2="16"/>
        </svg>
        <input type="search" id="searchProd" placeholder="Cerca prodotto…" oninput="filterProducts()">
      </div>
    </div>

    <!-- Table -->
    <div class="cat-panel">
      <table class="cat-table" id="productTable">
        <thead>
          <tr>
            <th>Prodotto</th>
            <th>Categoria</th>
            <th>Reparto</th>
            <th class="text-end">Prezzo</th>
            <th class="text-end">IVA</th>
            <th class="text-center">Scorte</th>
            <th class="text-center">Stato</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
          <tr><td colspan="7">
            <div class="cat-empty">
              <div class="cat-empty-icon">📦</div>
              Nessun prodotto trovato.
            </div>
          </td></tr>
          <?php endif; ?>
          <?php foreach ($products as $p):
            $cat  = $catMap[(int)$p['category_id']] ?? null;
            $dept = $cat['department'] ?? '';
            $dc   = $deptColors[$dept] ?? ['bg'=>'var(--surface-2)','ink'=>'var(--muted)'];
          ?>
          <tr class="prod-row"
              data-cat="<?= (int)$p['category_id'] ?>"
              data-dept="<?= e($dept) ?>"
              data-active="<?= $p['active'] ? '1' : '0' ?>"
              data-name="<?= e(mb_strtolower($p['name'])) ?>"
              style="cursor:pointer"
              onclick='editProduct(<?= json_encode($p) ?>)'>
            <td>
              <div class="prod-name"><?= e($p['name']) ?></div>
              <?php if ($p['notes']): ?>
                <div class="prod-note"><?= e(mb_substr($p['notes'], 0, 60)) ?><?= mb_strlen($p['notes']) > 60 ? '…' : '' ?></div>
              <?php endif; ?>
            </td>
            <td><?= e($cat['name'] ?? '—') ?></td>
            <td>
              <span class="dept-chip" style="background:<?= $dc['bg'] ?>;color:<?= $dc['ink'] ?>">
                <?= e($deptLabels[$dept] ?? $dept) ?>
              </span>
            </td>
            <td class="text-end">
              <span class="price-val">€&nbsp;<?= number_format((float)$p['price'], 2, ',', '.') ?></span>
            </td>
            <td class="text-end" style="color:var(--muted);font-size:12px">
              <?= $p['vat_rate'] !== null ? number_format((float)$p['vat_rate'], 0) . '%' : '—' ?>
            </td>
            <td class="text-center">
              <?php if ($p['stock_enabled']): ?>
                <span class="stock-badge"><?= $p['stock_qty'] !== null ? number_format((float)$p['stock_qty'], 0) : '∞' ?></span>
              <?php else: ?>
                <span style="color:var(--muted-2);font-size:13px">—</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($p['active']): ?>
                <span class="active-yes">Attivo</span>
              <?php else: ?>
                <span class="active-no">Inattivo</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ══ TAB CATEGORIE ════════════════════════════════════════ -->
  <div id="pane-categories" style="display:none">

    <div class="cat-panel">
      <table class="cat-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Reparto</th>
            <th class="text-center">Stato</th>
          </tr>
        </thead>
        <tbody id="catTableBody">
          <?php if (empty($categories)): ?>
          <tr><td colspan="3">
            <div class="cat-empty">
              <div class="cat-empty-icon">🗂️</div>
              Nessuna categoria.
            </div>
          </td></tr>
          <?php endif; ?>
          <?php foreach ($categories as $c):
            $dc = $deptColors[$c['department']] ?? ['bg'=>'var(--surface-2)','ink'=>'var(--muted)'];
          ?>
          <tr id="cat-row-<?= (int)$c['id'] ?>" style="cursor:pointer" onclick='editCategory(<?= json_encode($c) ?>)'>
            <td style="font-weight:700"><?= e($c['name']) ?></td>
            <td>
              <span class="dept-chip" style="background:<?= $dc['bg'] ?>;color:<?= $dc['ink'] ?>">
                <?= e($deptLabels[$c['department']] ?? $c['department']) ?>
              </span>
            </td>
            <td class="text-center">
              <?php if ($c['active']): ?>
                <span class="active-yes">Attiva</span>
              <?php else: ?>
                <span class="active-no">Inattiva</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /cat-page -->

<!-- ══ MODAL PRODOTTO ════════════════════════════════════════════ -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalTitle">Nuovo prodotto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="pm_id">
        <div class="row g-3">
          <div class="col-md-7">
            <div class="form-floating">
              <input type="text" class="form-control" id="pm_name" maxlength="160" placeholder="Nome" required>
              <label for="pm_name">Nome *</label>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-floating">
              <select class="form-select" id="pm_category_id" required>
                <option value="">— seleziona —</option>
                <?php foreach ($categories as $c): ?>
                  <?php if ($c['active']): ?>
                  <option value="<?= (int)$c['id'] ?>" data-dept="<?= e($c['department']) ?>">
                    <?= e($c['name']) ?> (<?= e($deptLabels[$c['department']] ?? $c['department']) ?>)
                  </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <label for="pm_category_id">Categoria *</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" class="form-control" id="pm_price" min="0" step="0.01" placeholder="Prezzo" required>
              <label for="pm_price">Prezzo (€) *</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" class="form-control" id="pm_vat_rate" min="0" max="100" step="0.01" placeholder="IVA">
              <label for="pm_vat_rate">IVA %</label>
            </div>
          </div>
          <div class="col-md-4 d-flex align-items-center">
            <div class="form-check ms-2">
              <input class="form-check-input" type="checkbox" id="pm_active">
              <label class="form-check-label" for="pm_active" style="font-size:13px">Attivo</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="pm_stock_enabled" onchange="toggleStock()">
              <label class="form-check-label" for="pm_stock_enabled" style="font-size:13px">Gestione scorte</label>
            </div>
          </div>
          <div class="col-md-4" id="pm_stock_qty_wrap" style="display:none">
            <div class="form-floating">
              <input type="number" class="form-control" id="pm_stock_qty" min="0" step="0.01" placeholder="Quantità">
              <label for="pm_stock_qty">Quantità in magazzino</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-floating">
              <textarea class="form-control" id="pm_notes" maxlength="500" placeholder="Note" style="height:90px"></textarea>
              <label for="pm_notes">Note</label>
            </div>
          </div>
        </div>
        <div id="pm_error" class="alert alert-danger mt-3 d-none" style="font-size:13px;border-radius:9px"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-danger" id="pm_delete_btn" style="display:none" onclick="deleteProductFromModal()">🗑 Elimina prodotto</button>
        <button type="button" class="btn btn-primary ms-auto" onclick="saveProduct()">Salva prodotto</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL CATEGORIA ═══════════════════════════════════════════ -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoryModalTitle">Nuova categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="cm_id">
        <div class="mb-3">
          <label class="form-label">Nome *</label>
          <input type="text" class="form-control" id="cm_name" maxlength="120" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Reparto *</label>
          <select class="form-select" id="cm_department">
            <?php foreach ($deptLabels as $k => $v): ?>
            <option value="<?= e($k) ?>"><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cm_active">
          <label class="form-check-label" for="cm_active" style="font-size:13px">Attiva</label>
        </div>
        <div id="cm_error" class="alert alert-danger mt-3 d-none" style="font-size:13px;border-radius:9px"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-danger" id="cm_delete_btn" style="display:none" onclick="deleteCategoryFromModal()">🗑 Elimina categoria</button>
        <button type="button" class="btn btn-primary ms-auto" onclick="saveCategory()">Salva categoria</button>
      </div>
    </div>
  </div>
</div>

<div id="pg-toast"></div>

<script>
const SAVE_PROD_URL = '<?= url('/api/products/save.php') ?>';
const DEL_PROD_URL  = '<?= url('/api/products/delete.php') ?>';
const SAVE_CAT_URL  = '<?= url('/api/products/save-category.php') ?>';
const DEL_CAT_URL   = '<?= url('/api/products/delete-category.php') ?>';
const CSRF = <?= json_encode(csrf_token()) ?>;

/* ── Tabs ────────────────────────────────────────────────────── */
function showTab(tab) {
  const isProd = tab === 'products';
  document.getElementById('pane-products').style.display   = isProd ? '' : 'none';
  document.getElementById('pane-categories').style.display = isProd ? 'none' : '';
  document.getElementById('tab-products').classList.toggle('active',   isProd);
  document.getElementById('tab-categories').classList.toggle('active', !isProd);
  document.getElementById('btnAddProduct').style.display = isProd ? '' : 'none';
  document.getElementById('btnAddCategory').style.display = isProd ? 'none' : '';
}

/* ── Filter prodotti ─────────────────────────────────────────── */
function filterProducts() {
  const cat    = document.getElementById('filterCat').value;
  const dept   = document.getElementById('filterDept').value;
  const active = document.getElementById('filterActive').checked;
  const q      = document.getElementById('searchProd').value.toLowerCase().trim();
  document.querySelectorAll('.prod-row').forEach(function (tr) {
    const ok = (!cat    || tr.dataset.cat    === cat)
            && (!dept   || tr.dataset.dept   === dept)
            && (!active || tr.dataset.active  === '1')
            && (!q      || tr.dataset.name.includes(q));
    tr.style.display = ok ? '' : 'none';
  });
}

/* ── Modal prodotto ──────────────────────────────────────────── */
function openProductModal(data) {
  document.getElementById('pm_id').value            = data ? data.id    : '';
  document.getElementById('pm_name').value          = data ? data.name  : '';
  setSelectValue('pm_category_id', data ? data.category_id : '');
  document.getElementById('pm_price').value         = data ? data.price : '';
  document.getElementById('pm_vat_rate').value      = data && data.vat_rate !== null ? data.vat_rate : '';
  document.getElementById('pm_active').checked      = data ? !!parseInt(data.active) : true;
  document.getElementById('pm_stock_enabled').checked = data ? !!parseInt(data.stock_enabled) : false;
  document.getElementById('pm_stock_qty').value     = data && data.stock_qty !== null ? data.stock_qty : '';
  document.getElementById('pm_notes').value         = data ? (data.notes || '') : '';
  document.getElementById('pm_error').classList.add('d-none');
  document.getElementById('productModalTitle').textContent = data ? 'Modifica prodotto' : 'Nuovo prodotto';
  // Delete button only when editing an existing product
  var delBtn = document.getElementById('pm_delete_btn');
  delBtn.style.display = data ? '' : 'none';
  delBtn.dataset.name = data ? data.name : '';
  toggleStock();
  new bootstrap.Modal(document.getElementById('productModal')).show();
}
function editProduct(data) { openProductModal(data); }

function deleteProductFromModal() {
  var id   = document.getElementById('pm_id').value;
  var name = document.getElementById('pm_delete_btn').dataset.name || '';
  if (!id) return;
  deleteProduct(id, name, function () {
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
  });
}

function toggleStock() {
  document.getElementById('pm_stock_qty_wrap').style.display =
    document.getElementById('pm_stock_enabled').checked ? '' : 'none';
}

function saveProduct() {
  const name  = document.getElementById('pm_name').value.trim();
  const catId = document.getElementById('pm_category_id').value;
  const price = document.getElementById('pm_price').value;
  const errEl = document.getElementById('pm_error');
  if (!name || !catId || price === '') {
    errEl.textContent = 'Compila tutti i campi obbligatori (nome, categoria, prezzo).';
    errEl.classList.remove('d-none'); return;
  }
  fetch(SAVE_PROD_URL, {
    method: 'POST',
    body: new URLSearchParams({
      _csrf: CSRF,
      id:            document.getElementById('pm_id').value,
      name, category_id: catId, price,
      vat_rate:      document.getElementById('pm_vat_rate').value,
      active:        document.getElementById('pm_active').checked ? '1' : '0',
      stock_enabled: document.getElementById('pm_stock_enabled').checked ? '1' : '0',
      stock_qty:     document.getElementById('pm_stock_qty').value,
      notes:         document.getElementById('pm_notes').value,
    })
  }).then(r => r.json()).then(function (resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
      toast('Prodotto salvato');
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

function deleteProduct(id, name, onOk) {
  if (!confirm('Eliminare il prodotto "' + name + '"?\nSe ha uno storico movimenti verrà solo disattivato.')) return;
  fetch(DEL_PROD_URL, { method: 'POST', body: new URLSearchParams({ _csrf: CSRF, id }) })
  .then(r => r.json()).then(function (resp) {
    if (resp.success) {
      if (onOk) onOk();
      toast(resp.soft ? name + ' disattivato (ha storico).' : name + ' eliminato.');
      setTimeout(function () { location.reload(); }, 500);
    } else {
      toast('Errore: ' + (resp.error || 'sconosciuto'), true);
    }
  });
}

/* ── Modal categoria ─────────────────────────────────────────── */
function openCategoryModal(data) {
  document.getElementById('cm_id').value         = data ? data.id         : '';
  document.getElementById('cm_name').value       = data ? data.name       : '';
  setSelectValue('cm_department', data ? data.department : 'bar');
  document.getElementById('cm_active').checked   = data ? !!parseInt(data.active) : true;
  document.getElementById('cm_error').classList.add('d-none');
  document.getElementById('categoryModalTitle').textContent = data ? 'Modifica categoria' : 'Nuova categoria';
  var delBtn = document.getElementById('cm_delete_btn');
  delBtn.style.display = data ? '' : 'none';
  delBtn.dataset.name = data ? data.name : '';
  new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
function editCategory(data) { openCategoryModal(data); }

function deleteCategoryFromModal() {
  var id   = document.getElementById('cm_id').value;
  var name = document.getElementById('cm_delete_btn').dataset.name || '';
  if (!id) return;
  deleteCategory(id, name, function () {
    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
  });
}

function saveCategory() {
  const name  = document.getElementById('cm_name').value.trim();
  const errEl = document.getElementById('cm_error');
  if (!name) { errEl.textContent = 'Nome obbligatorio.'; errEl.classList.remove('d-none'); return; }
  fetch(SAVE_CAT_URL, {
    method: 'POST',
    body: new URLSearchParams({
      _csrf:      CSRF,
      id:         document.getElementById('cm_id').value,
      name,
      department: document.getElementById('cm_department').value,
      active:     document.getElementById('cm_active').checked ? '1' : '0',
    })
  }).then(r => r.json()).then(function (resp) {
    if (resp.success) {
      bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
      toast('Categoria salvata');
      setTimeout(function () { location.reload(); }, 500);
    } else {
      errEl.textContent = resp.error || 'Errore.';
      errEl.classList.remove('d-none');
    }
  }).catch(function () {
    errEl.textContent = 'Errore di rete.';
    errEl.classList.remove('d-none');
  });
}

function deleteCategory(id, name, onOk) {
  if (!confirm('Eliminare la categoria "' + name + '"?\nImpossibile se contiene prodotti.')) return;
  fetch(DEL_CAT_URL, { method: 'POST', body: new URLSearchParams({ _csrf: CSRF, id }) })
  .then(r => r.json()).then(function (resp) {
    if (resp.success) {
      if (onOk) onOk();
      toast(name + ' eliminata.');
      setTimeout(function () { location.reload(); }, 500);
    } else {
      toast('Errore: ' + (resp.error || 'sconosciuto'), true);
    }
  });
}

/* ── Toast ───────────────────────────────────────────────────── */
(function () {
  var _t;
  window.toast = function (msg, isErr) {
    var el = document.getElementById('pg-toast');
    el.textContent = msg;
    el.classList.toggle('err', !!isErr);
    el.classList.add('show');
    clearTimeout(_t);
    _t = setTimeout(function () { el.classList.remove('show'); }, 2800);
  };
}());

/* init */
filterProducts();
</script>
