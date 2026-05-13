<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';

try {
    $pdo  = getDB();
    $stmt = $pdo->query(
        'SELECT id, title, description, technologies, category, github_url, live_url, image_path
           FROM projects
          ORDER BY featured DESC, created_at DESC'
    );
    $projects = $stmt->fetchAll();

    echo json_encode(['success' => true, 'projects' => $projects]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not load projects.']);
}
