<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Construit les fragments HTML statiques (menu principal, ...).
 */
class HtmlController
{
    public function menu(): string
    {
        $currentPath = $this->currentPath();
        $items = [
            'Accueil' => '/accueil',
            'Écuries' => '/ecuries',
            'Calendrier 2026' => '/calendrier',
            'Classement' => '/classements',
            'Paris sportifs' => '/paris',
        ];
        $html = '<nav class="main-nav" id="main-navigation"><ul class="nav-list">';
        foreach ($items as $label => $href) {
            $active = $this->isActivePath($currentPath, $href);
            $class = $active ? 'nav-link is-active' : 'nav-link';
            $ariaCurrent = $active ? ' aria-current="page"' : '';
            $html .= '<li class="nav-item"><a class="' . $class . '" href="' . $href . '"' . $ariaCurrent . '>' . $label . '</a></li>';
        }
        $html .= '</ul></nav>';
        return $html;
    }

    private function currentPath(): string
    {
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $normalized = is_string($path) && $path !== '' ? rtrim($path, '/') : '/';
        return $normalized === '' ? '/' : $normalized;
    }

    private function isActivePath(string $currentPath, string $itemPath): bool
    {
        $current = rtrim($currentPath, '/');
        $item = rtrim($itemPath, '/');
        $current = $current === '' ? '/' : $current;
        $item = $item === '' ? '/' : $item;

        if ($current === $item) {
            return true;
        }

        if ($item === '/') {
            return false;
        }

        return str_starts_with($current, $item . '/');
    }
}

?>
