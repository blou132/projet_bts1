# TPFormula1 - Gestion F1 (BTS SIO SLAM)

Application PHP (MVC leger) : ecuries, pilotes, calendrier, resultats et paris podium.

## Guide examinateur (simple)

Base locale : `http://localhost:8000`

Comptes demo :
- Admin : `admin@example.com` / `admin123`
- Utilisateur : `example@gmail.com` / `123456789`

Parcours conseille (3 minutes) :
1. `/accueil`
2. `/calendrier`
3. `/calendrier/course/1`
4. `/paris`
5. `/documentation`

Note importante :
- `/docs` ouvre directement la doc Doxygen.
- `/documentation` ouvre le hub de docs du projet.

## Installation rapide

Prerequis :
- PHP 8.1+
- MySQL 5.7+ / 8+

Commandes :

```sql
CREATE DATABASE tpformula1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
cp .env.example .env
php init_db.php
php -S localhost:8000 router.php
```

Puis ouvrir : `http://localhost:8000/accueil`

## Ce qu'il faut verifier

- CRUD ecuries/pilotes via `/ecuries` (admin).
- Resultats d'une course via `/calendrier/course/ID` (admin).
- Classement paris d'une course dans la page course.
- Classement paris global via `/paris`.
- Points F1 auto selon position (25, 18, 15, 12, 10, 8, 6, 4, 2, 1).
- Protection CSRF sur les formulaires POST.

## Tests

```bash
php tests/run.php
```

## Documentation

- Hub docs : `/documentation`
- Doxygen direct : `/docs`
- Fichiers utiles :
  - `docs/texte-projet.md`
  - `docs/architecture.md`
  - `docs/backlog.md`
  - `docs/plan-tests.md`
  - `docs/phpdoc.md`
  - `docs/fail2ban.md`
  - `docs/credentials.txt`

## Trace GitHub

Historique de dev :
- `https://github.com/blou132/projet_bts1_casser.git`

## Depannage rapide

- `Access denied for user ...` : verifier `.env` (DB_USER / DB_PASS).
- `Unknown database 'tpformula1'` : creer la base puis relancer `php init_db.php`.
