<?php
/* Mappa lettini — layout fedele alla piantina cartacea, single-screen */

$byCode = [];
foreach ($places as $p) {
    $byCode[$p['code']] = $p;
}

// Costruisce griglia [pos_row][pos_col] = place, raggruppata per area
$mapGrid      = [];  // [pos_row][pos_col] => place
$mapRowArea   = [];  // [pos_row] => area_id
$mapAreaNames = [];  // [area_id] => area_name
$mapUnpos     = [];  // posti senza posizione

foreach ($places as $p) {
    $aid = (int)$p['area_id'];
    $mapAreaNames[$aid] = $p['area_name'];
    if ($p['pos_row'] !== null && $p['pos_col'] !== null) {
        $r = (int)$p['pos_row'];
        $c = (int)$p['pos_col'];
        $mapGrid[$r][$c] = $p;
        $mapRowArea[$r]  = $aid;
    } else {
        $mapUnpos[] = $p;
    }
}
ksort($mapGrid);

// Numero colonne massimo nella griglia
$mapMaxCol = 9;
foreach ($mapGrid as $_row) {
    if ($_row) $mapMaxCol = max($mapMaxCol, max(array_keys($_row)));
}
$mapNCols = $mapMaxCol + 1;
$mapNRows = count($mapGrid);
unset($_row);

$placesForJs = array_values(array_map(static fn($p) => [
    'id'         => (int) $p['id'],
    'day_status' => $p['day_status'],
    'en'         => $p['entry_customer_name'] ?? null,
    're'         => $p['res_customer_name']   ?? null,
], $places));

function seat(string $code, array &$byCode): string
{
    $s = $byCode[$code] ?? null;
    if (!$s) {
        return '<span class="seat seat-nd" title="' . htmlspecialchars($code, ENT_QUOTES) . '"></span>';
    }
    $status = htmlspecialchars($s['day_status'], ENT_QUOTES);
    $id     = (int) $s['id'];
    $name   = $s['entry_customer_name'] ?? $s['res_customer_name'] ?? '';
    $title  = htmlspecialchars($code . ($name ? ' · ' . $name : ''), ENT_QUOTES);
    $lbl    = strlen($code) > 1 ? substr($code, 1) : $code;
    return "<button class=\"seat seat-{$status}\" data-id=\"{$id}\" data-code=\"" .
           htmlspecialchars($code, ENT_QUOTES) . "\" title=\"{$title}\">{$lbl}</button>";
}

$statusCls   = ['confermata' => 'res-s-confermata', 'in attesa' => 'res-s-attesa', 'completata' => 'res-s-completata'];
$statusLabel = ['confermata' => 'Confermata', 'in attesa' => 'In attesa', 'completata' => 'Completata'];
?>
<div class="lg-page">

  <!-- ══ Topbar ══ -->
  <div class="lg-topbar">
    <div class="d-flex align-items-center gap-1">
      <a href="<?= url('/places?date=' . urlencode($prevDate)) ?>" class="lg-nav-btn" title="Giorno precedente">&#8249;</a>
      <input type="date" id="mapDate" class="form-control form-control-sm lg-date-input"
             value="<?= e($date) ?>" max="<?= e(date('Y-m-d', strtotime('+180 days'))) ?>">
      <a href="<?= url('/places?date=' . urlencode($nextDate)) ?>" class="lg-nav-btn" title="Giorno successivo">&#8250;</a>
      <?php if ($date !== date('Y-m-d')): ?>
        <a href="<?= url('/places') ?>" class="lg-today-btn">Oggi</a>
      <?php endif; ?>
    </div>

    <div class="lg-legend">
      <span class="ll-i"><span class="ll-dot ll-disp"></span>Libero</span>
      <span class="ll-sep">·</span>
      <span class="ll-i"><span class="ll-dot ll-att"></span>Att.</span>
      <span class="ll-i"><span class="ll-dot ll-att-m"></span>Att.M</span>
      <span class="ll-i"><span class="ll-dot ll-att-p"></span>Att.P</span>
      <span class="ll-sep">·</span>
      <span class="ll-i"><span class="ll-dot ll-pren"></span>Conf.</span>
      <span class="ll-i"><span class="ll-dot ll-con-m"></span>Conf.M</span>
      <span class="ll-i"><span class="ll-dot ll-con-p"></span>Conf.P</span>
      <span class="ll-sep">·</span>
      <span class="ll-i"><span class="ll-dot ll-occ"></span>Occ.</span>
      <span class="ll-i"><span class="ll-dot ll-occ-m"></span>Occ.M</span>
      <span class="ll-i"><span class="ll-dot ll-occ-p"></span>Occ.P</span>
    </div>

    <div id="mapSummary" class="d-flex gap-2 align-items-center"></div>

    <div class="lg-topbar-right ms-auto d-flex align-items-center gap-2">
      <!-- Zoom -->
      <div class="lg-zoom-ctrl">
        <button id="zoomOut" class="lg-zoom-btn" title="Zoom out">−</button>
        <span id="zoomVal" class="lg-zoom-val">100%</span>
        <button id="zoomIn"  class="lg-zoom-btn" title="Zoom in">+</button>
        <button id="zoomReset" class="lg-zoom-reset" title="Reset zoom">↺</button>
      </div>
      <!-- Sidebar toggle -->
      <button id="sidebarToggleBtn" class="lg-sidebar-btn" onclick="toggleSidebar()">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M2 3.5A1.5 1.5 0 013.5 2h9A1.5 1.5 0 0114 3.5v9a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 012 12.5v-9zM3.5 3a.5.5 0 00-.5.5v9a.5.5 0 00.5.5H10V3H3.5zM11 13h1.5a.5.5 0 00.5-.5v-9a.5.5 0 00-.5-.5H11v10z"/></svg>
        Prenotazioni
        <?php if (!empty($reservations)): ?>
          <span class="lg-sidebar-badge" id="resBadgeTop"><?= count($reservations) ?></span>
        <?php endif; ?>
      </button>
      <!-- Nuova prenotazione -->
      <button class="btn btn-sm btn-primary" style="font-size:.75rem;padding:4px 10px" onclick="openNewResModal()">+ Nuova</button>
      <!-- Lettino extra -->
      <button class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:4px 10px" onclick="openAddExtraModal()" title="Aggiungi lettino extra">+ Lettino</button>
      <!-- Editor schema -->
      <a href="<?= url('/places/layout?date=' . urlencode($date)) ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:4px 10px" title="Editor schema lettini">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" style="margin-right:3px"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>Schema
      </a>
    </div>
  </div>

  <!-- ══ Mappa full-width ══ -->
  <div class="lg-body">
    <div class="lg-map lg-map-full" id="seatMap">
      <div class="lg-map-inner" id="mapInner">
