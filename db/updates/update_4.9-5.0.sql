CREATE TABLE `ingReplacements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ing_name` varchar(255) NOT NULL,
  `ing_cas` varchar(255) NOT NULL,
  `ing_rep_name` varchar(255) NOT NULL,
  `ing_rep_cas` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;