<?php
declare(strict_types=1);

use App\Security\Csrf;

test('CSRF: generation + validation du token', function (): void {
    $_SESSION = [];

    $token = Csrf::getToken();

    expectTrue($token !== '', 'Le token CSRF ne doit pas etre vide.');
    expectTrue(strlen($token) >= 32, 'Le token CSRF doit etre assez long.');
    expectTrue(Csrf::isValid($token), 'Le token genere doit etre valide.');
});

test('CSRF: token stable sur la meme session', function (): void {
    $_SESSION = [];

    $first = Csrf::getToken();
    $second = Csrf::getToken();

    expectSame($first, $second, 'Le token CSRF doit rester le meme dans la session courante.');
});

test('CSRF: rejet d un token invalide', function (): void {
    $_SESSION = [];
    Csrf::getToken();

    expectTrue(!Csrf::isValid(null), 'Un token null doit etre refuse.');
    expectTrue(!Csrf::isValid('token-invalide'), 'Un token incorrect doit etre refuse.');
});
