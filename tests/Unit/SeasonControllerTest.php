<?php
declare(strict_types=1);

use App\Controllers\SeasonController;

test('SeasonController: bareme F1 de la position 1 a 10', function (): void {
    $controller = new SeasonController();
    $method = new ReflectionMethod(SeasonController::class, 'pointsForPosition');
    $method->setAccessible(true);

    $expected = [
        1 => 25,
        2 => 18,
        3 => 15,
        4 => 12,
        5 => 10,
        6 => 8,
        7 => 6,
        8 => 4,
        9 => 2,
        10 => 1,
    ];

    foreach ($expected as $position => $points) {
        $actual = $method->invoke($controller, $position);
        expectSame($points, $actual, 'Bareme F1 incorrect pour la position ' . $position . '.');
    }
});

test('SeasonController: au dela du top 10 le score vaut 0', function (): void {
    $controller = new SeasonController();
    $method = new ReflectionMethod(SeasonController::class, 'pointsForPosition');
    $method->setAccessible(true);

    expectSame(0, $method->invoke($controller, 11), 'La position 11 doit valoir 0 point.');
    expectSame(0, $method->invoke($controller, 22), 'La position 22 doit valoir 0 point.');
});
