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
        $betPodium = $this->fetchPodium($courseId);
        $courseBets = $this->fetchCourseBets($courseId);
        $betLeaderboard = [];
        $userBetScore = null;
        if ($betPodium) {
            foreach ($courseBets as $bet) {
                $score = $this->computeBetScore($bet, $betPodium);
                $bet['score'] = $score['total'];
                $bet['score_detail'] = $score;
                $betLeaderboard[] = $bet;
                if ($currentUser && (int)$bet['user_id'] === (int)$currentUser['id']) {
                    $userBetScore = $score;
                }
            }
            usort($betLeaderboard, static function (array $a, array $b): int {
                $scoreCmp = ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
                if ($scoreCmp !== 0) {
                    return $scoreCmp;
                }
                $nameA = strtolower((string)($a['name'] ?? $a['email'] ?? ''));
                $nameB = strtolower((string)($b['name'] ?? $b['email'] ?? ''));
                return $nameA <=> $nameB;
            });
        }
        $betStats = [
            'total' => count($courseBets),
            'scored' => $betPodium ? count($betLeaderboard) : 0,
        ];

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
            'betPodium' => $betPodium,
            'betLeaderboard' => $betLeaderboard,
            'betStats' => $betStats,
            'userBetScore' => $userBetScore,
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

    /** Affiche le classement general des paris. */
    public function bets(): void
    {
        $pdo = Database::getInstance();
        $users = $pdo->query('SELECT id, name, email FROM users ORDER BY name')->fetchAll();
        $bets = $pdo->query('SELECT user_id, course_id, first_joueur_id, second_joueur_id, third_joueur_id FROM bets')->fetchAll();
        $podiums = $this->fetchAllPodiums();

        $leaderboard = [];
        foreach ($users as $user) {
            $userId = (int)$user['id'];
            $leaderboard[$userId] = [
                'user_id' => $userId,
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'points' => 0,
                'perfect' => 0,
                'exact' => 0,
                'partial' => 0,
                'bets' => 0,
            ];
        }

        foreach ($bets as $bet) {
            $courseId = (int)$bet['course_id'];
            if (!isset($podiums[$courseId])) {
                continue;
            }
            $userId = (int)$bet['user_id'];
            if (!isset($leaderboard[$userId])) {
                continue;
            }
            $score = $this->computeBetScore($bet, $podiums[$courseId]);
            $leaderboard[$userId]['points'] += $score['total'];
            $leaderboard[$userId]['perfect'] += $score['perfect'] ? 1 : 0;
            $leaderboard[$userId]['exact'] += $score['exact'];
            $leaderboard[$userId]['partial'] += $score['partial'];
            $leaderboard[$userId]['bets'] += 1;
        }

        $entries = array_values(array_filter($leaderboard, static fn(array $row): bool => $row['bets'] > 0));
        usort($entries, static function (array $a, array $b): int {
            $pointsCmp = $b['points'] <=> $a['points'];
            if ($pointsCmp !== 0) {
                return $pointsCmp;
            }
            $perfectCmp = $b['perfect'] <=> $a['perfect'];
            if ($perfectCmp !== 0) {
                return $perfectCmp;
            }
            $exactCmp = $b['exact'] <=> $a['exact'];
            if ($exactCmp !== 0) {
                return $exactCmp;
            }
            $nameA = strtolower((string)($a['name'] ?? $a['email'] ?? ''));
            $nameB = strtolower((string)($b['name'] ?? $b['email'] ?? ''));
            return $nameA <=> $nameB;
        });

        $this->render('bets.lame.php', [
            'leaderboard' => $entries,
            'coursesScored' => count($podiums),
            'betsTotal' => count($bets),
        ]);
    }

    /** Ajoute un résultat pour une course. */
    public function addResult(): void
    {
        $this->requireAdmin();
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
        $this->requireAdmin();
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
        $this->requireAdmin();
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
        $isAdmin = isset($currentUser['role']) && $currentUser['role'] === 'admin';
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

    private function fetchCourseBets(int $courseId): array
    {
        $stmt = Database::getInstance()->prepare('SELECT b.user_id, b.course_id, b.first_joueur_id, b.second_joueur_id, b.third_joueur_id,
                                                         u.name, u.email
                                                  FROM bets b
                                                  JOIN users u ON u.id = b.user_id
                                                  WHERE b.course_id = ?
                                                  ORDER BY u.name, u.email');
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    private function fetchPodium(int $courseId): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT position, joueur_id
                                                  FROM course_results
                                                  WHERE course_id = ? AND position IN (1, 2, 3)');
        $stmt->execute([$courseId]);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }
        $podium = [];
        foreach ($rows as $row) {
            $pos = (int)$row['position'];
            if ($pos < 1 || $pos > 3 || isset($podium[$pos])) {
                continue;
            }
            $podium[$pos] = (int)$row['joueur_id'];
        }
        if (count($podium) !== 3) {
            return null;
        }
        ksort($podium);
        return $podium;
    }

    private function fetchAllPodiums(): array
    {
        $rows = Database::getInstance()->query('SELECT course_id, position, joueur_id
                                                FROM course_results
                                                WHERE position IN (1, 2, 3)
                                                ORDER BY course_id')->fetchAll();
        $podiums = [];
        foreach ($rows as $row) {
            $courseId = (int)$row['course_id'];
            $pos = (int)$row['position'];
            if ($pos < 1 || $pos > 3) {
                continue;
            }
            if (!isset($podiums[$courseId])) {
                $podiums[$courseId] = [];
            }
            if (!isset($podiums[$courseId][$pos])) {
                $podiums[$courseId][$pos] = (int)$row['joueur_id'];
            }
        }
        foreach ($podiums as $courseId => $podium) {
            if (count($podium) !== 3) {
                unset($podiums[$courseId]);
                continue;
            }
            ksort($podium);
            $podiums[$courseId] = $podium;
        }
        return $podiums;
    }

    private function computeBetScore(array $bet, array $podium): array
    {
        $predicted = [
            1 => (int)($bet['first_joueur_id'] ?? 0),
            2 => (int)($bet['second_joueur_id'] ?? 0),
            3 => (int)($bet['third_joueur_id'] ?? 0),
        ];
        $actual = [
            1 => (int)($podium[1] ?? 0),
            2 => (int)($podium[2] ?? 0),
            3 => (int)($podium[3] ?? 0),
        ];
        $actualValues = array_values($actual);

        $exact = 0;
        $partial = 0;
        foreach ($predicted as $position => $driverId) {
            if ($driverId > 0 && $driverId === $actual[$position]) {
                $exact++;
            } elseif ($driverId > 0 && in_array($driverId, $actualValues, true)) {
                $partial++;
            }
        }

        $total = ($exact * 3) + $partial;
        $perfect = ($exact === 3);
        if ($perfect) {
            $total += 2;
        }

        return [
            'total' => $total,
            'exact' => $exact,
            'partial' => $partial,
            'perfect' => $perfect,
        ];
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
