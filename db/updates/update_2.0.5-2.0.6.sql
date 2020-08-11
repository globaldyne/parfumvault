ALTER TABLE `ingredients` ADD COLUMN FEMA VARCHAR(50) NULL AFTER cas;
ALTER TABLE `ingredients` ADD `flavor_use` INT(11) NULL DEFAULT '0';
ALTER TABLE `ingredients` ADD `cat1` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat2` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat3` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat4` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat5A` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat5B` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat5C` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat5D` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat6` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat7A` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat7B` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat8` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat9` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat10A` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat10B` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat11A` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat11B` varchar(255) DEFAULT NULL;
ALTER TABLE `ingredients` ADD `cat12` varchar(255) DEFAULT NULL;
ALTER TABLE `settings` ADD `pubChem` INT(11) DEFAULT NULL;

CREATE TABLE `makeFormula` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `ingredient` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `quantity` decimal(8,2) DEFAULT NULL,
 `toAdd` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin