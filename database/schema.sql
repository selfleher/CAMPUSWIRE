-- ═══════════════════════════════════════════════════
-- CampusWire — MySQL Schema
-- Run this in phpMyAdmin after creating `campuswire_db`
-- Compatible with MySQL 5.7+ and MySQL 8.0+
-- ═══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS campuswire_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE campuswire_db;

-- ── Users ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255)  NOT NULL,
    email         VARCHAR(255)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('student','faculty','admin') NOT NULL DEFAULT 'student',
    profile_pic   VARCHAR(255)  DEFAULT NULL,
    is_active     TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ── News ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS news (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    author_id        INT           NOT NULL,
    title            VARCHAR(500)  NOT NULL,
    summary          TEXT          NULL,
    content          LONGTEXT      NOT NULL,
    category         VARCHAR(50)   NOT NULL DEFAULT 'general',
    department       VARCHAR(100)  DEFAULT 'all',
    priority         ENUM('normal','urgent','emergency') DEFAULT 'normal',
    status           ENUM('pending','approved','rejected') DEFAULT 'pending',
    rejection_reason TEXT          NULL,
    image_url        VARCHAR(500)  NULL,
    views            INT UNSIGNED  DEFAULT 0,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status   (status),
    INDEX idx_category (category),
    INDEX idx_created  (created_at)
) ENGINE=InnoDB;

-- ── Events ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS events (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    author_id   INT          NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT         NULL,
    event_date  DATE         NOT NULL,
    event_time  VARCHAR(20)  DEFAULT '10:00',
    location    VARCHAR(255) DEFAULT '',
    type        VARCHAR(50)  DEFAULT 'General',
    attendees   INT UNSIGNED DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Event RSVPs (Many-to-Many) ───────────────────
CREATE TABLE IF NOT EXISTS event_rsvps (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    event_id  INT NOT NULL,
    user_id   INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    UNIQUE KEY unique_rsvp (event_id, user_id)
) ENGINE=InnoDB;

-- ── Blogs ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blogs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    author_id  INT          NOT NULL,
    title      VARCHAR(500) NOT NULL,
    excerpt    TEXT         NULL,
    content    LONGTEXT     NOT NULL,
    image_url  VARCHAR(500) NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Community Discussions ────────────────────────
CREATE TABLE IF NOT EXISTS discussions (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT      NOT NULL,
    content    TEXT     NOT NULL,
    tags       VARCHAR(255) DEFAULT '',
    likes      INT UNSIGNED DEFAULT 0,
    replies    INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Discussion Replies ───────────────────────────
CREATE TABLE IF NOT EXISTS discussion_replies (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    discussion_id  INT  NOT NULL,
    user_id        INT  NOT NULL,
    content        TEXT NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Alerts ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS alerts (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    created_by      INT          NOT NULL,
    title           VARCHAR(255) NOT NULL,
    message         TEXT         NOT NULL,
    type            ENUM('info','warning','danger','success') DEFAULT 'info',
    severity        VARCHAR(20)  DEFAULT 'medium',
    target_audience VARCHAR(50)  DEFAULT 'all',
    is_active       TINYINT(1)   DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Comments (polymorphic for news/blogs) ────────
CREATE TABLE IF NOT EXISTS comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT  NOT NULL,
    news_id    INT  NULL,
    blog_id    INT  NULL,
    content    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id)   ON DELETE CASCADE,
    FOREIGN KEY (blog_id) REFERENCES blogs(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Achievements ─────────────────────────────────
-- FIX: Removed (CURRENT_DATE) default — not supported in MySQL < 8.0.13
CREATE TABLE IF NOT EXISTS achievements (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL,
    title        VARCHAR(255) NOT NULL,
    description  TEXT         NULL,
    date_awarded DATE         NULL,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Notifications ────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    message    VARCHAR(500) NOT NULL,
    is_read    TINYINT(1)   DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB;
