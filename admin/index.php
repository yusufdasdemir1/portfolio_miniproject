<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['admin_logged_in'])) { header('Location: dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../php/config.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user']      = $user['username'];
            $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <span class="login-logo">YD</span>
            <h1>Admin Panel</h1>
            <p>Sign in to manage your portfolio</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <div class="field">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="admin" required autofocus>
            </div>
            <div class="field">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-admin btn-primary-admin">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        <a href="../index.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Portfolio</a>
    </div>
</body>
</html>
