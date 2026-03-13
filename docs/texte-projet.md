# Texte projet (dossier / soutenance)

## 1) Contexte

Le projet TPFormula1 est une application web pedagogique realisee en PHP.  
Le besoin est de disposer d'un outil simple pour:
- gerer des ecuries et des pilotes,
- suivre un calendrier de courses,
- saisir des resultats,
- proposer un systeme de paris podium.

## 2) Objectifs fonctionnels

- Consulter les pages publiques: accueil, calendrier, classement pilotes, classement paris.
- Permettre a un administrateur de gerer les donnees metier (ecuries, pilotes, resultats).
- Permettre a un utilisateur connecte de creer/modifier son pari podium pour une course.

## 3) Choix techniques

- Langage: PHP 8+
- Base de donnees: MySQL (PDO, requetes preparees)
- Architecture: front controller `index.php` + controllers + views
- Front-end: HTML/CSS/JS sans framework lourd

Ces choix gardent le projet lisible et maintenable pour un contexte BTS SIO SLAM.

## 4) Securite et qualite

- Jeton CSRF sur toutes les actions POST.
- Controle d'acces par session et role (`admin` / `user` / visiteur).
- Validation serveur des donnees avant ecriture en base.
- Echapement des sorties HTML pour limiter les injections.

## 5) Resultats obtenus

- Application fonctionnelle de bout en bout (de la consultation au CRUD et au pari).
- Base initialisable rapidement avec `init_db.php`.
- Documentation technique disponible dans `docs/`.
- Interface responsive maintenue avec une feuille CSS compacte.

## 6) Axes d'amelioration

- Ajouter des tests automatises (PHPUnit + tests E2E navigateur).
- Ajouter un systeme de logs et de monitoring simple.
- Proposer une API JSON en lecture.
- Mettre en place des URL rewriter (sans query string) pour production.

## 7) Base pedagogique utilisee

La structure de documentation a ete alignee sur les themes du depot cours:
`https://github.com/blou132/cour.git`

Themes de reference utilises:
- README et organisation d'un mini projet CRUD PHP.
- Validation formulaire cote serveur.
- Notions d'architecture applicative (MVC / separation des couches).
- Bonnes pratiques de gestion de version Git.
