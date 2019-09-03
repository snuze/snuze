/**
 * This file contains DDL statements to build the full Snuze MySQL schema,
 * version: 1000800 (Snuze 0.8.0).
 *
 * The statements in this file should only be run once, when you first 
 * set up your MySQL database to use with Snuze. (Or if you decide to nuke
 * everything and start fresh.)
 * 
 * When upgrading Snuze to a new version, a series of smaller .sql files may 
 * appear in this directory. Those are patch files, which contain the database 
 * schema changes between Snuze versions. When Snuze detects that the schema
 * is out of data, it will prompt you with instructions to apply the patches.
 *
 * *****************************************************************************
 * This file is part of Snuze, a PHP client for the Reddit API.
 * Copyright 2019 Shaun Cummiskey <shaun@shaunc.com> <https://shaunc.com/>
 * Repository: <https://github.com/snuze/snuze/>
 * Documentation: <https://snuze.shaunc.com/>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
--
CREATE TABLE `snuze` (
    `schema_version` MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY(`schema_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
INSERT `snuze` (`schema_version`) VALUES(1000800);
--
CREATE TABLE `access_tokens` (
    `username` VARCHAR(32) NOT NULL,
    `access_token` VARCHAR(64) NOT NULL,
    `expires` INTEGER UNSIGNED NOT NULL,
    `scope` VARCHAR(255) NOT NULL,
    `token_type` VARCHAR(24) NOT NULL DEFAULT 'bearer',
    `refresh_token` VARCHAR(32) NULL,
    PRIMARY KEY(`username`, `access_token`, `expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
CREATE TABLE `subreddits` (
    id VARCHAR(16) NOT NULL,
    display_name VARCHAR(24) NOT NULL,
    accounts_active MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
    accounts_active_is_fuzzed BIT(1) NOT NULL DEFAULT 0,
    active_user_count MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
    advertiser_category VARCHAR(32) NULL,
    all_original_content BIT(1) NOT NULL DEFAULT 0,
    allow_discovery BIT(1) NOT NULL DEFAULT 0,
    allow_images BIT(1) NOT NULL DEFAULT 0,
    allow_videogifs BIT(1) NOT NULL DEFAULT 0,
    allow_videos BIT(1) NOT NULL DEFAULT 0,
    banner_background_color VARCHAR(8) NULL,
    banner_background_image VARCHAR(255) NULL,
    banner_img VARCHAR(128) NULL,
    banner_size VARCHAR(12) NULL,
    can_assign_link_flair BIT(1) NOT NULL DEFAULT 0,
    can_assign_user_flair BIT(1) NOT NULL DEFAULT 0,
    coins INTEGER UNSIGNED NOT NULL DEFAULT 0,
    collapse_deleted_comments BIT(1) NOT NULL DEFAULT 0,
    collections_enabled BIT(1) NOT NULL DEFAULT 0,
    comment_score_hide_mins SMALLINT NOT NULL DEFAULT 0,
    community_icon VARCHAR(128) NULL,
    content_category VARCHAR(32) NULL,
    created INTEGER UNSIGNED NOT NULL,
    created_utc INTEGER UNSIGNED NOT NULL,
    description TEXT NULL,
    description_html TEXT NULL,
    disable_contributor_requests BIT(1) NOT NULL DEFAULT 0,
    display_name_prefixed VARCHAR(24) NOT NULL,
    emojis_custom_size VARCHAR(12) NULL,
    emojis_enabled BIT(1) NOT NULL DEFAULT 0,
    event_posts_enabled BIT(1) NOT NULL DEFAULT 0,
    free_form_reports BIT(1) NOT NULL DEFAULT 0,
    has_menu_widget BIT(1) NOT NULL DEFAULT 0,
    header_img VARCHAR(128) NULL,
    header_size VARCHAR(12) NULL,
    header_title VARCHAR(1024) NULL,
    hide_ads BIT(1) NOT NULL DEFAULT 0,
    icon_img VARCHAR(128) NULL,
    icon_size VARCHAR(12) NULL,
    is_enrolled_in_new_modmail BIT(1) NULL,
    key_color VARCHAR(8) NULL,
    lang VARCHAR(8) NOT NULL DEFAULT 'en',
    link_flair_enabled BIT(1) NOT NULL DEFAULT 0,
    link_flair_position VARCHAR(8) NULL,
    mobile_banner_image VARCHAR(128) NULL,
    `name` VARCHAR(16) NOT NULL,
    notification_level VARCHAR(16) NULL,
    original_content_tag_enabled BIT(1) NOT NULL DEFAULT 0,
    over18 BIT(1) NOT NULL DEFAULT 0,
    primary_color VARCHAR(8) NULL,
    public_description TEXT NULL,
    public_description_html TEXT NULL,
    public_traffic BIT(1) NOT NULL DEFAULT 0,
    quarantine BIT(1) NOT NULL DEFAULT 0,
    restrict_commenting BIT(1) NOT NULL DEFAULT 0,
    restrict_posting BIT(1) NOT NULL DEFAULT 0,
    show_media BIT(1) NOT NULL DEFAULT 0,
    show_media_preview BIT(1) NOT NULL DEFAULT 0,
    spoilers_enabled BIT(1) NOT NULL DEFAULT 0,
    submission_type VARCHAR(4) NOT NULL DEFAULT 'any',
    submit_link_label VARCHAR(255) NULL,
    submit_text TEXT NOT NULL,
    submit_text_html TEXT NULL,
    submit_text_label VARCHAR(255) NULL,
    subreddit_type VARCHAR(16) NOT NULL DEFAULT 'public',
    subscribers INT UNSIGNED NOT NULL DEFAULT 0,
    suggested_comment_sort VARCHAR(16) NULL,
    title VARCHAR(255) NULL,
    url VARCHAR(32) NOT NULL,
    user_can_flair_in_sr BIT(1) NULL,
    user_flair_background_color VARCHAR(8) NULL,
    user_flair_css_class VARCHAR(48) NULL,
    user_flair_enabled_in_sr BIT(1) NOT NULL DEFAULT 0,
    user_flair_position VARCHAR(8) NOT NULL,
    user_flair_richtext TEXT NOT NULL,
    user_flair_template_id VARCHAR(48) NULL,
    user_flair_text TEXT NULL,
    user_flair_text_color VARCHAR(8) NULL,
    user_flair_type VARCHAR(12) NOT NULL,
    user_has_favorited BIT(1) NOT NULL DEFAULT 0,
    user_is_banned BIT(1) NOT NULL DEFAULT 0,
    user_is_contributor BIT(1) NOT NULL DEFAULT 0,
    user_is_moderator BIT(1) NOT NULL DEFAULT 0,
    user_is_muted BIT(1) NOT NULL DEFAULT 0,
    user_is_subscriber BIT(1) NOT NULL DEFAULT 0,
    user_sr_flair_enabled BIT(1) NULL,
    user_sr_theme_enabled BIT(1) NOT NULL DEFAULT 0,
    videostream_links_count INT UNSIGNED NULL,
    whitelist_status VARCHAR(24) NULL,
    wiki_enabled BIT(1) NULL,
    wls SMALLINT UNSIGNED NULL,
    PRIMARY KEY(id),
    INDEX `ix_display_name`(`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
CREATE TABLE `links`(
    `id` VARCHAR(16) NOT NULL,
    `created` INTEGER NOT NULL,
    `created_utc` INTEGER NOT NULL,
    `all_awardings` TEXT NULL,
    `allow_live_comments` BIT(1) NOT NULL DEFAULT 0,
    `approved` BIT(1) NULL DEFAULT 0,
    `approved_at_utc` INTEGER NULL,
    `approved_by` VARCHAR(32) NULL,
    `archived` BIT(1) NOT NULL DEFAULT 0,
    `author` VARCHAR(32) NOT NULL,
    `author_cakeday` BIT(1) NULL,
    `author_flair_background_color` VARCHAR(12) NULL,
    `author_flair_css_class` VARCHAR(128) NULL,
    `author_flair_richtext` TEXT NULL,
    `author_flair_template_id` VARCHAR(36) NULL,
    `author_flair_text` TEXT NULL,
    `author_flair_text_color` VARCHAR(8) NULL,
    `author_flair_type` VARCHAR(12) NOT NULL,
    `author_fullname` VARCHAR(16) NOT NULL,
    `author_patreon_flair` BIT(1) NOT NULL DEFAULT 0,
    `banned_at_utc` INTEGER NULL,
    `banned_by` VARCHAR(32) NULL,
    `can_gild` BIT(1) NOT NULL DEFAULT 1,
    `can_mod_post` BIT(1) NOT NULL DEFAULT 0,
    `category` VARCHAR(32) NULL,
    `clicked` BIT(1) NOT NULL DEFAULT 0,
    `collections` TEXT NULL,
    `content_categories` VARCHAR(64) NULL,
    `contest_mode` BIT(1) NOT NULL DEFAULT 0,
    `crosspost_parent` VARCHAR(16) NULL,
    `crosspost_parent_list` MEDIUMTEXT NULL,
    `discussion_type` VARCHAR(8) NULL,
    `distinguished` VARCHAR(32) NULL,
    `domain` VARCHAR(255) NOT NULL,
    `downs` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
    `edited` INTEGER NOT NULL DEFAULT 0,
    `event_end` INTEGER NULL,
    `event_is_live` BIT(1) NULL,
    `event_start` INTEGER NULL,
    `gilded` SMALLINT NOT NULL DEFAULT 0,
    `gildings` TEXT NOT NULL,
    `hidden` BIT(1) NOT NULL DEFAULT 0,
    `hide_score` BIT(1) NOT NULL DEFAULT 0,
    `ignore_reports` BIT(1) NULL DEFAULT 0,
    `is_crosspostable` BIT(1) NOT NULL DEFAULT 1,
    `is_meta` BIT(1) NOT NULL DEFAULT 0,
    `is_original_content` BIT(1) NOT NULL DEFAULT 0,
    `is_reddit_media_domain` BIT(1) NOT NULL DEFAULT 0,
    `is_robot_indexable` BIT(1) NOT NULL DEFAULT 0,
    `is_self` BIT(1) NOT NULL DEFAULT 0,
    `is_video` BIT(1) NOT NULL DEFAULT 0,
    `likes` BIT(1) NULL,
    `link_flair_background_color` VARCHAR(8) NOT NULL,
    `link_flair_css_class` VARCHAR(64) NULL,
    `link_flair_richtext` TEXT NULL,
    `link_flair_template_id` VARCHAR(36),
    `link_flair_text` TEXT NULL,
    `link_flair_text_color` VARCHAR(8) NOT NULL,
    `link_flair_type` VARCHAR(8) NOT NULL,
    `locked` BIT(1) NOT NULL DEFAULT 0,
    `media` TEXT NULL,
    `media_embed` TEXT NULL,
    `media_metadata` TEXT NULL,
    `media_only` BIT(1) NOT NULL DEFAULT 0,
    `mod_note` TEXT NULL,
    `mod_reason_by` TEXT NULL,
    `mod_reason_title` TEXT NULL,
    `mod_reports` TEXT NULL,
    `name` VARCHAR(16) NOT NULL,
    `no_follow` BIT(1) NOT NULL DEFAULT 0,
    `num_comments` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `num_crossposts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `num_reports` SMALLINT UNSIGNED NULL,
    `over_18` BIT(1) NOT NULL DEFAULT 0,
    `parent_whitelist_status` VARCHAR(24) NOT NULL,
    `permalink` VARCHAR(255) NOT NULL,
    `pinned` BIT(1) NOT NULL DEFAULT 0,
    `post_hint` VARCHAR(64) NULL,
    `preview` TEXT NULL,
    `pwls` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `quarantine` BIT(1) NOT NULL DEFAULT 0,
    `removal_reason` TEXT NULL,
    `removed` BIT(1) NULL DEFAULT 0,
    `report_reasons` TEXT NULL,
    `rte_mode` VARCHAR(24) NULL,
    `saved` BIT(1) NOT NULL DEFAULT 0,
    `score` MEDIUMINT NOT NULL DEFAULT 0,
    `secure_media` TEXT NULL,
    `secure_media_embed` TEXT NULL,
    `selftext` MEDIUMTEXT NULL,
    `selftext_html` MEDIUMTEXT NULL,
    `send_replies` BIT(1) NOT NULL DEFAULT 0,
    `spam` BIT(1) NULL DEFAULT 0,
    `spoiler` BIT(1) NOT NULL DEFAULT 0,
    `stickied` BIT(1) NOT NULL DEFAULT 0,
    `subreddit` VARCHAR(24) NOT NULL,
    `subreddit_id` VARCHAR(16) NOT NULL,
    `subreddit_name_prefixed` VARCHAR(24) NOT NULL,
    `subreddit_subscribers` INTEGER NOT NULL DEFAULT 0,
    `subreddit_type` VARCHAR(24) NULL,
    `suggested_sort` VARCHAR(24) NULL,
    `thumbnail` VARCHAR(255) NOT NULL,
    `thumbnail_height` SMALLINT UNSIGNED NULL,
    `thumbnail_width` SMALLINT UNSIGNED NULL,
    `title` VARCHAR(1024) NOT NULL,
    `total_awards_received` SMALLINT NOT NULL DEFAULT 0,
    `ups` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
    `url` TEXT NOT NULL, /* observed 2356 (!) in t3_clsftk */
    `user_reports` TEXT NULL,
    `view_count` INTEGER UNSIGNED NULL,
    `visited` BIT(1) NOT NULL DEFAULT 0,
    `whitelist_status` VARCHAR(24) NOT NULL,
    `wls` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY(id),
    INDEX `ix_created`(`created`),
    INDEX `ix_subreddit`(`subreddit`),
    INDEX `ix_subreddit_id`(`subreddit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
