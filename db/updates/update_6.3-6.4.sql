ALTER TABLE `users` ADD `pvToken` VARCHAR(255) NOT NULL AFTER `password`; 
ALTER TABLE `users` ADD `avatar` VARCHAR(255) NOT NULL AFTER `fullName`; 