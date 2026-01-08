<section class="season-view">
  <header class="season-head">
    <h2>Calendrier <?= htmlspecialchars((string)($year ?? date('Y'))) ?></h2>
    <p>Consultez chaque Grand Prix et retrouvez le classement complet en cliquant sur une manche.</p>
  </header>

  <div class="calendar-highlight">
    <div class="next-race">
      <span class="next-race-label">Prochaine course</span>
      <strong class="next-race-name js-next-name">-</strong>
      <span class="next-race-meta js-next-meta">-</span>
      <span class="next-countdown js-next-countdown">-</span>
    </div>
  </div>

  <div class="calendar-grid">
    <?php foreach ($calendar as $event): ?>
      <?php
        $date = new DateTime($event['date_course']);
        $location = $event['ville'] . ' · ' . $event['pays'];
        $title = $event['nom'] . ' - ' . $location . ' - ' . $date->format('d/m/Y');
      ?>
      <a class="calendar-card" href="?route=calendrier&action=course&course=<?= (int)$event['id'] ?>"
         data-date="<?= htmlspecialchars($event['date_course']) ?>"
         data-name="<?= htmlspecialchars($event['nom']) ?>"
         data-location="<?= htmlspecialchars($location) ?>"
         title="<?= htmlspecialchars($title) ?>">
        <header>
          <span class="calendar-round">Manche <?= (int)$event['ordre'] ?></span>
          <?php if (!empty($event['flag'])): ?>
            <span class="calendar-flag" aria-hidden="true"><?= htmlspecialchars($event['flag']) ?></span>
          <?php endif; ?>
        </header>
        <h3><?= htmlspecialchars($event['nom']) ?></h3>
        <p class="calendar-meta"><?= htmlspecialchars($event['ville']) ?> · <?= htmlspecialchars($date->format('d/m/Y')) ?></p>
      </a>
    <?php endforeach; ?>
  </div>

</section>
