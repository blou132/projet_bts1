# Architecture applicative

## Vue d’ensemble

```
Navigateur → index.php → Controller → (Validation, CSRF, Uploads)
                             ↓
                         Database
                             ↓
                           SQLite
```

- **index.php** oriente vers le bon contrôleur en fonction des paramètres `route` et `action`.
- **Controllers/** mettent en œuvre la logique métier et orchestrent les appels à la base de données.
- **Models/** sont des objets simples (POPO) représentant les entités Grands Prix, Écuries, Pilotes.
- **Views/** structurent l’interface HTML, le layout et les formulaires.
- **Database/Database.php** expose un singleton PDO et les migrations SQLite.
- **Security/Csrf.php** fournit le jeton CSRF injecté dans chaque vue.

## Flux CRUD (exemple Pilote)

1. L’utilisateur soumet le formulaire `?route=joueurs&action=store`.
2. `JoueurController::store()` appelle `requireCsrf()`, nettoie et valide les champs.
3. Si valide, upload de la photo (`handleImageUpload`) puis insertion via PDO préparé.
4. Redirection `Location: ?route=joueurs`.
5. `index()` recharge la liste et la vue `Views/joueur.lame.php`.

## Gestion de la base

- SQLite stockée dans `Database/database.sqlite`.
- Tables : `championnats`, `equipes`, `joueurs` avec clés étrangères et cascade.
- Script `init_db.php` permet de reset/migrer la base.

## Conventions

- Nommage PSR-4 (Autoload minimal).
- HTML échappé via `htmlspecialchars`.
- Uploads stockés en `Public/uploads` (à servir via front controller).

