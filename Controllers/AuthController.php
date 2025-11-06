<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Security\Csrf;

/**
 * Gère l'authentification basique (login/logout) pour l'espace back-office.
 */
class AuthController extends BaseController
{
    /** Affiche le formulaire de connexion. */
    public function login(): void
    {
        $flash = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->render('auth/login.lame.php', [
            'csrfToken' => Csrf::getToken(),
            'flashError' => $flash,
            'old' => [],
        ]);
    }

    /** Traite la soumission du formulaire de connexion. */
    public function authenticate(): void
    {
        $this->requireCsrf();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse e-mail invalide.';
        }
        if ($password === '') {
            $errors[] = 'Mot de passe requis.';
        }

        if ($errors) {
            $flash = $_SESSION['flash_error'] ?? null;
            unset($_SESSION['flash_error']);
            $this->render('auth/login.lame.php', [
                'errors' => $errors,
                'old' => ['email' => $email],
                'csrfToken' => Csrf::getToken(),
                'flashError' => $flash,
            ]);
            return;
        }

        $stmt = Database::getInstance()->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $flash = $_SESSION['flash_error'] ?? null;
            unset($_SESSION['flash_error']);
            $this->render('auth/login.lame.php', [
                'errors' => ['Identifiants invalides.'],
                'old' => ['email' => $email],
                'csrfToken' => Csrf::getToken(),
                'flashError' => $flash,
            ]);
            return;
        }

        unset($user['password']);
        $_SESSION['user'] = $user;

        $this->redirectTo('accueil');
    }

    /** Déconnecte l'utilisateur courant. */
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        $_SESSION['flash_error'] = 'Session terminée.';
        $this->redirectTo('auth', ['action' => 'login']);
    }
}

?>
