ALTER TABLE `formulasMetaData` ADD `customer_id` INT NOT NULL DEFAULT '0' AFTER `madeOn`;
ALTER TABLE `ingredients` ADD `einecs` VARCHAR(255) NULL AFTER `cas`;
