<section class="results-panel bet-panel">
  <header class="results-head">
    <h3>Pari sur le podium</h3>
    <p>Paris reserves aux comptes connectes. Ouverture apres la course precedente, cloture J-1.</p>
  </header>

  <?php if (empty($currentUser)): ?>
    <div class="bet-login" id="bet">
      <p class="auth-subtitle">Connectez-vous pour parier.</p>
      <form method="post" action="?route=auth&action=authenticate">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars('?route=calendrier&action=course&course=' . (int)$course['id'] . '#bet') ?>">
        <label>
          Adresse e-mail
          <input type="email" name="email" required>
        </label>
        <label>
          Mot de passe
          <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn">Se connecter</button>
      </form>
      <p class="auth-subtitle">Pas encore de compte ? <a href="?route=auth&action=register&redirect=<?= htmlspecialchars('?route=calendrier&action=course&course=' . (int)$course['id'] . '#bet') ?>">Creer un compte</a></p>
    </div>
  <?php else: ?>
    <?php if (!empty($betErrors)): ?>
      <div class="alert">
        <?php foreach ($betErrors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($betFlash)): ?>
      <div class="alert success">
        <p><?= htmlspecialchars($betFlash) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!$betWindow['isOpen']): ?>
      <div class="alert">
        <p><?= htmlspecialchars($betWindow['reason'] ?? 'Paris fermes.') ?></p>
      </div>
    <?php endif; ?>

    <div class="bet-meta">
      <?php if (!empty($betWindow['openAt'])): ?>
        <span>Ouverture: <?= htmlspecialchars($betWindow['openAt']->format('d/m/Y')) ?></span>
      <?php else: ?>
        <span>Ouverture: immediate</span>
      <?php endif; ?>
      <span>Cloture: <?= htmlspecialchars($betWindow['closeAt']->format('d/m/Y')) ?></span>
    </div>

    <?php if (!empty($userBet)): ?>
      <div class="bet-summary">
        <strong>Votre pari actuel</strong>
        <ol>
          <?php
            $first = $driversById[(int)$userBet['first_joueur_id']] ?? null;
            $second = $driversById[(int)$userBet['second_joueur_id']] ?? null;
            $third = $driversById[(int)$userBet['third_joueur_id']] ?? null;
          ?>
          <li><?= htmlspecialchars(($first['prenom'] ?? '') . ' ' . ($first['nom'] ?? '')) ?></li>
          <li><?= htmlspecialchars(($second['prenom'] ?? '') . ' ' . ($second['nom'] ?? '')) ?></li>
          <li><?= htmlspecialchars(($third['prenom'] ?? '') . ' ' . ($third['nom'] ?? '')) ?></li>
        </ol>
      </div>
    <?php endif; ?>

    <?php if ($betWindow['isOpen']): ?>
      <form class="result-form bet-form" method="post" action="?route=calendrier&action=placeBet">
        <fieldset>
          <legend>Composer votre podium</legend>
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
          <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
          <label>1er
            <select name="first_pilote_id" required>
              <option value="">- choisir un pilote -</option>
              <?php foreach ($drivers as $driver): ?>
                <option value="<?= (int)$driver['id'] ?>" <?= !empty($userBet) && (int)$userBet['first_joueur_id'] === (int)$driver['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom'] . ' - ' . $driver['ecurie']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>2e
            <select name="second_pilote_id" required>
              <option value="">- choisir un pilote -</option>
              <?php foreach ($drivers as $driver): ?>
                <option value="<?= (int)$driver['id'] ?>" <?= !empty($userBet) && (int)$userBet['second_joueur_id'] === (int)$driver['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom'] . ' - ' . $driver['ecurie']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>3e
            <select name="third_pilote_id" required>
              <option value="">- choisir un pilote -</option>
              <?php foreach ($drivers as $driver): ?>
                <option value="<?= (int)$driver['id'] ?>" <?= !empty($userBet) && (int)$userBet['third_joueur_id'] === (int)$driver['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($driver['prenom'] . ' ' . $driver['nom'] . ' - ' . $driver['ecurie']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <button><?= !empty($userBet) ? 'Mettre a jour mon pari' : 'Valider mon pari' ?></button>
        </fieldset>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</section>
