<section>
  <h2>Pilotes par écurie</h2>
  <table>
    <thead><tr><th>Écurie</th><th>Logo</th><th>Pilote</th><th>Rôle</th><th>Portrait</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['ecurie']) ?></td>
          <td><?php if ($r['blason']): ?><img src="<?= htmlspecialchars($r['blason']) ?>" class="thumb" alt="logo écurie"><?php endif; ?></td>
          <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
          <td><?= htmlspecialchars($r['poste']) ?></td>
          <td><?php if ($r['photo']): ?><img src="<?= htmlspecialchars($r['photo']) ?>" class="thumb" alt="portrait pilote"><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
