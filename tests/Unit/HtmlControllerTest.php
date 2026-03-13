<?php
declare(strict_types=1);

use App\Controllers\HtmlController;

test('Menu: contient les entrees principales', function (): void {
    $html = (new HtmlController())->menu();

    expectContains('/accueil', $html, 'Le lien Accueil est manquant.');
    expectContains('/ecuries', $html, 'Le lien Ecuries est manquant.');
    expectContains('/calendrier', $html, 'Le lien Calendrier est manquant.');
    expectContains('/classements', $html, 'Le lien Classement est manquant.');
    expectContains('/paris', $html, 'Le lien Paris sportifs est manquant.');
});

test('Menu: libelle Paris sportifs present', function (): void {
    $html = (new HtmlController())->menu();

    expectContains('Paris sportifs', $html, 'Le libelle "Paris sportifs" doit etre present dans le menu.');
});
