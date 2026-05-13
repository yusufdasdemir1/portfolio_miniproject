<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

try {
    $pdo    = getDB();
    $skills = $pdo->query("SELECT * FROM skills ORDER BY category, display_order")->fetchAll();
    $tools  = $pdo->query("SELECT * FROM tools  ORDER BY display_order")->fetchAll();
    echo json_encode(['success' => true, 'skills' => $skills, 'tools' => $tools]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false]);
}
