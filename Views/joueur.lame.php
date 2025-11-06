<section>
  <h2>Pilotes</h2>
  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" action="?route=joueurs&action=store">
    <fieldset>
      <legend>Ajouter un pilote</legend>
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
      <label>Nom <input name="nom" type="text" required></label><br/>
      <label>Prénom <input name="prenom" type="text" required></label><br/>
      <label>Rôle <input name="poste" type="text" placeholder="Pilote titulaire / Réserve..." required></label><br/>
      <label>Écurie
        <select name="id_equipe" required>
          <option value="">— choisir —</option>
          <?php foreach ($equipes as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
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

  <table>
    <thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Rôle</th><th>Écurie</th><th>Portrait</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($joueurs as $j): ?>
      <tr>
        <td><?= $j['id'] ?></td>
        <td><?= htmlspecialchars($j['nom']) ?></td>
        <td><?= htmlspecialchars($j['prenom']) ?></td>
        <td><?= htmlspecialchars($j['poste']) ?></td>
        <td><?= htmlspecialchars($j['equipe']) ?></td>
        <td><?php if ($j['photo']): ?><img src="<?= htmlspecialchars($j['photo']) ?>" alt="portrait pilote" class="thumb"><?php endif; ?></td>
        <td>
          <details>
            <summary>Éditer</summary>
            <form method="post" enctype="multipart/form-data" action="?route=joueurs&action=update">
              <input type="hidden" name="id" value="<?= $j['id'] ?>">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
              <label>Nom <input name="nom" value="<?= htmlspecialchars($j['nom']) ?>" required></label>
              <label>Prénom <input name="prenom" value="<?= htmlspecialchars($j['prenom']) ?>" required></label>
              <label>Rôle <input name="poste" value="<?= htmlspecialchars($j['poste']) ?>" required></label>
              <label>Écurie
                <select name="id_equipe" required>
                  <?php foreach ($equipes as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $e['id']==$j['id_equipe']?'selected':'' ?>><?= htmlspecialchars($e['nom']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <?php if ($j['photo']): ?><input type="hidden" name="photo_exist" value="<?= htmlspecialchars($j['photo']) ?>"><?php endif; ?>
              <label>Nouveau portrait <input type="file" name="photo" accept="image/*"></label>
              <button>Mettre à jour</button>
            </form>
            <form method="post" action="?route=joueurs&action=delete" onsubmit="return confirm('Supprimer ce pilote&nbsp;?')">
              <input type="hidden" name="id" value="<?= $j['id'] ?>">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
              <button class="danger">Supprimer</button>
            </form>
          </details>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
