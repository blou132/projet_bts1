<section>
  <h2>Pilotes</h2>
  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($isAdmin)): ?>
    <form method="post" enctype="multipart/form-data" action="?route=pilotes&action=store">
      <fieldset>
        <legend>Ajouter un pilote</legend>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <label>Nom <input name="nom" type="text" required></label><br/>
        <label>Prénom <input name="prenom" type="text" required></label><br/>
        <label>Rôle <input name="poste" type="text" placeholder="Pilote titulaire / Réserve..." required></label><br/>
        <label>Écurie
          <select name="id_ecurie" required>
            <option value="">— choisir —</option>
            <?php foreach ($ecuries as $ecurie): ?>
              <option value="<?= $ecurie['id'] ?>"><?= htmlspecialchars($ecurie['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </label><br/>
        <label>Portrait</label>
          <label class="file-upload btn">
            <input type="file" name="photo" accept="image/*">
            <span>Téléverser un portrait</span>
          </label><br/><br/>
        <button class="file-upload btn">Créer</button>
      </fieldset>
    </form>
  <?php endif; ?>

  <div class="table-tools">
    <input class="filter-input" type="search" placeholder="Rechercher un pilote..." data-filter-table="pilotes-table" data-filter-count="pilotes-count">
    <span class="filter-count" id="pilotes-count"></span>
    <span class="sort-hint">Cliquer sur un titre pour trier</span>
  </div>

  <table id="pilotes-table" data-sortable="true">
    <thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Rôle</th><th>Écurie</th><th data-sort="false">Portrait</th><?php if (!empty($isAdmin)): ?><th data-sort="false">Actions</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($pilotes as $pilote): ?>
      <tr>
        <td><?= $pilote['id'] ?></td>
        <td><?= htmlspecialchars($pilote['nom']) ?></td>
        <td><?= htmlspecialchars($pilote['prenom']) ?></td>
        <td><?= htmlspecialchars($pilote['poste']) ?></td>
        <td><?= htmlspecialchars($pilote['ecurie']) ?></td>
        <td><?php if ($pilote['photo']): ?><img src="<?= htmlspecialchars($pilote['photo']) ?>" alt="portrait pilote" class="thumb"><?php endif; ?></td>
        <?php if (!empty($isAdmin)): ?>
        <td>
          <details>
            <summary>Éditer</summary>
              <form method="post" enctype="multipart/form-data" action="?route=pilotes&action=update">
                <input type="hidden" name="id" value="<?= $pilote['id'] ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                <label>Nom <input name="nom" value="<?= htmlspecialchars($pilote['nom']) ?>" required></label>
                <label>Prénom <input name="prenom" value="<?= htmlspecialchars($pilote['prenom']) ?>" required></label>
                <label>Rôle <input name="poste" value="<?= htmlspecialchars($pilote['poste']) ?>" required></label>
                <label>Écurie
                  <select name="id_ecurie" required>
                    <?php foreach ($ecuries as $ecurie): ?>
                      <option value="<?= $ecurie['id'] ?>" <?= $ecurie['id']==$pilote['id_ecurie']?'selected':'' ?>><?= htmlspecialchars($ecurie['nom']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <?php if ($pilote['photo']): ?><input type="hidden" name="photo_exist" value="<?= htmlspecialchars($pilote['photo']) ?>"><?php endif; ?>
                <label>Nouveau portrait <input type="file" name="photo" accept="image/*"></label>
                <button>Mettre à jour</button>
              </form>
              <form method="post" action="?route=pilotes&action=delete" onsubmit="return confirm('Supprimer ce pilote&nbsp;?')">
                <input type="hidden" name="id" value="<?= $pilote['id'] ?>">
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
