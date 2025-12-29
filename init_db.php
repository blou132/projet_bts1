<?php
declare(strict_types=1);

require __DIR__ . '/Autoload.php';

use App\Database\Database;

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
    ['nom' => 'Mercedes-AMG Petronas', 'pays' => 'Royaume-Uni', 'championnat' => 3, 'blason' => null],
    ['nom' => 'Oracle Red Bull Racing','pays' => 'Royaume-Uni', 'championnat' => 1, 'blason' => null],
    ['nom' => 'Scuderia Ferrari',      'pays' => 'Italie',      'championnat' => 2, 'blason' => null],
];

$courses = [
    ['ordre' => 1,  'code' => 'AUS', 'nom' => 'Grand Prix d\'Australie',            'pays' => 'Australie',          'ville' => 'Melbourne',    'date' => '2026-03-08', 'flag' => '🇦🇺'],
    ['ordre' => 2,  'code' => 'CHN', 'nom' => 'Grand Prix de Chine',                'pays' => 'Chine',              'ville' => 'Shanghai',     'date' => '2026-03-22', 'flag' => '🇨🇳'],
    ['ordre' => 3,  'code' => 'JPN', 'nom' => 'Grand Prix du Japon',                'pays' => 'Japon',              'ville' => 'Suzuka',       'date' => '2026-04-05', 'flag' => '🇯🇵'],
    ['ordre' => 4,  'code' => 'BHR', 'nom' => 'Grand Prix de Bahreïn',              'pays' => 'Bahreïn',            'ville' => 'Sakhir',       'date' => '2026-04-19', 'flag' => '🇧🇭'],
    ['ordre' => 5,  'code' => 'SAU', 'nom' => 'Grand Prix d\'Arabie saoudite',      'pays' => 'Arabie saoudite',    'ville' => 'Djeddah',      'date' => '2026-05-03', 'flag' => '🇸🇦'],
    ['ordre' => 6,  'code' => 'CAN', 'nom' => 'Grand Prix du Canada',               'pays' => 'Canada',             'ville' => 'Montréal',     'date' => '2026-05-17', 'flag' => '🇨🇦'],
    ['ordre' => 7,  'code' => 'MON', 'nom' => 'Grand Prix de Monaco',               'pays' => 'Monaco',             'ville' => 'Monte-Carlo',  'date' => '2026-05-31', 'flag' => '🇲🇨'],
    ['ordre' => 8,  'code' => 'ESP', 'nom' => 'Grand Prix d\'Espagne - Barcelone',  'pays' => 'Espagne',            'ville' => 'Barcelone',    'date' => '2026-06-14', 'flag' => '🇪🇸'],
    ['ordre' => 9,  'code' => 'AUT', 'nom' => 'Grand Prix d\'Autriche',             'pays' => 'Autriche',           'ville' => 'Spielberg',    'date' => '2026-06-28', 'flag' => '🇦🇹'],
    ['ordre' => 10, 'code' => 'GBR', 'nom' => 'Grand Prix de Grande-Bretagne',      'pays' => 'Royaume-Uni',        'ville' => 'Silverstone',  'date' => '2026-07-12', 'flag' => '🇬🇧'],
    ['ordre' => 11, 'code' => 'BEL', 'nom' => 'Grand Prix de Belgique',             'pays' => 'Belgique',           'ville' => 'Spa',          'date' => '2026-07-26', 'flag' => '🇧🇪'],
    ['ordre' => 12, 'code' => 'HUN', 'nom' => 'Grand Prix de Hongrie',              'pays' => 'Hongrie',            'ville' => 'Budapest',     'date' => '2026-08-09', 'flag' => '🇭🇺'],
    ['ordre' => 13, 'code' => 'NED', 'nom' => 'Grand Prix des Pays-Bas',            'pays' => 'Pays-Bas',           'ville' => 'Zandvoort',    'date' => '2026-08-23', 'flag' => '🇳🇱'],
    ['ordre' => 14, 'code' => 'ITA', 'nom' => 'Grand Prix d\'Italie',               'pays' => 'Italie',             'ville' => 'Monza',        'date' => '2026-09-06', 'flag' => '🇮🇹'],
    ['ordre' => 15, 'code' => 'MAD', 'nom' => 'Grand Prix d\'Espagne - Madrid',     'pays' => 'Espagne',            'ville' => 'Madrid',       'date' => '2026-09-20', 'flag' => '🇪🇸'],
    ['ordre' => 16, 'code' => 'AZE', 'nom' => 'Grand Prix d\'Azerbaïdjan',          'pays' => 'Azerbaïdjan',        'ville' => 'Bakou',        'date' => '2026-10-04', 'flag' => '🇦🇿'],
    ['ordre' => 17, 'code' => 'SIN', 'nom' => 'Grand Prix de Singapour',            'pays' => 'Singapour',          'ville' => 'Singapour',    'date' => '2026-10-18', 'flag' => '🇸🇬'],
    ['ordre' => 18, 'code' => 'USA', 'nom' => 'Grand Prix des États-Unis',          'pays' => 'États-Unis',         'ville' => 'Austin',       'date' => '2026-11-01', 'flag' => '🇺🇸'],
    ['ordre' => 19, 'code' => 'MEX', 'nom' => 'Grand Prix du Mexique',              'pays' => 'Mexique',            'ville' => 'Mexico City',  'date' => '2026-11-15', 'flag' => '🇲🇽'],
    ['ordre' => 20, 'code' => 'BRA', 'nom' => 'Grand Prix du Brésil',               'pays' => 'Brésil',             'ville' => 'São Paulo',    'date' => '2026-11-22', 'flag' => '🇧🇷'],
    ['ordre' => 21, 'code' => 'LVS', 'nom' => 'Grand Prix de Las Vegas',            'pays' => 'États-Unis',         'ville' => 'Las Vegas',    'date' => '2026-12-06', 'flag' => '🇺🇸'],
    ['ordre' => 22, 'code' => 'QAT', 'nom' => 'Grand Prix du Qatar',                'pays' => 'Qatar',              'ville' => 'Lusail',       'date' => '2026-12-13', 'flag' => '🇶🇦'],
    ['ordre' => 23, 'code' => 'ABU', 'nom' => 'Grand Prix d\'Abou Dabi',            'pays' => 'Émirats arabes unis','ville' => 'Yas Marina',   'date' => '2026-12-20', 'flag' => '🇦🇪'],
];

