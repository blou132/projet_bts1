<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Rassemble les règles de validation utilisées par l'ensemble des formulaires.
 */
class ValidationController
{
    public static function clean(string $s): string
    {
        return trim(filter_var($s, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    }

    public static function nom(string $s): bool
    {
        // Lettres, espaces, tirets, apostrophes (2..80)
        return (bool)preg_match("/^[A-Za-zÀ-ÿ' -]{2,80}$/u", $s);
    }

    public static function poste(string $s): bool
    {
        // ex: Pilote titulaire, Réserve, Directeur
        return (bool)preg_match("/^[A-Za-zÀ-ÿ' -]{2,40}$/u", $s);
    }

    public static function pays(string $s): bool
    {
        return self::nom($s);
    }

    public static function ville(string $s): bool
    {
        return self::nom($s);
    }
}

?>
