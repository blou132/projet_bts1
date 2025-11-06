<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;

/**
 * Présente des vues synthétiques de la saison : calendrier et classement démo.
 */
class SeasonController extends BaseController
{
    /** Affiche le calendrier F1 2026 basé sur les données MySQL. */
    public function calendar(): void
    {
        $pdo = Database::getInstance();
        $calendar = $pdo->query('SELECT id, ordre, code, nom, pays, ville, date_course, flag FROM courses ORDER BY ordre')->fetchAll();

        $selectedId = isset($_GET['course']) ? (int)$_GET['course'] : null;
        if (($selectedId === null || $selectedId <= 0) && !empty($calendar)) {
            $selectedId = (int)$calendar[0]['id'];
        }

        $selectedCourse = null;
        $courseResults = [];

        if ($selectedId) {
            foreach ($calendar as $course) {
                if ((int)$course['id'] === $selectedId) {
                    $selectedCourse = $course;
                    break;
                }
            }

            if ($selectedCourse) {
                $stmt = $pdo->prepare('SELECT cr.id AS result_id, cr.position, cr.points, j.id AS joueur_id, j.prenom, j.nom, e.nom AS equipe
                                       FROM course_results cr
                                       JOIN joueurs j ON j.id = cr.joueur_id
                                       JOIN equipes e ON e.id = j.id_equipe
                                       WHERE cr.course_id = ?
                                       ORDER BY cr.position');
                $stmt->execute([$selectedId]);
                $courseResults = $stmt->fetchAll();
            }
        }

        $drivers = $pdo->query('SELECT j.id, j.prenom, j.nom, e.nom AS equipe
                                FROM joueurs j
                                JOIN equipes e ON e.id = j.id_equipe
                                ORDER BY e.nom, j.nom')->fetchAll();

        $flashErrors = $_SESSION['calendar_errors'] ?? [];
        $flashSuccess = $_SESSION['calendar_flash'] ?? null;
        unset($_SESSION['calendar_errors'], $_SESSION['calendar_flash']);

        $this->render('calendar.lame.php', [
            'calendar' => $calendar,
            'year' => 2026,
            'selectedCourse' => $selectedCourse,
            'selectedCourseId' => $selectedId,
            'courseResults' => $courseResults,
            'drivers' => $drivers,
            'calendarErrors' => $flashErrors,
            'calendarFlash' => $flashSuccess,
        ]);
    }

    /** Affiche le classement pilotes mis à jour à partir des résultats en base. */
    public function standings(): void
    {
        $pdo = Database::getInstance();

        $courses = $pdo->query('SELECT id, ordre, code, nom FROM courses ORDER BY ordre')->fetchAll();

        $drivers = $pdo->query('SELECT j.id, j.nom, j.prenom, e.nom AS equipe, COALESCE(SUM(cr.points), 0) AS total
                                FROM joueurs j
                                LEFT JOIN equipes e ON e.id = j.id_equipe
                                LEFT JOIN course_results cr ON cr.joueur_id = j.id
                                GROUP BY j.id, j.nom, j.prenom, equipe
                                ORDER BY total DESC, j.nom, j.prenom')->fetchAll();

        $pointsByDriver = [];
        $stmt = $pdo->query('SELECT course_id, joueur_id, points, position FROM course_results');
        foreach ($stmt->fetchAll() as $row) {
            $joueurId = (int)$row['joueur_id'];
            $courseId = (int)$row['course_id'];
            $pointsByDriver[$joueurId][$courseId] = [
                'points' => (int)$row['points'],
                'position' => (int)$row['position'],
            ];
        }

        $this->render('standings.lame.php', [
            'courses' => $courses,
            'drivers' => $drivers,
            'pointsByDriver' => $pointsByDriver,
        ]);
    }

    /** Ajoute un résultat pour une course. */
    public function addResult(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $courseId = (int)($_POST['course_id'] ?? 0);
        $joueurId = (int)($_POST['joueur_id'] ?? 0);
        $position = (int)($_POST['position'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);

        $errors = $this->validateResult($courseId, $joueurId, $position, $points);
        if ($errors) {
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['course' => $courseId]);
        }

        Database::query(
            'INSERT INTO course_results (course_id, joueur_id, position, points) VALUES (?, ?, ?, ?)',
            [$courseId, $joueurId, $position, $points]
        );
        $_SESSION['calendar_flash'] = 'Résultat ajouté.';
        $this->redirectTo('calendrier', ['course' => $courseId]);
    }

    /** Met à jour un résultat existant. */
    public function updateResult(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $resultId = (int)($_POST['result_id'] ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);
        $joueurId = (int)($_POST['joueur_id'] ?? 0);
        $position = (int)($_POST['position'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);

        if ($resultId <= 0) {
            $_SESSION['calendar_errors'] = ['Résultat introuvable.'];
            $this->redirectTo('calendrier', ['course' => $courseId]);
        }

        $exists = Database::query('SELECT course_id FROM course_results WHERE id = ?', [$resultId])->fetchColumn();
        if (!$exists) {
            $_SESSION['calendar_errors'] = ['Résultat introuvable.'];
            $this->redirectTo('calendrier', ['course' => $courseId]);
        }
        $courseId = (int)$exists;

        $errors = $this->validateResult($courseId, $joueurId, $position, $points, $resultId);
        if ($errors) {
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['course' => $courseId]);
        }

        Database::query(
            'UPDATE course_results SET joueur_id = ?, position = ?, points = ? WHERE id = ?',
            [$joueurId, $position, $points, $resultId]
        );
        $_SESSION['calendar_flash'] = 'Résultat mis à jour.';
        $this->redirectTo('calendrier', ['course' => $courseId]);
    }

    /** Supprime un résultat. */
    public function deleteResult(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $resultId = (int)($_POST['result_id'] ?? 0);
        if ($resultId <= 0) {
            $_SESSION['calendar_errors'] = ['Résultat introuvable.'];
            $this->redirectTo('calendrier');
        }

        $row = Database::query('SELECT course_id FROM course_results WHERE id = ?', [$resultId])->fetch();
        if (!$row) {
            $_SESSION['calendar_errors'] = ['Résultat introuvable.'];
            $this->redirectTo('calendrier');
        }

        Database::query('DELETE FROM course_results WHERE id = ?', [$resultId]);
        $_SESSION['calendar_flash'] = 'Résultat supprimé.';
        $this->redirectTo('calendrier', ['course' => (int)$row['course_id']]);
    }

    /**
     * Valide un résultat de course et retourne un tableau d'erreurs.
     *
     * @param int $courseId
     * @param int $joueurId
     * @param int $position
     * @param int $points
     * @param int|null $excludeId
     * @return array<int, string>
     */
    private function validateResult(int $courseId, int $joueurId, int $position, int $points, ?int $excludeId = null): array
    {
        $errors = [];

        if ($courseId <= 0) {
            $errors[] = 'Grand Prix invalide.';
        } else {
            $courseExists = Database::query('SELECT 1 FROM courses WHERE id = ?', [$courseId])->fetchColumn();
            if (!$courseExists) {
                $errors[] = 'Grand Prix introuvable.';
            }
        }

        if ($joueurId <= 0) {
            $errors[] = 'Pilote invalide.';
        } else {
            $driverExists = Database::query('SELECT 1 FROM joueurs WHERE id = ?', [$joueurId])->fetchColumn();
            if (!$driverExists) {
                $errors[] = 'Pilote introuvable.';
            }
        }

        if ($position <= 0) {
            $errors[] = 'Position invalide.';
        }
        if ($points < 0) {
            $errors[] = 'Points invalides.';
        }

        if (!$errors) {
            $params = [$courseId, $joueurId];
            $sql = 'SELECT id FROM course_results WHERE course_id = ? AND joueur_id = ?';
            if ($excludeId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $excludeId;
            }
            $exists = Database::query($sql, $params)->fetchColumn();
            if ($exists) {
                $errors[] = 'Ce pilote a déjà un résultat pour cette manche.';
            }

            $params = [$courseId, $position];
            $sql = 'SELECT id FROM course_results WHERE course_id = ? AND position = ?';
            if ($excludeId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $excludeId;
            }
            $exists = Database::query($sql, $params)->fetchColumn();
            if ($exists) {
                $errors[] = 'Cette position est déjà attribuée.';
            }
        }

        return $errors;
    }
}

?>
