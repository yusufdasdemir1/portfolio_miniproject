<?php
require_once 'auth.php';
require_once '../php/config.php';
$pdo    = getDB();
$unread = unread_count($pdo);
$success= flash('success');

if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE contacts SET is_read=1 WHERE id=?")->execute([(int)$_GET['read']]);
    flash('success', 'Marked as read.'); header('Location: contacts.php'); exit;
}
if (isset($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) die('Invalid token.');
    $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('success', 'Message deleted.'); header('Location: contacts.php'); exit;
}
if (isset($_GET['read_all'])) {
    $pdo->exec("UPDATE contacts SET is_read=1");
    flash('success', 'All messages marked as read.'); header('Location: contacts.php'); exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$token    = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
    <?php include '_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-envelope"></i> Messages <?php if ($unread): ?><span class="header-badge"><?= $unread ?> new</span><?php endif; ?></h1>
            <div class="header-actions">
                <?php if ($unread): ?><a href="contacts.php?read_all=1" class="btn-sm">Mark all read</a><?php endif; ?>
                <div class="header-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_user']) ?></div>
            </div>
        </header>
        <div class="admin-content">
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

            <div class="panel">
                <div class="panel-head"><h2>All Messages <span class="count"><?= count($contacts) ?></span></h2></div>
                <?php if (!$contacts): ?>
                    <div class="empty-state"><i class="fas fa-inbox"></i><p>No messages yet.</p></div>
                <?php else: ?>
                <div class="message-list">
                    <?php foreach ($contacts as $c): ?>
                    <div class="message-card <?= $c['is_read'] ? 'read' : 'unread' ?>">
                        <div class="msg-meta">
                            <div class="msg-sender">
                                <span class="msg-name"><?= htmlspecialchars($c['name']) ?></span>
                                <?php if (!$c['is_read']): ?><span class="msg-new">New</span><?php endif; ?>
                            </div>
                            <div class="msg-right">
                                <span class="msg-date"><?= date('d M Y, H:i', strtotime($c['created_at'])) ?></span>
                                <div class="msg-actions">
                                    <?php if (!$c['is_read']): ?><a href="contacts.php?read=<?= $c['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-check"></i></a><?php endif; ?>
                                    <a href="contacts.php?delete=<?= $c['id'] ?>&token=<?= $token ?>" class="btn-sm btn-delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="msg-email"><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a></div>
                        <div class="msg-subject"><strong><?= htmlspecialchars($c['subject']) ?></strong></div>
                        <div class="msg-body"><?= nl2br(htmlspecialchars($c['message'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
