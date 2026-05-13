-- =============================================
--  PORTFOLIO DATABASE SCHEMA & SEED DATA
--  Run this in phpMyAdmin or MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS portfolio_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE portfolio_db;

-- ─── CONTACTS ─────────────────────────────────
CREATE TABLE IF NOT EXISTS contacts (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL,
    subject    VARCHAR(200)  NOT NULL,
    message    TEXT          NOT NULL,
    is_read    TINYINT(1)    NOT NULL DEFAULT 0,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── PROJECTS ─────────────────────────────────
CREATE TABLE IF NOT EXISTS projects (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(200)                                    NOT NULL,
    description  TEXT                                            NOT NULL,
    technologies VARCHAR(500)                                    NOT NULL,
    category     ENUM('frontend','backend','fullstack')          NOT NULL DEFAULT 'fullstack',
    github_url   VARCHAR(500)                                    DEFAULT NULL,
    live_url     VARCHAR(500)                                    DEFAULT NULL,
    featured     TINYINT(1)                                      NOT NULL DEFAULT 0,
    created_at   TIMESTAMP                                       NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── SEED PROJECTS (GitHub Repos) ────────────
INSERT INTO projects (title, description, technologies, category, github_url, live_url, featured) VALUES

('luckofWheel',
 'Gift wheel application for company events. Handles wheel configuration, prize management and result tracking via a Python backend API.',
 'Python',
 'backend', 'https://github.com/yusufdasdemir1/luckofWheel', NULL, 1),

('luckofWheel Frontend',
 'Interactive lucky wheel frontend with animated spinning wheel UI. Used for company gift distributions and events.',
 'TypeScript, CSS, JavaScript',
 'frontend', 'https://github.com/yusufdasdemir1/luckofWheel-fe', NULL, 1),

('Nakliyat Website',
 'Professional moving and shipping company website. Features service listings, quote requests and contact forms, containerized with Docker.',
 'TypeScript, CSS, JavaScript, Docker',
 'fullstack', 'https://github.com/yusufdasdemir1/nakliyat-website', NULL, 1),

('Spor Salonu',
 'Sports hall management system. Handles member registration, class scheduling and facility management built with Python.',
 'Python',
 'backend', 'https://github.com/yusufdasdemir1/spor_salonu', NULL, 0),

('Portfolio',
 'Personal portfolio website showcasing projects and skills with modern design, dark/light theme and smooth animations.',
 'TypeScript, CSS, JavaScript',
 'frontend', 'https://github.com/yusufdasdemir1/Portfolio', NULL, 0);
