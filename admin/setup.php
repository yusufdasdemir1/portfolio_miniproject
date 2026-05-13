<?php
/**
 * ONE-TIME SETUP — run once at http://localhost:8080/admin/setup.php
 * DELETE this file afterwards!
 */
require_once '../php/config.php';
$pdo = getDB();

$pdo->exec("
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS about_content (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    heading        VARCHAR(255) NOT NULL DEFAULT 'Building scalable systems with data-driven solutions.',
    bio_1          TEXT,
    bio_2          TEXT,
    bio_3          TEXT,
    photo_path     VARCHAR(500) NOT NULL DEFAULT 'assets/images/me.jpeg',
    stat_projects  INT NOT NULL DEFAULT 6,
    stat_years     INT NOT NULL DEFAULT 3,
    stat_clients   INT NOT NULL DEFAULT 5,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS skills (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category      ENUM('frontend','backend','devops') NOT NULL,
    name          VARCHAR(100) NOT NULL,
    icon          VARCHAR(80)  NOT NULL DEFAULT '',
    percent       TINYINT      NOT NULL DEFAULT 80,
    display_order INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tools (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    icon          VARCHAR(80)  NOT NULL DEFAULT '',
    display_order INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* Admin user */
$existing = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
if (!$existing) {
    $hash = password_hash('Admin@2024', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)")
        ->execute(['admin', $hash]);
}

/* About seed */
$existing = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
if (!$existing) {
    $pdo->exec("INSERT INTO about_content (heading, bio_1, bio_2, bio_3, photo_path, stat_projects, stat_years, stat_clients) VALUES (
        'Building scalable systems with data-driven solutions.',
        'I''m a third-year Software Engineering student at Haliç University and a backend-focused developer based in Turkey. Since February 2024, I have been working at Pure Technology, where I contribute to backend development, data science, web scraping, and large-scale automation projects.',
        'My experience includes developing scalable backend services with Django, building workflow automation systems using Temporal, and working with data processing technologies such as pandas, NumPy, and SQL.',
        'My journey in software development started with curiosity and quickly evolved into a passion for solving real-world engineering problems. I enjoy every stage of development — from backend architecture and database optimization to automation workflows and intelligent data processing systems.',
        'assets/images/me.jpeg', 6, 3, 5
    )");
}

/* Skills seed */
$existing = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
if (!$existing) {
    $pdo->exec("INSERT INTO skills (category, name, icon, percent, display_order) VALUES
        ('frontend', 'HTML5',      'fab fa-html5',      90, 1),
        ('frontend', 'CSS3',       'fab fa-css3-alt',   85, 2),
        ('frontend', 'JavaScript', 'fab fa-js',         80, 3),
        ('frontend', 'TypeScript', 'fas fa-code',       75, 4),
        ('backend',  'Python',     'fab fa-python',     90, 1),
        ('backend',  'Java',       'fab fa-java',       60, 2),
        ('backend',  'MySQL',      'fas fa-database',   80, 3),
        ('backend',  'REST API',   'fas fa-exchange-alt',85, 4),
        ('devops',   'Docker',     'fab fa-docker',     78, 1),
        ('devops',   'Linux',      'fab fa-linux',      72, 2),
        ('devops',   'CI / CD',    'fas fa-code-branch',65, 3),
        ('devops',   'Nginx',      'fas fa-network-wired',60, 4)
    ");
}

/* Tools seed */
$existing = $pdo->query("SELECT COUNT(*) FROM tools")->fetchColumn();
if (!$existing) {
    $pdo->exec("INSERT INTO tools (name, icon, display_order) VALUES
        ('GitHub',   'fab fa-github',    1),
        ('Bitbucket','fab fa-bitbucket', 2),
        ('Git',      'fab fa-git-alt',   3),
        ('Jira',     'fab fa-jira',      4),
        ('VS Code',  'fas fa-terminal',  5),
        ('Postman',  'fas fa-vial',      6),
        ('Figma',    'fab fa-figma',     7),
        ('DataGrip', 'fas fa-database',  8)
    ");
}

echo '<h2 style="font-family:sans-serif;color:green">✅ Setup complete!</h2>';
echo '<p style="font-family:sans-serif">Login: <b>admin</b> / <b>Admin@2024</b></p>';
echo '<p style="font-family:sans-serif;color:red"><b>Delete this file immediately after setup.</b></p>';
echo '<a href="index.php" style="font-family:sans-serif">→ Go to Admin Login</a>';
