<?php
header('Content-Type: application/json');

$dbPass = getenv('DB_PASSWORD') ?: 'mdp';
$dbHost = getenv('DB_HOST') ?: 'db';
$dbName = getenv('DB_NAME') ?: 'production_db';
$dbUser = getenv('DB_USER') ?: 'some_user';

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);

    $stmt = $pdo->query("SELECT NOW() as db_time, VERSION() as version");
    $data = $stmt->fetch();

    echo json_encode([
        'status' => 'success',
        'message' => 'Connexion BDD réussie via secret tmpfs',
        'database_time' => $data['db_time'],
        'database_version' => $data['version']
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}