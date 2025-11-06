<?php
// Tableau de bord d'accueil : statistiques, top écuries, pilotes et vitrine des GP
?>
<section class="dashboard">
  <header class="dashboard-head">
    <h2>Bienvenue sur le paddock</h2>
    <p>Surveillez l'écosystème Formule&nbsp;1 depuis un seul cockpit&nbsp;: Grands Prix, écuries, pilotes.</p>
  </header>

  <?php // Cartes statistiques principales ?>
  <div class="dashboard-cards">
    <article class="card card-stat">
      <span class="card-label">Grands Prix actifs</span>
      <strong class="card-value"><?= $stats['grands_prix'] ?? 0 ?></strong>
    </article>
    <article class="card card-stat">
      <span class="card-label">Écuries enregistrées</span>
      <strong class="card-value"><?= $stats['ecuries'] ?? 0 ?></strong>
    </article>
    <article class="card card-stat">
      <span class="card-label">Pilotes sous contrat</span>
      <strong class="card-value"><?= $stats['pilotes'] ?? 0 ?></strong>
    </article>
  </div>

  <?php // Grille principale : Grands Prix, écuries, pilotes ?>
  <div class="dashboard-grid">
    <section class="panel">
      <header class="panel-head">
        <h3>Grands Prix en vitrine</h3>
      </header>
      <?php // Vitrine des prochains Grands Prix ?>
      <div class="gp-grid">
        <?php foreach ($grandsPrix as $gp): ?>
          <article class="gp-card">
            <?php if (!empty($gp['blason'])): ?>
              <img src="<?= htmlspecialchars($gp['blason']) ?>" alt="affiche Grand Prix" class="gp-thumb">
            <?php endif; ?>
            <div>
              <strong><?= htmlspecialchars($gp['nom']) ?></strong>
              <span><?= htmlspecialchars($gp['pays']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="panel">
      <header class="panel-head">
        <h3>Top écuries (effectif)</h3>
      </header>
      <?php // Liste des écuries avec logo et effectif ?>
      <ul class="list list-teams">
        <?php foreach ($topTeams as $team): ?>
          <li class="list-item">
            <?php if (!empty($team['blason'])): ?>
              <img src="<?= htmlspecialchars($team['blason']) ?>" alt="logo écurie" class="list-thumb">
            <?php endif; ?>
            <div class="list-content">
              <strong><?= htmlspecialchars($team['nom']) ?></strong>
              <span><?= (int)$team['pilotes'] ?> pilote<?= ((int)$team['pilotes']) > 1 ? 's' : '' ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section class="panel">
      <header class="panel-head">
        <h3>Pilotes à l'affiche</h3>
      </header>
      <?php // Piliers présentant les pilotes phares ?>
      <div class="pill-grid">
        <?php foreach ($pilotesSpotlight as $pilote): ?>
          <article class="pill">
            <?php if (!empty($pilote['photo'])): ?>
              <img src="<?= htmlspecialchars($pilote['photo']) ?>" alt="portrait pilote" class="pill-thumb">
            <?php endif; ?>
            <div>
              <strong><?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?></strong>
              <span><?= htmlspecialchars($pilote['equipe']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

  </div>
</section>
