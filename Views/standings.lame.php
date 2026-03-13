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

$maxTotal = 0;
foreach ($drivers as $driver) {
    $maxTotal = max($maxTotal, (int)$driver['total']);
}
?>
<section class="season-view">
  <header class="season-head">
    <h2>Classement pilotes</h2>
    <p>Total des points calculé automatiquement à partir des résultats officiels. Passez la souris sur les cellules pour voir les détails d'une manche.</p>
  </header>

  <?php if (empty($drivers)): ?>
    <p class="info-guest">Aucun pilote enregistré pour le moment.</p>
  <?php else: ?>
    <div class="standings-wrapper">
      <table class="points-table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Pilote</th>
            <th scope="col">Écurie</th>
            <?php foreach ($courses as $course): ?>
              <th scope="col" class="col-round" title="<?= htmlspecialchars($course['nom']) ?>"><?= htmlspecialchars($course['code']) ?></th>
            <?php endforeach; ?>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php $rank = 1; ?>
          <?php foreach ($drivers as $driver): ?>
            <?php $driverId = (int)$driver['id']; ?>
            <tr>
              <th scope="row"><?= $rank ?></th>
              <td>
                <strong><?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom']) ?></strong>
              </td>
              <td><?= htmlspecialchars($driver['ecurie'] ?? '—') ?></td>
              <?php foreach ($courses as $course): ?>
                <?php $courseId = (int)$course['id']; ?>
                <?php $cell = $pointsByDriver[$driverId][$courseId] ?? null; ?>
                <?php $pts = $cell['points'] ?? 0; ?>
                <td class="<?= $heatLevel((int)$pts) ?>" title="<?= $cell ? 'Position ' . (int)$cell['position'] . ' · ' . (int)$pts . ' pt' . ((int)$pts === 1 ? '' : 's') : 'Non classé' ?>">
                  <?= $pts > 0 ? (int)$pts : '·' ?>
                </td>
              <?php endforeach; ?>
              <td class="total-cell">
                <span class="total-value"><?= (int)$driver['total'] ?></span>
                <?php $denominator = $maxTotal > 0 ? $maxTotal : 1; ?>
                <span class="total-bar" style="--score: <?= number_format(min(1, $driver['total'] / $denominator), 2, '.', '') ?>"></span>
              </td>
            </tr>
            <?php $rank++; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