<?php
/* ── Render dinamico da pos_row / pos_col ─────────────────── */
$areaIcon  = [];  // populated on first encounter: area_id => emoji
$iconPool  = ['☀️','⛱️','🌊','🏖️','⚓'];
$iconIdx   = 0;
$prevAreaId  = null;
$prevRowIdx  = null;
$zoneOpen    = false;
$rowIndices  = array_keys($mapGrid);

foreach ($rowIndices as $ri => $rowIdx):
    $rowSeats = $mapGrid[$rowIdx];
    $areaId   = $mapRowArea[$rowIdx];
    $areaName = $mapAreaNames[$areaId] ?? 'Zona';

    // Assegna icona area
    if (!isset($areaIcon[$areaId])) {
        $areaIcon[$areaId] = $iconPool[$iconIdx++ % count($iconPool)];
    }

    // Apertura / cambio zona (area diversa o salto > 1 di righe)
    $gapRows = ($prevRowIdx !== null) ? ($rowIdx - $prevRowIdx) : 0;
    $areaChanged = ($areaId !== $prevAreaId);

    if ($areaChanged || $gapRows > 1):
        if ($zoneOpen): ?></div><!-- /lg-zone --><?php endif;
        if ($gapRows > 1 && !$areaChanged): ?>
        <div class="lg-corridor"><span>— Corridoio —</span></div>
<?php   endif; ?>
        <div class="lg-zone">
          <div class="lg-zone-lbl"><?= $areaIcon[$areaId] ?> <strong><?= e($areaName) ?></strong></div>
<?php
        $zoneOpen = true;
    endif;
?>
          <div class="lg-row">
            <div class="lr-seats lr-seats-grid" style="grid-template-columns:repeat(<?= $mapNCols ?>,var(--seat-w))">
<?php   ksort($rowSeats);
        foreach ($rowSeats as $col => $p): ?>
              <div style="grid-column:<?= $col + 1 ?>">
                <?= seat($p['code'], $byCode) ?>
              </div>
<?php   endforeach; ?>
            </div>
          </div>
<?php
    $prevAreaId = $areaId;
    $prevRowIdx = $rowIdx;
endforeach;
if ($zoneOpen): ?></div><!-- /lg-zone --><?php endif;
?>

<?php if (!empty($mapUnpos)): ?>
        <div class="lg-zone">
          <div class="lg-zone-lbl">➕ <strong>Lettini non posizionati</strong></div>
          <div class="lg-row">
            <div class="lr-seats" style="flex-wrap:wrap;gap:var(--seat-gap)">
              <?php foreach ($mapUnpos as $_p) echo seat($_p['code'], $byCode); ?>
            </div>
          </div>
        </div>
<?php endif; ?>

      </div><!-- /lg-map-inner -->
    </div><!-- /lg-map -->
  </div><!-- /lg-body -->

  <!-- ── Barra selezione lettini (compare durante selezione da modal) ── -->
  <div id="seatSelectBar" class="seat-select-bar" style="display:none">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm1 11H9v-2h2v2zm0-4H9V7h2v2z"/></svg>
    <span class="ssb-hint">Clicca i lettini <strong>verdi</strong> sulla mappa</span>
    <span id="ssbCount" class="ssb-count">0 selezionati</span>
    <div class="ms-auto d-flex gap-2">
      <button class="ssb-btn ssb-confirm" onclick="confirmSeatSelection()">✓ Conferma</button>
      <button class="ssb-btn ssb-cancel"  onclick="cancelSeatSelection()">✕ Annulla</button>
    </div>
  </div>

</div><!-- /lg-page -->

