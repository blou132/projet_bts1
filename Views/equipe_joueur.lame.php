<section>
  <h2>Pilotes par écurie</h2>
  <div class="table-tools">
    <input class="filter-input" type="search" placeholder="Rechercher..." data-filter-table="pilotes-ecurie-table" data-filter-count="pilotes-ecurie-count">
    <span class="filter-count" id="pilotes-ecurie-count"></span>
    <span class="sort-hint">Cliquer sur un titre pour trier</span>
  </div>
  <table id="pilotes-ecurie-table" data-sortable="true">
    <thead><tr><th>Écurie</th><th data-sort="false">Logo</th><th>Pilote</th><th>Rôle</th><th data-sort="false">Portrait</th></tr></thead>
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
