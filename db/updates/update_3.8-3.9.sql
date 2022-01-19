ALTER TABLE `formulasMetaData` ADD `isMade` INT NOT NULL DEFAULT '0' AFTER `finalType`, ADD `madeOn` DATETIME NULL DEFAULT NULL AFTER `isMade`;
ALTER TABLE `formulaCategories` ADD `colorKey` VARCHAR(255) NULL DEFAULT NULL AFTER `type`;
