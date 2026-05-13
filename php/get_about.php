<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

try {
    $pdo   = getDB();
    $about = $pdo->query("SELECT * FROM about_content LIMIT 1")->fetch();
    if ($about) {
        echo json_encode(['success' => true, 'about' => $about]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false]);
}
