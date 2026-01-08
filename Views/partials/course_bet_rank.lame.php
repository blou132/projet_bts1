<?php
$podiumLabel = '';
if (!empty($betPodium)) {
  $labels = [];
  foreach ([1, 2, 3] as $pos) {
    $driverId = $betPodium[$pos] ?? 0;
    $driver = $driversById[(int)$driverId] ?? null;
    $name = $driver ? trim($driver['prenom'] . ' ' . $driver['nom']) : '-';
    $posLabel = $pos === 1 ? '1er' : ($pos . 'e');
    $labels[] = $posLabel . ': ' . $name;
  }
  $podiumLabel = implode(' | ', $labels);
}
?>
<section class="results-panel bet-panel bet-rank js-bet-rank">
  <header class="results-head">
    <h3>Classement des paris</h3>
    <p>Points attribues quand le podium officiel est saisi. Bareme: 3 pts par position exacte, 1 pt par pilote sur le podium, bonus +2 si podium parfait.</p>
  </header>

  <?php if (empty($betPodium)): ?>
    <?php if (!empty($betStats['total'])): ?>
      <div class="bet-meta">
        <span><?= (int)$betStats['total'] ?> paris enregistres</span>
      </div>
    <?php endif; ?>
    <p class="results-empty">Classement disponible apres saisie du podium officiel.</p>
  <?php elseif (empty($betLeaderboard)): ?>
    <?php if ($podiumLabel !== ''): ?>
      <div class="bet-meta">
        <span>Podium officiel: <?= htmlspecialchars($podiumLabel) ?></span>
      </div>
    <?php endif; ?>
    <p class="results-empty">Aucun pari enregistre pour cette course.</p>
  <?php else: ?>
    <div class="bet-meta">
      <?php if ($podiumLabel !== ''): ?>
        <span>Podium officiel: <?= htmlspecialchars($podiumLabel) ?></span>
      <?php endif; ?>
      <span><?= (int)$betStats['total'] ?> paris enregistres</span>
    </div>
    <table class="results-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Joueur</th>
          <th>Podium pronostique</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
        <?php $rank = 1; ?>
        <?php foreach ($betLeaderboard as $row): ?>
          <?php
            $isMe = !empty($currentUser) && (int)$currentUser['id'] === (int)$row['user_id'];
            $displayName = $row['name'] ?: $row['email'];
            $score = $row['score_detail'] ?? [];
            $first = $driversById[(int)($row['first_joueur_id'] ?? 0)] ?? null;
            $second = $driversById[(int)($row['second_joueur_id'] ?? 0)] ?? null;
            $third = $driversById[(int)($row['third_joueur_id'] ?? 0)] ?? null;
          ?>
          <tr class="<?= $isMe ? 'is-user' : '' ?>">
            <td><?= $rank ?></td>
            <td><?= htmlspecialchars($displayName) ?></td>
            <td>
              <div class="bet-picks">
                <span>1er: <?= htmlspecialchars(($first['prenom'] ?? '') . ' ' . ($first['nom'] ?? '')) ?></span>
                <span>2e: <?= htmlspecialchars(($second['prenom'] ?? '') . ' ' . ($second['nom'] ?? '')) ?></span>
                <span>3e: <?= htmlspecialchars(($third['prenom'] ?? '') . ' ' . ($third['nom'] ?? '')) ?></span>
              </div>
            </td>
            <td class="bet-score">
              <?= (int)($row['score'] ?? 0) ?>
              <?php if (!empty($score['perfect'])): ?>
                <span class="bet-badge">Parfait</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php $rank++; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
