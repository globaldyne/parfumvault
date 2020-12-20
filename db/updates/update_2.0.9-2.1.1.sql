ALTER TABLE `allergens` ADD PRIMARY KEY (`id`);
ALTER TABLE `allergens` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `allergens` CHANGE `percentage` `percentage` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NULL;
ALTER TABLE `ingredients` ADD `usage_type` VARCHAR(255) NULL AFTER `created`;
