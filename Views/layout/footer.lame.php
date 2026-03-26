</main>
<footer class="site-footer">
  <small>&copy; <?= date('Y'); ?> Formula 1 Manager — Projet pédagogique BTS SIO SLAM</small>
  <p class="footer-links">
    <a href="/mentions-legales.html">Mentions legales</a>
    <span aria-hidden="true">|</span>
    <a href="/politique-confidentialite.html">Politique de confidentialite</a>
  </p>
</footer>
<aside class="cookie-banner" id="cookie-banner" hidden>
  <p>Ce site utilise des cookies techniques de session. En continuant, vous acceptez leur utilisation.</p>
  <button type="button" class="btn" id="cookie-accept">OK</button>
</aside>
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
