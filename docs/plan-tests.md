# Plan de tests (manuels)

## Table Grands Prix

| ID | Scénario | Étapes | Résultat attendu |
|----|----------|--------|------------------|
| GP01 | Créer un Grand Prix valide | Remplir nom + pays, soumettre | Nouveau GP listé, message OK (redirection) |
| GP02 | Champ obligatoire manquant | Laisser le nom vide, soumettre | Retour formulaire avec message « Nom de Grand Prix invalide » |
| GP03 | Upload d’affiche | Joindre image PNG | Fichier stocké dans `Public/uploads`, aperçu dans la table |
| GP04 | Suppression | Cliquer « Supprimer », confirmer | GP supprimé + cascade sur écuries/pilotes |

## Table Écuries

| ID | Scénario | Étapes | Résultat attendu |
|----|----------|--------|------------------|
| EQ01 | Création valide | Choisir GP existant, saisir nom/base | Écurie listée avec logo (si fourni) |
| EQ02 | GP inexistant | Forger requête POST avec ID invalide | Message erreur « Grand Prix introuvable » |
| EQ03 | Mise à jour | Modifier base ou logo | Table mise à jour, ancien logo conservé si non remplacé |
| EQ04 | Suppression | Confirmation | Écurie supprimée, pilotes associés supprimés |

## Table Pilotes

| ID | Scénario | Étapes | Résultat attendu |
|----|----------|--------|------------------|
| PI01 | Ajout pilote | Renseigner nom/prénom/rôle/écurie | Pilote listé, portrait si upload |
| PI02 | Validation rôle | Saisir caractères invalides | Message « Rôle invalide » |
| PI03 | Mise à jour portrait | Charger nouvelle image | Nouveau portrait affiché, ancien supprimé manuellement si nécessaire |
| PI04 | Suppression | Confirmer | Pilote supprimé |

## Sécurité

| ID | Test | Méthode | Résultat |
|----|------|---------|----------|
| SEC01 | Jeton CSRF manquant | Envoyer POST sans `_csrf` | Réponse 419 + message « Jeton CSRF invalide » |
| SEC02 | Injection HTML | Saisir `<script>` dans nom | Affichage échappé dans la table |
| SEC03 | Upload non image | Joindre `.exe` | Upload refusé, valeur null |
| SEC04 | Auth requise pour modifications | Envoyer POST `?route=championnats&action=store` sans session | Redirection vers login |
| SEC05 | Lecture publique | Visiter `?route=championnats` sans session | Liste visible, formulaires masqués |

## Accessibilité / UX

- Vérifier navigation clavier.
- Vérifier contraste (CSS à ajuster si besoin).

> À compléter par des tests automatisés (PHPUnit / Playwright) pour couvrir les compétences A2.3 et A4.1.4.
