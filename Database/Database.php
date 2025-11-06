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
    }

    /** Danger ! Supprime tout. */
    public static function reset(): void
    {
        $pdo = self::getInstance();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('DROP TABLE IF EXISTS joueurs');
        $pdo->exec('DROP TABLE IF EXISTS equipes');
        $pdo->exec('DROP TABLE IF EXISTS championnats');
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
