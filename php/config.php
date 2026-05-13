<?php
define('DB_HOST', getenv('DB_HOST'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_CHAR', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHAR);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    return $pdo;
}
