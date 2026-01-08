<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;

/**
 * Gère le CRUD des Grands Prix (anciennement championnats).
 */
class ChampionnatController extends BaseController
{
    /** Prépare la vue index en rechargeant la liste + erreurs éventuelles. */
    private function renderList(array $errors = []): void
    {
        $rows = Database::getInstance()->query('SELECT * FROM championnats ORDER BY nom')->fetchAll();
        $this->render('championnat.lame.php', [
            'championnats' => $rows,
            'errors' => $errors,
        ]);
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
        $pays = ValidationController::clean($_POST['pays'] ?? '');
        $errors = [];
        if (!ValidationController::nom($nom)) $errors[] = 'Nom de Grand Prix invalide';
        if (!ValidationController::pays($pays)) $errors[] = 'Pays hôte invalide';
        $blason = $this->handleImageUpload('blason');
        if ($errors) {
            $this->renderList($errors);
            return;
        }
        $pdo->prepare('INSERT INTO championnats(nom,pays,blason) VALUES (?,?,?)')
            ->execute([$nom, $pays, $blason]);
        $this->redirectTo('championnats');
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $id = (int)($_POST['id'] ?? 0);
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $pays = ValidationController::clean($_POST['pays'] ?? '');
        $blason = $this->handleImageUpload('blason') ?? $this->sanitizeExistingUpload('blason_exist');
        $errors = [];
        if ($id <= 0) {
            $errors[] = 'Identifiant invalide';
        }
        if (!ValidationController::nom($nom)) $errors[] = 'Nom de Grand Prix invalide';
        if (!ValidationController::pays($pays)) $errors[] = 'Pays hôte invalide';

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM championnats WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Grand Prix introuvable';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $pdo->prepare('UPDATE championnats SET nom=?, pays=?, blason=? WHERE id=?')
            ->execute([$nom, $pays, $blason, $id]);
        $this->redirectTo('championnats');
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $id = (int)($_POST['id'] ?? 0);
        $errors = [];
        if ($id <= 0) {
            $errors[] = 'Identifiant invalide';
        } else {
            $stmt = $pdo->prepare('DELETE FROM championnats WHERE id = ?');
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                $errors[] = 'Grand Prix introuvable ou déjà supprimé';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $this->redirectTo('championnats');
    }
}

?>
