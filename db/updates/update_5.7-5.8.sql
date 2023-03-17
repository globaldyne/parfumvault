ALTER TABLE `makeFormula` CHANGE `quantity` `quantity` DECIMAL(8,4) NULL DEFAULT NULL;
ALTER TABLE `makeFormula` ADD `overdose` DOUBLE(8,4) NOT NULL DEFAULT '0' AFTER `quantity`;
ALTER TABLE `makeFormula` ADD `originalQuantity` DECIMAL(8,4) NOT NULL AFTER `overdose`;
ALTER TABLE `makeFormula` ADD `ingredient_id` INT NOT NULL AFTER `ingredient`;

ALTER TABLE `formulasMetaData` ADD `schedulledOn` DATETIME NULL DEFAULT NULL AFTER `madeOn`; 

ALTER TABLE `formulas` ADD `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `notes`, ADD `updated` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created`; 
  
ALTER TABLE `formulas` CHANGE `fid` `fid` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `ingredient` `ingredient` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `ingredient_id` `ingredient_id` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `dilutant` `dilutant` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `notes` `notes` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;