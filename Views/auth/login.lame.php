<section class="auth-wrapper">
  <div class="auth-card">
    <h2>Connexion</h2>
    <p class="auth-subtitle">Identifiez-vous pour accéder au paddock.</p>

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

    <form method="post" action="?route=auth&action=authenticate">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
      <label>
        Adresse e-mail
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </label>
      <label>
        Mot de passe
        <input type="password" name="password" required>
      </label>
      <button type="submit" class="btn">Se connecter</button>
    </form>
  </div>
</section>
