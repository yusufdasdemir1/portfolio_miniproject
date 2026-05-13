<?php
require_once 'auth.php';
require_once '../php/config.php';
$pdo     = getDB();
$unread  = unread_count($pdo);
$success = flash('success');

$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'contacts' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
    'unread'   => $unread,
    'skills'   => $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
];

$recent = $pdo->query("SELECT name, email, subject, created_at FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
    <?php include '_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
            <div class="header-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_user']) ?></div>
        </header>
        <div class="admin-content">
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

            <div class="stat-grid">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-code"></i></div>
                    <div class="stat-info"><span class="stat-num"><?= $stats['projects'] ?></span><span class="stat-lbl">Projects</span></div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                    <div class="stat-info"><span class="stat-num"><?= $stats['contacts'] ?></span><span class="stat-lbl">Messages</span></div>
                </div>
                <div class="stat-box <?= $unread ? 'stat-alert' : '' ?>">
                    <div class="stat-icon"><i class="fas fa-bell"></i></div>
                    <div class="stat-info"><span class="stat-num"><?= $stats['unread'] ?></span><span class="stat-lbl">Unread</span></div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-tools"></i></div>
                    <div class="stat-info"><span class="stat-num"><?= $stats['skills'] ?></span><span class="stat-lbl">Skills</span></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Recent Messages</h2><a href="contacts.php" class="btn-sm">View All</a></div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td><?= htmlspecialchars($r['subject']) ?></td>
                        <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?><tr><td colspan="4" class="empty">No messages yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="quick-links">
                <a href="projects.php" class="ql-card"><i class="fas fa-plus"></i><span>Add Project</span></a>
                <a href="about.php"    class="ql-card"><i class="fas fa-edit"></i><span>Edit About</span></a>
                <a href="skills.php"   class="ql-card"><i class="fas fa-plus"></i><span>Add Skill</span></a>
                <a href="contacts.php" class="ql-card"><i class="fas fa-envelope"></i><span>Read Messages</span></a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
