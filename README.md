# TPFormula1 — Gestion F1 (BTS SIO SLAM)

Application PHP (MVC léger) pour gérer un univers Formule 1 : Grands Prix, écuries et pilotes.  
La stack a été adaptée pour fonctionner avec **MySQL** (PDO, InnoDB, UTF-8).

## Installation

1. **Cloner** le projet puis se placer dans le dossier `F1/`.
2. **Créer une base MySQL** (exemple) :
   ```sql
   CREATE DATABASE tpformula1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. **Configurer la connexion MySQL** : deux options
   - via fichier `.env` (recommandé) :
     ```bash
     cp .env.example .env
     # éditez .env pour refléter votre configuration (ex. DB_NAME=projetF1, DB_PASS=123456789)
     ```
   - ou via export dans votre terminal :
     ```bash
     export DB_HOST=127.0.0.1
     export DB_PORT=3306
     export DB_NAME=tpformula1
     export DB_USER=root
     export DB_PASS=secret
     ```
4. **Initialiser la base** avec les données de démonstration :
   ```bash
   php init_db.php
   ```
5. **Lancer le serveur PHP interne** :
   ```bash
   php -S localhost:8000
   ```
6. Ouvrir `http://localhost:8000/index.php`.

## Navigation

- `?route=accueil` : tableau de bord (statistiques, écuries, pilotes, Grands Prix).
- `?route=championnats` : CRUD des Grands Prix.
- `?route=equipes` : CRUD des écuries.
- `?route=joueurs` : CRUD des pilotes.
- `?route=jointure` : vue synthétique pilotes/écuries.
- `?route=calendrier` : calendrier saison 2026.
- `?route=classements` : classement pilotes (heatmap démo).

## Authentification

- URL de connexion : `?route=auth&action=login`
- Identifiant par défaut (chargé par `init_db.php`)  
  - Email : `admin@example.com`  
  - Mot de passe : `admin123`
- Les pages publiques (accueil, listes, calendrier, classements) restent accessibles en lecture sans connexion.  
  La modification (ajout, édition, suppression) requiert en revanche une session active.

## Dossier utile

- `Controllers/` : logique métier.
- `Models/` : entités de domaine.
- `Views/` : templates.
- `Database/Database.php` : gestion MySQL + migrations.
- `Public/assets/` : logos, portraits, affiches.

## Scripts

- `init_db.php` : remet la base à zéro (`DROP TABLE` + seed) et recharge les données de démo.
  > Attention : nécessite des droits suffisants sur le schéma cible.

## Dépendances

Aucune dépendance Composer. PHP ≥ 8.1 et MySQL ≥ 5.7/8 sont recommandés.  
Pour production, pensez à :
- sécuriser l’accès aux scripts (`init_db.php`),
- créer un utilisateur MySQL dédié avec droits limités,
- adapter les valeurs par défaut (`DB_*`).
