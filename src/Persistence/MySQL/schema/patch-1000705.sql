/**
 * These DDL statements implement the changes made to the Snuze MySQL schema in
 * version: 1000705 (Snuze 0.7.5).
 */
--
ALTER TABLE `subreddits` 
ADD COLUMN `coins` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 
AFTER `can_assign_user_flair`;
--
UPDATE `snuze` SET `schema_version` = 1000705;
