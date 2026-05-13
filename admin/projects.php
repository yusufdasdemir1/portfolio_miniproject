<?php
require_once 'auth.php';
require_once '../php/config.php';
$pdo    = getDB();
$unread = unread_count($pdo);
$error  = flash('error');
$success= flash('success');

/* ── Handle POST actions ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action      = $_POST['action'] ?? '';
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies= trim($_POST['technologies'] ?? '');
    $category    = $_POST['category'] ?? 'fullstack';
    $github_url  = trim($_POST['github_url'] ?? '') ?: null;
    $live_url    = trim($_POST['live_url'] ?? '') ?: null;
    $featured    = isset($_POST['featured']) ? 1 : 0;

    if (!$title || !$description) {
        flash('error', 'Title and description are required.');
        header('Location: projects.php'); exit;
    }
    if (!in_array($category, ['frontend','backend','fullstack'])) $category = 'fullstack';

    /* ── Photo upload ── */
    $image_path = trim($_POST['current_image'] ?? '') ?: null;
    if (!empty($_FILES['project_image']['tmp_name'])) {
        $file    = $_FILES['project_image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) {
            flash('error', 'Only JPG, PNG, WEBP or GIF images allowed.');
            header('Location: projects.php'); exit;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            flash('error', 'Image must be under 5MB.');
            header('Location: projects.php'); exit;
        }
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'project_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destDir  = dirname(__DIR__) . '/assets/images/projects/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $destDir . $filename)) {
            $image_path = 'assets/images/projects/' . $filename;
        }
    }

    if ($action === 'add') {
        $pdo->prepare("INSERT INTO projects (title,description,technologies,category,github_url,live_url,featured,image_path) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$title,$description,$technologies,$category,$github_url,$live_url,$featured,$image_path]);
        flash('success', "Project \"$title\" added.");
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE projects SET title=?,description=?,technologies=?,category=?,github_url=?,live_url=?,featured=?,image_path=? WHERE id=?")
            ->execute([$title,$description,$technologies,$category,$github_url,$live_url,$featured,$image_path,$id]);
        flash('success', 'Project updated.');
    }
    header('Location: projects.php'); exit;
}

/* ── Delete ── */
if (isset($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) die('Invalid token.');
    $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('success', 'Project deleted.');
    header('Location: projects.php'); exit;
}

/* ── Remove image only ── */
if (isset($_GET['remove_img'])) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) die('Invalid token.');
    $pdo->prepare("UPDATE projects SET image_path=NULL WHERE id=?")->execute([(int)$_GET['remove_img']]);
    flash('success', 'Project image removed.');
    header('Location: projects.php?edit=' . (int)$_GET['remove_img']); exit;
}

