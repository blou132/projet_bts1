<?php
$remaining = (int)($throttleRemaining ?? 0);
$isBlocked = $remaining > 0;
?>

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

    <?php if ($isBlocked): ?>
      <div class="alert js-lockout" data-seconds="<?= $remaining ?>">
        <p>Trop de tentatives. Reessayez dans <strong class="js-lockout-counter"></strong>.</p>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars(route_path('auth/authenticate')) ?>">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
      <?php if (!empty($_GET['redirect'])): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars((string)$_GET['redirect']) ?>">
      <?php endif; ?>
      <label>
        Adresse e-mail
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" <?= $isBlocked ? 'disabled' : '' ?> required>
      </label>
      <label>
        Mot de passe
        <input type="password" name="password" <?= $isBlocked ? 'disabled' : '' ?> required>
      </label>
      <button type="submit" class="btn" <?= $isBlocked ? 'disabled' : '' ?>>Se connecter</button>
    </form>

    <p class="auth-subtitle">Pas encore de compte ? <a href="<?= htmlspecialchars(route_path('auth/register')) ?>">Creer un compte</a></p>
  </div>
</section>
