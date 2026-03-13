<?php
use App\Routes\Web;
use App\Security\Csrf;

$web = new Web();
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin = $isAdmin ?? (!empty($currentUser['role']) && $currentUser['role'] === 'admin');
if (!isset($csrfToken)) {
  $csrfToken = Csrf::getToken();
}
if (!function_exists('asset_path')) {
  function asset_path(string $path): string
  {
    if ($path === '') {
      return '/';
    }
    if (preg_match('#^(https?:)?//#i', $path) === 1) {
      return $path;
    }
    return '/' . ltrim($path, '/');
  }
}
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TPFormula1</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_path('Public/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
  <div class="brand">
    <span class="brand-accent"><img src="<?= htmlspecialchars(asset_path('Public/assets/pm-emblem.svg')) ?>" alt="Logo Paddock Manager"></span>
    <div class="brand-copy">
      <span class="brand-title">Paddock Manager</span>
      <small class="brand-tagline">Administration des Grands Prix, écuries et pilotes</small>
    </div>
  </div>
  <div class="header-right">
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">Menu</button>
    <?= $web->menu(); ?>
    <div class="header-actions">
      <?php if (!empty($currentUser)): ?>
      <div class="user-chip">
        <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? $currentUser['email'] ?? '') ?></span>
        <a class="user-logout" href="/auth/logout">Déconnexion</a>
      </div>
      <?php else: ?>
      <a class="btn login-link" href="/auth/login" data-open-modal="login">Se connecter</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
