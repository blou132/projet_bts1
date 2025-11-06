# Backlog / User stories

| ID | User story | Priorité | Critères d’acceptation |
|----|------------|----------|------------------------|
| US1 | En tant qu’administrateur, je veux gérer les Grands Prix pour maintenir le calendrier F1. | Haute | CRUD complet, validation des champs nom/pays, upload d’affiche optionnelle |
| US2 | En tant qu’administrateur, je veux gérer les écuries afin d’associer pilotes et Grand Prix. | Haute | CRUD complet, sélection d’un Grand Prix existant, upload de logo |
| US3 | En tant qu’administrateur, je veux gérer les pilotes pour préparer les feuilles de course. | Haute | CRUD complet, sélection d’une écurie, upload de portrait |
| US4 | En tant que spectateur, je veux visualiser les pilotes par écurie. | Moyenne | Vue read-only `?route=jointure`, tri par écurie |
| US5 | En tant que responsable SI, je veux sécuriser l’accès aux actions sensibles. | Haute | Jeton CSRF, validation serveur, sanitation des sorties |
| US6 | En tant que responsable qualité, je veux un plan de tests pour vérifier les fonctionnalités clés. | Moyenne | Cf. `docs/plan-tests.md` |
| US7 | En tant qu’évaluateur BTS SIO, je veux une documentation claire reliant l’appli au référentiel SLAM. | Haute | README, documentation architecture / tests / backlog |

## Tâches techniques complémentaires

- T1 : Ajouter authentification + rôles (à réaliser).
- T2 : Générer des jeux de données de démo.
- T3 : Mettre en place des tests unitaires (PHPUnit).
- T4 : Ajouter une API REST (JSON) en lecture.
- T5 : Automatiser l’intégration (workflow CI).
