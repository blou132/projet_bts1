# Plan de tests (manuels)

## Tests automatiques (runner PHP)

Commande:

```bash
php tests/run.php
```

Resultat attendu:
- sortie `[OK]` pour chaque test
- ligne finale `Resultat: X/X tests OK`
- etat actuel valide : `20/20 tests OK`

Couverture automatique actuelle:
- securite CSRF
- menu principal
- modeles metier
- bareme F1 des points
- connexion MySQL et tables principales
- pages publiques (`/accueil`, `/documentation`, `/docs`, `/calendrier`, `/paris`, `/auth/login`, `/auth/register`)
- verification d'une redirection vers la connexion sur action POST non authentifiee
- tests HTTP reels lances via le serveur PHP integre

## Authentification

| ID | Scenario | Etapes | Resultat attendu |
|----|----------|--------|------------------|
| AUTH01 | Connexion valide | Ouvrir `/auth/login`, saisir compte admin | Session ouverte, redirection accueil |
| AUTH02 | Mot de passe invalide | Saisir email valide + mauvais mot de passe | Message "Identifiants invalides" |
| AUTH03 | Inscription | Ouvrir `/auth/register`, remplir formulaire | Compte cree et session ouverte |

## Ecuries / Pilotes (admin)

| ID | Scenario | Etapes | Resultat attendu |
|----|----------|--------|------------------|
| EQ01 | Ajouter ecurie | POST `/ecuries/store` avec nom valide | Ecurie ajoutee |
| EQ02 | Ajouter pilote | POST `/pilotes/store` avec ecurie existante | Pilote visible dans la carte ecurie |
| EQ03 | Limite 2 pilotes | Ajouter un 3e pilote dans meme ecurie | Erreur "deja 2 pilotes" |
| EQ04 | Modifier/supprimer | Utiliser les formulaires update/delete | Donnees mises a jour ou supprimees |

## Calendrier / Resultats

| ID | Scenario | Etapes | Resultat attendu |
|----|----------|--------|------------------|
| CAL01 | Voir calendrier | Ouvrir `/calendrier` | Liste des 23 courses chargee |
| CAL02 | Detail course | Cliquer une manche | Page course + resultats + bloc paris |
| CAL03 | Ajouter resultat (admin) | POST `/calendrier/addResult` | Ligne ajoutee dans resultats |
| CAL04 | Position en doublon | Ajouter position deja prise | Erreur de validation |

## Paris podium

| ID | Scenario | Etapes | Resultat attendu |
|----|----------|--------|------------------|
| BET01 | Pari sans connexion | Ouvrir course et tenter pari | Invitation a se connecter |
| BET02 | Pari valide | Connecte, choisir 3 pilotes differents | Pari enregistre |
| BET03 | Pari invalide | Choisir deux fois le meme pilote | Message d'erreur |
| BET04 | Classement paris | Ouvrir `/paris` | Tableau des scores visible |

## Securite

| ID | Test | Methode | Resultat attendu |
|----|------|---------|------------------|
| SEC01 | CSRF manquant | Envoyer POST sans `_csrf` | Reponse 419 |
| SEC02 | POST sans session | Envoyer POST vers action metier non auth | Redirection login |
| SEC03 | Action admin par user simple | Utiliser un compte non admin sur action admin | Reponse 403 |
| SEC04 | Injection HTML | Saisir `<script>` dans un nom | Texte affiche echappe |

## Responsive (manuel)

- Tester en largeur `360x740`, `390x844`, `768x1024`.
- Verifier header/menu/bouton connexion sans chevauchement.
- Verifier lisibilite du bloc "Prochaine course" et des cartes.
- Verifier tableaux scrollables horizontalement.
