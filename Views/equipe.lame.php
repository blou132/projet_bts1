<section>
  <h2>Écuries</h2>
  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" action="?route=equipes&action=store">
    <fieldset>
      <legend>Ajouter une écurie</legend>
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
      <label>Nom <input name="nom" type="text" required></label><br/>
      <label>Base (Ville) <input name="ville" type="text" required></label><br/>
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

  <table>
    <thead><tr><th>ID</th><th>Écurie</th><th>Base</th><th>Grand Prix</th><th>Logo</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($equipes as $e): ?>
      <tr>
        <td><?= $e['id'] ?></td>
        <td><?= htmlspecialchars($e['nom']) ?></td>
        <td><?= htmlspecialchars($e['ville']) ?></td>
        <td><?= htmlspecialchars($e['championnat']) ?></td>
        <td><?php if ($e['blason']): ?><img src="<?= htmlspecialchars($e['blason']) ?>" alt="logo écurie" class="thumb"><?php endif; ?></td>
        <td>
          <details>
            <summary>Éditer</summary>
            <form method="post" enctype="multipart/form-data" action="?route=equipes&action=update">
              <input type="hidden" name="id" value="<?= $e['id'] ?>">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
              <label>Nom <input name="nom" value="<?= htmlspecialchars($e['nom']) ?>" required></label>
              <label>Base (Ville) <input name="ville" value="<?= htmlspecialchars($e['ville']) ?>" required></label>
              <label>Grand Prix associé
                <select name="id_championnat" required>
                  <?php foreach ($championnats as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id']==$e['id_championnat']?'selected':'' ?>><?= htmlspecialchars($c['nom']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <?php if ($e['blason']): ?><input type="hidden" name="blason_exist" value="<?= htmlspecialchars($e['blason']) ?>"><?php endif; ?>
              <label>Nouveau logo <input type="file" name="blason" accept="image/*"></label>
              <button>Mettre à jour</button>
            </form>
            <form method="post" action="?route=equipes&action=delete" onsubmit="return confirm('Supprimer cette écurie&nbsp;?')">
              <input type="hidden" name="id" value="<?= $e['id'] ?>">
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
