ALTER TABLE `ingredients` ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `cat12`; 
ALTER TABLE `ingredients` ADD `manufacturer` VARCHAR(255) NULL AFTER `cat12`; 
ALTER TABLE `cart` ADD `quantity` VARCHAR(255) NULL AFTER `name`; 
ALTER TABLE `ingredients` CHANGE `impact` `logp` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL; 
ALTER TABLE `ingredients` ADD `impact_top` VARCHAR(10) NULL AFTER `manufacturer`, ADD `impact_heart` VARCHAR(10) NULL AFTER `impact_top`, ADD `impact_base` VARCHAR(10) NULL AFTER `impact_heart`; 

CREATE TABLE `ingImpact` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingredient` varchar(255) COLLATE utf8_bin NOT NULL,
 `top` varchar(10) COLLATE utf8_bin NOT NULL,
 `heart` varchar(10) COLLATE utf8_bin NOT NULL,
 `base` varchar(10) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;