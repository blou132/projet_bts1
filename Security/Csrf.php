<?php
declare(strict_types=1);

namespace App\Security;

/**
 * Class Csrf
 *
 * Service statique charge de generer et verifier les jetons CSRF.
 */
class Csrf
{
    /**
     * @var string Cle de session utilisee pour stocker le jeton.
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * Retourne le jeton CSRF courant de la session.
     *
     * @throws \RuntimeException Si la session PHP n'est pas demarree.
     * @return string
     */
    public static function getToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('La session doit être démarrée avant d\'utiliser Csrf::getToken().');
        }

        $token = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $_SESSION[self::SESSION_KEY] = $token;
        }

        return $token;
    }

    /**
     * Verifie si un jeton fourni correspond au jeton stocke en session.
     *
     * @param string|null $token Jeton recu depuis le formulaire.
     * @return bool
     */
    public static function isValid(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        if (!is_string($token) || !is_string($sessionToken) || $token === '' || $sessionToken === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}

?>
