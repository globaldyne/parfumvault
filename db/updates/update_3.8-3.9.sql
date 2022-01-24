ALTER TABLE `formulasMetaData` ADD `isMade` INT NOT NULL DEFAULT '0' AFTER `finalType`, ADD `madeOn` DATETIME NULL DEFAULT NULL AFTER `isMade`;
ALTER TABLE `formulaCategories` ADD `colorKey` VARCHAR(255) NULL DEFAULT NULL AFTER `type`;
ALTER TABLE `suppliers` CHANGE `manufactured` `purchased` DATE NULL DEFAULT NULL;
ALTER TABLE `formulas` ADD `exclude_from_calculation` INT NOT NULL DEFAULT '0' AFTER `exclude_from_summary`;
