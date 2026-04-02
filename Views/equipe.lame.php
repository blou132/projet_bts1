<section class="teams">
  <header class="teams-head">
    <h2>Écuries</h2>
    <p>Chaque equipe regroupe ses pilotes. Cliquez sur une equipe pour voir son effectif.</p>
  </header>

  <?php if (!empty($errors) || !empty($pilotErrors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
      <?php foreach ($pilotErrors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($pilotFlash)): ?>
    <div class="alert success">
      <p><?= htmlspecialchars($pilotFlash) ?></p>
    </div>
  <?php endif; ?>

  <?php if (!empty($isAdmin)): ?>
    <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars(route_path('ecuries/store')) ?>" class="team-form">
      <fieldset>
        <legend>Ajouter une ecurie</legend>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <label>Nom <input name="nom" type="text" required></label>
        <label>Logo</label>
          <label class="file-upload btn">
            <input type="file" name="blason" accept="image/*">
            <span>Choisir un logo</span>
          </label>
        <button class="file-upload btn">Creer</button>
      </fieldset>
    </form>
  <?php endif; ?>

  <div class="team-list">
    <?php foreach ($ecuries as $ecurie): ?>
      <?php
        $ecurieId = (int)$ecurie['id'];
        $pilotes = $pilotesParEcurie[$ecurieId] ?? [];
        $pilotCount = count($pilotes);
        $logo = $ecurie['blason'] ?: 'Public/assets/logos/ferrari.svg';
        $isOpen = !empty($focusId) && $focusId === $ecurieId;
      ?>
      <details class="team-card" id="team-<?= $ecurieId ?>" <?= $isOpen ? 'open' : '' ?>>
        <summary class="team-summary">
          <div class="team-summary-left">
            <img src="<?= htmlspecialchars(asset_path($logo)) ?>" alt="logo ecurie" class="team-logo">
            <div class="team-summary-text">
              <strong><?= htmlspecialchars($ecurie['nom']) ?></strong>
              <span>Equipe officielle</span>
            </div>
          </div>
          <span class="team-count"><?= $pilotCount ?> pilote<?= $pilotCount > 1 ? 's' : '' ?></span>
        </summary>
        <div class="team-body">
          <?php if (!empty($pilotes)): ?>
            <div class="team-pilots">
              <?php foreach ($pilotes as $pilote): ?>
                <?php
                  $photo = $pilote['photo'] ?: 'Public/assets/pilotes/hamilton.svg';
                ?>
                <article class="pilot-card">
                  <div class="pilot-main">
                    <img src="<?= htmlspecialchars(asset_path($photo)) ?>" alt="portrait pilote" class="pilot-thumb">
                    <div>
                      <strong><?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?></strong>
                      <span class="pilot-role"><?= htmlspecialchars($pilote['poste']) ?></span>
                    </div>
                  </div>
                  <?php if (!empty($isAdmin)): ?>
                    <details class="pilot-actions">
                      <summary>Modifier</summary>
                      <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars(route_path('pilotes/update')) ?>">
                        <input type="hidden" name="id" value="<?= (int)$pilote['id'] ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                        <label>Nom <input name="nom" value="<?= htmlspecialchars($pilote['nom']) ?>" required></label>
                        <label>Prenom <input name="prenom" value="<?= htmlspecialchars($pilote['prenom']) ?>" required></label>
                        <label>Role <input name="poste" value="<?= htmlspecialchars($pilote['poste']) ?>" required></label>
                        <label>Ecurie
                          <select name="id_ecurie" required>
                            <?php foreach ($ecuries as $opt): ?>
                              <option value="<?= $opt['id'] ?>" <?= (int)$opt['id'] === (int)$pilote['id_equipe'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt['nom']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <?php if ($pilote['photo']): ?><input type="hidden" name="photo_exist" value="<?= htmlspecialchars($pilote['photo']) ?>"><?php endif; ?>
                        <label>Nouveau portrait <input type="file" name="photo" accept="image/*"></label>
                        <button>Mettre a jour</button>
                      </form>
                      <form method="post" action="<?= htmlspecialchars(route_path('pilotes/delete')) ?>" onsubmit="return confirm('Supprimer ce pilote ?')">
                        <input type="hidden" name="id" value="<?= (int)$pilote['id'] ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                        <button class="danger">Supprimer</button>
                      </form>
                    </details>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="results-empty">Aucun pilote assigne a cette ecurie.</p>
          <?php endif; ?>

          <?php if (!empty($isAdmin)): ?>
            <div class="team-admin">
              <details class="team-admin-block">
                <summary>Modifier l'ecurie</summary>
                <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars(route_path('ecuries/update')) ?>">
                  <input type="hidden" name="id" value="<?= $ecurieId ?>">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                  <label>Nom <input name="nom" value="<?= htmlspecialchars($ecurie['nom']) ?>" required></label>
                  <?php if ($ecurie['blason']): ?><input type="hidden" name="blason_exist" value="<?= htmlspecialchars($ecurie['blason']) ?>"><?php endif; ?>
                  <label>Nouveau logo <input type="file" name="blason" accept="image/*"></label>
                  <button>Mettre a jour</button>
                </form>
                <form method="post" action="<?= htmlspecialchars(route_path('ecuries/delete')) ?>" onsubmit="return confirm('Supprimer cette ecurie ?')">
                  <input type="hidden" name="id" value="<?= $ecurieId ?>">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                  <button class="danger">Supprimer</button>
                </form>
              </details>

              <details class="team-admin-block">
                <summary>Ajouter un pilote</summary>
                <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars(route_path('pilotes/store')) ?>">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                  <input type="hidden" name="id_ecurie" value="<?= $ecurieId ?>">
                  <label>Nom <input name="nom" type="text" required></label>
                  <label>Prenom <input name="prenom" type="text" required></label>
                  <label>Role <input name="poste" type="text" placeholder="Pilote titulaire / Reserve..." required></label>
                  <label>Portrait <input type="file" name="photo" accept="image/*"></label>
                  <button>Ajouter le pilote</button>
                </form>
              </details>
            </div>
          <?php endif; ?>
        </div>
      </details>
    <?php endforeach; ?>
  </div>
</section>
