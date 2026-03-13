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
        $ecuries = $pdo->query('SELECT id, nom, blason
                                FROM equipes
                                ORDER BY nom')->fetchAll();
        $pilotes = $pdo->query('SELECT j.id, j.nom, j.prenom, j.poste, j.photo, j.id_equipe
                                FROM joueurs j
                                ORDER BY j.nom')->fetchAll();
        $pilotesParEcurie = [];
        foreach ($pilotes as $pilote) {
            $idEcurie = (int)$pilote['id_equipe'];
            if (!isset($pilotesParEcurie[$idEcurie])) {
                $pilotesParEcurie[$idEcurie] = [];
            }
            $pilotesParEcurie[$idEcurie][] = $pilote;
        }
        $pilotErrors = $_SESSION['pilotes_errors'] ?? [];
        $pilotFlash = $_SESSION['pilotes_flash'] ?? null;
        $focusId = (int)($_GET['focus'] ?? 0);
        unset($_SESSION['pilotes_errors'], $_SESSION['pilotes_flash']);
        $this->render('equipe.lame.php', compact('ecuries', 'errors', 'pilotesParEcurie', 'pilotErrors', 'pilotFlash', 'focusId'));
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
        $errors = [];
        if (!ValidationController::nom($nom)) $errors[] = 'Nom d\'écurie invalide';
        $idc = (int)$pdo->query('SELECT id FROM championnats ORDER BY id LIMIT 1')->fetchColumn();
        if ($idc <= 0) {
            $errors[] = 'Ajoutez un Grand Prix avant de creer une ecurie.';
        }
        $blason = $this->handleImageUpload('blason');
        if ($errors) {
            $this->renderList($errors);
            return;
        }
        $pdo->prepare('INSERT INTO equipes(nom,ville,id_championnat,blason) VALUES (?,?,?,?)')
            ->execute([$nom,'Inconnu',$idc,$blason]);
        $this->redirectTo('ecuries');
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $pdo = Database::getInstance();
        $id = (int)($_POST['id'] ?? 0);
        $nom = ValidationController::clean($_POST['nom'] ?? '');
        $blason = $this->handleImageUpload('blason') ?? $this->sanitizeExistingUpload('blason_exist');
        $errors = [];
        if ($id <= 0) $errors[] = 'Identifiant invalide';
        if (!ValidationController::nom($nom)) $errors[] = 'Nom d\'écurie invalide';

        if (!$errors) {
            $stmt = $pdo->prepare('SELECT 1 FROM equipes WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetchColumn()) {
                $errors[] = 'Écurie introuvable';
            }
        }

        if ($errors) {
            $this->renderList($errors);
            return;
        }

        $pdo->prepare('UPDATE equipes SET nom=?, blason=? WHERE id=?')
            ->execute([$nom,$blason,$id]);
        $this->redirectTo('ecuries');
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

        $this->redirectTo('ecuries');
    }
}

?>
