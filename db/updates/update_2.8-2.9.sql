ALTER TABLE `ingredients` ADD `reach` VARCHAR(255) NULL AFTER `cas`; 
ALTER TABLE `formulas` ADD `exclude_from_summary` INT NOT NULL DEFAULT '0' AFTER `notes`; 
