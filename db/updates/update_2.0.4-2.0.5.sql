CREATE TABLE `pv_meta` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `schema_ver` varchar(255) COLLATE utf8_bin NOT NULL,
 `app_ver` varchar(255) COLLATE utf8_bin NOT NULL,
 `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin