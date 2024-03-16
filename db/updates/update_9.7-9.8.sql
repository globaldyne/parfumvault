ALTER TABLE `IFRALibrary` DROP INDEX `id_2`;
ALTER TABLE `IFRALibrary` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `formula_history` CHANGE `fid` `fid` VARCHAR(255) NOT NULL; 
ALTER TABLE `backup_provider` ADD `schedule` TIME NOT NULL DEFAULT '00:00' AFTER `provider`; 
ALTER TABLE `backup_provider` ADD `description` VARCHAR(255) NOT NULL AFTER `enabled`; 
ALTER TABLE `settings` ADD `bk_srv_host` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_pref_eng`; 
ALTER TABLE `backup_provider` ADD `gdrive_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pvault' AFTER `description`; 
