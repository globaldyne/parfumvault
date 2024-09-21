ALTER TABLE `settings` ADD `temp_sys` VARCHAR(255) NOT NULL DEFAULT '°C' AFTER `bs_theme`; 
ALTER TABLE `ingredients` ADD `shelf_life` INT NOT NULL DEFAULT '0' AFTER `cid`; 