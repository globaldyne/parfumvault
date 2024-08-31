ALTER TABLE `batchIDHistory` CHANGE `id` `id` VARCHAR(255) NOT NULL; 
ALTER TABLE `settings` ADD `currency_code` VARCHAR(255) NOT NULL DEFAULT 'GBP' AFTER `currency`; 
ALTER TABLE `settings` CHANGE `currency` `currency` VARCHAR(255) NOT NULL DEFAULT '£'; 