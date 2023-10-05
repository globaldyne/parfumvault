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
ALTER TABLE `ingCategory` ADD `colorKey` VARCHAR(255) NULL AFTER `image`; 
INSERT INTO `ingTypes` (`id`, `name`) VALUES (NULL, 'Base');

CREATE TABLE IF NOT EXISTS `colorKey` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `rgb` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `colorKey` (`id`, `name`, `rgb`) VALUES
(1, 'Cyan', '0, 255, 255, 0.8'),
(2, 'Azure', '240, 255, 255, 0.8'),
(3, 'Beige', '245, 245, 220, 0.8'),
(4, 'Brown', '165, 42, 42, 0.8'),
(5, 'Black', '0, 0, 0, 0.8'),
(6, 'Blue', '0, 0, 255, 0.8'),
(7, 'Dark Blue', '0, 0, 139, 0.8'),
(8, 'Dark Cyan', '0, 139, 139, 0.8'),
(9, 'Dark Green', '0, 100, 0, 0.8'),
(10, 'Dark Grey', '169, 169, 169, 0.8'),
(11, 'Dark Khaki', '189, 183, 107, 0.8'),
(12, 'Dark Orange', '255, 140, 0, 0.8'),
(13, 'Dark Orchid', '153, 50, 204, 0.8'),
(14, 'Dark Salmon', '233, 150, 122, 0.8'),
(15, 'Magenta', '255, 0, 255, 0.8'),
(16, 'Gold', '255, 215, 0, 0.8'),
(17, 'Green', '109, 135, 59, 0.8'),
(18, 'Khaki', '240, 230, 140, 0.8'),
(19, 'Light Blue', '173, 216, 230, 0.8'),
(20, 'Light Cyan', '224, 255, 255, 0.8'),
(21, 'Light Grey', '211, 211, 211, 0.8'),
(22, 'Light Green', '144, 238, 144, 0.8'),
(23, 'Light Pink', '255, 182, 193, 0.8'),
(24, 'Light Yellow', '255, 255, 224, 0.8'),
(25, 'Lime', '0, 255, 0, 0.8'),
(26, 'Navy', '0, 0, 128, 0.8'),
(27, 'Purple', '128, 0, 128, 0.8'),
(28, 'Olive', '128, 128, 0, 0.8'),
(29, 'Orange', '255, 165, 0, 0.8'),
(30, 'Red', '255, 0, 0, 0.8'),
(31, 'Pink', '255, 192, 203, 0.8'),
(32, 'Silver', '192, 192, 192, 0.8'),
(33, 'Yellow', '255, 255, 0, 0.8'),
(34, 'White', '255, 255, 255, 0.8'),
(35, 'Gourmand', '219, 184, 119, 0.8'),
(36, 'Oud', '173, 26, 26, 0.8'),
(37, 'Citrus', '222, 212, 31, 0.8'),
(38, 'Balsamic', '206, 169, 122, 0.8'),
(39, 'Spices', '228, 27, 24, 0.8'),
(40, 'Chypre', '199, 186, 171, 0.8'),
(41, 'Caramel', '217, 62, 21, 0.8'),
(42, 'Coffee', '31, 9, 10, 0.8'),
(43, 'Vanilla', '217, 183, 117, 0.8'),
(44, 'Leathery', '117, 92, 82, 0.8'),
(45, 'Flowery Notes', '40, 130, 185, 0.8'),
(46, 'Ambery', '224, 162, 121, 0.8'),
(47, 'Animalic', '89, 75, 69, 0.8'),
(48, 'Mint', '69, 172, 52, 0.8'),
(49, 'Mossy', '22, 74, 9, 0.8'),
(50, 'Aromatic', '180, 214, 149, 0.8'),
(51, 'Aldehydic', '126, 174, 191, 0.8'),
(52, 'Woody', '92, 60, 9, 0.8'),
(53, 'Alcohol', '178, 52, 16, 0.8'),
(54, 'Tea', '189, 214, 132, 0.8'),
(55, 'Fruity', '240, 132, 8, 0.8'),
(56, 'Sweet', '136, 136, 136, 0.8');

ALTER TABLE `colorKey`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `settings` ADD `defIngView` INT NOT NULL DEFAULT '1' AFTER `defCatClass`; 
 