$pilotes = [
    ['nom' => 'Hamilton',   'prenom' => 'Lewis',   'poste' => 'Pilote titulaire', 'equipe' => 1, 'photo' => null],
    ['nom' => 'Russell',    'prenom' => 'George',  'poste' => 'Pilote titulaire', 'equipe' => 1, 'photo' => null],
    ['nom' => 'Verstappen', 'prenom' => 'Max',     'poste' => 'Pilote titulaire', 'equipe' => 2, 'photo' => null],
    ['nom' => 'Perez',      'prenom' => 'Sergio',  'poste' => 'Pilote titulaire', 'equipe' => 2, 'photo' => null],
    ['nom' => 'Leclerc',    'prenom' => 'Charles', 'poste' => 'Pilote titulaire', 'equipe' => 3, 'photo' => null],
    ['nom' => 'Sainz',      'prenom' => 'Carlos',  'poste' => 'Pilote titulaire', 'equipe' => 3, 'photo' => null],
];

$courseResults = [
    // Bahreïn
    ['course_code' => 'BHR', 'joueur' => 3, 'position' => 1, 'points' => 25],
    ['course_code' => 'BHR', 'joueur' => 1, 'position' => 2, 'points' => 18],
    ['course_code' => 'BHR', 'joueur' => 5, 'position' => 3, 'points' => 15],
    ['course_code' => 'BHR', 'joueur' => 2, 'position' => 4, 'points' => 12],
    ['course_code' => 'BHR', 'joueur' => 4, 'position' => 5, 'points' => 10],
    ['course_code' => 'BHR', 'joueur' => 6, 'position' => 6, 'points' => 8],
    // Arabie saoudite
    ['course_code' => 'SAU', 'joueur' => 3, 'position' => 1, 'points' => 25],
    ['course_code' => 'SAU', 'joueur' => 4, 'position' => 2, 'points' => 18],
    ['course_code' => 'SAU', 'joueur' => 5, 'position' => 3, 'points' => 15],
    ['course_code' => 'SAU', 'joueur' => 6, 'position' => 4, 'points' => 12],
    ['course_code' => 'SAU', 'joueur' => 1, 'position' => 5, 'points' => 10],
    ['course_code' => 'SAU', 'joueur' => 2, 'position' => 6, 'points' => 8],
    // Australie
    ['course_code' => 'AUS', 'joueur' => 5, 'position' => 1, 'points' => 25],
    ['course_code' => 'AUS', 'joueur' => 3, 'position' => 2, 'points' => 18],
    ['course_code' => 'AUS', 'joueur' => 1, 'position' => 3, 'points' => 15],
    ['course_code' => 'AUS', 'joueur' => 6, 'position' => 4, 'points' => 12],
    ['course_code' => 'AUS', 'joueur' => 4, 'position' => 5, 'points' => 10],
    ['course_code' => 'AUS', 'joueur' => 2, 'position' => 6, 'points' => 8],
    // Italie
    ['course_code' => 'ITA', 'joueur' => 5, 'position' => 1, 'points' => 25],
    ['course_code' => 'ITA', 'joueur' => 6, 'position' => 2, 'points' => 18],
    ['course_code' => 'ITA', 'joueur' => 3, 'position' => 3, 'points' => 15],
    ['course_code' => 'ITA', 'joueur' => 1, 'position' => 4, 'points' => 12],
    ['course_code' => 'ITA', 'joueur' => 2, 'position' => 5, 'points' => 10],
    ['course_code' => 'ITA', 'joueur' => 4, 'position' => 6, 'points' => 8],
];

