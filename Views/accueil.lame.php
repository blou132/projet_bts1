<?php
$headlineTeam = $topTeams[0] ?? null;
$headlinePilot = $pilotesSpotlight[0] ?? null;
?>
<section class="home">
  <section class="home-top card">
    <div class="home-top-copy">
      <span class="home-kicker">Saison 2026</span>
      <h1>Paddock Manager</h1>
      <p>Suivez les Grands Prix, pilotez les ecuries et comparez vos paris en un coup d'oeil.</p>
      <div class="home-cta">
        <a class="home-btn home-btn-primary" href="/calendrier">Voir le calendrier</a>
        <a class="home-btn home-btn-ghost" href="/paris">Classement des paris</a>
      </div>
    </div>
    <div class="home-top-side">
      <img class="home-top-logo" src="<?= htmlspecialchars(asset_path('Public/assets/pm-banner.svg')) ?>" alt="Banniere Paddock Manager">
      <div class="home-glance">
        <?php if (!empty($headlineTeam)): ?>
          <p><span>Ecurie en tete</span><strong><?= htmlspecialchars($headlineTeam['nom']) ?></strong></p>
        <?php endif; ?>
        <?php if (!empty($headlinePilot)): ?>
          <p><span>Pilote du moment</span><strong><?= htmlspecialchars($headlinePilot['prenom'] . ' ' . $headlinePilot['nom']) ?></strong></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <div class="home-metrics">
    <article class="home-metric">
      <span>Grands Prix</span>
      <strong><?= (int)($stats['grands_prix'] ?? 0) ?></strong>
    </article>
    <article class="home-metric">
      <span>Ecuries</span>
      <strong><?= (int)($stats['ecuries'] ?? 0) ?></strong>
    </article>
    <article class="home-metric">
      <span>Pilotes</span>
      <strong><?= (int)($stats['pilotes'] ?? 0) ?></strong>
    </article>
  </div>

  <div class="home-grid">
    <section class="home-panel">
      <header class="home-panel-head">
        <h3>Top ecuries</h3>
        <p>Classement par effectif actif.</p>
      </header>
      <div class="home-team-list">
        <?php foreach ($topTeams as $i => $team): ?>
          <article class="home-team-item">
            <span class="home-rank"><?= (int)$i + 1 ?></span>
            <img src="<?= htmlspecialchars(asset_path($team['blason'] ?: 'Public/assets/logos/ferrari.svg')) ?>" alt="logo ecurie" class="home-list-thumb">
            <div class="home-item-copy">
              <strong><?= htmlspecialchars($team['nom']) ?></strong>
              <span><?= (int)$team['pilotes'] ?> pilote<?= ((int)$team['pilotes']) > 1 ? 's' : '' ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="home-panel">
      <header class="home-panel-head">
        <h3>Pilotes a l'affiche</h3>
        <p>Profils a suivre cette semaine.</p>
      </header>
      <div class="home-driver-list">
        <?php foreach ($pilotesSpotlight as $pilote): ?>
          <article class="home-driver-item">
            <img src="<?= htmlspecialchars(asset_path($pilote['photo'] ?: 'Public/assets/pilotes/hamilton.svg')) ?>" alt="portrait pilote" class="home-driver-thumb">
            <div class="home-item-copy">
              <strong><?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?></strong>
              <span><?= htmlspecialchars($pilote['ecurie']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</section>
