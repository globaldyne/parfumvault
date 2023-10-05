CREATE TABLE `formula_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` int(11) NOT NULL,
 `change_made` text COLLATE utf8_bin NOT NULL,
 `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 `user` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

