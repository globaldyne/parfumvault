CREATE TABLE `user_prefs` ( 
	`pref_name` VARCHAR(255) NOT NULL,
	`pref_data` LONGTEXT NOT NULL,
	`created_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL,
	`owner` INT NOT NULL 
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 
ALTER TABLE `user_prefs` ADD UNIQUE (pref_name);
ALTER TABLE `settings` ADD `user_pref_eng` INT NOT NULL DEFAULT '1' AFTER `editor`;
