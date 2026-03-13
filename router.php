<?php
declare(strict_types=1);

$uri = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$path = is_string($uri) && $uri !== '' ? $uri : '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

if ($path !== '/' && is_dir($file) && is_file(rtrim($file, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.php')) {
    return false;
}

require __DIR__ . '/index.php';
