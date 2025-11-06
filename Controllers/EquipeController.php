<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;

/**
 * Contrôleur CRUD des écuries de Formule 1.
 */
class EquipeController extends BaseController
{
    /** Recharge la page principale avec les écuries, GPs et éventuels messages. */
    private function renderList(array $errors = []): void
    {
        $pdo = Database::getInstance();
        $equipes = $pdo->query('SELECT e.*, c.nom AS championnat FROM equipes e JOIN championnats c ON c.id=e.id_championnat ORDER BY e.nom')->fetchAll();
        $championnats = $pdo->query('SELECT * FROM championnats ORDER BY nom')->fetchAll();
        $this->render('equipe.lame.php', compact('equipes', 'championnats', 'errors'));
    }

    public function index(): void
    {
        $this->renderList();
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $ville = ValidationController::clean($_POST['ville'] ?? '');
        $idc = (int)($_POST['id_championnat'] ?? 0);
        $errors = [];
        if (!ValidationController::nom($nom)) $errors[] = 'Nom d\'écurie invalide';
        if (!ValidationController::ville($ville)) $errors[] = 'Ville / base invalide';
        if ($idc <= 0) $errors[] = 'Grand Prix requis';
        $blason = $this->handleImageUpload('blason');
        if ($idc > 0) {
            $stmt = $pdo->prepare('SELECT 1 FROM championnats WHERE id = ?');
            $stmt->execute([$idc]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Grand Prix introuvable';
            }
        }
        if ($errors) {
            $this->renderList($errors);
            return;
        }
        $pdo->prepare('INSERT INTO equipes(nom,ville,id_championnat,blason) VALUES (?,?,?,?)')
            ->execute([$nom,$ville,$idc,$blason]);
        $this->redirectTo('equipes');
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $id = (int)($_POST['id'] ?? 0);
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $ville = ValidationController::clean($_POST['ville'] ?? '');
        $idc = (int)($_POST['id_championnat'] ?? 0);
        $blason = $this->handleImageUpload('blason') ?? $this->sanitizeExistingUpload('blason_exist');
        $errors = [];
        if ($id <= 0) $errors[] = 'Identifiant invalide';
        if (!ValidationController::nom($nom)) $errors[] = 'Nom d\'écurie invalide';
        if (!ValidationController::ville($ville)) $errors[] = 'Ville / base invalide';
        if ($idc <= 0) $errors[] = 'Grand Prix requis';

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM equipes WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Écurie introuvable';
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM championnats WHERE id = ?');
            $stmt->execute([$idc]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Grand Prix introuvable';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $pdo->prepare('UPDATE equipes SET nom=?, ville=?, id_championnat=?, blason=? WHERE id=?')
            ->execute([$nom,$ville,$idc,$blason,$id]);
        $this->redirectTo('equipes');
    }

    public function delete(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $pdo = Database::getInstance();
        $errors = [];
        if ($id <= 0) {
            $errors[] = 'Identifiant invalide';
        } else {
            $stmt = $pdo->prepare('DELETE FROM equipes WHERE id=?');
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                $errors[] = 'Écurie introuvable ou déjà supprimée';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $this->redirectTo('equipes');
    }
}

?>
