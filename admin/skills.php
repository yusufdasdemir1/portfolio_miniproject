<?php
require_once 'auth.php';
require_once '../php/config.php';
$pdo    = getDB();
$unread = unread_count($pdo);
$success= flash('success');
$error  = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'add_skill' || $action === 'edit_skill') {
        $name    = trim($_POST['name'] ?? '');
        $icon    = trim($_POST['icon'] ?? '');
        $percent = min(100, max(0, (int)($_POST['percent'] ?? 80)));
        $cat     = $_POST['category'] ?? 'backend';
        $order   = (int)($_POST['display_order'] ?? 0);
        if (!in_array($cat, ['frontend','backend','devops'])) $cat = 'backend';
        if (!$name) { flash('error', 'Name is required.'); header('Location: skills.php'); exit; }

        if ($action === 'add_skill') {
            $pdo->prepare("INSERT INTO skills (category,name,icon,percent,display_order) VALUES (?,?,?,?,?)")
                ->execute([$cat,$name,$icon,$percent,$order]);
            flash('success', "Skill \"$name\" added.");
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("UPDATE skills SET category=?,name=?,icon=?,percent=?,display_order=? WHERE id=?")
                ->execute([$cat,$name,$icon,$percent,$order,$id]);
            flash('success', 'Skill updated.');
        }
    } elseif ($action === 'add_tool' || $action === 'edit_tool') {
        $name  = trim($_POST['tool_name'] ?? '');
        $icon  = trim($_POST['tool_icon'] ?? '');
        $order = (int)($_POST['tool_order'] ?? 0);
        if (!$name) { flash('error', 'Tool name required.'); header('Location: skills.php'); exit; }

        if ($action === 'add_tool') {
            $pdo->prepare("INSERT INTO tools (name,icon,display_order) VALUES (?,?,?)")->execute([$name,$icon,$order]);
            flash('success', "Tool \"$name\" added.");
        } else {
            $id = (int)($_POST['tool_id'] ?? 0);
            $pdo->prepare("UPDATE tools SET name=?,icon=?,display_order=? WHERE id=?")->execute([$name,$icon,$order,$id]);
            flash('success', 'Tool updated.');
        }
    }
    header('Location: skills.php'); exit;
}

if (isset($_GET['del_skill'])) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) die('Invalid token.');
    $pdo->prepare("DELETE FROM skills WHERE id=?")->execute([(int)$_GET['del_skill']]);
    flash('success', 'Skill deleted.'); header('Location: skills.php'); exit;
}
if (isset($_GET['del_tool'])) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) die('Invalid token.');
    $pdo->prepare("DELETE FROM tools WHERE id=?")->execute([(int)$_GET['del_tool']]);
    flash('success', 'Tool deleted.'); header('Location: skills.php'); exit;
}

$edit_skill = null;
if (isset($_GET['edit_skill'])) {
    $s = $pdo->prepare("SELECT * FROM skills WHERE id=?"); $s->execute([(int)$_GET['edit_skill']]);
    $edit_skill = $s->fetch();
}
$edit_tool = null;
if (isset($_GET['edit_tool'])) {
    $s = $pdo->prepare("SELECT * FROM tools WHERE id=?"); $s->execute([(int)$_GET['edit_tool']]);
    $edit_tool = $s->fetch();
}

