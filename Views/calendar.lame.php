<section class="season-view">
  <header class="season-head">
    <h2>Calendrier <?= htmlspecialchars((string)($year ?? date('Y'))) ?></h2>
    <p>Planification indicative des 23 manches du championnat du monde de Formule&nbsp;1.</p>
  </header>

  <div class="calendar-grid">
    <?php foreach ($calendar as $event): ?>
      <article class="calendar-card">
        <header>
          <span class="calendar-round">Manche <?= (int)$event['round'] ?></span>
          <span class="calendar-flag" aria-hidden="true"><?= htmlspecialchars($event['flag']) ?></span>
        </header>
        <h3><?= htmlspecialchars($event['country']) ?></h3>
        <p class="calendar-meta"><?= htmlspecialchars($event['city']) ?> · <?= htmlspecialchars($event['dates']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>
