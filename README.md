# TPFormula1 — Gestion F1 (BTS SIO SLAM)

Application PHP (MVC léger) pour gérer un univers Formule 1 : Grands Prix, écuries et pilotes. Le projet vise à illustrer les compétences du référentiel BTS SIO option SLAM (développement d’applications).

## Objectifs pédagogiques (Référentiel SLAM)

| Compétence | Illustration dans le projet |
|------------|-----------------------------|
| A1.1.1 Analyse du cahier des charges | Users stories « gestion des Grands Prix / écuries / pilotes », formulaires CRUD, règles métier explicites |
| A1.2.2 Rédaction des spécifications techniques | Structure MVC, `Controllers/`, `Models/`, documentation dans `docs/` |
| A2.1.1 Conception d’une base de données | Modèle relationnel SQLite (`Database/Database.php`, script `init_db.php`) |
| A2.2.1 Maquettage / prototypage | Vues `.lame.php` et gabarits layout header/footer |
| A2.3.1 Mise en place d’environnements de test | Utilisation de SQLite embarqué, instructions d’exécution locale |
| A4.1.2 Développement d’une application sécurisée | Validation des entrées, protection CSRF, contrôle des uploads |
| A4.1.3 Développement d’une interface utilisateur | Templates HTML/CSS, gestion des formulaires CRUD |
| A4.1.6 Rédaction de la documentation technique | Ce README, dossiers `docs/` (architecture, plan de tests) |
| A4.2.2 Maintenance corrective/évolutive | Organisation modulaire, contrôleurs séparés, validations réutilisables |
| A5.1.1 Gestion du patrimoine informatique | Script de migration, documentation d’installation, conformité RGPD (à compléter) |

Des compléments (tests automatisés, gestion des rôles, monitoring…) peuvent être ajoutés pour couvrir d’autres compétences.

## Architecture

- **index.php** : point d’entrée, instancie les contrôleurs selon la route.
- **Controllers/** : logique métier (Grands Prix, écuries, pilotes), validation, CSRF, uploads.
- **Models/** : entités de domaine (POPO).
- **Views/** : templates HTML (header/footer + pages CRUD).
- **Database/** : gestion SQLite (singleton, migrations, reset).
- **Security/** : module CSRF.
- **Public/** : assets (CSS, uploads).
- **docs/** : ressources pédagogiques (plan de tests, architecture, backlog).

## Installation

```bash
git clone <repo>
cd projet-bts
php -S localhost:8000
```

1. PHP ≥ 8.1 recommandé.
2. `index.php` crée automatiquement la base `Database/database.sqlite`.
3. Pour repartir à zéro et charger les données de démonstration (grands prix, écuries, pilotes, visuels) : `php init_db.php`.

## Utilisation

- `?route=championnats` : CRUD des Grands Prix.
- `?route=equipes` : CRUD des écuries.
- `?route=joueurs` : CRUD des pilotes.
- `?route=jointure` : vue synthétique pilotes/écuries.
- `?route=calendrier` : calendrier saison 2026 (données FOM inspirées).
- `?route=classements` : tableau de points démo type heatmap.
- La page d’accueil présente un tableau de bord (effectifs par écurie, pilotes à l’affiche, vitrine des Grands Prix) alimenté par les données de démonstration.

Chaque formulaire embarque un jeton CSRF et des validations côté serveur.

## Sécurité & Qualité

- Validation serveur (`ValidationController`).
- Limitation à des formats images pour uploads + stockage dans `Public/uploads`.
- Jeton CSRF obligatoire sur les actions POST.
- SQLite avec contraintes d’intégrité (clé étrangère ON DELETE CASCADE).
- Sanitation des sorties (`htmlspecialchars`).

Axes d’amélioration :

- Authentification + gestion de rôles.
- Journalisation des actions sensibles.
- Tests unitaires (ex. PHPUnit) et tests fonctionnels.
- CI/CD basique (GitHub Actions).

## Dossier pédagogique (`docs/`)

- `docs/architecture.md` : schéma MVC, flux de données.
- `docs/backlog.md` : user stories / tâches.
- `docs/plan-tests.md` : cas de tests manuels.

Ces documents facilitent l’évaluation des compétences et la présentation orale (E4/E6).

## Licence / Crédits

Projet d’apprentissage pour le BTS SIO SLAM – Teo & Maxime. Réutilisation pédagogique libre tant que les crédits sont conservés.