$users = [
    [
        'name' => 'Administrateur',
        'email' => 'admin@example.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
    ],
];

try {
    $pdo->beginTransaction();

    $stmtGp = $pdo->prepare('INSERT INTO championnats (nom, pays, blason) VALUES (?, ?, ?)');
    foreach ($grandsPrix as $gp) {
        $stmtGp->execute([$gp['nom'], $gp['pays'], $gp['blason']]);
    }

    $stmtEquipe = $pdo->prepare('INSERT INTO equipes (nom, ville, id_championnat, blason) VALUES (?, ?, ?, ?)');
    foreach ($equipes as $equipe) {
        $stmtEquipe->execute([$equipe['nom'], $equipe['pays'], $equipe['championnat'], $equipe['blason']]);
    }

    $stmtCourse = $pdo->prepare('INSERT INTO courses (ordre, code, nom, pays, ville, date_course, flag) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $courseIds = [];
    foreach ($courses as $course) {
        $stmtCourse->execute([$course['ordre'], $course['code'], $course['nom'], $course['pays'], $course['ville'], $course['date'], $course['flag']]);
        $courseIds[$course['code']] = (int)$pdo->lastInsertId();
    }

    $stmtPilote = $pdo->prepare('INSERT INTO joueurs (nom, prenom, poste, id_equipe, photo) VALUES (?, ?, ?, ?, ?)');
    foreach ($pilotes as $pilote) {
        $stmtPilote->execute([$pilote['nom'], $pilote['prenom'], $pilote['poste'], $pilote['equipe'], $pilote['photo']]);
    }

    $stmtResult = $pdo->prepare('INSERT INTO course_results (course_id, joueur_id, position, points) VALUES (?, ?, ?, ?)');
    foreach ($courseResults as $result) {
        $code = $result['course_code'];
        if (!isset($courseIds[$code])) {
            continue;
        }
        $stmtResult->execute([$courseIds[$code], $result['joueur'], $result['position'], $result['points']]);
    }

    $stmtUser = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    foreach ($users as $user) {
        $stmtUser->execute([$user['name'], $user['email'], $user['password']]);
    }

    $pdo->commit();
    echo "Base MySQL réinitialisée avec jeu de données de démonstration.\n";
} catch (\PDOException $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Erreur lors de la réinitialisation : ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
