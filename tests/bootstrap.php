<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require dirname(__DIR__) . '/Autoload.php';

$GLOBALS['__tests'] = [];

/**
 * Enregistre un test a executer.
 */
function test(string $name, callable $fn): void
{
    $GLOBALS['__tests'][] = ['name' => $name, 'fn' => $fn];
}

/**
 * Assertion booleenne.
 */
function expectTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/**
 * Assertion d'egalite stricte.
 */
function expectSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        $debug = sprintf(
            "%s\nAttendu: %s\nRecu: %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        );
        throw new RuntimeException($debug);
    }
}

/**
 * Assertion de presence d'une sous-chaine.
 */
function expectContains(string $needle, string $haystack, string $message): void
{
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException($message);
    }
}
