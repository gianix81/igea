<?php
$itGiorni  = ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'];
$itMesi    = ['','gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre'];
$oggi      = $itGiorni[(int)date('w')] . ' ' . (int)date('j') . ' ' . $itMesi[(int)date('n')] . ' ' . date('Y');
$user      = current_user();
$h         = (int)date('H');
$saluto    = $h < 12 ? 'Buongiorno' : ($h < 18 ? 'Buon pomeriggio' : 'Buonasera');
$firstName = $user ? explode(' ', trim($user['name']))[0] : '';
?>
<style>
/* ── Dashboard: zero scroll, fill viewport ─────────────────── */
body.db-active { overflow: hidden !important; }
body.db-active .topbar { position: relative !important; }
body.db-active main.app {
  display: flex; flex-direction: column;
  height: calc(100dvh - 57px);
  overflow: hidden;
}

.db-wrap {
  flex: 1; min-height: 0;
  padding: 16px 18px 12px;
  display: flex; flex-direction: column;
  gap: 14px;
}

/* ── ROW 1: Greeting ──────────────────────────────────────── */
.db-greeting {
  display: flex; align-items: center; justify-content: space-between;
  flex-shrink: 0;
}
.db-greeting-name {
  font-family:'Bricolage Grotesque',sans-serif;
  font-size: 28px; font-weight: 800; color: var(--text); line-height: 1.1;
}
.db-greeting-date { font-size: 12px; color: var(--muted); margin-top: 2px; }
.db-clock {
  font-family:'Bricolage Grotesque',sans-serif;
  font-size: 44px; font-weight: 800; color: var(--accent); letter-spacing: -.02em;
}

