<?php
declare(strict_types=1);

test('HTTP: page accueil accessible', function (): void {
    $response = httpRequest('GET', '/accueil');

    expectSame(200, $response['status'], 'La page /accueil doit repondre en 200.');
    expectContains('Paddock Manager', $response['body'], 'Le contenu de l accueil est manquant.');
});

test('HTTP: documentation accessible sans connexion', function (): void {
    $response = httpRequest('GET', '/documentation');

    expectSame(200, $response['status'], 'La page /documentation doit repondre en 200.');
    expectContains('Documentation projet', $response['body'], 'Le hub documentation doit etre visible.');
});

test('HTTP: alias /docs actif', function (): void {
    $response = httpRequest('GET', '/docs');

    expectSame(200, $response['status'], 'La route /docs doit fonctionner.');
    expectContains('Documentation projet', $response['body'], 'La route /docs doit mener au hub documentation.');
});

test('HTTP: calendrier accessible', function (): void {
    $response = httpRequest('GET', '/calendrier');

    expectSame(200, $response['status'], 'La page /calendrier doit repondre en 200.');
    expectContains('Calendrier 2026', $response['body'], 'Le titre du calendrier est introuvable.');
});

test('HTTP: detail course accessible', function (): void {
    $courseId = (int)App\Database\Database::query('SELECT id FROM courses ORDER BY ordre LIMIT 1')->fetchColumn();
    $response = httpRequest('GET', '/calendrier/course/' . $courseId);

    expectSame(200, $response['status'], 'Le detail d une course doit repondre en 200.');
    expectContains('Resultats', $response['body'], 'Le bloc resultats doit etre present.');
    expectContains('Pari sur le podium', $response['body'], 'Le bloc de pari doit etre present.');
});

test('HTTP: classement global des paris accessible', function (): void {
    $response = httpRequest('GET', '/paris');

    expectSame(200, $response['status'], 'La page /paris doit repondre en 200.');
    expectContains('Classement des paris', $response['body'], 'Le classement global des paris doit etre visible.');
});

test('HTTP: formulaires auth accessibles', function (): void {
    $login = httpRequest('GET', '/auth/login');
    $register = httpRequest('GET', '/auth/register');

    expectSame(200, $login['status'], 'La page /auth/login doit etre accessible.');
    expectSame(200, $register['status'], 'La page /auth/register doit etre accessible.');
    expectContains('Connexion', $login['body'], 'Le formulaire de connexion est introuvable.');
    expectContains('Creer un compte', $register['body'], 'Le formulaire d inscription est introuvable.');
});

test('HTTP: une action POST metier sans connexion redirige vers la connexion', function (): void {
    $response = httpRequest('POST', '/calendrier/placeBet', [
        'course_id' => '1',
    ]);

    expectSame(200, $response['status'], 'La redirection vers la page de connexion doit aboutir.');
    expectContains('Connexion', $response['body'], 'La page de connexion doit etre affichee apres redirection.');
});
