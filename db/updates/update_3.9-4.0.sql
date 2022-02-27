ALTER TABLE `formulasMetaData` ADD `customer_id` INT NOT NULL DEFAULT '0' AFTER `madeOn`;
ALTER TABLE `ingredients` ADD `einecs` VARCHAR(255) NULL AFTER `cas`;

CREATE TABLE `synonyms` ( `id` INT NOT NULL , `ing` VARCHAR(255) NOT NULL, `cid` INT(10) NULL DEFAULT NULL , `synonym` VARCHAR(255) NOT NULL , `source` VARCHAR(255) NULL DEFAULT NULL ) ENGINE = InnoDB;

ALTER TABLE `synonyms` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`); 
ALTER TABLE `formulasMetaData` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 
ALTER TABLE `formulasMetaData` CHANGE `product_name` `product_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
