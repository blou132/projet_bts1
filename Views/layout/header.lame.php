<?php
use App\Routes\Web;
$web = new Web();
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TPFormula1</title>
  <link rel="stylesheet" href="Public/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="brand">
    <span class="brand-accent">F1</span> Paddock Manager
    <small class="brand-tagline">Administration des Grands Prix, écuries et pilotes</small>
  </div>
  <div class="header-right">
    <?= $web->menu(); ?>
    <?php if (!empty($currentUser)): ?>
    <div class="user-chip">
      <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? $currentUser['email'] ?? '') ?></span>
      <a class="user-logout" href="?route=auth&action=logout">Déconnexion</a>
    </div>
    <?php else: ?>
    <a class="btn login-link" href="?route=auth&action=login">Se connecter</a>
    <?php endif; ?>
    <a class="f1-logo" href="https://www.formula1.com" target="_blank" rel="noopener">
      <img src="Public/assets/f1-logo.svg" alt="F1 logo">
    </a>
  </div>
</header>
<main class="container">
