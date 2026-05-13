<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

require_once __DIR__ . '/config.php';

// Sanitize & validate inputs
$name    = trim(filter_input(INPUT_POST, 'name',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$email   = trim(filter_input(INPUT_POST, 'email',   FILTER_SANITIZE_EMAIL)         ?? '');
$subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

if (!$name || !$email || !$subject || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if (strlen($name) > 100 || strlen($subject) > 200 || strlen($message) > 5000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Input exceeds allowed length.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)'
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
    ]);

    echo json_encode(['success' => true, 'message' => 'Message received! I\'ll get back to you soon.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
