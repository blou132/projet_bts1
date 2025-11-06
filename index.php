<?php
declare(strict_types=1);

// Point d'entrée unique
require __DIR__ . '/Autoload.php';

use App\Database\Database;
use App\Controllers\ChampionnatController;
use App\Controllers\EquipeController;
use App\Controllers\JoueurController;
use App\Controllers\SeasonController;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Debug (optionnel en dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Crée les tables si absentes
Database::migrate();

// Routage très simple
$route  = $_GET['route']  ?? 'accueil';
$action = $_GET['action'] ?? 'index';

switch ($route) {
    case 'championnats':
        $controller = new ChampionnatController();
        break;

    case 'equipes':
        $controller = new EquipeController();
        break;

    case 'joueurs':
        $controller = new JoueurController();
        break;

    case 'jointure': // joueurs + équipes
        $controller = new JoueurController();
        $action = 'withEquipes';
        break;

    case 'calendrier':
        $controller = new SeasonController();
        $action = 'calendar';
        break;

    case 'classements':
        $controller = new SeasonController();
        $action = 'standings';
        break;

    default:
        // Vue d'accueil
        $pdo = Database::getInstance();
        $stats = [
            'grands_prix' => (int)$pdo->query('SELECT COUNT(*) FROM championnats')->fetchColumn(),
            'ecuries' => (int)$pdo->query('SELECT COUNT(*) FROM equipes')->fetchColumn(),
            'pilotes' => (int)$pdo->query('SELECT COUNT(*) FROM joueurs')->fetchColumn(),
        ];
        $topTeams = $pdo->query('SELECT e.nom, e.blason, COUNT(j.id) AS pilotes
                                  FROM equipes e
                                  LEFT JOIN joueurs j ON j.id_equipe = e.id
                                  GROUP BY e.id
                                  ORDER BY pilotes DESC, e.nom
                                  LIMIT 3')->fetchAll();
        $pilotesSpotlight = $pdo->query('SELECT j.nom, j.prenom, j.photo, e.nom AS equipe
                                          FROM joueurs j
                                          JOIN equipes e ON e.id = j.id_equipe
                                          ORDER BY j.nom
                                          LIMIT 6')->fetchAll();
        $grandsPrix = $pdo->query('SELECT nom, pays, blason FROM championnats ORDER BY nom LIMIT 3')->fetchAll();
        require __DIR__ . '/Views/layout/header.lame.php';
        require __DIR__ . '/Views/accueil.lame.php';
        require __DIR__ . '/Views/layout/footer.lame.php';
        exit;
}

// Sécurité: vérifie que l'action existe
if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action introuvable";
    exit;
}

// Exécute l'action
$controller->$action();
