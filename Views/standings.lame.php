<?php
$heatLevel = static function (int $points): string {
    if ($points >= 25) {
        return 'heat-max';
    }
    if ($points >= 18) {
        return 'heat-high';
    }
    if ($points >= 12) {
        return 'heat-mid';
    }
    if ($points >= 6) {
        return 'heat-low';
    }
    if ($points > 0) {
        return 'heat-verylow';
    }
    return 'heat-zero';
};
?>
<section class="season-view">
  <header class="season-head">
    <h2>Classement pilotes (démonstration)</h2>
    <p>Tableau inspiré des points cumulés par Grand Prix. Passez la souris pour consulter les scores par manche.</p>
  </header>

  <div class="standings-wrapper">
    <table class="points-table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Pilote</th>
          <th scope="col">Écurie</th>
          <?php foreach ($grandsPrix as $roundCode): ?>
            <th scope="col" class="col-round" title="<?= htmlspecialchars($roundCode) ?>"><?= htmlspecialchars($roundCode) ?></th>
          <?php endforeach; ?>
          <th scope="col">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $rank = 1; ?>
        <?php foreach ($drivers as $driver): ?>
          <tr>
            <th scope="row"><?= $rank ?></th>
            <td>
              <strong><?= htmlspecialchars($driver['code']) ?></strong>
            </td>
            <td><?= htmlspecialchars($driver['team']) ?></td>
            <?php foreach ($driver['points'] as $pts): ?>
              <td class="<?= $heatLevel((int)$pts) ?>" title="<?= (int)$pts ?> pt<?= ((int)$pts) === 1 ? '' : 's' ?>">
                <?= $pts > 0 ? (int)$pts : '·' ?>
              </td>
            <?php endforeach; ?>
            <td class="total-cell">
              <span class="total-value"><?= (int)$driver['total'] ?></span>
              <?php $ratio = min(1, $driver['total'] / max(1, $drivers[0]['total'])); ?>
              <span class="total-bar" style="--score: <?= number_format($ratio, 2, '.', '') ?>"></span>
            </td>
          </tr>
          <?php $rank++; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
