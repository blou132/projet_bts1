<section class="auth-wrapper">
  <div class="auth-card">
    <h2>Creer un compte</h2>
    <p class="auth-subtitle">Inscrivez-vous pour acceder au paddock.</p>

    <?php if (!empty($flashError)): ?>
      <div class="alert">
        <p><?= htmlspecialchars($flashError) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="?route=auth&action=store">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
      <?php if (!empty($_GET['redirect'])): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars((string)$_GET['redirect']) ?>">
      <?php endif; ?>
      <label>
        Nom
        <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
      </label>
      <label>
        Adresse e-mail
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </label>
      <label>
        Mot de passe
        <input type="password" name="password" required>
      </label>
      <label>
        Confirmation du mot de passe
        <input type="password" name="password_confirm" required>
      </label>
      <button type="submit" class="btn">Creer mon compte</button>
    </form>

    <p class="auth-subtitle">Deja inscrit ? <a href="?route=auth&action=login">Se connecter</a></p>
  </div>
</section>
