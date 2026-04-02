<?php
declare(strict_types=1);

if (!function_exists('app_base_path')) {
    /**
     * Retourne le prefixe d'URL du projet (ex: /projet_bts1) ou chaine vide a la racine.
     */
    function app_base_path(): string
    {
        static $basePath = null;
        if ($basePath !== null) {
            return $basePath;
        }

        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptName = str_replace('\\', '/', $scriptName);
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        $scriptDir = trim($scriptDir);

        if ($scriptDir === '' || $scriptDir === '.' || $scriptDir === '/') {
            $basePath = '';
            return $basePath;
        }

        $basePath = '/' . trim($scriptDir, '/');
        return $basePath;
    }
}

if (!function_exists('route_path')) {
    /**
     * Construit une URL locale en tenant compte du sous-dossier de deploiement.
     */
    function route_path(string $path = ''): string
    {
        if ($path === '') {
            $base = app_base_path();
            return $base !== '' ? $base : '/';
        }

        if (preg_match('#^(https?:)?//#i', $path) === 1) {
            return $path;
        }

        $base = app_base_path();
        $raw = $path;
        $fragment = '';
        $query = '';

        $fragmentPos = strpos($raw, '#');
        if ($fragmentPos !== false) {
            $fragment = substr($raw, $fragmentPos);
            $raw = substr($raw, 0, $fragmentPos);
        }

        $queryPos = strpos($raw, '?');
        if ($queryPos !== false) {
            $query = substr($raw, $queryPos);
            $raw = substr($raw, 0, $queryPos);
        }

        $normalizedPath = '/' . ltrim($raw, '/');
        $relativePath = $normalizedPath;
        if ($base !== '' && ($relativePath === $base || str_starts_with($relativePath, $base . '/'))) {
            $relativePath = substr($relativePath, strlen($base));
            if ($relativePath === '') {
                $relativePath = '/';
            }
        }

        if ($relativePath === '/index.php' || str_starts_with($relativePath, '/index.php/')) {
            $final = $base . $relativePath;
            return $final . $query . $fragment;
        }

        $segments = array_values(array_filter(explode('/', trim($relativePath, '/')), static fn(string $s): bool => $s !== ''));
        $first = $segments[0] ?? '';
        $second = $segments[1] ?? '';

        $isMvcRoute = in_array($first, [
            'accueil',
            'documentation',
            'ecuries',
            'equipes',
            'pilotes',
            'joueurs',
            'jointure',
            'calendrier',
            'classements',
            'paris',
            'auth',
        ], true);

        if (!$isMvcRoute && $first === 'docs' && $second === '') {
            $isMvcRoute = true;
        }

        if ($isMvcRoute) {
            $suffix = $relativePath === '/' ? '' : $relativePath;
            $final = $base . '/index.php' . $suffix;
            return $final . $query . $fragment;
        }

        if ($base !== '' && ($normalizedPath === $base || str_starts_with($normalizedPath, $base . '/'))) {
            return $normalizedPath . $query . $fragment;
        }

        return $base . $normalizedPath . $query . $fragment;
    }
}

if (!function_exists('asset_path')) {
    /**
     * Alias semantique pour les ressources statiques.
     */
    function asset_path(string $path): string
    {
        return route_path($path);
    }
}
