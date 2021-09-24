ALTER TABLE `settings` ADD `api` INT NOT NULL DEFAULT '0' AFTER `mUnit`, ADD `api_key` VARCHAR(255) NULL AFTER `api`; 
ALTER TABLE `ingredients` CHANGE `category` `category` INT(10) NOT NULL DEFAULT '1'; 
ALTER TABLE `ingredients` CHANGE `physical_state` `physical_state` INT NULL DEFAULT '1'; 
UPDATE ingredients SET cat1 = '100' WHERE cat1  REGEXP '[a-zA-Z]|^$|s+' OR cat1 IS NULL;
UPDATE ingredients SET cat2 = '100' WHERE cat2  REGEXP '[a-zA-Z]|^$|s+' OR cat2 IS NULL;
UPDATE ingredients SET cat3 = '100' WHERE cat3  REGEXP '[a-zA-Z]|^$|s+' OR cat3 IS NULL;
UPDATE ingredients SET cat4 = '100' WHERE cat4  REGEXP '[a-zA-Z]|^$|s+' OR cat4 IS NULL;
UPDATE ingredients SET cat5A = '100' WHERE cat5A  REGEXP '[a-zA-Z]|^$|s+' OR cat5A IS NULL;
UPDATE ingredients SET cat5B = '100' WHERE cat5B  REGEXP '[a-zA-Z]|^$|s+' OR cat5B IS NULL;
UPDATE ingredients SET cat5C = '100' WHERE cat5C  REGEXP '[a-zA-Z]|^$|s+' OR cat5C IS NULL;
UPDATE ingredients SET cat5D = '100' WHERE cat5D  REGEXP '[a-zA-Z]|^$|s+' OR cat5D IS NULL;
UPDATE ingredients SET cat6 = '100' WHERE cat6  REGEXP '[a-zA-Z]|^$|s+' OR cat6 IS NULL;
UPDATE ingredients SET cat7A = '100' WHERE cat7A  REGEXP '[a-zA-Z]|^$|s+' OR cat7A IS NULL;
UPDATE ingredients SET cat7B = '100' WHERE cat7B  REGEXP '[a-zA-Z]|^$|s+' OR cat7B IS NULL;
UPDATE ingredients SET cat8 = '100' WHERE cat8  REGEXP '[a-zA-Z]|^$|s+' OR cat8 IS NULL;
UPDATE ingredients SET cat9 = '100' WHERE cat9  REGEXP '[a-zA-Z]|^$|s+' OR cat9 IS NULL;
UPDATE ingredients SET cat10A = '100' WHERE cat10A  REGEXP '[a-zA-Z]|^$|s+' OR cat10A IS NULL;
UPDATE ingredients SET cat10B = '100' WHERE cat10B  REGEXP '[a-zA-Z]|^$|s+' OR cat10B IS NULL;
UPDATE ingredients SET cat11A = '100' WHERE cat11A  REGEXP '[a-zA-Z]|^$|s+' OR cat11A IS NULL;
UPDATE ingredients SET cat11B = '100' WHERE cat11B  REGEXP '[a-zA-Z]|^$|s+' OR cat11B IS NULL;
UPDATE ingredients SET cat12 = '100' WHERE cat12  REGEXP '[a-zA-Z]|^$|s+' OR cat12 IS NULL;

ALTER TABLE `ingredients` CHANGE `cat1` `cat1` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat2` `cat2` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat3` `cat3` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat4` `cat4` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat5A` `cat5A` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat5B` `cat5B` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat5C` `cat5C` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat5D` `cat5D` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat6` `cat6` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat7A` `cat7A` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat7B` `cat7B` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat8` `cat8` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat9` `cat9` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat10A` `cat10A` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat10B` `cat10B` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat11A` `cat11A` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat11B` `cat11B` DOUBLE NOT NULL DEFAULT '100', CHANGE `cat12` `cat12` DOUBLE NOT NULL DEFAULT '100';

UPDATE ingredients SET physical_state = '1' WHERE physical_state = '0';
