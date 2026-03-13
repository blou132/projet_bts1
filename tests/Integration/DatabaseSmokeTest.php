<?php
declare(strict_types=1);

use App\Database\Database;

test('Database: connexion MySQL et tables principales disponibles', function (): void {
    $pdo = Database::getInstance();

    expectTrue($pdo instanceof PDO, 'La connexion PDO doit etre disponible.');

    $tables = ['championnats', 'equipes', 'joueurs', 'courses', 'course_results', 'users', 'bets'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
        $stmt->execute([$table]);
        $exists = (int)$stmt->fetchColumn();
        expectSame(1, $exists, 'La table ' . $table . ' est introuvable.');
    }
});

test('Database: jeu de donnees de demonstration charge', function (): void {
    $courses = (int)Database::query('SELECT COUNT(*) FROM courses')->fetchColumn();
    $teams = (int)Database::query('SELECT COUNT(*) FROM equipes')->fetchColumn();
    $drivers = (int)Database::query('SELECT COUNT(*) FROM joueurs')->fetchColumn();
    $users = (int)Database::query('SELECT COUNT(*) FROM users')->fetchColumn();

    expectTrue($courses >= 20, 'Le calendrier doit contenir au moins 20 courses.');
    expectTrue($teams >= 5, 'La base doit contenir plusieurs ecuries.');
    expectTrue($drivers >= 10, 'La base doit contenir plusieurs pilotes.');
    expectTrue($users >= 2, 'Les comptes de demonstration doivent etre presents.');
});
