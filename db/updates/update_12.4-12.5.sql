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
    `value` VARCHAR(255) NOT NULL , 
    `slug` VARCHAR(255) NOT NULL , 
    `type` VARCHAR(255) NOT NULL , 
    `description` VARCHAR(255) NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB; 

INSERT INTO `system_settings` (`id`, `key_name`, `value`, `slug`, `type`, `description`) VALUES
(1, 'SMTP_host', '', 'SMTP Host', 'text', 'Configure SMTP host'),
(2, 'SSO_status', '0', 'Enable SSO', 'checkbox', 'Enable or disable SSO'),
(3, 'SSO_clientID', '', 'Client ID', 'text', 'Your SSO client ID'),
(4, 'SSO_clientSecret', '', 'Client Secret', 'password', 'Your SSO secret'),
(5, 'SSO_redirectUri', '', 'Redirect URI', 'text', 'SSO redirect URI'),
(6, 'SSO_authUrl', '', 'Auth URL', 'text', 'SSO auth URL'),
(7, 'SSO_tokenUrl', '', 'Token URL', 'text', 'SSO token url'),
(8, 'SSO_userInfoUrl', '', 'User Info URL', 'text', 'SSO info url');


ALTER TABLE `settings`
  DROP `api`,
  DROP `api_key`,
  DROP `brandName`,
  DROP `brandAddress`,
  DROP `brandEmail`,
  DROP `brandPhone`,
  DROP `brandLogo`
  DROP `sds_disclaimer`,
  DROP `pv_library_api_url`; 

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
) ENGINE = InnoDB; 

ALTER TABLE `ingredient_safety_data` CHANGE `owner_id` `owner_id` INT(11) NOT NULL; 
ALTER TABLE `ingredients` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` CHANGE `notes` `notes` TEXT NULL; 

ALTER TABLE `ingredient_safety_data` DROP PRIMARY KEY;
ALTER TABLE `ingredient_safety_data` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 
ALTER TABLE `ingredient_safety_data` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`; 


ALTER TABLE `bottles` CHANGE `ml` `ml` DOUBLE NOT NULL; 
ALTER TABLE `bottles` CHANGE `height` `height` DOUBLE NULL DEFAULT 0; 
