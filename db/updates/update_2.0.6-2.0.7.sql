ALTER TABLE `settings` ADD `pv_maker` INT(11) DEFAULT 0;
ALTER TABLE `settings` ADD `pv_maker_host` varchar(255) COLLATE utf8_bin DEFAULT NULL;
ALTER TABLE `ingredients` ADD `soluble` VARCHAR(255) NULL AFTER `flavor_use`, ADD `impact` VARCHAR(255) NULL AFTER `soluble`; 

CREATE TABLE `allergens` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ing` varchar(255) COLLATE utf8_bin NOT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `cas` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `percentage` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
