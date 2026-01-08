<section class="season-view">
  <header class="season-head">
    <h2><?= htmlspecialchars($course['nom']) ?></h2>
    <p><?= htmlspecialchars($course['ville']) ?> · <?= htmlspecialchars((new DateTime($course['date_course']))->format('d F Y')) ?></p>
    <p><a href="?route=calendrier">← Retour au calendrier</a></p>
  </header>

  <?php require __DIR__ . '/partials/course_results.lame.php'; ?>
  <?php require __DIR__ . '/partials/course_bet.lame.php'; ?>
  <?php require __DIR__ . '/partials/course_bet_rank.lame.php'; ?>
</section>