/* ── Fetch for editing ── */
$editing = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $editing = $s->fetch();
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, created_at DESC")->fetchAll();
$token    = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .img-preview-wrap { position: relative; display: inline-block; }
        .img-preview { width: 120px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border); display: block; }
        .img-remove { position: absolute; top: -6px; right: -6px; background: #dc2626; color: #fff; border-radius: 50%; width: 20px; height: 20px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .upload-zone { border: 2px dashed var(--border); border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: border-color 0.2s; }
        .upload-zone:hover { border-color: var(--steel); }
        .upload-zone input { display: none; }
        .upload-zone .uz-icon { font-size: 1.6rem; color: var(--steel); margin-bottom: 6px; }
        .upload-zone p { font-size: 0.78rem; color: var(--text-2); }
        .new-preview { width: 100%; max-height: 140px; object-fit: cover; border-radius: 8px; margin-top: 10px; display: none; }
        .project-thumb-cell img { width: 50px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border); }
        .no-img { font-size: 0.72rem; color: var(--text-2); }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include '_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-code"></i> Projects</h1>
            <div class="header-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_user']) ?></div>
        </header>
        <div class="admin-content">
            <?php if ($error):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

            <!-- Form -->
            <div class="panel">
                <div class="panel-head"><h2><?= $editing ? 'Edit Project' : 'Add New Project' ?></h2></div>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?= $token ?>">
                    <input type="hidden" name="action"        value="<?= $editing ? 'edit' : 'add' ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($editing['image_path'] ?? '') ?>">
                    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>

                    <div class="form-row">
                        <div class="field">
                            <label>Title *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editing['title'] ?? '') ?>" required placeholder="Project name">
                        </div>
                        <div class="field">
                            <label>Category *</label>
                            <select name="category">
                                <?php foreach (['fullstack','frontend','backend'] as $cat): ?>
                                <option value="<?= $cat ?>" <?= ($editing['category'] ?? 'fullstack') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Description *</label>
                        <textarea name="description" rows="3" placeholder="Brief project description..."><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
                    </div>

                    <div class="field">
                        <label>Technologies <span class="hint">(comma separated)</span></label>
                        <input type="text" name="technologies" value="<?= htmlspecialchars($editing['technologies'] ?? '') ?>" placeholder="Python, Django, MySQL">
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label>GitHub URL</label>
                            <input type="url" name="github_url" value="<?= htmlspecialchars($editing['github_url'] ?? '') ?>" placeholder="https://github.com/...">
                        </div>
                        <div class="field">
                            <label>Live URL</label>
                            <input type="url" name="live_url" value="<?= htmlspecialchars($editing['live_url'] ?? '') ?>" placeholder="https://...">
                        </div>
                    </div>

                    <!-- Photo -->
                    <div class="field">
                        <label>Project Image</label>
                        <?php if (!empty($editing['image_path'])): ?>
                        <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px">
                            <div class="img-preview-wrap">
                                <img src="../<?= htmlspecialchars($editing['image_path']) ?>" class="img-preview" id="currentImg">
                                <a href="projects.php?remove_img=<?= $editing['id'] ?>&token=<?= $token ?>"
                                   class="img-remove" title="Remove image" onclick="return confirm('Remove image?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <span class="hint">Current image. Upload below to replace it.</span>
                        </div>
                        <?php endif; ?>
                        <label class="upload-zone" id="uploadZone">
                            <input type="file" name="project_image" accept="image/*" id="projectImageInput">
                            <div class="uz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <p>Click to upload or drag & drop<br><span style="font-size:0.7rem">JPG, PNG, WEBP · Max 5MB</span></p>
                        </label>
                        <img id="newPreview" class="new-preview" alt="New preview">
                    </div>

                    <div class="field field-check">
                        <label class="checkbox-label">
                            <input type="checkbox" name="featured" <?= !empty($editing['featured']) ? 'checked' : '' ?>>
                            <span>Featured project</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-admin btn-primary-admin">
                            <i class="fas fa-save"></i> <?= $editing ? 'Update Project' : 'Add Project' ?>
                        </button>
                        <?php if ($editing): ?>
                        <a href="projects.php" class="btn-admin btn-ghost-admin"><i class="fas fa-times"></i> Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="panel">
                <div class="panel-head"><h2>All Projects <span class="count"><?= count($projects) ?></span></h2></div>
                <table class="admin-table">
                    <thead>
                        <tr><th>Image</th><th>Title</th><th>Category</th><th>Featured</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projects as $p): ?>
                    <tr>
                        <td class="project-thumb-cell">
                            <?php if ($p['image_path']): ?>
                                <img src="../<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                            <?php else: ?>
                                <span class="no-img"><i class="fas fa-image"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                        <td><span class="badge-cat badge-<?= $p['category'] ?>"><?= $p['category'] ?></span></td>
                        <td><?= $p['featured'] ? '<i class="fas fa-star" style="color:#5DF8D8"></i>' : '—' ?></td>
                        <td class="actions">
                            <a href="projects.php?edit=<?= $p['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="projects.php?delete=<?= $p['id'] ?>&token=<?= $token ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this project?')"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
const input   = document.getElementById('projectImageInput');
const preview = document.getElementById('newPreview');
const zone    = document.getElementById('uploadZone');

input.addEventListener('change', () => {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
        zone.style.borderColor = '#3B7597';
    }
});

/* Drag & drop */
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.style.borderColor = '#093C5D'; });
zone.addEventListener('dragleave', ()  => zone.style.borderColor = '');
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = '';
    if (e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    }
});
</script>
</body>
</html>
