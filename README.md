# TPFormula1 - Gestion F1 (BTS SIO SLAM)

Application PHP (MVC léger) pour gérer des écuries, pilotes, calendrier 2026, résultats et paris podium.

## Guide examinateur (1 minute)

Base URL locale : `http://localhost:8000`

Comptes de démonstration :
- Admin : `admin@example.com` / `admin123`
- Utilisateur : `example@gmail.com` / `123456789`

Parcours express (2-3 min) :
1. `/accueil` -> vue globale
2. `/docs` (ou `/documentation`) -> hub docs (texte, architecture, tests, credentials)
3. `/calendrier` puis `/calendrier/course/ID` -> détails course + pari
4. `/paris` -> classement global des parieurs
5. `/ecuries` (admin) -> CRUD écuries/pilotes

Différence importante :
- `Classement des paris` sur `/calendrier/course/ID` = classement de la course en cours.
- `Classement des paris` sur `/paris` = classement cumulé saison.

## Prérequis

- PHP 8.1+
- MySQL 5.7+ / 8+

## Installation rapide

1. Créer la base :
   ```sql
   CREATE DATABASE tpformula1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Configurer `.env` :
   ```bash
   cp .env.example .env
   ```
3. Initialiser la base et les données de démo :
   ```bash
   php init_db.php
   ```
4. Lancer le serveur :
   ```bash
   php -S localhost:8000 router.php
   ```
5. Ouvrir `http://localhost:8000/accueil`

## Routes principales

- `/accueil` : tableau de bord.
- `/docs` : alias court vers la documentation.
- `/documentation` : hub de documentation.
- `/ecuries` : gestion écuries + pilotes.
- `/calendrier` : calendrier saison 2026.
- `/calendrier/course/ID` : détail course, résultats et pari podium.
- `/classements` : classement pilotes cumulé.
- `/paris` : classement global des paris.
- `/auth/login` : connexion.
- `/auth/register` : création de compte.

## Tests automatiques

Runner de tests local :

```bash
php tests/run.php
```

Fichiers de tests :
- `tests/Unit/CsrfTest.php`
- `tests/Unit/HtmlControllerTest.php`
- `tests/Unit/ModelsTest.php`

## Documentation PHP (PHPDoc / phpDocumentor)

Génération HTML :

```bash
phpdoc -d Controllers,Models,Security,Database,Routes -t docs/api --ignore "Public/*,Views/*,tests/*,docs/api/*"
```

Sortie : `docs/api`

## Variables d'environnement

Exemple (`.env.example`) :

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=tpformula1
DB_USER=root
DB_PASS=secret
DB_CHARSET=utf8mb4
```

## Règles d'accès

- Sans connexion : lecture des pages publiques.
- Documentation accessible sans connexion : `/docs` et `/documentation`.
- Utilisateur connecté : peut poser/modifier son pari podium.
- Admin : gestion (ajout/modification/suppression) écuries, pilotes, résultats.
- Résultats course : points attribués automatiquement selon le barème F1 (25, 18, 15, 12, 10, 8, 6, 4, 2, 1).
- Toutes les actions POST sont protégées par CSRF.

## Structure utile

- `index.php` : point d'entrée + routage.
- `Controllers/` : logique applicative.
- `Views/` : templates.
- `Database/Database.php` : connexion MySQL, migration, reset.
- `Public/` : CSS, JS, assets, uploads.
- `init_db.php` : reset + jeu de données de démonstration.

## Documentation projet

- `docs/texte-projet.md` : version dossier / oral.
- `docs/architecture.md` : architecture technique.
- `docs/backlog.md` : user stories et priorités.
- `docs/plan-tests.md` : plan de tests manuels + runner.
- `docs/phpdoc.md` : guide de génération de doc PHP.
- `docs/credentials.txt` : identifiants de démonstration.

## Dépannage rapide

- `Access denied for user ...` : vérifier `DB_USER` / `DB_PASS`.
- `Unknown database 'tpformula1'` : recréer la base.
- Si nécessaire : relancer `php init_db.php`.
