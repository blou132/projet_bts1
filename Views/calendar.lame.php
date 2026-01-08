<section class="season-view">
  <header class="season-head">
    <h2>Calendrier <?= htmlspecialchars((string)($year ?? date('Y'))) ?></h2>
    <p>Consultez chaque Grand Prix et retrouvez le classement complet en cliquant sur une manche.</p>
  </header>

  <div class="calendar-grid">
    <?php foreach ($calendar as $event): ?>
      <?php $date = new DateTime($event['date_course']); ?>
      <a class="calendar-card" href="?route=calendrier&action=course&course=<?= (int)$event['id'] ?>">
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
