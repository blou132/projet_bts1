<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Gestionnaire de connexion SQLite (pattern singleton) et migrations.
 */
class Database
{
    private static ?PDO $pdo = null;

    /** Retourne l'instance PDO (singleton) */
    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            // Chemin vers .../Database/database.sqlite (depuis App/Database)
            $dbPath = __DIR__ . '/database.sqlite';
            $dsn = 'sqlite:' . $dbPath;
            $dir = dirname($dbPath);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new PDOException('Impossible de créer le dossier de base de données : ' . $dir);
            }
            try {
                self::$pdo = new PDO($dsn);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->exec('PRAGMA foreign_keys = ON');
            } catch (PDOException $e) {
                die('Connexion SQLite impossible: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /** Crée les tables si absentes (statique pour pouvoir faire Database::migrate()) */
    public static function migrate(): void
    {
        $pdo = self::getInstance();

        $pdo->exec('CREATE TABLE IF NOT EXISTS championnats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            pays TEXT NOT NULL,
            blason TEXT NULL
        )');

        $pdo->exec('CREATE TABLE IF NOT EXISTS equipes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            ville TEXT NOT NULL,
            id_championnat INTEGER NOT NULL,
            blason TEXT NULL,
            FOREIGN KEY(id_championnat) REFERENCES championnats(id) ON DELETE CASCADE
        )');

        $pdo->exec('CREATE TABLE IF NOT EXISTS joueurs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            prenom TEXT NOT NULL,
            poste TEXT NOT NULL,
            id_equipe INTEGER NOT NULL,
            photo TEXT NULL,
            FOREIGN KEY(id_equipe) REFERENCES equipes(id) ON DELETE CASCADE
        )');
    }

    /** Danger ! Supprime tout */
    public static function reset(): void
    {
        $pdo = self::getInstance();
        $pdo->exec('DROP TABLE IF EXISTS joueurs');
        $pdo->exec('DROP TABLE IF EXISTS equipes');
        $pdo->exec('DROP TABLE IF EXISTS championnats');
    }

    /** Helper requête préparée */
    public static function query(string $sql, array $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}


?>
