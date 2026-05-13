<?php
require_once 'auth.php';
require_once '../php/config.php';
$pdo    = getDB();
$unread = unread_count($pdo);
$success= flash('success');
$error  = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $heading       = trim($_POST['heading'] ?? '');
    $bio_1         = trim($_POST['bio_1'] ?? '');
    $bio_2         = trim($_POST['bio_2'] ?? '');
    $bio_3         = trim($_POST['bio_3'] ?? '');
    $stat_projects = (int)($_POST['stat_projects'] ?? 6);
    $stat_years    = (int)($_POST['stat_years'] ?? 3);
    $stat_clients  = (int)($_POST['stat_clients'] ?? 5);

    /* Handle photo upload */
    $photo_path = trim($_POST['current_photo'] ?? 'assets/images/me.jpeg');
    if (!empty($_FILES['photo']['tmp_name'])) {
        $file    = $_FILES['photo'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($file['type'], $allowed)) {
            flash('error', 'Only JPG, PNG, WEBP images allowed.');
            header('Location: about.php'); exit;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            flash('error', 'Image must be under 5MB.');
            header('Location: about.php'); exit;
        }
        $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename   = 'me_' . time() . '.' . strtolower($ext);
        $dest       = dirname(__DIR__) . '/assets/images/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $photo_path = 'assets/images/' . $filename;
        }
    }

    $existing = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
    if ($existing) {
        $pdo->prepare("UPDATE about_content SET heading=?,bio_1=?,bio_2=?,bio_3=?,photo_path=?,stat_projects=?,stat_years=?,stat_clients=? WHERE id=1")
            ->execute([$heading,$bio_1,$bio_2,$bio_3,$photo_path,$stat_projects,$stat_years,$stat_clients]);
    } else {
        $pdo->prepare("INSERT INTO about_content (heading,bio_1,bio_2,bio_3,photo_path,stat_projects,stat_years,stat_clients) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$heading,$bio_1,$bio_2,$bio_3,$photo_path,$stat_projects,$stat_years,$stat_clients]);
    }
    flash('success', 'About section updated successfully.');
    header('Location: about.php'); exit;
}

$about = $pdo->query("SELECT * FROM about_content LIMIT 1")->fetch();
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
    <?php include '_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-user"></i> About Section</h1>
            <div class="header-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_user']) ?></div>
        </header>
        <div class="admin-content">
            <?php if ($error):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <input type="hidden" name="current_photo" value="<?= htmlspecialchars($about['photo_path'] ?? 'assets/images/me.jpeg') ?>">

                <div class="panel">
                    <div class="panel-head"><h2>Profile Photo</h2></div>
                    <div class="photo-upload-area">
                        <img src="../<?= htmlspecialchars($about['photo_path'] ?? 'assets/images/me.jpeg') ?>"
                             alt="Current photo" class="current-photo" id="photoPreview">
                        <div class="photo-upload-right">
                            <label class="btn-admin btn-ghost-admin file-label">
                                <i class="fas fa-upload"></i> Choose New Photo
                                <input type="file" name="photo" accept="image/*" style="display:none" onchange="previewPhoto(this)">
                            </label>
                            <p class="hint">JPG, PNG or WEBP · Max 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head"><h2>Content</h2></div>
                    <div class="field">
                        <label>Heading</label>
                        <input type="text" name="heading" value="<?= htmlspecialchars($about['heading'] ?? '') ?>" placeholder="Building scalable systems...">
                    </div>
                    <div class="field">
                        <label>Bio Paragraph 1</label>
                        <textarea name="bio_1" rows="4"><?= htmlspecialchars($about['bio_1'] ?? '') ?></textarea>
                    </div>
                    <div class="field">
                        <label>Bio Paragraph 2</label>
                        <textarea name="bio_2" rows="4"><?= htmlspecialchars($about['bio_2'] ?? '') ?></textarea>
                    </div>
                    <div class="field">
                        <label>Bio Paragraph 3</label>
                        <textarea name="bio_3" rows="4"><?= htmlspecialchars($about['bio_3'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head"><h2>Stats</h2></div>
                    <div class="form-row-3">
                        <div class="field">
                            <label>Projects Done</label>
                            <input type="number" name="stat_projects" value="<?= $about['stat_projects'] ?? 6 ?>" min="0">
                        </div>
                        <div class="field">
                            <label>Years Experience</label>
                            <input type="number" name="stat_years" value="<?= $about['stat_years'] ?? 3 ?>" min="0">
                        </div>
                        <div class="field">
                            <label>Happy Clients</label>
                            <input type="number" name="stat_clients" value="<?= $about['stat_clients'] ?? 5 ?>" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin btn-primary-admin">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
