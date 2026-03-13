# Backlog / User stories

| ID | User story | Priorite | Criteres d'acceptation |
|----|------------|----------|------------------------|
| US1 | En tant que visiteur, je veux voir le calendrier 2026 et ouvrir le detail d'une course. | Haute | Liste des courses, details lisibles, lien vers chaque manche |
| US2 | En tant qu'administrateur, je veux gerer les ecuries et leurs pilotes. | Haute | Ajout/modification/suppression avec validations serveur |
| US3 | En tant qu'administrateur, je veux saisir les resultats d'une course. | Haute | Resultat ajoute/modifie/supprime, contraintes de position/pilote respectees |
| US4 | En tant qu'utilisateur connecte, je veux parier sur le podium d'une course. | Haute | 3 pilotes distincts, pari enregistrable dans la fenetre autorisee |
| US5 | En tant que visiteur, je veux voir le classement pilotes calcule depuis les resultats. | Moyenne | Table de classement chargee depuis `course_results` |
| US6 | En tant qu'utilisateur, je veux voir le classement global des paris. | Moyenne | Score, podiums parfaits, details des points visibles |
| US7 | En tant que responsable qualite, je veux une documentation claire et des tests manuels. | Haute | README + docs architecture/backlog/tests coherents |

## Taches techniques

- T1: Ajouter pagination et recherche avancee sur les tableaux.
- T2: Ajouter tests automatises (PHPUnit + scenario navigateur).
- T3: Ameliorer la gestion des erreurs utilisateur (messages plus precis).
- T4: Mettre en place des URL plus propres (rewrite) en conservant les routes actuelles.
- T5: Ajouter export CSV pour classements et resultats.
