ALTER TABLE `makeFormula` CHANGE `quantity` `quantity` DECIMAL(8,4) NULL DEFAULT NULL;
ALTER TABLE `makeFormula` ADD `overdose` DOUBLE(8,4) NOT NULL DEFAULT '0' AFTER `quantity`;
ALTER TABLE `makeFormula` ADD `originalQuantity` DECIMAL(8,4) NOT NULL AFTER `overdose`;