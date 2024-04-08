ALTER TABLE `makeFormula` ADD `notes` MEDIUMTEXT NULL AFTER `originalQuantity`; 
ALTER TABLE `makeFormula` ADD `skip` INT NOT NULL DEFAULT '0' AFTER `notes`; 
ALTER TABLE `makeFormula` ADD `replacement_id` INT NOT NULL DEFAULT '0' AFTER `ingredient_id`; 