</main>
<footer class="site-footer">
  <small>&copy; <?= date('Y'); ?> Formula 1 Manager — Projet pédagogique BTS SIO SLAM</small>
</footer>
<?php if (empty($currentUser)): ?>
  <?php
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/accueil';
  $modalRedirect = is_string($requestUri) && $requestUri !== '' ? $requestUri : '/accueil';
  ?>
  <div class="modal" id="login-modal" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>
    <div class="modal-card auth-card" role="dialog" aria-modal="true" aria-labelledby="login-modal-title">
      <button type="button" class="modal-close" data-close-modal aria-label="Fermer">&times;</button>
      <h2 id="login-modal-title">Connexion</h2>
      <p class="auth-subtitle">Connectez-vous pour acceder au paddock.</p>
      <form class="modal-form" method="post" action="/auth/authenticate">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($modalRedirect) ?>" class="js-modal-redirect">
        <label>
          Adresse e-mail
          <input type="email" name="email" autocomplete="email" required>
        </label>
        <label>
          Mot de passe
          <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button type="submit" class="btn">Se connecter</button>
      </form>
      <p class="auth-subtitle">Pas encore de compte ? <a class="js-modal-register" href="/auth/register?redirect=<?= htmlspecialchars(rawurlencode($modalRedirect)) ?>">Creer un compte</a></p>
    </div>
  </div>
<?php endif; ?>
<script src="<?= htmlspecialchars(asset_path('Public/js/app.js')) ?>"></script>
</body>
</html>
