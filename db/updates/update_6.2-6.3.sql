ALTER TABLE `ingSuppliers` ADD `address` VARCHAR(255) NULL AFTER `name`, ADD `po` VARCHAR(255) NULL AFTER `address`, ADD `country` VARCHAR(255) NULL AFTER `po`, ADD `telephone` VARCHAR(255) NULL AFTER `country`, ADD `url` VARCHAR(255) NULL AFTER `telephone`, ADD `email` VARCHAR(255) NULL AFTER `url`;

ALTER TABLE `templates` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 
