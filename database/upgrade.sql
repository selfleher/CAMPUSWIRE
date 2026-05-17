-- ═══════════════════════════════════════════════════
-- CampusWire — Safe Idempotent Upgrade v2
-- Uses IF NOT EXISTS / IF EXISTS guards
-- Run in phpMyAdmin → SQL tab on campuswire_db
-- ═══════════════════════════════════════════════════

USE campuswire_db;

-- ── Add user columns (safe) ───────────────────────
SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='campuswire_db' AND TABLE_NAME='users' AND COLUMN_NAME='bio'
);
SET @sql = IF(@col_exists=0,
    'ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_pic',
    'SELECT "bio column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists2 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='campuswire_db' AND TABLE_NAME='users' AND COLUMN_NAME='skills'
);
SET @sql2 = IF(@col_exists2=0,
    'ALTER TABLE users ADD COLUMN skills VARCHAR(500) NULL AFTER bio',
    'SELECT "skills column already exists"');
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

SET @col_exists3 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='campuswire_db' AND TABLE_NAME='users' AND COLUMN_NAME='department'
);
SET @sql3 = IF(@col_exists3=0,
    'ALTER TABLE users ADD COLUMN department VARCHAR(100) NULL AFTER skills',
    'SELECT "department column already exists"');
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

SET @col_exists4 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='campuswire_db' AND TABLE_NAME='users' AND COLUMN_NAME='roll_no'
);
SET @sql4 = IF(@col_exists4=0,
    'ALTER TABLE users ADD COLUMN roll_no VARCHAR(50) NULL AFTER department',
    'SELECT "roll_no column already exists"');
PREPARE stmt4 FROM @sql4; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

-- ── Clubs table ───────────────────────────────────
CREATE TABLE IF NOT EXISTS clubs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    slug          VARCHAR(255) NOT NULL UNIQUE,
    description   TEXT         NULL,
    achievements  TEXT         NULL,
    category      VARCHAR(50)  DEFAULT 'General',
    initial       VARCHAR(10)  DEFAULT 'C',
    bg_color      VARCHAR(20)  DEFAULT '#E0E7FF',
    text_color    VARCHAR(20)  DEFAULT '#4338CA',
    members_count INT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Club Members ──────────────────────────────────
CREATE TABLE IF NOT EXISTS club_members (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    club_id   INT NOT NULL,
    user_id   INT NOT NULL,
    role      ENUM('admin','member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (club_id, user_id)
) ENGINE=InnoDB;

-- ── Club Join Requests ────────────────────────────
CREATE TABLE IF NOT EXISTS club_join_requests (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    club_id    INT          NOT NULL,
    user_id    INT          NOT NULL,
    branch     VARCHAR(100) NOT NULL,
    year       VARCHAR(20)  NOT NULL,
    skills     VARCHAR(500) NULL,
    reason     TEXT         NOT NULL,
    status     ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_request (club_id, user_id)
) ENGINE=InnoDB;

-- ── Add parent_reply_id to discussion_replies (safe) ──
SET @rep_col = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA='campuswire_db' AND TABLE_NAME='discussion_replies' AND COLUMN_NAME='parent_reply_id'
);
SET @rsql = IF(@rep_col=0,
    'ALTER TABLE discussion_replies ADD COLUMN parent_reply_id INT NULL AFTER discussion_id',
    'SELECT "parent_reply_id already exists"');
PREPARE rstmt FROM @rsql; EXECUTE rstmt; DEALLOCATE PREPARE rstmt;

-- ── Event RSVPs ───────────────────────────────────
CREATE TABLE IF NOT EXISTS event_rsvps (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    event_id   INT NOT NULL,
    user_id    INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rsvp (event_id, user_id)
) ENGINE=InnoDB;

-- ── Achievements ──────────────────────────────────
CREATE TABLE IF NOT EXISTS achievements (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    title        VARCHAR(255) NOT NULL,
    description  TEXT NULL,
    date_awarded DATE NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Seed Clubs (skip if already exist) ───────────
INSERT IGNORE INTO clubs (name, slug, description, achievements, category, initial, bg_color, text_color, members_count) VALUES
('Astro Club',    'astro-club',    'Stargazing, astronomy workshops, and exploring the cosmos together. We conduct monthly sky-watching events and inter-college astronomy competitions.',
 'Winner of State Astronomy Olympiad 2024 | Best Technical Club Award 2023 | 3 National Paper Presentations',
 'Science', 'AC', '#E0E7FF', '#4338CA', 120),

('Clicks Club',   'clicks-club',   'Official photography and videography club covering all major campus events. We run workshops on DSLR, post-processing, and cinematography.',
 'Gold Medal — Inter-College Photo Fest 2024 | Cover story in Campus Magazine | Best Videography Award',
 'Arts', 'CC', '#FCE7F3', '#BE185D', 185),

('Techno Hub',    'techno-hub',    'The core technical society for robotics, coding, and innovation projects. We participate in hackathons and build open-source tools for campus.',
 '1st Place Smart India Hackathon 2023 | Robotics State Championship 2024 | 12 Published Open-Source Projects',
 'Technology', 'TH', '#DBEAFE', '#1E40AF', 450),

('PAC',           'pac',           'Performing Arts Club specializing in theater, drama, and stage performances. We stage 4 major productions every academic year including inter-college festivals.',
 'Best Drama Club — State Level 2024 | National Theatre Festival Participation | Best Director Award',
 'Arts', 'PAC', '#FEF3C7', '#B45309', 150),

('Vista Club',    'vista-club',    'Creative arts and design community fostering visual storytelling through illustration, graphic design, UI/UX, and digital art.',
 'Best Design Club 2023 | National Design Challenge Winners | 200+ Campus Branding Projects Completed',
 'Design', 'VC', '#F3E8FF', '#7E22CE', 110),

('Quizbizz Club', 'quizbizz-club', 'For the geeks and know-it-alls. We participate in inter-college quiz bowls, KBC-style events, and organise the annual Quizbizz championship.',
 'State Quiz Championship 2024 Champions | KBC Campus Edition Winners | 5x Inter-College Wins',
 'Academics', 'QB', '#DCFCE7', '#15803D', 95),

('GDSC',          'gdsc',          'Google Developer Student Clubs — building open-source solutions, mentoring students in tech, and connecting with Google engineers worldwide.',
 'Best GDSC Chapter — Central India 2024 | 50+ Projects Built | Google Solution Challenge Finalists',
 'Technology', 'G', '#DBEAFE', '#1E40AF', 520);