/* ── ROW 2: 3 tiles ───────────────────────────────────────── */
.db-tiles {
  display: grid; grid-template-columns: repeat(3,1fr);
  gap: 14px; flex-shrink: 0;
}
.db-tile {
  border-radius: 16px; padding: 18px 20px;
  display: flex; flex-direction: column;
  text-decoration: none; position: relative; overflow: hidden;
  transition: transform .15s, box-shadow .15s;
}
.db-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,.22); }
.db-tile::before {
  content:''; position:absolute; top:-16px; right:-16px;
  width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,.07);
}
.db-tile-piscina { background: linear-gradient(135deg,#0b3e50,#0e7e9b); }
.db-tile-bar     { background: linear-gradient(135deg,#0e3b5c,#17b3c4); }
.db-tile-risto   { background: linear-gradient(135deg,#5c3a00,#cc8800); }

.db-tile-head  { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.db-tile-icon  { font-size:26px; }
.db-tile-title { font-family:'Bricolage Grotesque',sans-serif; font-size:20px; font-weight:800; color:#fff; }

.db-tile-stats { display:flex; }
.db-tile-stat  { flex:1; text-align:center; padding:0 6px; border-left:1px solid rgba(255,255,255,.13); }
.db-tile-stat:first-child { border-left:none; padding-left:0; }
.db-tile-stat-v { font-family:'Bricolage Grotesque',sans-serif; font-size:30px; font-weight:800; color:#fff; line-height:1; }
.db-tile-stat-l { font-size:11px; font-weight:700; color:rgba(255,255,255,.65); text-transform:uppercase; letter-spacing:.05em; margin-top:4px; }
.db-tile-cta { margin-top:10px; font-size:11px; font-weight:700; color:rgba(255,255,255,.5); transition:color .15s; }
.db-tile:hover .db-tile-cta { color:rgba(255,255,255,.9); }

/* ── ROW 3: Bottom — sinistra / centro / destra ───────────── */
.db-bottom {
  flex: 1; min-height: 0;
  display: grid;
  grid-template-columns: 248px 1fr 320px;
  gap: 14px;
}

.db-panel {
  background:var(--surface); border:1px solid var(--border);
  border-radius:13px; padding:12px 14px;
  display:flex; flex-direction:column; overflow:hidden; min-height:0;
}
.db-panel-lbl {
  font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.09em;
  color:var(--muted); margin-bottom:8px; flex-shrink:0;
  display:flex; align-items:center; gap:7px;
}
.db-panel-lbl::after { content:''; flex:1; height:1px; background:var(--border); }

/* ── Accesso rapido (sinistra) ────────────────────────────── */
.db-qa {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column; justify-content: space-between;
  gap: 0;
}
.db-qa-group { display: flex; flex-direction: column; gap: 4px; }
.db-qa-section {
  font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.09em;
  color:var(--muted-2); padding:0 3px 4px; flex-shrink:0;
}
.db-qa-item {
  display:flex; align-items:center; gap:10px; padding:12px 12px;
  border-radius:9px; background:var(--surface-2); border:1px solid var(--border);
  text-decoration:none; color:var(--text); font-size:14px; font-weight:700;
  transition:border-color .12s,color .12s,background .12s; flex-shrink:0;
}
.db-qa-item:hover { border-color:var(--accent); color:var(--accent); background:color-mix(in srgb, var(--accent) 6%, var(--surface)); }
.db-qa-icon { font-size:19px; width:24px; text-align:center; flex-shrink:0; }
.db-qa-lbl  { flex:1; }
.db-qa-arr  { margin-left:auto; color:var(--muted-2); font-size:14px; }

/* ── Feed movimenti (centro) ──────────────────────────────── */
.db-feed { flex:1; overflow-y:auto; min-height:0; scrollbar-width:none; }
.db-feed::-webkit-scrollbar { display:none; }
.db-feed-row {
  display:flex; align-items:center; gap:9px;
  padding:8px 0; border-bottom:1px solid var(--border);
}
.db-feed-row:last-child { border-bottom:none; }
.db-feed-time  { color:var(--muted-2); width:36px; flex-shrink:0; font-size:13px; font-weight:600; }
.db-feed-chip  { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; padding:2px 7px; border-radius:8px; color:#fff; flex-shrink:0; }
.db-feed-info  { flex:1; min-width:0; }
.db-feed-name  { font-size:14.5px; font-weight:700; color:var(--text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.db-feed-desc  { font-size:12px; color:var(--muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:1px; }
.db-feed-amt   { font-weight:800; white-space:nowrap; font-size:15px; }
.db-feed-empty { text-align:center; color:var(--muted-2); font-size:13px; padding:32px 0; }

/* ── Pannello destra: Meteo + Calendario ──────────────────── */
/* Meteo — riga orizzontale */
.db-weather {
  flex-shrink: 0;
  display: flex; align-items: center; gap: 14px;
  padding: 6px 0 12px;
  border-bottom: 1px solid var(--border); margin-bottom: 8px;
}
.db-w-icon  { font-size:50px; line-height:1; flex-shrink:0; }
.db-w-main  { flex:1; min-width:0; }
.db-w-temp  { font-family:'Bricolage Grotesque',sans-serif; font-size:38px; font-weight:800; color:var(--text); line-height:1; }
.db-w-desc  { font-size:12px; color:var(--muted); margin-top:2px; }
.db-w-loc   { font-size:10px; color:var(--muted-2); margin-top:2px; }
.db-w-meta  { display:flex; flex-direction:column; gap:4px; align-items:flex-end; font-size:11px; color:var(--muted-2); font-weight:600; flex-shrink:0; }
.db-w-loading { animation: db-pulse 1.4s ease infinite; }
@keyframes db-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* Calendario */
.db-cal { flex:1; display:flex; flex-direction:column; min-height:0; }
.db-cal-nav {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:6px; flex-shrink:0;
}
.db-cal-nav-btn {
  width:28px; height:28px; border:1px solid var(--border); border-radius:6px;
  background:var(--surface-2); color:var(--muted); cursor:pointer; font-size:15px;
  display:flex; align-items:center; justify-content:center;
  transition:border-color .1s,color .1s; flex-shrink:0; user-select:none;
}
.db-cal-nav-btn:hover { border-color:var(--accent); color:var(--accent); }
.db-cal-month-lbl {
  font-family:'Bricolage Grotesque',sans-serif; font-size:16px; font-weight:800; color:var(--text);
}
.db-cal-grid {
  flex:1; min-height:0;
  display:grid; grid-template-columns:repeat(7,1fr); grid-auto-rows:1fr;
  gap:2px;
}
.db-cal-dow {
  display:flex; align-items:center; justify-content:center;
  font-size:9px; font-weight:800; color:var(--muted-2); text-transform:uppercase; padding-bottom:2px;
}
.db-cal-day {
  display:flex; align-items:center; justify-content:center;
  font-size:14px; font-weight:600; color:var(--muted);
  border-radius:6px; cursor:pointer; line-height:1;
  transition:background .1s;
}
.db-cal-day:hover { background:var(--surface-2); color:var(--text); }
.db-cal-day.today { background:var(--accent); color:#fff; font-weight:800; }
.db-cal-day.empty { pointer-events:none; }
</style>

<script>document.body.classList.add('db-active');</script>

<div class="db-wrap">

  <!-- ROW 1: Greeting -->
  <div class="db-greeting">
    <div>
      <div class="db-greeting-name"><?= e($saluto . ($firstName ? ', ' . $firstName : '')) ?></div>
      <div class="db-greeting-date"><?= e($oggi) ?></div>
    </div>
    <div class="db-clock" id="db_clock"><?= date('H:i') ?></div>
  </div>

  <!-- ROW 2: Tiles -->
  <div class="db-tiles">

    <a class="db-tile db-tile-piscina" href="<?= url('/entries') ?>">
      <div class="db-tile-head"><span class="db-tile-icon">🏊</span><span class="db-tile-title">Piscina</span></div>
      <div class="db-tile-stats">
        <div class="db-tile-stat">
          <div class="db-tile-stat-v"><?= $stats['resPren'] ?></div>
          <div class="db-tile-stat-l">Confermate</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v" style="color:<?= $stats['resWait']>0?'#ffd580':'#fff' ?>"><?= $stats['resWait'] ?></div>
          <div class="db-tile-stat-l">Da confermare</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v"><?= $stats['inside'] ?></div>
          <div class="db-tile-stat-l">Presenti</div>
        </div>
      </div>
      <span class="db-tile-cta">Reception →</span>
    </a>

    <a class="db-tile db-tile-bar" href="<?= url('/bar') ?>">
      <div class="db-tile-head"><span class="db-tile-icon">🍹</span><span class="db-tile-title">Bar</span></div>
      <div class="db-tile-stats">
        <div class="db-tile-stat">
          <div class="db-tile-stat-v"><?= $stats['barOrders'] ?></div>
          <div class="db-tile-stat-l">Ordini</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v" style="font-size:22px">€ <?= number_format($stats['barTotal'],2,',','.') ?></div>
          <div class="db-tile-stat-l">Addebitato</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v" style="font-size:22px;color:<?= $stats['barOpen']>0.01?'#ffd580':'rgba(255,255,255,.5)' ?>">€ <?= number_format($stats['barOpen'],2,',','.') ?></div>
          <div class="db-tile-stat-l">Da incassare</div>
        </div>
      </div>
      <span class="db-tile-cta">Consumazioni →</span>
    </a>

    <a class="db-tile db-tile-risto" href="<?= url('/bar') ?>">
      <div class="db-tile-head"><span class="db-tile-icon">🍽️</span><span class="db-tile-title">Ristorazione</span></div>
      <div class="db-tile-stats">
        <div class="db-tile-stat">
          <div class="db-tile-stat-v"><?= $stats['ristoOrders'] ?></div>
          <div class="db-tile-stat-l">Ordini</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v" style="font-size:22px">€ <?= number_format($stats['ristoTotal'],2,',','.') ?></div>
          <div class="db-tile-stat-l">Addebitato</div>
        </div>
        <div class="db-tile-stat">
          <div class="db-tile-stat-v" style="font-size:22px;color:<?= $stats['ristoOpen']>0.01?'#ffd580':'rgba(255,255,255,.5)' ?>">€ <?= number_format($stats['ristoOpen'],2,',','.') ?></div>
          <div class="db-tile-stat-l">Da incassare</div>
        </div>
      </div>
      <span class="db-tile-cta">Registra consumazione →</span>
    </a>

  </div>

  <!-- ROW 3: Accesso rapido | Movimenti | Meteo + Calendario -->
  <div class="db-bottom">

    <!-- SINISTRA: Accesso rapido -->
    <div class="db-panel">
      <div class="db-panel-lbl">Accesso rapido</div>
      <div class="db-qa">

        <div class="db-qa-group">
          <div class="db-qa-section">Clienti &amp; Card</div>
          <a class="db-qa-item" href="<?= url('/customers/register') ?>">
            <span class="db-qa-icon">👤</span><span class="db-qa-lbl">Nuovo cliente</span><span class="db-qa-arr">›</span>
          </a>
        </div>

        <div class="db-qa-group">
          <div class="db-qa-section">Piscina</div>
          <a class="db-qa-item" href="<?= url('/entries') ?>">
            <span class="db-qa-icon">🚪</span><span class="db-qa-lbl">Reception</span><span class="db-qa-arr">›</span>
          </a>
        </div>

        <div class="db-qa-group">
          <div class="db-qa-section">Bar &amp; Risto</div>
          <a class="db-qa-item" href="<?= url('/bar') ?>">
            <span class="db-qa-icon">🍹</span><span class="db-qa-lbl">Consumazioni</span><span class="db-qa-arr">›</span>
          </a>
          <a class="db-qa-item" href="<?= url('/cashdesk') ?>" style="margin-top:4px">
            <span class="db-qa-icon">💰</span><span class="db-qa-lbl">Cassa</span><span class="db-qa-arr">›</span>
          </a>
        </div>

        <div class="db-qa-group">
          <div class="db-qa-section">Gestione</div>
          <a class="db-qa-item" href="<?= url('/reports') ?>">
            <span class="db-qa-icon">📊</span><span class="db-qa-lbl">Report</span><span class="db-qa-arr">›</span>
          </a>
        </div>

      </div>
    </div>

    <!-- CENTRO: Movimenti di oggi -->
    <div class="db-panel">
      <div class="db-panel-lbl">Movimenti di oggi <span id="db_mov_count" style="font-size:9px;background:var(--accent);color:var(--accent-ink);padding:1px 6px;border-radius:10px;font-weight:800;margin-left:4px"><?= count($latest) ?></span></div>
      <div class="db-feed">
        <?php if (empty($latest)): ?>
          <div class="db-feed-empty">Nessun movimento registrato oggi</div>
        <?php else:
          $dc = ['bar'=>'#17b3c4','ristorante'=>'#e8a020','cassa'=>'#2f9e72','reception'=>'#0b5e74','extra'=>'#7a6bb0'];
          $dl = ['bar'=>'Bar','ristorante'=>'Risto','cassa'=>'Cassa','reception'=>'Recep.','extra'=>'Extra'];
          foreach ($latest as $m):
            $dept      = $m['department'] ?? 'extra';
            $isPay     = in_array($m['movement_type'], ['payment','refund'], true);
            $cancelled = ($m['status'] ?? '') === 'cancelled';
        ?>
          <div class="db-feed-row" style="<?= $cancelled ? 'opacity:.4;text-decoration:line-through' : '' ?>">
            <span class="db-feed-time"><?= date('H:i', strtotime($m['created_at'])) ?></span>
            <?php if ($isPay): ?>
            <span class="db-feed-chip" style="background:var(--good)">Pagam.</span>
            <?php else: ?>
            <span class="db-feed-chip" style="background:<?= $dc[$dept] ?? '#999' ?>"><?= e($dl[$dept] ?? $dept) ?></span>
            <?php endif; ?>
            <div class="db-feed-info">
              <div class="db-feed-name"><?= e($m['last_name'] . ' ' . $m['first_name']) ?></div>
              <div class="db-feed-desc"><?= e($m['description'] ?? '') ?> · <?= e($m['card_code']) ?></div>
            </div>
            <span class="db-feed-amt" style="color:<?= $isPay ? 'var(--good)' : 'var(--text)' ?>;font-weight:<?= $isPay ? '800' : '700' ?>">
              <?= $isPay ? '+' : '' ?>€&nbsp;<?= number_format((float)$m['total_amount'], 2, ',', '.') ?>
            </span>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- DESTRA: Meteo + Calendario -->
    <div class="db-panel">

      <!-- Meteo -->
      <div class="db-panel-lbl">Meteo</div>
      <div class="db-weather">
        <div class="db-w-icon db-w-loading" id="wIcon">🌡️</div>
        <div class="db-w-main">
          <div class="db-w-temp" id="wTemp">—°C</div>
          <div class="db-w-desc" id="wDesc">Rilevamento…</div>
          <div class="db-w-loc" id="wLoc"></div>
        </div>
        <div class="db-w-meta">
          <span id="wHum"></span>
          <span id="wWind"></span>
        </div>
      </div>

      <!-- Calendario -->
      <div class="db-panel-lbl">Calendario</div>
      <div class="db-cal">
        <div class="db-cal-nav">
          <button class="db-cal-nav-btn" type="button" onclick="calMove(-1)">&#8249;</button>
          <span class="db-cal-month-lbl" id="calTitle"></span>
          <button class="db-cal-nav-btn" type="button" onclick="calMove(1)">&#8250;</button>
        </div>
        <div class="db-cal-grid" id="calGrid"></div>
      </div>

    </div>

  </div>
</div>

<script>
/* ══ VARIABILI — devono stare PRIMA degli IIFE che le usano ══ */
var CAL_MESI = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
var CAL_DOW  = ['Lu','Ma','Me','Gi','Ve','Sa','Do'];

var WMO_ICON = {
  0:'☀️',  1:'🌤️', 2:'⛅',  3:'☁️',
  45:'🌫️', 48:'🌫️',
  51:'🌦️', 53:'🌦️', 55:'🌧️',
  61:'🌧️', 63:'🌧️', 65:'🌧️',
  71:'🌨️', 73:'🌨️', 75:'❄️',  77:'🌨️',
  80:'🌦️', 81:'🌧️', 82:'⛈️',
  85:'🌨️', 86:'❄️',
  95:'⛈️', 96:'⛈️', 99:'⛈️'
};
var WMO_DESC = {
  0:'Sereno', 1:'Prevalentemente soleggiato', 2:'Parzialmente nuvoloso', 3:'Coperto',
  45:'Nebbia', 48:'Nebbia con depositi',
  51:'Pioviggine leggera', 53:'Pioviggine', 55:'Pioviggine intensa',
  61:'Pioggia leggera', 63:'Pioggia', 65:'Pioggia forte',
  71:'Neve leggera', 73:'Neve', 75:'Neve intensa', 77:'Neve granulare',
  80:'Rovesci', 81:'Rovesci', 82:'Rovesci forti',
  85:'Nevicate', 86:'Nevicate intense',
  95:'Temporale', 96:'Temporale con grandine', 99:'Temporale forte'
};

var calY, calM;

/* ── Clock ──────────────────────────────────────────────────── */
setInterval(function () {
  var el = document.getElementById('db_clock');
  if (!el) return;
  var d = new Date();
  el.textContent = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
}, 1000);

/* ── Calendario ─────────────────────────────────────────────── */
function renderCal() {
  var titleEl = document.getElementById('calTitle');
  var gridEl  = document.getElementById('calGrid');
  if (!titleEl || !gridEl) return;
  titleEl.textContent = CAL_MESI[calM] + ' ' + calY;

  var now      = new Date();
  var todY = now.getFullYear(), todM = now.getMonth(), todD = now.getDate();
  var first    = new Date(calY, calM, 1);
  var startCol = (first.getDay() + 6) % 7;   /* Lun=0, Dom=6 */
  var nDays    = new Date(calY, calM + 1, 0).getDate();

  var html = '';
  for (var i = 0; i < 7; i++) html += '<div class="db-cal-dow">' + CAL_DOW[i] + '</div>';
  for (var i = 0; i < startCol; i++) html += '<div class="db-cal-day empty"></div>';
  for (var d = 1; d <= nDays; d++) {
    var cls = 'db-cal-day' + (d === todD && calM === todM && calY === todY ? ' today' : '');
    html += '<div class="' + cls + '">' + d + '</div>';
  }
  gridEl.innerHTML = html;
}

function calMove(delta) {
  calM += delta;
  if (calM > 11) { calM = 0; calY++; }
  if (calM < 0)  { calM = 11; calY--; }
  renderCal();
}

(function () {
  var n = new Date();
  calY = n.getFullYear();
  calM = n.getMonth();
  renderCal();
}());

/* ── Meteo ──────────────────────────────────────────────────── */
function applyWeather(lat, lon, locLabel) {
  var url = 'https://api.open-meteo.com/v1/forecast'
    + '?latitude=' + lat + '&longitude=' + lon
    + '&current=temperature_2m,relativehumidity_2m,weathercode,windspeed_10m'
    + '&wind_speed_unit=kmh&timezone=auto';

  fetch(url)
    .then(function (r) { return r.json(); })
    .then(function (data) {
      var c    = data.current;
      var code = Number(c.weathercode);
      var iconEl = document.getElementById('wIcon');
      iconEl.classList.remove('db-w-loading');
      iconEl.textContent                            = WMO_ICON[code] || '🌡️';
      document.getElementById('wTemp').textContent  = Math.round(c.temperature_2m) + '°C';
      document.getElementById('wDesc').textContent  = WMO_DESC[code] || ('Codice ' + code);
      document.getElementById('wHum').textContent   = '💧 ' + c.relativehumidity_2m + '%';
      document.getElementById('wWind').textContent  = '💨 ' + Math.round(c.windspeed_10m) + ' km/h';
      document.getElementById('wLoc').textContent   = locLabel ? '📍 ' + locLabel : '';
    })
    .catch(function () {
      document.getElementById('wIcon').classList.remove('db-w-loading');
      document.getElementById('wIcon').textContent = '❓';
      document.getElementById('wDesc').textContent = 'Meteo non disponibile';
    });
}

/* Geolocalizzazione → IP fallback → Roma */
function weatherFromIP() {
  fetch('https://ipapi.co/json/')
    .then(function (r) { return r.json(); })
    .then(function (d) {
      if (d.latitude && d.longitude) {
        applyWeather(d.latitude.toFixed(4), d.longitude.toFixed(4), d.city || d.region || '');
      } else {
        applyWeather(41.9028, 12.4964, 'Roma');
      }
    })
    .catch(function () {
      applyWeather(41.9028, 12.4964, 'Roma');
    });
}

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    function (pos) {
      applyWeather(
        pos.coords.latitude.toFixed(4),
        pos.coords.longitude.toFixed(4),
        'Posizione attuale'
      );
    },
    function () { weatherFromIP(); },   /* utente nega → prova IP */
    { timeout: 5000, maximumAge: 300000 }
  );
} else {
  weatherFromIP();
}

window.addEventListener('pagehide', function () {
  document.body.classList.remove('db-active');
});
</script>
