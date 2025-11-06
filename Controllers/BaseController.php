<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Security\Csrf;

/**
 * Classe de base pour les contrôleurs.
 * Centralise le rendu, la gestion CSRF et des utilitaires partagés.
 */
class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        if (!isset($data['csrfToken'])) {
            $data['csrfToken'] = Csrf::getToken();
        }
        extract($data);
        require __DIR__ . '/../Views/layout/header.lame.php';
        require __DIR__ . '/../Views/' . $view;
        require __DIR__ . '/../Views/layout/footer.lame.php';
    }

    /** Déplace un fichier image uploadé et retourne le chemin relatif ou null */
    protected function handleImageUpload(string $fieldName): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $file = $_FILES[$fieldName];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            return null; // type refusé
        }
        $ext = $allowed[$mime];
        $name = uniqid('img_', true) . '.' . $ext;
        $destDir = __DIR__ . '/../Public/uploads/';
        if (!is_dir($destDir) && !mkdir($destDir, 0775, true) && !is_dir($destDir)) {
            return null;
        }
        $destPath = $destDir . $name;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return null;
        }
        return 'Public/uploads/' . $name; // chemin relatif côté web
    }

    /** Vérifie la présence et la validité du jeton CSRF transmis. */
    protected function requireCsrf(): void
    {
        $token = $_POST['_csrf'] ?? null;
        if (!Csrf::isValid(is_string($token) ? $token : null)) {
            http_response_code(419);
            exit('Jeton CSRF invalide ou expiré.');
        }
    }

    /** Garantit que le chemin conservé correspond à un upload existant et sûr. */
    protected function sanitizeExistingUpload(string $fieldName): ?string
    {
        $value = $_POST[$fieldName] ?? null;
        if (!is_string($value)) {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $uploadsPrefix = 'Public/uploads/';
        if (strncmp($value, $uploadsPrefix, strlen($uploadsPrefix)) !== 0) {
            return null;
        }
        $fileName = basename($value);
        if ($fileName === '' || $fileName === '.' || $fileName === '..') {
            return null;
        }
        $safePath = $uploadsPrefix . $fileName;
        $fullPath = __DIR__ . '/../' . $safePath;
        if (!is_file($fullPath)) {
            return null;
        }
        return $safePath;
    }

    /** Redirige vers une route interne avec éventuels paramètres additionnels. */
    protected function redirectTo(string $route, array $params = []): void
    {
        $params['route'] = $route;
        $query = http_build_query($params);
        header('Location: ?' . $query);
        exit;
    }
}

?>
