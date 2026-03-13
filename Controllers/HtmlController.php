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
        $items = [
            'Accueil' => '/accueil',
            'Docs' => '/documentation',
            'Écuries' => '/ecuries',
            'Calendrier 2026' => '/calendrier',
            'Classement' => '/classements',
            'Paris sportifs' => '/paris',
        ];
        $html = '<nav class="main-nav" id="main-navigation"><ul class="nav-list">';
        foreach ($items as $label => $href) {
            $html .= '<li class="nav-item"><a class="nav-link" href="' . $href . '">' . $label . '</a></li>';
        }
        $html .= '</ul></nav>';
        return $html;
    }
}

?>
