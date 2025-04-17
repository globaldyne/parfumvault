ALTER TABLE `ingredients` ADD `aromaTrackID` VARCHAR(255) NULL DEFAULT NULL AFTER `shelf_life`; 
ALTER TABLE `ingSuppliers` ADD `currency` VARCHAR(255) NULL DEFAULT NULL AFTER `country`; 
INSERT INTO `system_settings` (`key_name`, `value`, `slug`, `type`, `description`, `created_at`, `updated_at`) VALUES ('API_enabled', '0', 'API access', 'checkbox', 'Enable or disable API access globally', current_timestamp(), current_timestamp());
UPDATE `ingTypes` SET `name` = 'Other/Unknown' WHERE `ingTypes`.`name` = 'Other/Uknown';