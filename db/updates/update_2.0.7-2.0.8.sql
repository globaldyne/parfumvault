ALTER TABLE `ingredients` ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `cat12`; 
ALTER TABLE `ingredients` ADD `manufacturer` VARCHAR(255) NULL AFTER `cat12`; 

CREATE TABLE `pv_online` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(255) COLLATE utf8_bin NOT NULL,
 `password` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
