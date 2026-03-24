<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Rassemble les règles de validation utilisées par l'ensemble des formulaires.
 */
class ValidationController
{
    /**
     * Liste minimale de mots interdits.
     *
     * @var array<int, string>
     */
    private const PROFANITY = [
        'connard',
        'con',
        'salope',
        'pute',
        'encule',
        'enculer',
        'fdp',
        'merde',
        'batard',
    ];

    public static function clean(string $s): string
    {
        return trim(filter_var($s, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    }

    public static function hasProfanity(string $s): bool
    {
        $lower = function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
        $ascii = $lower;

        if (function_exists('transliterator_transliterate')) {
            $ascii = transliterator_transliterate('Any-Latin; Latin-ASCII;', $lower);
        } else {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lower);
            if ($converted !== false) {
                $ascii = $converted;
            }
        }

        $tokens = preg_split('/[^a-z0-9]+/i', $ascii, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($tokens) || $tokens === []) {
            return false;
        }

        foreach ($tokens as $token) {
            if (in_array($token, self::PROFANITY, true)) {
                return true;
            }
        }

        return false;
    }

    public static function nom(string $s): bool
    {
        // Lettres, espaces, tirets, apostrophes (2..80)
        return !self::hasProfanity($s) && (bool)preg_match("/^[A-Za-zÀ-ÿ' -]{2,80}$/u", $s);
    }

    public static function poste(string $s): bool
    {
        // ex: Pilote titulaire, Réserve, Directeur
        return !self::hasProfanity($s) && (bool)preg_match("/^[A-Za-zÀ-ÿ' -]{2,40}$/u", $s);
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
