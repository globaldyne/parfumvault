ALTER TABLE `ingredients` DROP `IFRA`;
ALTER TABLE `settings` ADD `defCatClass` VARCHAR(255) NOT NULL DEFAULT 'cat4' AFTER `qStep`;