$skills = $pdo->query("SELECT * FROM skills ORDER BY category, display_order")->fetchAll();
$tools  = $pdo->query("SELECT * FROM tools  ORDER BY display_order")->fetchAll();
$token  = csrf_token();
$cats   = ['frontend','backend','devops'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
    <?php include '_sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-tools"></i> Skills & Tools</h1>
            <div class="header-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_user']) ?></div>
        </header>
        <div class="admin-content">
            <?php if ($error):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

            <div class="two-col">
                <!-- Skills Form -->
                <div class="panel">
                    <div class="panel-head"><h2><?= $edit_skill ? 'Edit Skill' : 'Add Skill' ?></h2></div>
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="action" value="<?= $edit_skill ? 'edit_skill' : 'add_skill' ?>">
                        <?php if ($edit_skill): ?><input type="hidden" name="id" value="<?= $edit_skill['id'] ?>"><?php endif; ?>
                        <div class="field">
                            <label>Skill Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($edit_skill['name'] ?? '') ?>" placeholder="Python" required>
                        </div>
                        <div class="field">
                            <label>Category</label>
                            <select name="category">
                                <?php foreach ($cats as $c): ?>
                                <option value="<?= $c ?>" <?= ($edit_skill['category'] ?? 'backend') === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Icon Class <span class="hint">(e.g. fab fa-python)</span></label>
                            <input type="text" name="icon" value="<?= htmlspecialchars($edit_skill['icon'] ?? '') ?>" placeholder="fab fa-python">
                        </div>
                        <div class="field">
                            <label>Percentage: <span id="pctVal"><?= $edit_skill['percent'] ?? 80 ?></span>%</label>
                            <input type="range" name="percent" min="0" max="100" value="<?= $edit_skill['percent'] ?? 80 ?>" oninput="document.getElementById('pctVal').textContent=this.value">
                        </div>
                        <div class="field">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="<?= $edit_skill['display_order'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-primary-admin"><i class="fas fa-save"></i> <?= $edit_skill ? 'Update' : 'Add' ?></button>
                            <?php if ($edit_skill): ?><a href="skills.php" class="btn-admin btn-ghost-admin">Cancel</a><?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Tools Form -->
                <div class="panel">
                    <div class="panel-head"><h2><?= $edit_tool ? 'Edit Tool' : 'Add Tool' ?></h2></div>
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="action" value="<?= $edit_tool ? 'edit_tool' : 'add_tool' ?>">
                        <?php if ($edit_tool): ?><input type="hidden" name="tool_id" value="<?= $edit_tool['id'] ?>"><?php endif; ?>
                        <div class="field">
                            <label>Tool Name</label>
                            <input type="text" name="tool_name" value="<?= htmlspecialchars($edit_tool['name'] ?? '') ?>" placeholder="GitHub" required>
                        </div>
                        <div class="field">
                            <label>Icon Class</label>
                            <input type="text" name="tool_icon" value="<?= htmlspecialchars($edit_tool['icon'] ?? '') ?>" placeholder="fab fa-github">
                        </div>
                        <div class="field">
                            <label>Display Order</label>
                            <input type="number" name="tool_order" value="<?= $edit_tool['display_order'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-primary-admin"><i class="fas fa-save"></i> <?= $edit_tool ? 'Update' : 'Add' ?></button>
                            <?php if ($edit_tool): ?><a href="skills.php" class="btn-admin btn-ghost-admin">Cancel</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Skills Table -->
            <div class="panel">
                <div class="panel-head"><h2>All Skills <span class="count"><?= count($skills) ?></span></h2></div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Category</th><th>%</th><th>Icon</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($skills as $s): ?>
                    <tr>
                        <td><i class="<?= htmlspecialchars($s['icon']) ?>"></i> <?= htmlspecialchars($s['name']) ?></td>
                        <td><span class="badge-cat badge-<?= $s['category'] ?>"><?= $s['category'] ?></span></td>
                        <td><?= $s['percent'] ?>%</td>
                        <td><code><?= htmlspecialchars($s['icon']) ?></code></td>
                        <td><?= $s['display_order'] ?></td>
                        <td class="actions">
                            <a href="skills.php?edit_skill=<?= $s['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="skills.php?del_skill=<?= $s['id'] ?>&token=<?= $token ?>" class="btn-sm btn-delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tools Table -->
            <div class="panel">
                <div class="panel-head"><h2>All Tools <span class="count"><?= count($tools) ?></span></h2></div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Icon</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($tools as $t): ?>
                    <tr>
                        <td><i class="<?= htmlspecialchars($t['icon']) ?>"></i> <?= htmlspecialchars($t['name']) ?></td>
                        <td><code><?= htmlspecialchars($t['icon']) ?></code></td>
                        <td><?= $t['display_order'] ?></td>
                        <td class="actions">
                            <a href="skills.php?edit_tool=<?= $t['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="skills.php?del_tool=<?= $t['id'] ?>&token=<?= $token ?>" class="btn-sm btn-delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
