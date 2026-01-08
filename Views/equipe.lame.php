<section>
  <h2>Écuries</h2>
  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($isAdmin)): ?>
    <form method="post" enctype="multipart/form-data" action="?route=ecuries&action=store">
      <fieldset>
        <legend>Ajouter une écurie</legend>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <label>Nom <input name="nom" type="text" required></label><br/>
        <label>Pays <input name="pays" type="text" required></label><br/>
        <label>Grand Prix associé
          <select name="id_championnat" required>
            <option value="">— choisir —</option>
            <?php foreach ($championnats as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </label><br/>
        <label>Logo</label>
          <label class="file-upload btn">
            <input type="file" name="blason" accept="image/*">
            <span>Choisir un logo</span>
          </label><br/><br/>
        <button class="file-upload btn">Créer</button>
      </fieldset>
    </form>
  <?php endif; ?>

  <div class="table-tools">
    <input class="filter-input" type="search" placeholder="Rechercher une ecurie..." data-filter-table="ecuries-table" data-filter-count="ecuries-count">
    <span class="filter-count" id="ecuries-count"></span>
    <span class="sort-hint">Cliquer sur un titre pour trier</span>
  </div>

  <table id="ecuries-table" data-sortable="true">
    <thead><tr><th>ID</th><th>Écurie</th><th>Pays</th><th>Grand Prix</th><th data-sort="false">Logo</th><?php if (!empty($isAdmin)): ?><th data-sort="false">Actions</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($ecuries as $ecurie): ?>
      <tr>
        <td><?= $ecurie['id'] ?></td>
        <td><?= htmlspecialchars($ecurie['nom']) ?></td>
        <td><?= htmlspecialchars($ecurie['pays']) ?></td>
        <td><?= htmlspecialchars($ecurie['championnat']) ?></td>
        <td><?php if ($ecurie['blason']): ?><img src="<?= htmlspecialchars($ecurie['blason']) ?>" alt="logo écurie" class="thumb"><?php endif; ?></td>
        <?php if (!empty($isAdmin)): ?>
        <td>
          <details>
            <summary>Éditer</summary>
              <form method="post" enctype="multipart/form-data" action="?route=ecuries&action=update">
                <input type="hidden" name="id" value="<?= $ecurie['id'] ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                <label>Nom <input name="nom" value="<?= htmlspecialchars($ecurie['nom']) ?>" required></label>
                <label>Pays <input name="pays" value="<?= htmlspecialchars($ecurie['pays']) ?>" required></label>
                <label>Grand Prix associé
                  <select name="id_championnat" required>
                    <?php foreach ($championnats as $c): ?>
                      <option value="<?= $c['id'] ?>" <?= $c['id']==$ecurie['id_championnat']?'selected':'' ?>><?= htmlspecialchars($c['nom']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <?php if ($ecurie['blason']): ?><input type="hidden" name="blason_exist" value="<?= htmlspecialchars($ecurie['blason']) ?>"><?php endif; ?>
                <label>Nouveau logo <input type="file" name="blason" accept="image/*"></label>
                <button>Mettre à jour</button>
              </form>
              <form method="post" action="?route=ecuries&action=delete" onsubmit="return confirm('Supprimer cette écurie&nbsp;?')">
                <input type="hidden" name="id" value="<?= $ecurie['id'] ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="danger">Supprimer</button>
              </form>
          </details>
        </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
