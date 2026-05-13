<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

function active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

function unread_count(\PDO $pdo): int {
    return (int) $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
}

function flash(string $key, string $value = null): ?string {
    if ($value !== null) { $_SESSION['flash'][$key] = $value; return null; }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}
