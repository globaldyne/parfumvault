CREATE TABLE `documents` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ownerID` int(11) NOT NULL,
 `type` int(11) NOT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `notes` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `docData` longblob NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
