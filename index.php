<?php
declare(strict_types=1);

// Point d'entrée unique
require __DIR__ . '/Autoload.php';

use App\Database\Database;
use App\Controllers\EquipeController;
use App\Controllers\JoueurController;
use App\Controllers\SeasonController;
use App\Controllers\AuthController;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Debug (optionnel en dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Crée les tables si absentes
Database::migrate();

// Routage querystring + clean URLs (/route/action)
$resolvePathRoute = static function (): array {
    $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($uri, PHP_URL_PATH);
    $path = is_string($path) && $path !== '' ? rawurldecode($path) : '/';

    $scriptDir = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
    if ($scriptDir !== '' && $scriptDir !== '.') {
        if ($path === $scriptDir) {
            $path = '/';
        } elseif (str_starts_with($path, $scriptDir . '/')) {
            $path = substr($path, strlen($scriptDir));
        }
    }

    if (str_starts_with($path, '/index.php')) {
        $path = (string)substr($path, strlen('/index.php'));
        if ($path === '') {
            $path = '/';
        }
    }

    $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn(string $chunk): bool => $chunk !== ''));

    $resolvedRoute = 'accueil';
    $resolvedAction = 'index';
    $resolvedParams = [];

    if ($segments !== []) {
        $resolvedRoute = $segments[0];
        if (isset($segments[1])) {
            if ($resolvedRoute === 'calendrier' && ctype_digit($segments[1])) {
                $resolvedAction = 'course';
                $resolvedParams['course'] = $segments[1];
            } else {
                $resolvedAction = $segments[1];
                if ($resolvedRoute === 'calendrier' && $resolvedAction === 'course' && isset($segments[2]) && ctype_digit($segments[2])) {
                    $resolvedParams['course'] = $segments[2];
                }
            }
        }
    }

    return [$resolvedRoute, $resolvedAction, $resolvedParams];
};

[$pathRoute, $pathAction, $pathParams] = $resolvePathRoute();

$route = $_GET['route'] ?? $pathRoute;
$action = $_GET['action'] ?? $pathAction;
foreach ($pathParams as $name => $value) {
    if (!isset($_GET[$name])) {
        $_GET[$name] = $value;
    }
}

if ($route === 'auth' && $action === 'index') {
    $action = 'login';
}

$isAuthenticated = isset($_SESSION['user']);
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

$publicRoutes = [
    'accueil' => ['index'],
    'docs' => ['index'],
    'documentation' => ['index'],
    'ecuries' => ['index'],
    'equipes' => ['index'],
    'pilotes' => ['index'],
    'joueurs' => ['index'],
    'jointure' => ['index', 'withEquipes'],
    'calendrier' => ['index', 'calendar', 'course'],
    'classements' => ['index', 'standings'],
    'paris' => ['index', 'bets'],
];
$authRoutes = ['login', 'authenticate', 'register', 'store'];

if (!$isAuthenticated) {
    $routeAllowed = isset($publicRoutes[$route]) && in_array($action, $publicRoutes[$route], true);
    $isAuthRoute = $route === 'auth' && in_array($action, $authRoutes, true);

    if (!$routeAllowed || $isPost) {
        if (!$isAuthRoute) {
            header('Location: /auth/login');
            exit;
        }
    }
} elseif ($route === 'auth' && in_array($action, $authRoutes, true)) {
    header('Location: /accueil');
    exit;
}

switch ($route) {
    case 'accueil':
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
        $pilotesSpotlight = $pdo->query('SELECT j.nom, j.prenom, j.photo, e.nom AS ecurie
                                          FROM joueurs j
                                          JOIN equipes e ON e.id = j.id_equipe
                                          ORDER BY j.nom
                                          LIMIT 6')->fetchAll();
        require __DIR__ . '/Views/layout/header.lame.php';
        require __DIR__ . '/Views/accueil.lame.php';
        require __DIR__ . '/Views/layout/footer.lame.php';
        exit;

    case 'docs':
    case 'documentation':
        require __DIR__ . '/Views/layout/header.lame.php';
        require __DIR__ . '/Views/docs.lame.php';
        require __DIR__ . '/Views/layout/footer.lame.php';
        exit;

    case 'ecuries':
    case 'equipes':
        $controller = new EquipeController();
        break;

    case 'pilotes':
    case 'joueurs':
        $controller = new JoueurController();
        break;

    case 'jointure': // joueurs + équipes
        $controller = new JoueurController();
        $action = 'withEquipes';
        break;

    case 'calendrier':
        $controller = new SeasonController();
        if ($action === 'index') {
            $action = 'calendar';
        }
        break;

    case 'classements':
        $controller = new SeasonController();
        $action = 'standings';
        break;

    case 'paris':
        $controller = new SeasonController();
        if ($action === 'index') {
            $action = 'bets';
        }
        break;

    case 'auth':
        $controller = new AuthController();
        break;

    default:
        http_response_code(404);
        echo 'Page non trouvée';
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
