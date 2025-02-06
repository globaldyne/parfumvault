ALTER TABLE `batchIDHistory` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `bottles` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `cart` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `customers` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `documents` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formulaCategories` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formulas` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formulasMetaData` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formulasRevisions` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formulasTags` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `formula_history` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `IFRALibrary` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingCategory` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingredients` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingredient_compounds` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingredient_safety_data` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingReplacements` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingSafetyInfo` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `ingSuppliers` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `inventory_accessories` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `inventory_compounds` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `makeFormula` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `perfumeTypes` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `sds_data` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `suppliers` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `synonyms` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 
ALTER TABLE `templates` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL ; 

ALTER TABLE `user_prefs` DROP INDEX `pref_name`;
ALTER TABLE `users` CHANGE `id` `id` VARCHAR(255) NOT NULL; 

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


CREATE TABLE `password_resets` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `email` varchar(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL , 
    `expiry` TIMESTAMP NOT NULL , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8mb3 COLLATE utf8_general_ci; 

DROP TABLE `settings`;
DROP TABLE `backup_provider`;

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


ALTER TABLE `ingredient_safety_data` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL; 
ALTER TABLE `ingredients` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` DROP INDEX `name`;
ALTER TABLE `ingSuppliers` CHANGE `notes` `notes` TEXT NULL; 

ALTER TABLE `ingredient_safety_data` DROP PRIMARY KEY;
ALTER TABLE `ingredient_safety_data` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 
ALTER TABLE `ingredient_safety_data` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`; 


ALTER TABLE `bottles` CHANGE `ml` `ml` DOUBLE NOT NULL; 
ALTER TABLE `bottles` CHANGE `height` `height` DOUBLE NULL DEFAULT 0; 
ALTER TABLE `inventory_accessories` DROP INDEX `name`;
ALTER TABLE `bottles` DROP INDEX `name`;


ALTER TABLE `customers` DROP INDEX `name`;

CREATE TABLE `session_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` varchar(255) NOT NULL,
  `remaining_time` decimal(10,2) NOT NULL,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `sdsSettings` ( 
  `id` INT NOT NULL AUTO_INCREMENT , 
  `sds_disclaimer` TEXT NOT NULL DEFAULT 'PLEASE ADD A PROPER DISCLAIMER MESSAGE' , 
  `owner_id` VARCHAR(255) NOT NULL , 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `formulasMetaData` ADD INDEX(`owner_id`);
ALTER TABLE `formulas` ADD INDEX(`owner_id`);
ALTER TABLE `formulas` ADD INDEX(`fid`);
ALTER TABLE `formulas` ADD INDEX(`ingredient`);
ALTER TABLE `ingredients` ADD INDEX(`owner_id`);
ALTER TABLE `ingredients` ADD INDEX(`name`);

