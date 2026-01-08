<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Controllers\ValidationController;
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

    /** Affiche le formulaire de création de compte. */
    public function register(): void
    {
        $flash = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->render('auth/register.lame.php', [
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

    /** Crée un compte utilisateur. */
    public function store(): void
    {
        $this->requireCsrf();

        $name = ValidationController::clean($_POST['name'] ?? '');
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        $errors = [];
        if (!ValidationController::nom($name)) {
            $errors[] = 'Nom invalide.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse e-mail invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (!$errors) {
            $stmt = Database::getInstance()->prepare('SELECT 1 FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn()) {
                $errors[] = 'Adresse e-mail deja utilisee.';
            }
        }

        if ($errors) {
            $flash = $_SESSION['flash_error'] ?? null;
            unset($_SESSION['flash_error']);
            $this->render('auth/register.lame.php', [
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'email' => $email,
                ],
                'csrfToken' => Csrf::getToken(),
                'flashError' => $flash,
            ]);
            return;
        }

        $stmt = Database::getInstance()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

        $_SESSION['user'] = [
            'id' => (int)Database::getInstance()->lastInsertId(),
            'name' => $name,
            'email' => $email,
        ];

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
