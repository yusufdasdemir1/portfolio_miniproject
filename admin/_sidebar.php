<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-logo">YD</span>
        <div><span class="brand-title">Portfolio</span><span class="brand-sub">Admin Panel</span></div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= active('dashboard') ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="projects.php"  class="nav-item <?= active('projects')  ?>"><i class="fas fa-code"></i> Projects</a>
        <a href="about.php"     class="nav-item <?= active('about')     ?>"><i class="fas fa-user"></i> About</a>
        <a href="skills.php"    class="nav-item <?= active('skills')    ?>"><i class="fas fa-tools"></i> Skills</a>
        <a href="contacts.php"  class="nav-item <?= active('contacts')  ?>">
            <i class="fas fa-envelope"></i> Messages
            <?php if (!empty($unread) && $unread > 0): ?><span class="nav-badge"><?= $unread ?></span><?php endif; ?>
        </a>
    </nav>
    <a href="logout.php" class="sidebar-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</aside>
