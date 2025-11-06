<section>
  <h2>Grands Prix</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?>
        <p><?= htmlspecialchars($e) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($currentUser)): ?>
    <form method="post" enctype="multipart/form-data" action="?route=championnats&action=store">
      <fieldset>
        <legend>Ajouter un Grand Prix</legend>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <label>Nom du Grand Prix <input type="text" name="nom" required></label><br/>
        <label>Pays hôte <input name="pays" type="text" required></label><br/>
        <label>Affiche / Logo</label>
<label class="file-upload btn">
  <input type="file" name="blason" accept="image/*">
  <span>Choisir une affiche</span>
</label><br/><br/>
        <button class="file-upload btn">Créer</button>
      </fieldset>
    </form>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>ID</th><th>Grand Prix</th><th>Pays hôte</th><th>Affiche</th><?php if (!empty($currentUser)): ?><th>Actions</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($championnats as $c): ?>
        <tr>
          <td><?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['nom']) ?></td>
          <td><?= htmlspecialchars($c['pays']) ?></td>
          <td>
            <?php if ($c['blason']): ?>
              <img src="<?= htmlspecialchars($c['blason']) ?>" alt="affiche du Grand Prix" class="thumb">
            <?php endif; ?>
          </td>
          <?php if (!empty($currentUser)): ?>
          <td>
            <details>
              <summary>Éditer</summary>
                <form method="post" enctype="multipart/form-data" action="?route=championnats&action=update">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                  <label>Nom du Grand Prix <input name="nom" value="<?= htmlspecialchars($c['nom']) ?>" required></label>
                  <label>Pays hôte <input name="pays" value="<?= htmlspecialchars($c['pays']) ?>" required></label>
                  <?php if ($c['blason']): ?>
                    <input type="hidden" name="blason_exist" value="<?= htmlspecialchars($c['blason']) ?>">
                  <?php endif; ?>
                  <label>Nouvelle affiche <input type="file" name="blason" accept="image/*"></label>
                  <button>Mettre à jour</button>
                </form>
                <form method="post" action="?route=championnats&action=delete" onsubmit="return confirm('Supprimer ce Grand Prix&nbsp;?')">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
