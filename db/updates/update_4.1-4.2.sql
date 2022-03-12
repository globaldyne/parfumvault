DROP TABLE IF EXISTS `ingProfiles`;
CREATE TABLE `ingProfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingProfiles` (`id`, `name`, `notes`) VALUES
(1, 'Top', 'Top Note'),
(2, 'Heart', 'Heart Note'),
(3, 'Base', 'Base Note'),
(4, 'Solvent', 'Solvents and Carriers');

ALTER TABLE `ingProfiles` ADD UNIQUE KEY `id` (`id`);
