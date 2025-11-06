<?php
declare(strict_types=1);

require __DIR__ . '/Autoload.php';
use App\Database\Database;

/**
 * Script utilitaire pour repartir d'une base propre.
 */
Database::reset();
Database::migrate();

$pdo = Database::getInstance();

$grandsPrix = [
    ['slug' => 'bahrein', 'nom' => 'Grand Prix de Bahreïn', 'pays' => 'Bahreïn', 'blason' => 'Public/assets/grands-prix/bahrein.svg'],
    ['slug' => 'monaco', 'nom' => 'Grand Prix de Monaco', 'pays' => 'Monaco', 'blason' => 'Public/assets/grands-prix/monaco.svg'],
    ['slug' => 'silverstone', 'nom' => 'Grand Prix de Grande-Bretagne', 'pays' => 'Royaume-Uni', 'blason' => 'Public/assets/grands-prix/uk.svg'],
];

$equipesSeed = [
    ['slug' => 'mercedes', 'nom' => 'Mercedes-AMG Petronas', 'ville' => 'Brackley', 'champ_slug' => 'silverstone', 'blason' => 'Public/assets/logos/mercedes.svg'],
    ['slug' => 'redbull', 'nom' => 'Oracle Red Bull Racing', 'ville' => 'Milton Keynes', 'champ_slug' => 'bahrein', 'blason' => 'Public/assets/logos/redbull.svg'],
    ['slug' => 'ferrari', 'nom' => 'Scuderia Ferrari', 'ville' => 'Maranello', 'champ_slug' => 'monaco', 'blason' => 'Public/assets/logos/ferrari.svg'],
];

$pilotesSeed = [
    ['nom' => 'Hamilton', 'prenom' => 'Lewis', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'mercedes', 'photo' => 'Public/assets/pilotes/hamilton.svg'],
    ['nom' => 'Russell', 'prenom' => 'George', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'mercedes', 'photo' => 'Public/assets/pilotes/russell.svg'],
    ['nom' => 'Verstappen', 'prenom' => 'Max', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'redbull', 'photo' => 'Public/assets/pilotes/verstappen.svg'],
    ['nom' => 'Perez', 'prenom' => 'Sergio', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'redbull', 'photo' => 'Public/assets/pilotes/perez.svg'],
    ['nom' => 'Leclerc', 'prenom' => 'Charles', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'ferrari', 'photo' => 'Public/assets/pilotes/leclerc.svg'],
    ['nom' => 'Sainz', 'prenom' => 'Carlos', 'poste' => 'Pilote titulaire', 'equipe_slug' => 'ferrari', 'photo' => 'Public/assets/pilotes/sainz.svg'],
];

$gpMap = [];
$stmtGp = $pdo->prepare('INSERT INTO championnats (nom, pays, blason) VALUES (?, ?, ?)');
foreach ($grandsPrix as $gp) {
    $stmtGp->execute([$gp['nom'], $gp['pays'], $gp['blason']]);
    $gpMap[$gp['slug']] = (int)$pdo->lastInsertId();
}

$teamMap = [];
$stmtEq = $pdo->prepare('INSERT INTO equipes (nom, ville, id_championnat, blason) VALUES (?, ?, ?, ?)');
foreach ($equipesSeed as $equipe) {
    $champId = $gpMap[$equipe['champ_slug']] ?? null;
    if ($champId === null) {
        continue;
    }
    $stmtEq->execute([$equipe['nom'], $equipe['ville'], $champId, $equipe['blason']]);
    $teamMap[$equipe['slug']] = (int)$pdo->lastInsertId();
}

$stmtPilote = $pdo->prepare('INSERT INTO joueurs (nom, prenom, poste, id_equipe, photo) VALUES (?, ?, ?, ?, ?)');
foreach ($pilotesSeed as $pilote) {
    $teamId = $teamMap[$pilote['equipe_slug']] ?? null;
    if ($teamId === null) {
        continue;
    }
    $stmtPilote->execute([$pilote['nom'], $pilote['prenom'], $pilote['poste'], $teamId, $pilote['photo']]);
}

echo "Base réinitialisée.\n";

?>
