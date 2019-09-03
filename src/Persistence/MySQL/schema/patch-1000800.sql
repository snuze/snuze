/**
 * These DDL statements implement the changes made to the Snuze MySQL schema in
 * version: 1000800 (Snuze 0.8.0).
 */
--
CREATE TABLE `accounts`(
    `id` VARCHAR(16) NOT NULL,
    `created` INTEGER NOT NULL,
    `created_utc` INTEGER NOT NULL,
    `comment_karma` INTEGER NOT NULL DEFAULT 0,
    `has_subscribed` BIT(1) NOT NULL DEFAULT 1,
    `has_verified_email` BIT(1) NOT NULL DEFAULT 0,
    `hide_from_robots` BIT(1) NOT NULL DEFAULT 0,
    `icon_img` VARCHAR(255) NOT NULL DEFAULT '',
    `is_employee` BIT(1) NOT NULL DEFAULT 0,
    `is_friend` BIT(1) NOT NULL DEFAULT 0,
    `is_gold` BIT(1) NOT NULL DEFAULT 0,
    `is_mod` BIT(1) NOT NULL DEFAULT 0,
    `link_karma` INTEGER NOT NULL DEFAULT 0,
    `name` VARCHAR(24) NOT NULL,
    `pref_show_snoovatar` BIT(1) NOT NULL DEFAULT 0,
    `subreddit` TEXT NULL,
    `verified` BIT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`),
    INDEX `ix_name`(`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
UPDATE `snuze` SET `schema_version` = 1000800;
