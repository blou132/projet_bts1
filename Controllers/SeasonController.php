<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Présente des vues synthétiques de la saison : calendrier et classement démo.
 */
class SeasonController extends BaseController
{
    /** Affiche le calendrier F1 2026 (données statiques de démonstration). */
    public function calendar(): void
    {
        $calendar = [
            ['round' => 1,  'country' => 'Australie',        'city' => 'Melbourne',   'dates' => '06-08 mars',  'flag' => '🇦🇺'],
            ['round' => 2,  'country' => 'Chine',            'city' => 'Shanghai',    'dates' => '20-22 mars',  'flag' => '🇨🇳'],
            ['round' => 3,  'country' => 'Japon',            'city' => 'Suzuka',      'dates' => '03-05 avril', 'flag' => '🇯🇵'],
            ['round' => 4,  'country' => 'Bahreïn',          'city' => 'Sakhir',      'dates' => '17-19 avril', 'flag' => '🇧🇭'],
            ['round' => 5,  'country' => 'Arabie saoudite',  'city' => 'Djeddah',     'dates' => '01-03 mai',   'flag' => '🇸🇦'],
            ['round' => 6,  'country' => 'Canada',           'city' => 'Montréal',    'dates' => '15-17 mai',   'flag' => '🇨🇦'],
            ['round' => 7,  'country' => 'Monaco',           'city' => 'Monte-Carlo', 'dates' => '29-31 mai',   'flag' => '🇲🇨'],
            ['round' => 8,  'country' => 'Espagne',          'city' => 'Barcelone',   'dates' => '12-14 juin',  'flag' => '🇪🇸'],
            ['round' => 9,  'country' => 'Autriche',         'city' => 'Spielberg',   'dates' => '26-28 juin',  'flag' => '🇦🇹'],
            ['round' => 10, 'country' => 'Grande-Bretagne',  'city' => 'Silverstone', 'dates' => '10-12 juill.', 'flag' => '🇬🇧'],
            ['round' => 11, 'country' => 'Belgique',         'city' => 'Spa',         'dates' => '24-26 juill.', 'flag' => '🇧🇪'],
            ['round' => 12, 'country' => 'Hongrie',          'city' => 'Budapest',    'dates' => '07-09 août',  'flag' => '🇭🇺'],
            ['round' => 13, 'country' => 'Pays-Bas',         'city' => 'Zandvoort',   'dates' => '21-23 août',  'flag' => '🇳🇱'],
            ['round' => 14, 'country' => 'Italie',           'city' => 'Monza',       'dates' => '04-06 sept.', 'flag' => '🇮🇹'],
            ['round' => 15, 'country' => 'Espagne',          'city' => 'Madrid',      'dates' => '18-20 sept.', 'flag' => '🇪🇸'],
            ['round' => 16, 'country' => 'Azerbaïdjan',      'city' => 'Bakou',       'dates' => '02-04 oct.',  'flag' => '🇦🇿'],
            ['round' => 17, 'country' => 'Singapour',        'city' => 'Singapour',   'dates' => '16-18 oct.',  'flag' => '🇸🇬'],
            ['round' => 18, 'country' => 'États-Unis',       'city' => 'Austin',      'dates' => '30 oct.-01 nov.', 'flag' => '🇺🇸'],
            ['round' => 19, 'country' => 'Mexique',          'city' => 'Mexico City', 'dates' => '13-15 nov.',  'flag' => '🇲🇽'],
            ['round' => 20, 'country' => 'Brésil',           'city' => 'São Paulo',   'dates' => '20-22 nov.',  'flag' => '🇧🇷'],
            ['round' => 21, 'country' => 'Las Vegas',        'city' => 'Las Vegas',   'dates' => '04-06 déc.',  'flag' => '🇺🇸'],
            ['round' => 22, 'country' => 'Qatar',            'city' => 'Lusail',      'dates' => '11-13 déc.',  'flag' => '🇶🇦'],
            ['round' => 23, 'country' => 'Abou Dabi',        'city' => 'Yas Marina',  'dates' => '18-20 déc.',  'flag' => '🇦🇪'],
        ];

        $this->render('calendar.lame.php', [
            'calendar' => $calendar,
            'year' => 2026,
        ]);
    }

    /** Affiche un tableau de points saisonniers de démonstration. */
    public function standings(): void
    {
        $grandsPrix = ['BHR', 'SAU', 'AUS', 'JPN', 'CHN', 'MIA', 'CAN', 'ESP', 'AUT', 'GBR', 'HUN', 'BEL', 'NED', 'ITA', 'AZE', 'SIN', 'USA', 'MEX', 'BRA', 'QAT', 'ABU'];

        $drivers = [
            ['code' => 'VER', 'team' => 'Red Bull',  'points' => [26, 18, 26, 18, 26, 25, 26, 15, 26, 18, 10, 26, 25, 26, 12, 26, 31, 18, 26, 26, 25]],
            ['code' => 'NOR', 'team' => 'McLaren',   'points' => [18, 26, 18, 25, 18, 18, 16, 26, 18, 30, 26, 15, 12, 25, 18, 18, 26, 25, 26, 18, 18]],
            ['code' => 'LEC', 'team' => 'Ferrari',   'points' => [12, 19, 12, 19, 22, 12, 25, 18, 12, 18, 18, 25, 18, 18, 25, 18, 12, 30, 16, 12, 18]],
            ['code' => 'PIA', 'team' => 'McLaren',   'points' => [10, 12, 18, 10, 14, 18, 18, 12, 30, 10, 15, 18, 12, 18, 18, 25, 12, 25, 18, 12, 12]],
            ['code' => 'SAI', 'team' => 'Ferrari',   'points' => [8, 25, 8, 15, 12, 15, 12, 10, 12, 15, 12, 18, 25, 12, 12, 18, 25, 12, 18, 25, 12]],
            ['code' => 'RUS', 'team' => 'Mercedes',  'points' => [6, 8, 6, 9, 7, 10, 6, 9, 6, 30, 5, 12, 10, 6, 8, 6, 12, 10, 6, 12, 10]],
            ['code' => 'HAM', 'team' => 'Mercedes',  'points' => [0, 6, 2, 9, 8, 7, 6, 12, 10, 15, 15, 8, 12, 6, 6, 15, 18, 12, 15, 6, 12]],
            ['code' => 'PER', 'team' => 'Red Bull',  'points' => [18, 8, 18, 8, 12, 18, 10, 12, 8, 6, 12, 10, 10, 12, 8, 10, 12, 8, 10, 8, 8]],
            ['code' => 'ALO', 'team' => 'Aston Martin', 'points' => [4, 10, 4, 8, 6, 8, 4, 8, 6, 4, 8, 12, 10, 8, 8, 4, 8, 6, 8, 10, 6]],
            ['code' => 'GAS', 'team' => 'Alpine',    'points' => [6, 0, 0, 4, 2, 4, 6, 0, 2, 0, 6, 0, 0, 4, 0, 2, 0, 2, 0, 0, 0]],
        ];

        // Calcule les points cumulés de chaque pilote.
        foreach ($drivers as &$driver) {
            $driver['total'] = array_sum($driver['points']);
        }
        unset($driver);

        // Classement décroissant sur le total.
        usort($drivers, static function (array $a, array $b): int {
            return $b['total'] <=> $a['total'];
        });

        $this->render('standings.lame.php', [
            'grandsPrix' => $grandsPrix,
            'drivers' => $drivers,
        ]);
    }
}

?>
