UPDATE `settings` SET `pv_online_api_url` = 'https://online.perfumersvault.com/api-data/api.php' WHERE `settings`.`id` = 1;
ALTER TABLE `synonyms` ADD `created_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `source`; 
