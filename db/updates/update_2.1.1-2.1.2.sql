ALTER TABLE `ingredients` ADD `solvent` VARCHAR(255) NULL DEFAULT NULL AFTER `ml`;
ALTER TABLE `ingredients` CHANGE `ml` `ml` FLOAT(5) NULL DEFAULT NULL;
ALTER TABLE `formulas` CHANGE `quantity` `quantity` DECIMAL(8,3) NULL DEFAULT NULL;
ALTER TABLE `settings` ADD `qStep` INT(5) NOT NULL DEFAULT '2' AFTER `pv_maker_host`;