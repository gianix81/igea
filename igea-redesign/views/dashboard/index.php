<?php
/* Dashboard operativa — tema acqua. Usa $stats e $latest passati da public/index.php */
$deptLabels = ['bar' => 'Bar', 'ristorante' => 'Ristorante', 'reception' => 'Reception', 'cassa' => 'Cassa', 'extra' => 'Extra'];
$statusLabels = ['open' => 'Aperto', 'paid' => 'Pagato', 'cancelled' => 'Annullato'];

$itGiorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
$itMesi   = ['', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
$oggi     = sprintf('%s %d %s %s', $itGiorni[(int) date('w')], (int) date('j'), $itMesi[(int) date('n')], date('Y'));

$charges  = (float) ($stats['charges'] ?? 0);
$payments = (float) ($stats['payments'] ?? 0);
$maxRev   = max($charges, $payments, 1);
?>
<section class="kpi-band">
  <div class="page-head" style="margin-bottom:16px">
    <div>
      <h1>Dashboard operativa</h1>
      <div class="sub"><?= e($oggi) ?> · <?= e(date('H:i')) ?></div>
    </div>
    <form class="searchbox" method="get" action="<?= url('/cards') ?>">
      <span aria-hidden="true">&#9906;</span>
      <input name="code" placeholder="Cerca card o cliente…" autocomplete="off">
    </form>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Presenze</div><div class="value num"><?= (int) ($stats['inside'] ?? 0) ?></div></div>
    <div class="kpi"><div class="label">Card attive</div><div class="value num"><?= (int) ($stats['active_cards'] ?? 0) ?></div></div>
    <div class="kpi is-bad"><div class="label">Saldi aperti</div><div class="value num"><?= (int) ($stats['open_cards'] ?? 0) ?></div></div>
    <div class="kpi"><div class="label">Consumazioni</div><div class="value num"><?= e(money($charges)) ?></div></div>
    <div class="kpi is-good"><div class="label">Incassato</div><div class="value num"><?= e(money($payments)) ?></div></div>
    <div class="kpi"><div class="label">Prenotazioni</div><div class="value num"><?= (int) ($stats['reservations'] ?? 0) ?></div></div>
  </div>
</section>

<div class="page">
  <div class="grid-2">

    <!-- Flusso consumazioni -->
    <section class="panel">
      <div class="panel-head">
        <h3>Ultimi movimenti</h3>
        <span class="live"><span class="dot"></span>in tempo reale</span>
      </div>
      <div class="feed">
        <?php if (empty($latest)): ?>
          <div class="feed-row"><span class="desc"><b>Nessun movimento</b> <span>registrato oggi</span></span></div>
        <?php else: foreach ($latest as $row):
          $dept = (string) ($row['department'] ?? 'extra');
          $st   = (string) ($row['status'] ?? 'open');
          $isPos = in_array(($row['movement_type'] ?? ''), ['payment', 'refund'], true);
        ?>
          <div class="feed-row">
            <span class="time num"><?= e(date('H:i', strtotime((string) $row['created_at']))) ?></span>
            <span class="chip <?= e($dept) ?>"><?= e(strtoupper($deptLabels[$dept] ?? $dept)) ?></span>
            <span class="desc">
              <b><?= e($row['description']) ?></b>
              <span class="num"><?= e($row['card_code']) ?></span>
            </span>
            <span class="status-badge status-<?= e($st) ?>"><?= e($statusLabels[$st] ?? $st) ?></span>
            <span class="amt num <?= $isPos ? 'pos' : '' ?>"><?= e(money($row['total_amount'])) ?></span>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </section>

    <!-- Colonna destra -->
    <div class="stack">
      <section class="panel">
        <h3>Incasso vs consumazioni</h3>
        <div style="margin-bottom:14px">
          <div class="kv"><span class="k">Incassato oggi</span><span class="v num"><?= e(money($payments)) ?></span></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= round($payments / $maxRev * 100) ?>%"></div></div>
        </div>
        <div>
          <div class="kv"><span class="k">Consumazioni</span><span class="v num"><?= e(money($charges)) ?></span></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= round($charges / $maxRev * 100) ?>%;background:var(--warn)"></div></div>
        </div>
      </section>

      <section class="panel">
        <h3>Da chiudere</h3>
        <div class="kv" style="margin-bottom:0">
          <span class="k">Card con saldo aperto</span>
          <span class="v num" style="color:var(--bad)"><?= (int) ($stats['open_cards'] ?? 0) ?></span>
        </div>
        <a class="btn btn-sm btn-outline-secondary" style="margin-top:12px" href="<?= url('/cashdesk') ?>">Vai alla Cassa</a>
      </section>
    </div>

  </div>
</div>
