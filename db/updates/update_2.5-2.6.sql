ALTER TABLE `settings` ADD `pubchem_view` VARCHAR(4) NOT NULL DEFAULT '2d' AFTER `chkVersion`;
ALTER TABLE `ingredients` ADD `tradingName` VARCHAR(255) NULL AFTER `name`;
