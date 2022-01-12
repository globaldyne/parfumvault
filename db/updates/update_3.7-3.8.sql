CREATE TABLE `formulaCategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `cname` varchar(255) COLLATE utf8_bin NOT NULL,
 `type` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `formulaCategories` (`id`, `name`, `cname`, `type`) VALUES (NULL, 'Oriental', 'oriental', 'profile'), (NULL, 'Woody', 'woody', 'profile'), (NULL, 'Floral', 'floral', 'profile'), (NULL, 'Fresh', 'fresh', 'profile'), (NULL, 'Unisex', 'unisex', 'sex'), (NULL, 'Men', 'men', 'sex'), (NULL, 'Women', 'women', 'sex');

ALTER TABLE `pv_online` ADD `enabled` INT NOT NULL DEFAULT '0' AFTER `password`;
