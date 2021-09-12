UPDATE ingredients SET usage_type = '1' WHERE usage_type = '' OR usage_type = 'none';
ALTER TABLE `ingredients` CHANGE `INCI` `INCI` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; 
