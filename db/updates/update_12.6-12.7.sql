ALTER TABLE `ingredients` ADD `aromaTrackID` VARCHAR(255) NULL DEFAULT NULL AFTER `shelf_life`; 
ALTER TABLE `ingSuppliers` ADD `currency` VARCHAR(255) NULL DEFAULT NULL AFTER `country`; 
