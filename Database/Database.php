<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Gestionnaire de connexion MySQL (singleton) et migrations.
 */
class Database
{
    private static ?PDO $pdo = null;
    private static bool $envLoaded = false;

    /**
     * Retourne la configuration en s'appuyant sur les variables d'environnement,
     * avec des valeurs par défaut adaptées au développement local.
     */
    private static function getConfig(): array
    {
        self::bootEnv();

        return [
            'host' => self::env('DB_HOST', '127.0.0.1'),
            'port' => self::env('DB_PORT', '3306'),
            'name' => self::env('DB_NAME', 'tpformula1'),
            'user' => self::env('DB_USER', 'root'),
            'pass' => self::env('DB_PASS', ''),
            'charset' => self::env('DB_CHARSET', 'utf8mb4'),
        ];
    }

    /**
     * Retourne la valeur d'une variable d'environnement avec fallback.
     */
    private static function env(string $key, string $default = ''): string
    {
        if (array_key_exists($key, $_ENV)) {
            return (string)$_ENV[$key];
        }
        $value = getenv($key);
        return $value === false ? $default : (string)$value;
    }

    /**
     * Charge un éventuel fichier .env (format INI simple).
     */
    private static function bootEnv(): void
    {
        if (self::$envLoaded) {
            return;
        }
        self::$envLoaded = true;

        $envPath = dirname(__DIR__) . '/.env';
        if (!is_file($envPath)) {
            return;
        }

        $data = parse_ini_file($envPath, false, INI_SCANNER_TYPED);
        if ($data === false) {
            return;
        }

        foreach ($data as $key => $value) {
            $upperKey = strtoupper($key);
            if (!array_key_exists($upperKey, $_ENV)) {
                $_ENV[$upperKey] = $value;
            }
            if (getenv($upperKey) === false) {
                putenv($upperKey . '=' . $value);
            }
        }
    }

    /** Retourne l'instance PDO (singleton). */
    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            $config = self::getConfig();
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['name'],
                $config['charset']
            );

            try {
                self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die('Connexion MySQL impossible: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    /** Crée les tables si absentes (statique pour pouvoir faire Database::migrate()). */
    public static function migrate(): void
    {
        $pdo = self::getInstance();

        $pdo->exec('CREATE TABLE IF NOT EXISTS championnats (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(120) NOT NULL,
            pays VARCHAR(120) NOT NULL,
            blason VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS equipes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(120) NOT NULL,
            ville VARCHAR(120) NOT NULL,
            id_championnat INT UNSIGNED NOT NULL,
            blason VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_equipes_championnat FOREIGN KEY (id_championnat)
                REFERENCES championnats(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS joueurs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(120) NOT NULL,
            prenom VARCHAR(120) NOT NULL,
            poste VARCHAR(60) NOT NULL,
            id_equipe INT UNSIGNED NOT NULL,
            photo VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_joueurs_equipe FOREIGN KEY (id_equipe)
                REFERENCES equipes(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS courses (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ordre INT UNSIGNED NOT NULL,
            code VARCHAR(10) NOT NULL,
            nom VARCHAR(150) NOT NULL,
            pays VARCHAR(120) NOT NULL,
            ville VARCHAR(120) NOT NULL,
            date_course DATE NOT NULL,
            flag VARCHAR(8) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY courses_code_unique (code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS course_results (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            course_id INT UNSIGNED NOT NULL,
            joueur_id INT UNSIGNED NOT NULL,
            position TINYINT UNSIGNED NOT NULL,
            points INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_course_results_course FOREIGN KEY (course_id)
                REFERENCES courses(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_course_results_joueur FOREIGN KEY (joueur_id)
                REFERENCES joueurs(id)
                ON DELETE CASCADE,
            UNIQUE KEY course_results_unique (course_id, joueur_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $hasRole = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();
        if (!$hasRole) {
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
        }
        $pdo->exec("UPDATE users SET role = 'admin' WHERE email = 'admin@example.com' AND role = 'user'");

        $pdo->exec('CREATE TABLE IF NOT EXISTS bets (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            course_id INT UNSIGNED NOT NULL,
            first_joueur_id INT UNSIGNED NOT NULL,
            second_joueur_id INT UNSIGNED NOT NULL,
            third_joueur_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY bets_unique (user_id, course_id),
            CONSTRAINT fk_bets_user FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_bets_course FOREIGN KEY (course_id)
                REFERENCES courses(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_bets_first FOREIGN KEY (first_joueur_id)
                REFERENCES joueurs(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_bets_second FOREIGN KEY (second_joueur_id)
                REFERENCES joueurs(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_bets_third FOREIGN KEY (third_joueur_id)
                REFERENCES joueurs(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    /** Danger ! Supprime tout. */
    public static function reset(): void
    {
        $pdo = self::getInstance();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('DROP TABLE IF EXISTS bets');
        $pdo->exec('DROP TABLE IF EXISTS course_results');
        $pdo->exec('DROP TABLE IF EXISTS courses');
        $pdo->exec('DROP TABLE IF EXISTS joueurs');
        $pdo->exec('DROP TABLE IF EXISTS equipes');
        $pdo->exec('DROP TABLE IF EXISTS championnats');
        $pdo->exec('DROP TABLE IF EXISTS users');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /** Helper requête préparée. */
    public static function query(string $sql, array $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

?>
