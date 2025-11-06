<?php
declare(strict_types=1);

require __DIR__ . '/Autoload.php';

use App\Database\Database;
use PDOException;

// Réinitialise complètement le schéma (DROP + CREATE) puis charge un jeu de données d'exemple.
Database::reset();
Database::migrate();

$pdo = Database::getInstance();

$grandsPrix = [
    ['nom' => 'Grand Prix de Bahreïn',        'pays' => 'Bahreïn',      'blason' => null],
    ['nom' => 'Grand Prix de Monaco',         'pays' => 'Monaco',       'blason' => null],
    ['nom' => 'Grand Prix de Grande-Bretagne','pays' => 'Royaume-Uni',  'blason' => null],
];

$equipes = [
    ['nom' => 'Mercedes-AMG Petronas', 'ville' => 'Brackley',      'championnat' => 3, 'blason' => null],
    ['nom' => 'Oracle Red Bull Racing','ville' => 'Milton Keynes', 'championnat' => 1, 'blason' => null],
    ['nom' => 'Scuderia Ferrari',      'ville' => 'Maranello',     'championnat' => 2, 'blason' => null],
];

$pilotes = [
    ['nom' => 'Hamilton',   'prenom' => 'Lewis',   'poste' => 'Pilote titulaire', 'equipe' => 1, 'photo' => null],
    ['nom' => 'Russell',    'prenom' => 'George',  'poste' => 'Pilote titulaire', 'equipe' => 1, 'photo' => null],
    ['nom' => 'Verstappen', 'prenom' => 'Max',     'poste' => 'Pilote titulaire', 'equipe' => 2, 'photo' => null],
    ['nom' => 'Perez',      'prenom' => 'Sergio',  'poste' => 'Pilote titulaire', 'equipe' => 2, 'photo' => null],
    ['nom' => 'Leclerc',    'prenom' => 'Charles', 'poste' => 'Pilote titulaire', 'equipe' => 3, 'photo' => null],
    ['nom' => 'Sainz',      'prenom' => 'Carlos',  'poste' => 'Pilote titulaire', 'equipe' => 3, 'photo' => null],
];

try {
    $pdo->beginTransaction();

    $stmtGp = $pdo->prepare('INSERT INTO championnats (nom, pays, blason) VALUES (?, ?, ?)');
    foreach ($grandsPrix as $gp) {
        $stmtGp->execute([$gp['nom'], $gp['pays'], $gp['blason']]);
    }

    $stmtEquipe = $pdo->prepare('INSERT INTO equipes (nom, ville, id_championnat, blason) VALUES (?, ?, ?, ?)');
    foreach ($equipes as $equipe) {
        $stmtEquipe->execute([$equipe['nom'], $equipe['ville'], $equipe['championnat'], $equipe['blason']]);
    }

    $stmtPilote = $pdo->prepare('INSERT INTO joueurs (nom, prenom, poste, id_equipe, photo) VALUES (?, ?, ?, ?, ?)');
    foreach ($pilotes as $pilote) {
        $stmtPilote->execute([$pilote['nom'], $pilote['prenom'], $pilote['poste'], $pilote['equipe'], $pilote['photo']]);
    }

    $pdo->commit();
    echo "Base MySQL réinitialisée avec jeu de données de démonstration.\n";
} catch (PDOException $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Erreur lors de la réinitialisation : ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