<!-- ══ Sidebar prenotazioni ══ -->
<div id="resSidebar" class="res-sidebar">
  <div class="res-sidebar-hdr">
    <span class="res-sidebar-title">
      Prenotazioni
      <span class="res-sidebar-date"><?= e(date('d/m/Y', strtotime($date))) ?></span>
    </span>
    <?php if (!empty($reservations)): ?>
      <span class="res-count-badge"><?= count($reservations) ?></span>
    <?php endif; ?>
    <button class="res-sidebar-close" onclick="closeSidebar()" title="Chiudi">✕</button>
  </div>
  <div class="res-sidebar-body">
    <?php if (empty($reservations)): ?>
      <div class="lg-res-empty">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>Nessuna prenotazione</span>
      </div>
    <?php else: ?>
      <?php foreach ($reservations as $r): ?>
      <?php
        $cls = $statusCls[$r['status']] ?? 'res-s-attesa';
        $resData = htmlspecialchars(json_encode([
          'id'           => (int)$r['id'],
          'customer_id'  => (int)$r['customer_id'],
          'customer_name'=> $r['customer_name'],
          'usage_date'   => $r['usage_date'],
          'time_slot'    => $r['time_slot'],
          'people_count' => (int)$r['people_count'],
          'status'       => $r['status'],
          'total_amount' => (float)$r['total_amount'],
          'notes'        => $r['notes'] ?? '',
          'places_codes' => $r['places_codes'] ?? '',
          'places_ids'   => $r['places_ids'] ?? '',
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
      ?>
      <div class="lg-booking-card <?= $cls ?>" data-res-id="<?= (int)$r['id'] ?>">
        <div class="lbc-head">
          <div class="lbc-customer">
            <span class="lbc-name"><?= e($r['customer_name']) ?></span>
            <span class="lbc-code font-monospace"><?= e($r['reservation_code']) ?></span>
          </div>
          <span class="lbc-status-badge <?= $cls ?>"><?= e($statusLabel[$r['status']] ?? $r['status']) ?></span>
        </div>
        <div class="lbc-details">
          <span class="lbc-detail-item">
            <svg width="11" height="11" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="7" cy="7" r="5.5"/><polyline points="7,4 7,7 9.5,8.5"/></svg>
            <?= e($r['time_slot']) ?>
          </span>
          <span class="lbc-detail-item">
            <svg width="11" height="11" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="7" cy="4" r="2.5"/><path d="M2 13c0-2.76 2.24-5 5-5s5 2.24 5 5"/></svg>
            <?= (int)$r['people_count'] ?> pers.
          </span>
          <?php if ($r['total_amount'] > 0): ?>
          <span class="lbc-detail-item">
            <svg width="11" height="11" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="7" cy="7" r="5.5"/><line x1="7" y1="4" x2="7" y2="10"/><line x1="4.5" y1="5.5" x2="9.5" y2="5.5"/></svg>
            <?= money($r['total_amount']) ?>
          </span>
          <?php endif; ?>
        </div>
        <?php if ($r['places_codes']): ?>
        <div class="lbc-seats">
          <?php foreach (explode(', ', $r['places_codes']) as $pc): ?>
          <button class="lbc-seat-chip" onclick="highlightSeat('<?= e(trim($pc)) ?>')" title="Vai al lettino"><?= e(trim($pc)) ?></button>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="lbc-seats lbc-seats-none"><em>Nessun lettino abbinato</em></div>
        <?php endif; ?>
        <div class="lbc-actions">
          <button class="lbc-btn lbc-btn-edit" data-res="<?= $resData ?>" onclick="openEditResModal(JSON.parse(this.dataset.res))">✎ Modifica</button>
          <?php if ($r['status'] === 'confermata' || $r['status'] === 'in attesa'): ?>
          <button class="lbc-btn lbc-btn-danger" onclick="cancelReservation(<?= (int)$r['id'] ?>, '<?= e(addslashes($r['customer_name'])) ?>')">Cancella</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<div id="sidebarBackdrop" class="sidebar-backdrop" style="display:none" onclick="closeSidebar()"></div>

<!-- ══ Modal: aggiungi lettino extra ══ -->
<div class="modal fade" id="addExtraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Aggiungi lettino extra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addExtraForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold mb-1">Codice <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm font-monospace text-uppercase"
                   name="code" id="extraCode" placeholder="es. EX01" maxlength="32" required
                   pattern="[A-Za-z0-9_\-]+" oninput="this.value=this.value.toUpperCase()">
            <div class="form-text">Solo lettere e numeri. Es: EX01, VIP01, SPIAGGIA01</div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold mb-1">Area <span class="text-danger">*</span></label>
              <select class="form-select form-select-sm" name="area_id" required>
                <?php foreach ($poolAreas as $a): ?>
                <option value="<?= (int)$a['id'] ?>"><?= e($a['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold mb-1">Tipo</label>
              <select class="form-select form-select-sm" name="type">
                <option value="lettino">Lettino</option>
                <option value="sdraio">Sdraio</option>
                <option value="ombrellone">Ombrellone</option>
                <option value="tavolo">Tavolo</option>
                <option value="cabana">Cabana</option>
              </select>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Prezzo base (€)</label>
            <input type="number" class="form-control form-control-sm" name="base_price" value="0" min="0" step="0.50">
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Note</label>
            <input type="text" class="form-control form-control-sm" name="notes" placeholder="Opzionale">
          </div>
          <div id="addExtraErr" class="alert alert-danger py-2 mt-2 d-none small"></div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary btn-sm" id="addExtraSubmitBtn">Aggiungi lettino</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Modal: nuova prenotazione ══ -->
<div class="modal fade" id="newResModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Nuova prenotazione</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearSelectedSeat()"></button>
      </div>
      <form id="newResForm">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="new_reservation">
        <div class="modal-body">
          <!-- Lettini -->
          <div class="mb-2">
            <div class="form-sel-seat-label mb-1">
              <span class="fw-semibold small">Lettini</span>
              <button type="button" class="btn-sel-seat" onclick="startSeatSelection('new')">+ Seleziona dalla mappa ↗</button>
            </div>
            <div id="newSeatChips" class="sel-seat-display"></div>
          </div>
          <!-- Cliente -->
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Cliente <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm" name="customer_id" id="newCustomerId" required>
              <option value="">Seleziona cliente…</option>
              <?php foreach ($customers as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?><?= $c['phone'] ? ' · '.e($c['phone']) : '' ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Periodo -->
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Periodo</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="date" class="form-control form-control-sm" name="usage_date" id="newDateFrom" value="<?= e($date) ?>" required>
              <span class="text-muted small">→</span>
              <input type="date" class="form-control form-control-sm" name="usage_date_to" id="newDateTo" value="<?= e($date) ?>">
            </div>
          </div>
          <!-- Fascia + persone -->
          <div class="d-flex gap-2 mb-2">
            <div class="flex-fill">
              <label class="form-label small fw-semibold mb-1">Fascia oraria</label>
              <select class="form-select form-select-sm" name="time_slot">
                <option value="intera giornata">Intera giornata</option>
                <option value="mattina">Mattina</option>
                <option value="pomeriggio">Pomeriggio</option>
              </select>
            </div>
            <div style="width:80px">
              <label class="form-label small fw-semibold mb-1">Persone</label>
              <input type="number" class="form-control form-control-sm" name="people_count" min="1" value="1">
            </div>
          </div>
          <!-- Stato + importo -->
          <div class="d-flex gap-2 mb-2">
            <div class="flex-fill">
              <label class="form-label small fw-semibold mb-1">Stato</label>
              <select class="form-select form-select-sm" name="res_status">
                <option value="confermata">Confermata</option>
                <option value="in attesa">In attesa</option>
              </select>
            </div>
            <div style="width:110px">
              <label class="form-label small fw-semibold mb-1">Importo €</label>
              <input type="number" class="form-control form-control-sm" name="total_amount" step="0.01" min="0" value="0">
            </div>
          </div>
          <!-- Note -->
          <div class="mb-1">
            <label class="form-label small fw-semibold mb-1">Note</label>
            <input type="text" class="form-control form-control-sm" name="notes" placeholder="Opzionale">
          </div>
          <div id="newResErr" class="alert alert-danger py-1 small mt-2 d-none"></div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="clearSelectedSeat()">Annulla</button>
          <button type="submit" class="btn btn-primary btn-sm" id="newResSubmitBtn">Crea prenotazione</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Modal: modifica prenotazione ══ -->
<div class="modal fade" id="editResModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Modifica prenotazione</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearSelectedSeat()"></button>
      </div>
      <form id="editResForm">
        <?= csrf_field() ?>
        <input type="hidden" name="reservation_id" id="editResId">
        <div class="modal-body">
          <!-- Lettini -->
          <div class="mb-2">
            <div class="form-sel-seat-label mb-1">
              <span class="fw-semibold small">Lettini</span>
              <span id="editSeatsChangedBadge" class="badge text-bg-warning" style="display:none;font-size:.6rem">modificati</span>
              <button type="button" class="btn-sel-seat" onclick="startSeatSelection('edit')">↻ Cambia posti dalla mappa</button>
            </div>
            <div id="editSeatChips" class="sel-seat-display"></div>
          </div>
          <!-- Cliente -->
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Cliente <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm" name="customer_id" id="editCustomerId" required>
              <option value="">Seleziona cliente…</option>
              <?php foreach ($customers as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?><?= $c['phone'] ? ' · '.e($c['phone']) : '' ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Periodo -->
          <div class="mb-2">
            <label class="form-label small fw-semibold mb-1">Data utilizzo</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="date" class="form-control form-control-sm" name="usage_date" id="editDateFrom" required>
              <span class="text-muted small">→</span>
              <input type="date" class="form-control form-control-sm" name="usage_date_to" id="editDateTo">
            </div>
          </div>
          <!-- Fascia + persone -->
          <div class="d-flex gap-2 mb-2">
            <div class="flex-fill">
              <label class="form-label small fw-semibold mb-1">Fascia oraria</label>
              <select class="form-select form-select-sm" name="time_slot" id="editTimeSlot">
                <option value="intera giornata">Intera giornata</option>
                <option value="mattina">Mattina</option>
                <option value="pomeriggio">Pomeriggio</option>
              </select>
            </div>
            <div style="width:80px">
              <label class="form-label small fw-semibold mb-1">Persone</label>
              <input type="number" class="form-control form-control-sm" name="people_count" id="editPeople" min="1" value="1">
            </div>
          </div>
          <!-- Stato + importo -->
          <div class="d-flex gap-2 mb-2">
            <div class="flex-fill">
              <label class="form-label small fw-semibold mb-1">Stato</label>
              <select class="form-select form-select-sm" name="res_status" id="editStatus">
                <option value="confermata">Confermata</option>
                <option value="in attesa">In attesa</option>
                <option value="completata">Completata</option>
              </select>
            </div>
            <div style="width:110px">
              <label class="form-label small fw-semibold mb-1">Importo €</label>
              <input type="number" class="form-control form-control-sm" name="total_amount" id="editAmount" step="0.01" min="0" value="0">
            </div>
          </div>
          <!-- Note -->
          <div class="mb-1">
            <label class="form-label small fw-semibold mb-1">Note</label>
            <input type="text" class="form-control form-control-sm" name="notes" id="editNotes" placeholder="Opzionale">
          </div>
          <div id="editResErr" class="alert alert-danger py-1 small mt-2 d-none"></div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-outline-danger btn-sm me-auto" id="editCancelResBtn">Cancella prenotazione</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="clearSelectedSeat()">Chiudi</button>
          <button type="submit" class="btn btn-primary btn-sm" id="editResSubmitBtn">Salva modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Modal: dettaglio lettino ══ -->
<div class="modal fade" id="seatModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="seatModalTitle">Lettino</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="seatModalBody">
        <div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>
      </div>
      <div class="modal-footer py-2" id="seatModalFooter">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ Modal: assegna ══ -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Assegna — <span id="assignCode"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="assignForm">
        <div class="modal-body">
          <input type="hidden" name="place_id" id="assignPlaceId">
          <label class="form-label fw-semibold small">Codice Card <span class="text-danger">*</span></label>
          <input type="text" class="form-control font-monospace" name="card_code"
                 placeholder="Scansiona o digita il codice…" autocomplete="off" required>
          <div id="assignErr" class="alert alert-danger py-2 small mt-3 d-none"></div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-success btn-sm">Assegna</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  const CSRF      = <?= json_encode(csrf_token(), JSON_THROW_ON_ERROR) ?>;
  const MAP_NROWS = <?= (int)($mapNRows ?: 11) ?>;
  const MAP_NCOLS = <?= (int)($mapNCols ?: 16) ?>;
  const MAP_URL   = <?= json_encode(url('/api/places/map.php'), JSON_THROW_ON_ERROR) ?>;
  const BOOK_URL = <?= json_encode(url('/api/places/book.php'), JSON_THROW_ON_ERROR) ?>;
  const EDIT_URL = <?= json_encode(url('/api/places/edit-reservation.php'), JSON_THROW_ON_ERROR) ?>;
  const CANC_URL  = <?= json_encode(url('/api/places/cancel-reservation.php'), JSON_THROW_ON_ERROR) ?>;
  const EXTRA_URL = <?= json_encode(url('/api/places/create-extra.php'), JSON_THROW_ON_ERROR) ?>;
  const PLACES_URL = <?= json_encode(url('/places'), JSON_THROW_ON_ERROR) ?>;
  let currentDate  = <?= json_encode($date, JSON_THROW_ON_ERROR) ?>;

  // ── Bootstrap modals ──
  const seatModal   = new bootstrap.Modal(document.getElementById('seatModal'));
  const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
  const newResModal   = new bootstrap.Modal(document.getElementById('newResModal'));
  const editResModal  = new bootstrap.Modal(document.getElementById('editResModal'));
  const addExtraModal = new bootstrap.Modal(document.getElementById('addExtraModal'));

  // ── Calibrazione + Zoom ──
  let baseH = 30, baseW = 50, mapZoom = 1.0;
  const ZOOM_STEP = 0.15, ZOOM_MIN = 0.4, ZOOM_MAX = 2.5;

  function calibrate() {
    const mapEl = document.getElementById('seatMap');
    const avW   = mapEl ? mapEl.clientWidth  : (window.innerWidth  - 40);
    const avH   = mapEl ? mapEl.clientHeight : (window.innerHeight - 120);
    // Altezza: 14 file si adattano senza scroll verticale
    baseH = Math.max(16, Math.floor((avH - 92) / MAP_NROWS));
    // Larghezza: distribui le colonne su tutta la larghezza disponibile
    const gapTotal = (MAP_NCOLS - 1) * Math.max(2, Math.round(3 * mapZoom));
    const targetW  = Math.max(0, avW - gapTotal - 24);
    baseW = Math.max(baseH, Math.floor(targetW / MAP_NCOLS));
    applyZoom();
  }

  function applyZoom() {
    const sH = Math.round(baseH * mapZoom);
    const sW = Math.round(baseW * mapZoom);
    const r  = document.documentElement;
    r.style.setProperty('--seat-h',   sH + 'px');
    r.style.setProperty('--seat-w',   sW + 'px');
    r.style.setProperty('--seat-fs',  Math.max(0.38, sH * 0.026) + 'rem');
    r.style.setProperty('--seat-gap', Math.max(2, Math.round(3 * mapZoom)) + 'px');
    r.style.setProperty('--umb-fs',   Math.max(0.6, sH * 0.038) + 'rem');
    document.getElementById('zoomVal').textContent = Math.round(mapZoom * 100) + '%';
  }

  function setZoom(z) {
    mapZoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, z));
    applyZoom();
  }

  calibrate();
  window.addEventListener('resize', calibrate);
  document.getElementById('zoomIn').addEventListener('click',    () => setZoom(mapZoom + ZOOM_STEP));
  document.getElementById('zoomOut').addEventListener('click',   () => setZoom(mapZoom - ZOOM_STEP));
  document.getElementById('zoomReset').addEventListener('click', () => setZoom(1.0));

  document.getElementById('seatMap').addEventListener('wheel', function(e) {
    if (!e.ctrlKey) return;
    e.preventDefault();
    setZoom(mapZoom + (e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP));
  }, { passive: false });

  // ── Summary badges ──
  updateSummary(<?= json_encode($placesForJs, JSON_THROW_ON_ERROR) ?>);

  // ── Date picker ──
  document.getElementById('mapDate').addEventListener('change', function () {
    currentDate = this.value;
    window.location.href = PLACES_URL + '?date=' + encodeURIComponent(currentDate);
  });

  // ─────────────────────────────────────────
  // SIDEBAR
  // ─────────────────────────────────────────
  let sidebarOpen = false;

  window.toggleSidebar = function () {
    sidebarOpen ? closeSidebar() : openSidebar();
  };

  function openSidebar() {
    sidebarOpen = true;
    document.getElementById('resSidebar').classList.add('open');
    document.getElementById('sidebarBackdrop').style.display = '';
    document.getElementById('sidebarToggleBtn').classList.add('active');
    document.querySelector('.lg-body').classList.add('sidebar-open');
    setTimeout(calibrate, 270);
  }

  window.closeSidebar = function () {
    sidebarOpen = false;
    document.getElementById('resSidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').style.display = 'none';
    document.getElementById('sidebarToggleBtn').classList.remove('active');
    document.querySelector('.lg-body').classList.remove('sidebar-open');
    setTimeout(calibrate, 270);
  };

  // ─────────────────────────────────────────
  // SELEZIONE LETTINI (condivisa tra modal)
  // ─────────────────────────────────────────
  let seatSelectMode    = false;
  let seatSelectContext = null; // 'new' | 'edit'
  let selectedSeats     = [];   // [{id, code, btn}]

  // Contesto edit
  let editResCurrentData    = null;
  let editSeatsChanged      = false;
  let editOriginalCodes     = '';

  window.clearSelectedSeat = function () {
    selectedSeats.forEach(s => s.btn.classList.remove('seat-selected'));
    selectedSeats = [];
  };

  window.removeSelectedSeat = function (id) {
    const idx = selectedSeats.findIndex(s => s.id === id);
    if (idx === -1) return;
    selectedSeats[idx].btn.classList.remove('seat-selected');
    selectedSeats.splice(idx, 1);
    renderChips(seatSelectContext ?? 'new');
  };

  function renderChips(ctx) {
    const el = document.getElementById(ctx === 'edit' ? 'editSeatChips' : 'newSeatChips');
    if (!el) return;
    if (selectedSeats.length === 0) {
      el.innerHTML = '';
      return;
    }
    el.innerHTML = selectedSeats.map(s =>
      `<span class="lbc-seat-chip sel-chip">${s.code} <button type="button" class="sel-chip-x" onclick="removeSelectedSeat('${s.id}')">×</button></span>`
    ).join('') +
    `<button type="button" class="sel-seat-clear" onclick="clearSelectedSeat();renderChipsPublic('${ctx}')">✕ tutti</button>`;
  }

  window.renderChipsPublic = function(ctx) { renderChips(ctx); };

  // Avvia selezione da un modal
  window.startSeatSelection = function (ctx) {
    seatSelectContext = ctx;
    clearSelectedSeat();
    if (ctx === 'new')  newResModal.hide();
    else                editResModal.hide();
    seatSelectMode = true;
    document.getElementById('seatMap').classList.add('map-select-mode');
    document.getElementById('seatSelectBar').style.display = '';
    document.getElementById('ssbCount').textContent = '0 selezionati';
  };

  window.confirmSeatSelection = function () {
    document.getElementById('seatSelectBar').style.display = 'none';
    seatSelectMode = false;
    document.getElementById('seatMap').classList.remove('map-select-mode');
    renderChips(seatSelectContext);
    if (seatSelectContext === 'edit') {
      editSeatsChanged = true;
      const badge = document.getElementById('editSeatsChangedBadge');
      if (badge) badge.style.display = '';
      editResModal.show();
    } else {
      newResModal.show();
    }
    seatSelectContext = null;
  };

  window.cancelSeatSelection = function () {
    clearSelectedSeat();
    document.getElementById('seatSelectBar').style.display = 'none';
    seatSelectMode = false;
    document.getElementById('seatMap').classList.remove('map-select-mode');
    if (seatSelectContext === 'edit') {
      editSeatsChanged = false;
      renderEditSeatDisplay();
      editResModal.show();
    } else {
      newResModal.show();
    }
    seatSelectContext = null;
  };

  // Click sui lettini
  document.getElementById('seatMap').addEventListener('click', function (e) {
    const btn = e.target.closest('.seat[data-id]');
    if (!btn) return;

    if (seatSelectMode) {
      const cls    = [...btn.classList].find(c => c.startsWith('seat-') && !['seat-highlight','seat-selected'].includes(c));
      const status = cls ? cls.replace('seat-', '') : '';
      if (status !== 'disponibile') {
        return; // solo liberi
      }
      const id   = btn.dataset.id;
      const code = btn.dataset.code;
      const idx  = selectedSeats.findIndex(s => s.id === id);
      if (idx !== -1) {
        btn.classList.remove('seat-selected');
        selectedSeats.splice(idx, 1);
      } else {
        btn.classList.add('seat-selected');
        selectedSeats.push({ id, code, btn });
      }
      document.getElementById('ssbCount').textContent = selectedSeats.length + ' selezionati';
      return;
    }

    openDetail(btn.dataset.id, btn.dataset.code);
  });

  // ─────────────────────────────────────────
  // NUOVA PRENOTAZIONE
  // ─────────────────────────────────────────
  window.openNewResModal = function () {
    clearSelectedSeat();
    document.getElementById('newResForm').reset();
    document.getElementById('newDateFrom').value = currentDate;
    document.getElementById('newDateTo').value   = currentDate;
    document.getElementById('newSeatChips').innerHTML = '';
    document.getElementById('newResErr').classList.add('d-none');
    newResModal.show();
  };

  document.getElementById('newResForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('newResSubmitBtn');
    const err = document.getElementById('newResErr');
    btn.disabled = true; btn.textContent = '…';
    err.classList.add('d-none');

    const fd   = new FormData(this);
    const data = Object.fromEntries(fd);
    data._csrf = CSRF;

    const hasSeats = selectedSeats.length > 0;
    const apiUrl   = hasSeats ? BOOK_URL : PLACES_URL;
    const params   = new URLSearchParams();
    Object.entries(data).forEach(([k, v]) => params.append(k, v));
    if (hasSeats) {
      params.set('date', data.usage_date);
      selectedSeats.forEach(s => params.append('place_ids[]', s.id));
    }

    fetch(apiUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params.toString() })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          newResModal.hide();
          clearSelectedSeat();
          window.location.href = PLACES_URL + '?date=' + encodeURIComponent(data.usage_date);
        } else {
          err.textContent = resp.error ?? resp.message ?? 'Errore.';
          err.classList.remove('d-none');
          btn.disabled = false; btn.textContent = 'Crea prenotazione';
        }
      })
      .catch(() => {
        err.textContent = 'Errore di rete.';
        err.classList.remove('d-none');
        btn.disabled = false; btn.textContent = 'Crea prenotazione';
      });
  });

  // ─────────────────────────────────────────
  // MODIFICA PRENOTAZIONE
  // ─────────────────────────────────────────
  window.openEditResModal = function (data) {
    editResCurrentData = data;
    editSeatsChanged   = false;
    editOriginalCodes  = data.places_codes || '';
    clearSelectedSeat();

    document.getElementById('editResId').value      = data.id;
    document.getElementById('editCustomerId').value = data.customer_id;
    document.getElementById('editDateFrom').value   = data.usage_date;
    document.getElementById('editDateTo').value     = data.usage_date;
    document.getElementById('editTimeSlot').value   = data.time_slot;
    document.getElementById('editPeople').value     = data.people_count;
    document.getElementById('editStatus').value     = data.status;
    document.getElementById('editAmount').value     = data.total_amount;
    document.getElementById('editNotes').value      = data.notes;
    document.getElementById('editResErr').classList.add('d-none');
    document.getElementById('editSeatsChangedBadge').style.display = 'none';

    renderEditSeatDisplay();
    editResModal.show();
    if (document.getElementById('resSidebar').classList.contains('open')) {
      closeSidebar();
    }
  };

  function renderEditSeatDisplay() {
    const el = document.getElementById('editSeatChips');
    if (!el) return;
    if (editSeatsChanged && selectedSeats.length > 0) {
      renderChips('edit');
    } else if (editOriginalCodes) {
      el.innerHTML = editOriginalCodes.split(', ').filter(Boolean).map(c =>
        `<span class="lbc-seat-chip">${c}</span>`
      ).join('');
    } else {
      el.innerHTML = '<em class="text-muted small">Nessun lettino abbinato</em>';
    }
  }

  document.getElementById('editResForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('editResSubmitBtn');
    const err = document.getElementById('editResErr');
    btn.disabled = true; btn.textContent = '…';
    err.classList.add('d-none');

    const fd   = new FormData(this);
    const data = Object.fromEntries(fd);
    data._csrf = CSRF;

    const params = new URLSearchParams();
    Object.entries(data).forEach(([k, v]) => params.append(k, v));
    if (editSeatsChanged && selectedSeats.length > 0) {
      selectedSeats.forEach(s => params.append('place_ids[]', s.id));
    }

    fetch(EDIT_URL, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params.toString() })
      .then(r => r.text())
      .then(text => {
        let resp;
        try { resp = JSON.parse(text); } catch (_) { throw new Error('Risposta non valida dal server: ' + text.substring(0, 120)); }
        if (resp.success) {
          editResModal.hide();
          clearSelectedSeat();
          window.location.href = PLACES_URL + '?date=' + encodeURIComponent(data.usage_date);
        } else {
          err.textContent = resp.error ?? resp.message ?? 'Errore.';
          err.classList.remove('d-none');
          btn.disabled = false; btn.textContent = 'Salva modifiche';
        }
      })
      .catch(ex => {
        err.textContent = ex.message || 'Errore di rete.';
        err.classList.remove('d-none');
        btn.disabled = false; btn.textContent = 'Salva modifiche';
      });
  });

  // Pulsante "Cancella prenotazione" dentro modal edit
  document.getElementById('editCancelResBtn').addEventListener('click', function () {
    if (!editResCurrentData) return;
    const name = editResCurrentData.customer_name || 'questa prenotazione';
    if (!confirm('Cancellare la prenotazione di ' + name + '?')) return;
    editResModal.hide();
    doCancel(editResCurrentData.id);
  });

  // ─────────────────────────────────────────
  // CANCELLAZIONE
  // ─────────────────────────────────────────
  window.cancelReservation = function (rid, name) {
    if (!confirm('Cancellare la prenotazione di ' + name + '?')) return;
    doCancel(rid);
  };

  function doCancel(rid) {
    fetch(CANC_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ reservation_id: rid, _csrf: CSRF }).toString()
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        const card = document.querySelector(`.lg-booking-card[data-res-id="${rid}"]`);
        if (card) { card.style.opacity = '0'; card.style.transform = 'translateX(20px)'; setTimeout(() => card.remove(), 250); }
        refreshMap();
      } else { alert(d.error ?? 'Errore.'); }
    })
    .catch(() => alert('Errore di rete.'));
  }

  // ─────────────────────────────────────────
  // DETTAGLIO LETTINO (click su mappa)
  // ─────────────────────────────────────────
  function openDetail(id, code) {
    document.getElementById('seatModalTitle').textContent = 'Lettino ' + code;
    document.getElementById('seatModalBody').innerHTML =
      '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';
    document.getElementById('seatModalFooter').innerHTML =
      '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>';
    seatModal.show();
    fetch(MAP_URL + '?date=' + encodeURIComponent(currentDate) + '&place_id=' + encodeURIComponent(id))
      .then(r => r.json()).then(d => { if (d.success && d.place) renderDetail(d.place); })
      .catch(() => { document.getElementById('seatModalBody').innerHTML = '<p class="text-danger small">Errore caricamento.</p>'; });
  }

  function renderDetail(p) {
    const badgeCls = {
      disponibile: 'bg-success', prenotato: 'text-dark" style="background:#e8a020',
      'prenotato-attesa': 'text-dark" style="background:#64b5f6',
      'prenotato-confermato': 'text-dark" style="background:#e8a020',
      'prenotato-attesa-mattina': 'text-dark" style="background:#64b5f6',
      'prenotato-attesa-pomeriggio': 'text-dark" style="background:#64b5f6',
      'prenotato-confermato-mattina': 'text-dark" style="background:#e8a020',
      'prenotato-confermato-pomeriggio': 'text-dark" style="background:#e8a020',
      occupato: 'bg-danger', 'occupato-mattina': 'bg-danger', 'occupato-pomeriggio': 'bg-danger',
      manutenzione: 'bg-secondary', bloccato: 'bg-dark',
    };
    const statusLabel = p.day_status.startsWith('prenotato-confermato') ? 'Prenotato (confermato)'
      : p.day_status.startsWith('prenotato-attesa') ? 'Prenotato (in attesa)'
      : p.day_status.startsWith('occupato') ? 'Occupato'
      : p.day_status;
    let body = `<div class="d-flex align-items-center gap-2 mb-3">
      <span class="fw-bold" style="font-size:1.3rem;font-family:'Bricolage Grotesque',sans-serif;color:var(--accent)">${esc(p.code)}</span>
      <span class="badge ${badgeCls[p.day_status] ?? 'bg-secondary'}">${esc(statusLabel)}</span>
      <span class="ms-auto text-muted small">${esc(p.area_name)}</span>
    </div>`;
    if (p.day_status.startsWith('occupato') && p.entry_customer_name) {
      body += `<div class="lg-info-card lg-info-occ">
        <div class="fw-semibold">${esc(p.entry_customer_name)}</div>
        <div class="small text-muted">Card: <span class="font-monospace fw-bold">${esc(p.entry_card_code ?? '—')}</span></div>
        ${p.entry_customer_phone ? `<div class="small text-muted">Tel: ${esc(p.entry_customer_phone)}</div>` : ''}
        ${p.checkin_at ? `<div class="small text-muted">Check-in: ${esc(p.checkin_at.substring(11,16))}</div>` : ''}
      </div>`;
    }
    if (p.day_status.startsWith('prenotato') && p.res_customer_name) {
      body += `<div class="lg-info-card lg-info-res">
        <div class="fw-semibold">${esc(p.res_customer_name)}</div>
        <div class="small text-muted">Cod.: <span class="font-monospace fw-bold">${esc(p.reservation_code ?? '—')}</span></div>
        ${p.res_customer_phone ? `<div class="small text-muted">Tel: ${esc(p.res_customer_phone)}</div>` : ''}
        ${p.time_slot ? `<div class="small text-muted">Fascia: ${esc(p.time_slot)}</div>` : ''}
      </div>`;
    }
    if (p.day_status === 'disponibile') {
      body += `<p class="text-muted small mb-0">Nessuna assegnazione per questa data.</p>`;
    }
    document.getElementById('seatModalBody').innerHTML = body;

    let footer = '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>';
    if (p.day_status === 'disponibile') {
      footer += `<button class="btn btn-success btn-sm" onclick="openAssign(${p.id},'${esc(p.code)}')">Assegna presente</button>`;
      footer += `<button class="btn btn-warning btn-sm" onclick="seatModal.hide();openNewResModal()">Prenota</button>`;
    } else if (p.day_status.startsWith('prenotato')) {
      footer += `<button class="btn btn-success btn-sm" onclick="openAssign(${p.id},'${esc(p.code)}')">Check-in</button>`;
      footer += `<button class="btn btn-outline-danger btn-sm" onclick="doCancelPlace(${p.reservation_id})">Cancella prenotazione</button>`;
    } else if (p.day_status.startsWith('occupato')) {
      footer += `<button class="btn btn-outline-danger btn-sm" onclick="doRelease(${p.id})">Libera lettino</button>`;
    }
    document.getElementById('seatModalFooter').innerHTML = footer;
  }

  window.openAssign = function (id, code) {
    seatModal.hide();
    document.getElementById('assignPlaceId').value = id;
    document.getElementById('assignCode').textContent = code;
    document.getElementById('assignErr').classList.add('d-none');
    document.getElementById('assignForm').reset();
    document.getElementById('assignPlaceId').value = id;
    assignModal.show();
    setTimeout(() => document.querySelector('#assignForm [name=card_code]').focus(), 300);
  };

  window.doRelease = function (id) {
    if (!confirm('Liberare il lettino?')) return;
    seatModal.hide();
    apiPost(<?= json_encode(url('/api/places/release.php'), JSON_THROW_ON_ERROR) ?>, { place_id: id });
  };

  window.doCancelPlace = function (rid) {
    if (!confirm('Cancellare la prenotazione?')) return;
    seatModal.hide();
    doCancel(rid);
  };

  document.getElementById('assignForm').addEventListener('submit', function (e) {
    e.preventDefault();
    apiPost(<?= json_encode(url('/api/places/assign.php'), JSON_THROW_ON_ERROR) ?>, Object.fromEntries(new FormData(this)), 'assignErr');
  });

  function apiPost(url, data, errField) {
    data._csrf = CSRF;
    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(data).toString() })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          assignModal.hide();
          window.location.href = PLACES_URL + '?date=' + encodeURIComponent(currentDate);
        } else {
          const msg = resp.error ?? resp.message ?? 'Errore.';
          if (errField) { const el = document.getElementById(errField); el.textContent = msg; el.classList.remove('d-none'); }
          else alert(msg);
        }
      })
      .catch(() => alert('Errore di rete.'));
  }

  // ─────────────────────────────────────────
  // REFRESH MAPPA
  // ─────────────────────────────────────────
  function refreshMap() {
    fetch(MAP_URL + '?date=' + encodeURIComponent(currentDate))
      .then(r => r.json()).then(d => { if (d.success) { applyStatuses(d.places); updateSummary(d.places); } });
  }

  function applyStatuses(places) {
    const all = ['disponibile',
      'prenotato','prenotato-attesa','prenotato-confermato',
      'prenotato-attesa-mattina','prenotato-attesa-pomeriggio',
      'prenotato-confermato-mattina','prenotato-confermato-pomeriggio',
      'occupato','occupato-mattina','occupato-pomeriggio',
      'manutenzione','bloccato','liberato','nd'];
    places.forEach(p => {
      const btn = document.querySelector(`.seat[data-id="${p.id}"]`);
      if (!btn) return;
      all.forEach(s => btn.classList.remove('seat-' + s));
      btn.classList.add('seat-' + p.day_status);
      btn.title = (btn.dataset.code ?? '') + ((p.en ?? p.re) ? ' · ' + (p.en ?? p.re) : '');
    });
  }

  function updateSummary(places) {
    const c = {};
    places.forEach(p => { c[p.day_status] = (c[p.day_status] ?? 0) + 1; });
    const liberi   = c['disponibile'] ?? 0;
    const attesa   = (c['prenotato-attesa'] ?? 0) + (c['prenotato-attesa-mattina'] ?? 0) + (c['prenotato-attesa-pomeriggio'] ?? 0);
    const conferm  = (c['prenotato-confermato'] ?? 0) + (c['prenotato-confermato-mattina'] ?? 0) + (c['prenotato-confermato-pomeriggio'] ?? 0) + (c['prenotato'] ?? 0);
    const occupati = (c['occupato'] ?? 0) + (c['occupato-mattina'] ?? 0) + (c['occupato-pomeriggio'] ?? 0);
    const el = document.getElementById('mapSummary');
    el.innerHTML = '';
    if (liberi)   el.innerHTML += `<span class="badge px-2 py-1" style="font-size:.72rem;background:var(--good);color:#fff">Liberi <strong>${liberi}</strong></span>`;
    if (attesa)   el.innerHTML += `<span class="badge px-2 py-1" style="font-size:.72rem;background:#64b5f6;color:#0c2d5a">In attesa <strong>${attesa}</strong></span>`;
    if (conferm)  el.innerHTML += `<span class="badge px-2 py-1" style="font-size:.72rem;background:#e8a020;color:#1c0e00">Confermati <strong>${conferm}</strong></span>`;
    if (occupati) el.innerHTML += `<span class="badge px-2 py-1" style="font-size:.72rem;background:var(--bad);color:#fff">Occupati <strong>${occupati}</strong></span>`;
  }

  function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  window.highlightSeat = function (code) {
    const btn = document.querySelector(`.seat[data-code="${code}"]`);
    if (!btn) return;
    btn.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
    btn.classList.add('seat-highlight');
    setTimeout(() => btn.classList.remove('seat-highlight'), 2000);
  };

  // ─────────────────────────────────────────
  // LETTINO EXTRA
  // ─────────────────────────────────────────
  window.openAddExtraModal = function () {
    document.getElementById('addExtraForm').reset();
    document.getElementById('addExtraErr').classList.add('d-none');
    document.getElementById('addExtraSubmitBtn').disabled = false;
    document.getElementById('addExtraSubmitBtn').textContent = 'Aggiungi lettino';
    // Suggerisci il prossimo codice EX libero
    const existing = new Set([...document.querySelectorAll('.seat[data-code]')].map(b => b.dataset.code));
    let n = 1;
    while (existing.has('EX' + String(n).padStart(2, '0'))) n++;
    document.getElementById('extraCode').value = 'EX' + String(n).padStart(2, '0');
    addExtraModal.show();
  };

  document.getElementById('addExtraForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('addExtraSubmitBtn');
    const err = document.getElementById('addExtraErr');
    btn.disabled = true; btn.textContent = '…';
    err.classList.add('d-none');
    const params = new URLSearchParams(new FormData(this));
    params.set('_csrf', CSRF);
    fetch(EXTRA_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        addExtraModal.hide();
        window.location.href = PLACES_URL + '?date=' + encodeURIComponent(currentDate);
      } else {
        err.textContent = resp.error ?? 'Errore.';
        err.classList.remove('d-none');
        btn.disabled = false; btn.textContent = 'Aggiungi lettino';
      }
    })
    .catch(() => {
      err.textContent = 'Errore di rete.';
      err.classList.remove('d-none');
      btn.disabled = false; btn.textContent = 'Aggiungi lettino';
    });
  });

})();
</script>
