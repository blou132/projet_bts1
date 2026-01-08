<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;

/**
 * Contrôleur CRUD des pilotes et vue jointure avec leurs écuries.
 */
class JoueurController extends BaseController
{
    /** Recharge la vue pilote avec liste complète et messages d'erreurs. */
    private function renderList(array $errors = []): void
    {
        $pdo = Database::getInstance();
        $pilotes = $pdo->query('SELECT j.id, j.nom, j.prenom, j.poste, j.photo, j.id_equipe AS id_ecurie, e.nom AS ecurie
                                FROM joueurs j
                                JOIN equipes e ON e.id=j.id_equipe
                                ORDER BY j.nom')->fetchAll();
        $ecuries = $pdo->query('SELECT id, nom FROM equipes ORDER BY nom')->fetchAll();
        $this->render('joueur.lame.php', compact('pilotes', 'ecuries', 'errors'));
    }

    public function index(): void
    {
        $this->renderList();
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $prenom = ValidationController::clean($_POST['prenom'] ?? '');
        $poste = ValidationController::clean($_POST['poste'] ?? '');
        $idEcurie = (int)($_POST['id_ecurie'] ?? 0);
        $errors = [];
        if (!ValidationController::nom($nom)) $errors[] = 'Nom de pilote invalide';
        if (!ValidationController::nom($prenom)) $errors[] = 'Prénom de pilote invalide';
        if (!ValidationController::poste($poste)) $errors[] = 'Rôle invalide';
        if ($idEcurie <= 0) $errors[] = 'Écurie requise';
        $photo = $this->handleImageUpload('photo');
        if ($idEcurie > 0) {
            $stmt = $pdo->prepare('SELECT 1 FROM equipes WHERE id = ?');
            $stmt->execute([$idEcurie]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Écurie introuvable';
            }
            if (!$errors) {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM joueurs WHERE id_equipe = ?');
                $stmt->execute([$idEcurie]);
                if ((int)$stmt->fetchColumn() >= 2) {
                    $errors[] = 'Cette écurie a déjà 2 pilotes.';
                }
            }
        }
        if ($errors) {
            $this->renderList($errors);
            return;
        }
        $pdo->prepare('INSERT INTO joueurs(nom,prenom,poste,id_equipe,photo) VALUES (?,?,?,?,?)')
            ->execute([$nom,$prenom,$poste,$idEcurie,$photo]);
        $this->redirectTo('pilotes');
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $id = (int)($_POST['id'] ?? 0);
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $prenom = ValidationController::clean($_POST['prenom'] ?? '');
        $poste = ValidationController::clean($_POST['poste'] ?? '');
        $idEcurie = (int)($_POST['id_ecurie'] ?? 0);
        $photo = $this->handleImageUpload('photo') ?? $this->sanitizeExistingUpload('photo_exist');
        $errors = [];
        if ($id <= 0) $errors[] = 'Identifiant invalide';
        if (!ValidationController::nom($nom)) $errors[] = 'Nom de pilote invalide';
        if (!ValidationController::nom($prenom)) $errors[] = 'Prénom de pilote invalide';
        if (!ValidationController::poste($poste)) $errors[] = 'Rôle invalide';
        if ($idEcurie <= 0) $errors[] = 'Écurie requise';

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM joueurs WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Pilote introuvable';
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM equipes WHERE id = ?');
            $stmt->execute([$idEcurie]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Écurie introuvable';
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM joueurs WHERE id_equipe = ? AND id <> ?');
            $stmt->execute([$idEcurie, $id]);
            if ((int)$stmt->fetchColumn() >= 2) {
                $errors[] = 'Cette écurie a déjà 2 pilotes.';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $pdo->prepare('UPDATE joueurs SET nom=?, prenom=?, poste=?, id_equipe=?, photo=? WHERE id=?')
            ->execute([$nom,$prenom,$poste,$idEcurie,$photo,$id]);
        $this->redirectTo('pilotes');
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $pdo = Database::getInstance();
        $errors = [];
        if ($id <= 0) {
            $errors[] = 'Identifiant invalide';
        } else {
            $stmt = $pdo->prepare('DELETE FROM joueurs WHERE id=?');
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                $errors[] = 'Pilote introuvable ou déjà supprimé';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $this->redirectTo('pilotes');
    }

    /** Vue jointe Pilote + Écurie */
    public function withEquipes(): void
    {
        $pdo = Database::getInstance();
        $rows = $pdo->query('SELECT j.id, j.nom, j.prenom, j.poste, j.photo, e.nom AS ecurie, e.blason
                              FROM joueurs j JOIN equipes e ON e.id=j.id_equipe
                              ORDER BY e.nom, j.nom')->fetchAll();
        $this->render('equipe_joueur.lame.php', ['rows' => $rows]);
    }
}

?>
