/**
 * These DDL statements implement the changes made to the Snuze MySQL schema in
 * version: 1000801 (Snuze 0.8.1).
 */
--
ALTER TABLE `links`
ADD COLUMN `steward_reports` TEXT NULL AFTER `spoiler`;
--
ALTER TABLE `subreddits`
ADD COLUMN `is_crosspostable_subreddit` BIT(1) NOT NULL DEFAULT 0 AFTER `icon_size`;
--
UPDATE `snuze` SET `schema_version` = 1000801;
