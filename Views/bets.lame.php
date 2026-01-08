<section class="season-view">
  <header class="season-head">
    <h2>Classement paris</h2>
    <p><?= (int)$betsTotal ?> paris enregistres. Points cumules sur <?= (int)$coursesScored ?> manches avec podium officiel. Bareme: 3 pts par position exacte, 1 pt par pilote sur le podium, bonus +2 si podium parfait.</p>
  </header>

  <?php if (empty($leaderboard)): ?>
    <p class="info-guest">Aucun pari qualifie pour le moment.</p>
  <?php else: ?>
    <div class="standings-wrapper">
      <table class="points-table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Joueur</th>
            <th scope="col">Paris</th>
            <th scope="col">Exact</th>
            <th scope="col">Parfait</th>
            <th scope="col">Points</th>
          </tr>
        </thead>
        <tbody>
          <?php $rank = 1; ?>
          <?php foreach ($leaderboard as $row): ?>
            <?php
              $isMe = !empty($currentUser) && (int)$currentUser['id'] === (int)$row['user_id'];
              $displayName = $row['name'] ?: $row['email'];
            ?>
            <tr class="<?= $isMe ? 'is-user' : '' ?>">
              <th scope="row"><?= $rank ?></th>
              <td><?= htmlspecialchars($displayName) ?></td>
              <td><?= (int)$row['bets'] ?></td>
              <td><?= (int)$row['exact'] ?></td>
              <td><?= (int)$row['perfect'] ?></td>
              <td><strong><?= (int)$row['points'] ?></strong></td>
            </tr>
            <?php $rank++; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
