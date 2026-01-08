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
            'Accueil' => '?route=accueil',
            'Grands Prix' => '?route=championnats',
            'Écuries' => '?route=ecuries',
            'Pilotes' => '?route=pilotes',
            'Pilotes par écurie' => '?route=jointure',
            'Calendrier 2026' => '?route=calendrier',
            'Classement' => '?route=classements',
            'Paris' => '?route=paris',
        ];
        $html = '<nav class="main-nav"><ul class="nav-list">';
        foreach ($items as $label => $href) {
            $html .= '<li class="nav-item"><a class="nav-link" href="' . $href . '">' . $label . '</a></li>';
        }
        $html .= '</ul></nav>';
        return $html;
    }
}

?>
