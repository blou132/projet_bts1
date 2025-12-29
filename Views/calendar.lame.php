<section class="season-view">
  <header class="season-head">
    <h2>Calendrier <?= htmlspecialchars((string)($year ?? date('Y'))) ?></h2>
    <p>Consultez chaque Grand Prix et retrouvez le classement complet en cliquant sur une manche.</p>
  </header>

  <div class="calendar-grid">
    <?php foreach ($calendar as $event): ?>
      <?php
        $isActive = !empty($selectedCourseId) && (int)$selectedCourseId === (int)$event['id'];
        $date = new DateTime($event['date_course']);
      ?>
      <a class="calendar-card<?= $isActive ? ' is-active' : '' ?>" href="?route=calendrier&course=<?= (int)$event['id'] ?>">
        <header>
          <span class="calendar-round">Manche <?= (int)$event['ordre'] ?></span>
          <?php if (!empty($event['flag'])): ?>
            <span class="calendar-flag" aria-hidden="true"><?= htmlspecialchars($event['flag']) ?></span>
          <?php endif; ?>
        </header>
        <h3><?= htmlspecialchars($event['nom']) ?></h3>
        <p class="calendar-meta"><?= htmlspecialchars($event['ville']) ?> · <?= htmlspecialchars($date->format('d/m/Y')) ?></p>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!empty($selectedCourse)): ?>
    <section class="results-panel">
      <header class="results-head">
        <h3>Résultats du <?= htmlspecialchars($selectedCourse['nom']) ?></h3>
        <p><?= htmlspecialchars($selectedCourse['ville']) ?> · <?= htmlspecialchars((new DateTime($selectedCourse['date_course']))->format('d F Y')) ?></p>
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
                        <input type="hidden" name="course_id" value="<?= (int)$selectedCourse['id'] ?>">
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
            <input type="hidden" name="course_id" value="<?= (int)$selectedCourse['id'] ?>">
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
  <?php endif; ?>
</section>
