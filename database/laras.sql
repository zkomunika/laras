-- =========================================
-- CREATE DATABASE
-- =========================================
CREATE DATABASE IF NOT EXISTS laras
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE laras;

-- =========================================
-- SETTING
-- =========================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =========================================
-- TABLE: cache
-- =========================================
CREATE TABLE cache (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: cache_locks
-- =========================================
CREATE TABLE cache_locks (
    `key` VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: users
-- =========================================
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) DEFAULT NULL,
    last_seen_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: chapters
-- =========================================
CREATE TABLE chapters (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    number INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    theme VARCHAR(255) DEFAULT NULL,
    start_level INT UNSIGNED NOT NULL DEFAULT 1,
    end_level INT UNSIGNED NOT NULL DEFAULT 10,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY chapters_number_unique (number),
    UNIQUE KEY chapters_slug_unique (slug)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: levels
-- =========================================
CREATE TABLE levels (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    chapter_id BIGINT UNSIGNED NOT NULL,
    level_number INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    story_text TEXT NULL,
    target_text TEXT NOT NULL,
    target_wpm SMALLINT UNSIGNED NOT NULL DEFAULT 20,
    min_accuracy DECIMAL(5,2) NOT NULL DEFAULT 80.00,
    time_limit_seconds INT UNSIGNED DEFAULT NULL,
    max_mistakes INT UNSIGNED DEFAULT NULL,
    is_boss_level TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY levels_level_number_unique (level_number),
    KEY levels_chapter_id_level_number_index (chapter_id, level_number),

    CONSTRAINT levels_chapter_id_foreign
    FOREIGN KEY (chapter_id)
    REFERENCES chapters(id)
    ON DELETE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: chat_messages
-- =========================================
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),
    KEY chat_messages_user_id_foreign (user_id),

    CONSTRAINT chat_messages_user_id_foreign
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: player_progress
-- =========================================
CREATE TABLE player_progress (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    level_id BIGINT UNSIGNED NOT NULL,

    best_wpm DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    best_accuracy DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    best_score INT UNSIGNED NOT NULL DEFAULT 0,
    best_stars TINYINT UNSIGNED NOT NULL DEFAULT 0,
    attempts_count INT UNSIGNED NOT NULL DEFAULT 0,
    is_completed TINYINT(1) NOT NULL DEFAULT 0,

    unlocked_at TIMESTAMP NULL DEFAULT NULL,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY player_progress_user_id_level_id_unique (user_id, level_id),

    CONSTRAINT player_progress_user_id_foreign
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    CONSTRAINT player_progress_level_id_foreign
    FOREIGN KEY (level_id)
    REFERENCES levels(id)
    ON DELETE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABLE: typing_attempts
-- =========================================
CREATE TABLE typing_attempts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    level_id BIGINT UNSIGNED NOT NULL,

    typed_text LONGTEXT NULL,
    correct_chars INT UNSIGNED NOT NULL DEFAULT 0,
    wrong_chars INT UNSIGNED NOT NULL DEFAULT 0,
    mistakes INT UNSIGNED NOT NULL DEFAULT 0,

    accuracy DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    wpm DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    score INT UNSIGNED NOT NULL DEFAULT 0,
    stars TINYINT UNSIGNED NOT NULL DEFAULT 0,
    completed TINYINT(1) NOT NULL DEFAULT 0,

    duration_ms INT UNSIGNED NOT NULL DEFAULT 0,
    started_at TIMESTAMP NULL DEFAULT NULL,
    finished_at TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT typing_attempts_user_id_foreign
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    CONSTRAINT typing_attempts_level_id_foreign
    FOREIGN KEY (level_id)
    REFERENCES levels(id)
    ON DELETE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

COMMIT;