<?php
declare(strict_types=1);

// PSR-4 minimal pour l'espace de noms App\*
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR; // racine du projet

    // la classe n'utilise pas le préfixe ? on ignore
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // nom de classe relatif
    $relativeClass = substr($class, $len);

    // remplace namespace par séparateur de dossiers, ajoute .php
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
?>
