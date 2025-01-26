ALTER TABLE `backup_provider` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `batchIDHistory` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `bottles` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `cart` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `customers` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `documents` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formulaCategories` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formulas` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formulasMetaData` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formulasRevisions` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formulasTags` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `formula_history` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `IFRALibrary` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingCategory` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingredients` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingredient_compounds` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingredient_safety_data` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingReplacements` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingSafetyInfo` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingSuppliers` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `inventory_accessories` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `inventory_compounds` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `makeFormula` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `perfumeTypes` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `sds_data` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `suppliers` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `synonyms` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 
ALTER TABLE `templates` CHANGE `owner_id` `owner_id` INT(11) NOT NULL DEFAULT '1'; 

ALTER TABLE `user_prefs` DROP INDEX `pref_name`;

UPDATE `backup_provider` SET `owner_id` = '1'; 
UPDATE `batchIDHistory` SET `owner_id` = '1'; 
UPDATE `bottles` SET `owner_id` = '1'; 
UPDATE `cart` SET `owner_id` = '1'; 
UPDATE `customers` SET `owner_id` = '1'; 
UPDATE `documents` SET `owner_id` = '1'; 
UPDATE `formulaCategories` SET `owner_id` = '1'; 
UPDATE `formulas` SET `owner_id` = '1'; 
UPDATE `formulasMetaData` SET `owner_id` = '1'; 
UPDATE `formulasRevisions` SET `owner_id` = '1'; 
UPDATE `formulasTags` SET `owner_id` = '1'; 
UPDATE `formula_history` SET `owner_id` = '1'; 
UPDATE `IFRALibrary` SET `owner_id` = '1'; 
UPDATE `ingCategory` SET `owner_id` = '1'; 
UPDATE `ingredients` SET `owner_id` = '1'; 
UPDATE `ingredient_compounds` SET `owner_id` = '1'; 
UPDATE `ingredient_safety_data` SET `owner_id` = '1'; 
UPDATE `ingReplacements` SET `owner_id` = '1'; 
UPDATE `ingSafetyInfo` SET `owner_id` = '1'; 
UPDATE `ingSuppliers` SET `owner_id` = '1'; 
UPDATE `inventory_accessories` SET `owner_id` = '1'; 
UPDATE `inventory_compounds` SET `owner_id` = '1'; 
UPDATE `makeFormula` SET `owner_id` = '1'; 
UPDATE `perfumeTypes` SET `owner_id` = '1'; 
UPDATE `sds_data` SET `owner_id` = '1'; 
UPDATE `suppliers` SET `owner_id` = '1'; 
UPDATE `synonyms` SET `owner_id` = '1'; 
UPDATE `templates` SET `owner_id` = '1'; 

CREATE TABLE `system_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `key_name` VARCHAR(255) NOT NULL ,
    `value` LONGTEXT NOT NULL , 
    `slug` VARCHAR(255) NOT NULL , 
    `type` VARCHAR(255) NOT NULL , 
    `description` VARCHAR(255) NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `system_settings` ( `key_name`, `value`, `slug`, `type`, `description`) VALUES
('SYSTEM_chkVersion', '1', 'Check for updates', 'checkbox', 'Check for updates'),
('SYSTEM_pubChem', '1', 'Enable PubChem', 'checkbox', 'Enable or disable pubChem integration'),
('SYSTEM_server_url', '', 'Server URL', 'text', 'This is your Perfumers Vault installation server URL.'),
('INTEGRATIONS_enable', '0', 'Enable integrations', 'checkbox', 'Enable or disable integrations'),
('USER_selfRegister', '0', 'Enable user registration', 'checkbox', 'Enable or disable user self registartion'),
('USER_terms_url', 'https://www.perfumersvault.com/terms-of-service', 'Terms and Conditions', 'text', 'Point this to your web site that hosts the terms and confitions info for users'),
('LIBRARY_enable', '1', 'Enanle PV Library', 'checkbox', 'Enable or disable PV Library'),
('LIBRARY_apiurl', 'https://library.perfumersvault.com/api-data/api.php', 'Library API URL', 'text', 'Library API URL'),
('announcements', '', 'Announcement', 'textarea', 'Add here any anouncement for your users when login'),
('EMAIL_isEnabled', '0', 'Enable email', 'checkbox', 'Enable or disable email functions, like user welcome email when register, password reset, email confirmartion etc'),
('EMAIL_smtp_host', '', 'SMPT Host', 'text', 'This is your smtp email server ip or hostname'),
('EMAIL_smtp_port', '', 'SMTP Port', 'text', 'Optional, Defaults to 25'),
('EMAIL_from', '', 'From', 'text', 'This is the From address'),
('EMAIL_from_display_name', 'Perfumers Vault', 'From display name', 'text', 'A user-friendly name for the \'From\' address (optional).'),
('EMAIL_smtp_user', '', 'Username', 'text', 'Optional field, use only if your email server requires authentication'),
('EMAIL_smtp_pass', '', 'Password', 'password', 'Optional field, use only if your email server requires authentication'),
('EMAIL_smtp_secure', '0', 'Enable SSL', 'checkbox', 'Enable secure connection if your server supports it');


CREATE TABLE `user_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `key_name` VARCHAR(255) NOT NULL ,
    `value` LONGTEXT NOT NULL , 
    `owner_id` INT NOT NULL, 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `password_resets` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `email` varchar(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL , 
    `expiry` TIMESTAMP NOT NULL , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8mb3 COLLATE utf8_general_ci; 

DROP TABLE `settings`;

CREATE TABLE `branding` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `brandName` VARCHAR(255) NULL , 
    `brandAddress` VARCHAR(255) NULL , 
    `brandEmail` VARCHAR(255) NULL , 
    `brandPhone` VARCHAR(255) NULL , 
    `brandLogo` LONGBLOB NULL , 
    `owner_id` INT NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `ingredient_safety_data` CHANGE `owner_id` `owner_id` INT(11) NOT NULL; 
ALTER TABLE `ingredients` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` CHANGE `notes` `notes` TEXT NULL; 

ALTER TABLE `ingredient_safety_data` DROP PRIMARY KEY;
ALTER TABLE `ingredient_safety_data` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 
ALTER TABLE `ingredient_safety_data` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`; 


ALTER TABLE `bottles` CHANGE `ml` `ml` DOUBLE NOT NULL; 
ALTER TABLE `bottles` CHANGE `height` `height` DOUBLE NULL DEFAULT 0; 
