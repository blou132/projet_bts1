# TPFormula1 - Gestion F1 (BTS SIO SLAM)

Application PHP (MVC léger) pour gérer des écuries, pilotes, calendrier 2026, résultats et paris podium.

## Guide examinateur (1 minute)

Base URL locale : `http://localhost:8000`

Comptes de démonstration :
- Admin : `admin@example.com` / `admin123`
- Utilisateur : `example@gmail.com` / `123456789`

Parcours express (2-3 min) :
1. `/accueil` -> vue globale
2. `/documentation` -> hub docs (texte, architecture, tests, credentials)
3. `/calendrier` puis `/calendrier/course/ID` -> détails course + pari
4. `/paris` -> classement global des parieurs
5. `/ecuries` (admin) -> CRUD écuries/pilotes

Acces documentation (important) :
- le bouton "Docs" n'est plus dans la barre de navigation ;
- `http://localhost:8000/docs` ouvre directement la doc Doxygen ;
- `http://localhost:8000/documentation` ouvre le hub de documentation projet.

Différence importante :
- `Classement des paris` sur `/calendrier/course/ID` = classement de la course en cours.
- `Classement des paris` sur `/paris` = classement cumulé saison.

## Trace GitHub

Depot utilise pendant le developpement :
- `https://github.com/blou132/projet_bts1_casser.git`

Note pour l'examinateur :
- ce depot n'est plus utilisable en push actuellement ;
- il conserve toutefois l'historique des commits / pushes deja realises pendant le projet.

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
- `/docs` : accès direct à la doc Doxygen.
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

Etat actuel :
- suite complete (unitaires + integration) via `php tests/run.php`
- couverture unitaire + base de donnees + pages HTTP publiques

Fichiers de tests :
- `tests/Unit/CsrfTest.php`
- `tests/Unit/HtmlControllerTest.php`
- `tests/Unit/ModelsTest.php`
- `tests/Unit/SeasonControllerTest.php`
- `tests/Unit/ValidationControllerTest.php`
- `tests/Integration/DatabaseSmokeTest.php`
- `tests/Integration/PublicRoutesTest.php`

## Documentation PHP (DocBlocks + Doxygen)

Configuration fournie : `Doxyfile`

Generation HTML :

```bash
doxygen Doxyfile
```

Sortie :
- `docs/doxygen/html/index.html`
- URL locale : `http://localhost:8000/docs/doxygen/html/index.html`

Alternative possible (optionnelle) avec phpDocumentor :

```bash
phpdoc -d Controllers,Models,Security,Database,Routes -t docs/api --ignore "Public/*,Views/*,tests/*,docs/api/*"
```

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
- Documentation accessible sans connexion : `/docs` (Doxygen) et `/documentation` (hub).
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
- `docs/fail2ban.md` : mise en place fail2ban (anti brute force).
- `docs/credentials.txt` : identifiants de démonstration.

## Fail2ban (anti brute force)

- Les echecs de connexion sont journalises dans `storage/logs/security.log`.
- Config fail2ban fournie :
  - `ops/fail2ban/filter.d/tpformula1-auth.conf`
  - `ops/fail2ban/jail.d/tpformula1-auth.local`
- Guide d'installation serveur : `docs/fail2ban.md`.

## Dépannage rapide

- `Access denied for user ...` : vérifier `DB_USER` / `DB_PASS`.
- `Unknown database 'tpformula1'` : recréer la base.
- Si nécessaire : relancer `php init_db.php`.
