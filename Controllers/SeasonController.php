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

        $this->render('calendar.lame.php', [
            'calendar' => $calendar,
            'year' => 2026,
        ]);
    }

    /** Affiche une course et ses résultats sur une page dédiée. */
    public function course(): void
    {
        $courseId = (int)($_GET['course'] ?? 0);
        if ($courseId <= 0) {
            $this->redirectTo('calendrier');
        }

        $course = $this->fetchCourse($courseId);

        if (!$course) {
            http_response_code(404);
            exit('Course introuvable');
        }

        $courseResults = $this->fetchCourseResults($courseId);
        $drivers = $this->fetchDrivers();
        $driversById = [];
        foreach ($drivers as $driver) {
            $driversById[(int)$driver['id']] = $driver;
        }
        $currentUser = $this->currentUser();
        $userBet = $currentUser ? $this->fetchUserBet($courseId, (int)$currentUser['id']) : null;
        $betWindow = $this->getBetWindow($course);

        $flashErrors = $_SESSION['calendar_errors'] ?? [];
        $flashSuccess = $_SESSION['calendar_flash'] ?? null;
        $betErrors = $_SESSION['bet_errors'] ?? [];
        $betFlash = $_SESSION['bet_flash'] ?? null;
        unset($_SESSION['calendar_errors'], $_SESSION['calendar_flash'], $_SESSION['bet_errors'], $_SESSION['bet_flash']);

        if (($_GET['partial'] ?? '') === 'results') {
            $this->sendCourseResultsPartial($course, $courseResults, $drivers, $flashErrors, $flashSuccess);
        }

        $this->render('course.lame.php', [
            'course' => $course,
            'courseResults' => $courseResults,
            'drivers' => $drivers,
            'calendarErrors' => $flashErrors,
            'calendarFlash' => $flashSuccess,
            'betWindow' => $betWindow,
            'userBet' => $userBet,
            'driversById' => $driversById,
            'betErrors' => $betErrors,
            'betFlash' => $betFlash,
        ]);
    }

    /** Affiche le classement pilotes mis à jour à partir des résultats en base. */
    public function standings(): void
    {
        $pdo = Database::getInstance();

        $courses = $pdo->query('SELECT id, ordre, code, nom FROM courses ORDER BY ordre')->fetchAll();

        $drivers = $pdo->query('SELECT j.id, j.nom, j.prenom, e.nom AS ecurie, COALESCE(SUM(cr.points), 0) AS total
                                FROM joueurs j
                                LEFT JOIN equipes e ON e.id = j.id_equipe
                                LEFT JOIN course_results cr ON cr.joueur_id = j.id
                                GROUP BY j.id, j.nom, j.prenom, e.nom
                                ORDER BY total DESC, j.nom, j.prenom')->fetchAll();

        $pointsByDriver = [];
        $stmt = $pdo->query('SELECT course_id, joueur_id, points, position FROM course_results');
        foreach ($stmt->fetchAll() as $row) {
            $piloteId = (int)$row['joueur_id'];
            $courseId = (int)$row['course_id'];
            $pointsByDriver[$piloteId][$courseId] = [
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
        $piloteId = (int)($_POST['pilote_id'] ?? 0);
        $position = (int)($_POST['position'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);

        $errors = $this->validateResult($courseId, $piloteId, $position, $points);
        if ($errors) {
            if ($this->isAjaxRequest()) {
                http_response_code(422);
                $course = $this->fetchCourse($courseId);
                if ($course) {
                    $this->sendCourseResultsPartial(
                        $course,
                        $this->fetchCourseResults($courseId),
                        $this->fetchDrivers(),
                        $errors,
                        null
                    );
                }
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
        }

        Database::query(
            'INSERT INTO course_results (course_id, joueur_id, position, points) VALUES (?, ?, ?, ?)',
            [$courseId, $piloteId, $position, $points]
        );
        if ($this->isAjaxRequest()) {
            $course = $this->fetchCourse($courseId);
            if ($course) {
                $this->sendCourseResultsPartial(
                    $course,
                    $this->fetchCourseResults($courseId),
                    $this->fetchDrivers(),
                    [],
                    'Resultat ajoute.'
                );
            }
        }
        $_SESSION['calendar_flash'] = 'Résultat ajouté.';
        $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
    }

    /** Met à jour un résultat existant. */
    public function updateResult(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $resultId = (int)($_POST['result_id'] ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);
        $piloteId = (int)($_POST['pilote_id'] ?? 0);
        $position = (int)($_POST['position'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);

        if ($resultId <= 0) {
            $errors = ['Résultat introuvable.'];
            if ($this->isAjaxRequest() && $courseId > 0) {
                $course = $this->fetchCourse($courseId);
                if ($course) {
                    $this->sendCourseResultsPartial(
                        $course,
                        $this->fetchCourseResults($courseId),
                        $this->fetchDrivers(),
                        $errors,
                        null
                    );
                }
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
        }

        $exists = Database::query('SELECT course_id FROM course_results WHERE id = ?', [$resultId])->fetchColumn();
        if (!$exists) {
            $errors = ['Résultat introuvable.'];
            if ($this->isAjaxRequest() && $courseId > 0) {
                $course = $this->fetchCourse($courseId);
                if ($course) {
                    $this->sendCourseResultsPartial(
                        $course,
                        $this->fetchCourseResults($courseId),
                        $this->fetchDrivers(),
                        $errors,
                        null
                    );
                }
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
        }
        $courseId = (int)$exists;

        $errors = $this->validateResult($courseId, $piloteId, $position, $points, $resultId);
        if ($errors) {
            if ($this->isAjaxRequest()) {
                $course = $this->fetchCourse($courseId);
                if ($course) {
                    $this->sendCourseResultsPartial(
                        $course,
                        $this->fetchCourseResults($courseId),
                        $this->fetchDrivers(),
                        $errors,
                        null
                    );
                }
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
        }

        Database::query(
            'UPDATE course_results SET joueur_id = ?, position = ?, points = ? WHERE id = ?',
            [$piloteId, $position, $points, $resultId]
        );
        if ($this->isAjaxRequest()) {
            $course = $this->fetchCourse($courseId);
            if ($course) {
                $this->sendCourseResultsPartial(
                    $course,
                    $this->fetchCourseResults($courseId),
                    $this->fetchDrivers(),
                    [],
                    'Resultat mis a jour.'
                );
            }
        }
        $_SESSION['calendar_flash'] = 'Résultat mis à jour.';
        $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
    }

    /** Supprime un résultat. */
    public function deleteResult(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $resultId = (int)($_POST['result_id'] ?? 0);
        if ($resultId <= 0) {
            $errors = ['Résultat introuvable.'];
            if ($this->isAjaxRequest()) {
                http_response_code(404);
                exit('Resultat introuvable.');
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier');
        }

        $row = Database::query('SELECT course_id FROM course_results WHERE id = ?', [$resultId])->fetch();
        if (!$row) {
            $errors = ['Résultat introuvable.'];
            if ($this->isAjaxRequest()) {
                http_response_code(404);
                exit('Resultat introuvable.');
            }
            $_SESSION['calendar_errors'] = $errors;
            $this->redirectTo('calendrier');
        }

        Database::query('DELETE FROM course_results WHERE id = ?', [$resultId]);
        if ($this->isAjaxRequest()) {
            $courseId = (int)$row['course_id'];
            $course = $this->fetchCourse($courseId);
            if ($course) {
                $this->sendCourseResultsPartial(
                    $course,
                    $this->fetchCourseResults($courseId),
                    $this->fetchDrivers(),
                    [],
                    'Resultat supprime.'
                );
            }
        }
        $_SESSION['calendar_flash'] = 'Résultat supprimé.';
        $this->redirectTo('calendrier', ['action' => 'course', 'course' => (int)$row['course_id']]);
    }

    /** Enregistre un pari (podium) pour une course. */
    public function placeBet(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $courseId = (int)($_POST['course_id'] ?? 0);
        $first = (int)($_POST['first_pilote_id'] ?? 0);
        $second = (int)($_POST['second_pilote_id'] ?? 0);
        $third = (int)($_POST['third_pilote_id'] ?? 0);

        $errors = [];
        $course = $courseId > 0 ? $this->fetchCourse($courseId) : null;
        if (!$course) {
            $errors[] = 'Course introuvable.';
        }

        if (!$errors) {
            $betWindow = $this->getBetWindow($course);
            if (!$betWindow['isOpen']) {
                $errors[] = $betWindow['reason'] ?? 'Les paris sont fermes.';
            }
        }

        if ($first <= 0 || $second <= 0 || $third <= 0) {
            $errors[] = 'Veuillez selectionner le podium complet.';
        }

        if (count(array_unique([$first, $second, $third])) < 3) {
            $errors[] = 'Les pilotes doivent etre differents.';
        }

        if (!$errors) {
            $stmt = Database::getInstance()->prepare('SELECT COUNT(*) FROM joueurs WHERE id IN (?, ?, ?)');
            $stmt->execute([$first, $second, $third]);
            if ((int)$stmt->fetchColumn() !== 3) {
                $errors[] = 'Pilote invalide.';
            }
        }

        if ($errors) {
            $_SESSION['bet_errors'] = $errors;
            $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
        }

        $user = $this->currentUser();
        $userId = (int)$user['id'];

        Database::query(
            'INSERT INTO bets (user_id, course_id, first_joueur_id, second_joueur_id, third_joueur_id)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               first_joueur_id = VALUES(first_joueur_id),
               second_joueur_id = VALUES(second_joueur_id),
               third_joueur_id = VALUES(third_joueur_id)',
            [$userId, $courseId, $first, $second, $third]
        );

        $_SESSION['bet_flash'] = 'Pari enregistre.';
        $this->redirectTo('calendrier', ['action' => 'course', 'course' => $courseId]);
    }

    private function isAjaxRequest(): bool
    {
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return strtolower($requestedWith) === 'xmlhttprequest';
    }

    private function fetchCourse(int $courseId): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT id, ordre, code, nom, pays, ville, date_course, flag FROM courses WHERE id = ?');
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        return $course ?: null;
    }

    private function fetchCourseResults(int $courseId): array
    {
        $stmt = Database::getInstance()->prepare('SELECT cr.id AS result_id, cr.position, cr.points, j.id AS pilote_id, j.prenom, j.nom, e.nom AS ecurie
                                                 FROM course_results cr
                                                 JOIN joueurs j ON j.id = cr.joueur_id
                                                 JOIN equipes e ON e.id = j.id_equipe
                                                 WHERE cr.course_id = ?
                                                 ORDER BY cr.position');
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    private function fetchDrivers(): array
    {
        return Database::getInstance()->query('SELECT j.id, j.prenom, j.nom, e.nom AS ecurie
                                               FROM joueurs j
                                               JOIN equipes e ON e.id = j.id_equipe
                                               ORDER BY e.nom, j.nom')->fetchAll();
    }

    private function sendCourseResultsPartial(array $course, array $courseResults, array $drivers, array $calendarErrors, ?string $calendarFlash): void
    {
        $csrfToken = \App\Security\Csrf::getToken();
        $currentUser = $_SESSION['user'] ?? null;
        require __DIR__ . '/../Views/partials/course_results.lame.php';
        exit;
    }

    private function fetchUserBet(int $courseId, int $userId): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT id, first_joueur_id, second_joueur_id, third_joueur_id
                                                  FROM bets
                                                  WHERE course_id = ? AND user_id = ?');
        $stmt->execute([$courseId, $userId]);
        $bet = $stmt->fetch();
        return $bet ?: null;
    }

    private function getBetWindow(array $course): array
    {
        $now = new \DateTime('now');
        $courseDate = new \DateTime($course['date_course']);
        $closeAt = (clone $courseDate)->modify('-1 day')->setTime(23, 59, 59);

        $stmt = Database::getInstance()->prepare('SELECT date_course FROM courses WHERE ordre < ? ORDER BY ordre DESC LIMIT 1');
        $stmt->execute([(int)$course['ordre']]);
        $prevDate = $stmt->fetchColumn();
        $openAt = null;
        if (is_string($prevDate) && $prevDate !== '') {
            $openAt = (new \DateTime($prevDate))->setTime(23, 59, 59);
        }

        $isOpen = ($openAt === null || $now > $openAt) && $now <= $closeAt;
        $reason = null;
        if (!$isOpen) {
            if ($openAt !== null && $now <= $openAt) {
                $reason = 'Les paris ouvrent apres la fin de la course precedente.';
            } elseif ($now > $closeAt) {
                $reason = 'Les paris sont clotures (J-1).';
            } else {
                $reason = 'Les paris sont fermes.';
            }
        }

        return [
            'isOpen' => $isOpen,
            'openAt' => $openAt,
            'closeAt' => $closeAt,
            'reason' => $reason,
        ];
    }

    /**
     * Valide un résultat de course et retourne un tableau d'erreurs.
     *
     * @param int $courseId
     * @param int $piloteId
     * @param int $position
     * @param int $points
     * @param int|null $excludeId
     * @return array<int, string>
     */
    private function validateResult(int $courseId, int $piloteId, int $position, int $points, ?int $excludeId = null): array
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

        if ($piloteId <= 0) {
            $errors[] = 'Pilote invalide.';
        } else {
            $driverExists = Database::query('SELECT 1 FROM joueurs WHERE id = ?', [$piloteId])->fetchColumn();
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
            $params = [$courseId, $piloteId];
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
