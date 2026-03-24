<?php
declare(strict_types=1);

use App\Controllers\ValidationController;

test('Validation: nom valide accepte', function (): void {
    expectTrue(ValidationController::nom('Alexandre Albon'), 'Un nom standard doit etre accepte.');
    expectTrue(ValidationController::nom('Congo'), 'Un mot non insultant ne doit pas etre bloque.');
});

test('Validation: mots insultants refuses', function (): void {
    expectTrue(!ValidationController::nom('Jean Connard'), 'Les insultes doivent etre refusees dans les noms.');
    expectTrue(!ValidationController::poste('Pilote enculé'), 'Les insultes avec accent doivent etre refusees.');
});

test('Validation: detection explicite de profanity', function (): void {
    expectTrue(ValidationController::hasProfanity('Quel connard'), 'La detection de profanity doit fonctionner.');
    expectTrue(!ValidationController::hasProfanity('Pilote titulaire'), 'Un texte normal ne doit pas etre bloque.');
});
