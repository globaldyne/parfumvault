ALTER TABLE `documents` ADD `isSDS` INT NOT NULL DEFAULT '0' AFTER `isBatch`; 
CREATE TABLE `sds_data` ( `id` INT NOT NULL AUTO_INCREMENT , `product_name` VARCHAR(255) NOT NULL , `product_use` VARCHAR(255) NOT NULL , `country` VARCHAR(255) NOT NULL DEFAULT 'United Kingdom' , `language` VARCHAR(255) NOT NULL DEFAULT 'English' , `product_type` VARCHAR(255) NOT NULL DEFAULT 'Substance' , `state_type` VARCHAR(255) NOT NULL DEFAULT 'Liquid' , `supplier_id` INT NOT NULL , `docID` INT NOT NULL, `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 
ALTER TABLE `ingredient_compounds` CHANGE `percentage` `min_percentage` DECIMAL(8,4) NOT NULL; 
ALTER TABLE `ingredient_compounds` ADD `max_percentage` DECIMAL(8,4) NOT NULL AFTER `min_percentage`;
ALTER TABLE `settings` ADD `defPercentage` VARCHAR(255) NOT NULL DEFAULT 'max_percentage' AFTER `defCatClass`; 
