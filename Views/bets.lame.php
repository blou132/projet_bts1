<section class="season-view bets-standings">
  <header class="season-head bets-head">
    <div class="bets-headline">
      <h2>Classement des paris</h2>
      <p><?= (int)$betsTotal ?> paris enregistres. Points cumules sur <?= (int)$coursesScored ?> manches avec podium officiel. Bareme: 3 pts par position exacte, 1 pt par pilote sur le podium, bonus +2 si podium parfait.</p>
    </div>
    <div class="bets-trophy" aria-hidden="true">
      <svg viewBox="0 0 64 64" role="presentation" focusable="false">
        <path d="M20 8h24v8h10c0 11-7 20-18 22-1 5-4 9-8 11v5h12v6H24v-6h12v-5c-4-2-7-6-8-11-11-2-18-11-18-22h10V8zm-6 8h-4c1 7 6 13 13 14-3-3-5-7-5-12v-2H14zm36 0h-4v2c0 5-2 9-5 12 7-1 12-7 13-14z"/>
      </svg>
    </div>
  </header>

  <?php if (empty($leaderboard)): ?>
    <p class="info-guest">Aucun pari qualifie pour le moment.</p>
  <?php else: ?>
    <div class="standings-wrapper bets-table-wrapper">
      <div class="bets-table-frame">
        <table class="points-table bets-table">
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
    </div>
  <?php endif; ?>
</section>
