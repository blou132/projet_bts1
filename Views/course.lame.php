<section class="season-view">
  <header class="season-head">
    <h2><?= htmlspecialchars($course['nom']) ?></h2>
    <p><?= htmlspecialchars($course['ville']) ?> · <?= htmlspecialchars((new DateTime($course['date_course']))->format('d F Y')) ?></p>
    <p><a href="?route=calendrier">← Retour au calendrier</a></p>
  </header>

  <section class="results-panel">
    <header class="results-head">
      <h3>Résultats</h3>
    </header>

    <?php if (!empty($calendarErrors)): ?>
      <div class="alert">
        <?php foreach ($calendarErrors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($calendarFlash)): ?>
      <div class="alert success">
        <p><?= htmlspecialchars($calendarFlash) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($courseResults)): ?>
      <table class="results-table">
        <thead>
          <tr>
            <th>Position</th>
            <th>Pilote</th>
            <th>Écurie</th>
            <th>Points</th>
            <?php if (!empty($currentUser)): ?>
              <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($courseResults as $row): ?>
            <tr>
              <td><?= (int)$row['position'] ?></td>
              <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
              <td><?= htmlspecialchars($row['ecurie']) ?></td>
              <td><?= (int)$row['points'] ?></td>
              <?php if (!empty($currentUser)): ?>
                <td class="table-actions">
                  <details>
                    <summary>Modifier</summary>
                    <form method="post" action="?route=calendrier&action=updateResult">
                      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                      <input type="hidden" name="result_id" value="<?= (int)$row['result_id'] ?>">
                      <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
                      <label>Pilote
                        <select name="pilote_id" required>
                          <?php foreach ($drivers as $driver): ?>
                            <option value="<?= (int)$driver['id'] ?>" <?= (int)$driver['id'] === (int)$row['pilote_id'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom'] . ' — ' . $driver['ecurie']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <label>Position <input type="number" min="1" name="position" value="<?= (int)$row['position'] ?>" required></label>
                      <label>Points <input type="number" min="0" name="points" value="<?= (int)$row['points'] ?>" required></label>
                      <button>Mettre à jour</button>
                    </form>
                    <form method="post" action="?route=calendrier&action=deleteResult" onsubmit="return confirm('Supprimer ce résultat ?');">
                      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                      <input type="hidden" name="result_id" value="<?= (int)$row['result_id'] ?>">
                      <button class="danger">Supprimer</button>
                    </form>
                  </details>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="results-empty">Résultats à confirmer pour cette manche.</p>
    <?php endif; ?>

    <?php if (!empty($currentUser)): ?>
      <form class="result-form" method="post" action="?route=calendrier&action=addResult">
        <fieldset>
          <legend>Ajouter un résultat</legend>
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
          <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
          <label>Pilote
            <select name="pilote_id" required>
              <option value="">— choisir un pilote —</option>
              <?php foreach ($drivers as $driver): ?>
                <option value="<?= (int)$driver['id'] ?>">
                  <?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom'] . ' — ' . $driver['ecurie']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Position <input type="number" min="1" name="position" required></label>
          <label>Points <input type="number" min="0" name="points" required></label>
          <button>Ajouter</button>
        </fieldset>
      </form>
    <?php endif; ?>
  </section>
</section>
