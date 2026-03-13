# Architecture applicative

## Vue d'ensemble

```text
Navigateur
  -> index.php (front controller + route/action)
    -> Controllers/* (logique metier)
      -> Database/Database.php (PDO MySQL + migrations)
        -> MySQL (tables metier)
```

L'application suit un MVC leger:
- `index.php` gere le routage.
- `Controllers/` execute les cas d'usage.
- `Views/` rend les pages HTML.
- `Database/Database.php` centralise la connexion et le schema.

## Routage

Routes principales:
- `accueil`
- `ecuries` (alias `equipes`)
- `pilotes` / `joueurs` (redirige vers ecuries)
- `jointure` (redirige vers ecuries)
- `calendrier` (`calendar`, `course`, actions resultats/paris)
- `classements` (`standings`)
- `paris` (`bets`)
- `auth` (`login`, `authenticate`, `register`, `store`, `logout`)

Regle d'acces:
- Lecture publique possible sur les pages principales.
- Toute action POST sans session redirige vers login.
- Actions d'administration protegees par role `admin`.

## Composants techniques

- `BaseController`:
  - rendu commun (`header/footer`)
  - CSRF obligatoire sur POST
  - redirection interne
  - controle auth/admin
- `AuthController`:
  - connexion/inscription/deconnexion
  - hash mot de passe (`password_hash`, `password_verify`)
- `EquipeController` et `JoueurController`:
  - CRUD ecuries/pilotes
  - contraintes metier (2 pilotes max par ecurie)
- `SeasonController`:
  - calendrier 2026
  - resultats de course
  - classement pilotes
  - paris podium + scoring

## Base de donnees

Tables principales:
- `championnats`
- `equipes`
- `joueurs`
- `courses`
- `course_results`
- `users`
- `bets`

Migrations executees via `Database::migrate()`.
Jeu de donnees de demo charge par `init_db.php`.

## Securite et qualite

- CSRF sur tous les formulaires POST.
- Requetes SQL preparees (PDO).
- Echapement HTML cote vue (`htmlspecialchars`).
- Validation serveur dans les controleurs.
- Separation claire lecture (public) / ecriture (auth).

## Flux type

### Flux 1 - consultation calendrier
1. `GET /calendrier`
2. `SeasonController::calendar()`
3. Lecture de `courses`
4. Rendu `Views/calendar.lame.php`

### Flux 2 - ajout resultat course (admin)
1. `POST /calendrier/addResult`
2. Verif session admin + CSRF
3. Validation des champs
4. Insert dans `course_results`
5. Redirection vers la course

### Flux 3 - pari podium (utilisateur connecte)
1. `POST /calendrier/placeBet`
2. Verif session + CSRF
3. Controle unicite des 3 pilotes
4. Insert/Update dans `bets`
5. Affichage du score quand podium officiel disponible
