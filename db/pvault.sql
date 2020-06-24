SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `bottles`;
CREATE TABLE `bottles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ml` varchar(255) COLLATE utf8_bin NOT NULL,
  `price` varchar(255) COLLATE utf8_bin NOT NULL,
  `height` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `width` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `diameter` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `formulas`;
CREATE TABLE `formulas` (
  `id` int(11) NOT NULL,
  `fid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ingredient` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ingredient_id` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `concentration` decimal(5,2) DEFAULT 100.00,
  `quantity` varchar(10) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `formulasMetaData`;
CREATE TABLE `formulasMetaData` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `fid` varchar(255) COLLATE utf8_bin NOT NULL,
  `profile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sex` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastUpdate` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `IFRALibrary`;
CREATE TABLE `IFRALibrary` (
  `id` int(11) NOT NULL,
  `ifra_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amendment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prev_pub` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_pub` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deadline_existing` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deadline_new` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cas` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cas_comment` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `synonyms` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flavor_use` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `prohibited_notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `restricted_photo_notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `restricted_notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `specified_notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `risk` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contrib_others` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `contrib_others_notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat5A` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat5B` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat5C` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat5D` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat6` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat7A` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat7B` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat8` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat9` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat10A` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat10B` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat11A` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat11B` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat12` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `ingCategory`;
CREATE TABLE `ingCategory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ingProfiles`;
CREATE TABLE `ingProfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ingProfiles` (`id`, `name`, `notes`) VALUES
(1, 'Top', 'Top Note'),
(2, 'Base', 'Base Note'),
(4, 'Heart', 'Heart Note');

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `strength` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `IFRA` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `purity` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `cas` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `SDS` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `tenacity` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `chemical_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `flash_point` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `appearance` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ml` int(5) DEFAULT NULL,
  `odor` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ingStrength`;
CREATE TABLE `ingStrength` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ingStrength` (`id`, `name`) VALUES
(1, 'Medium'),
(2, 'Low'),
(3, 'High');

DROP TABLE IF EXISTS `ingSuppliers`;
CREATE TABLE `ingSuppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ingTypes`;
CREATE TABLE `ingTypes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ingTypes` (`id`, `name`) VALUES
(1, 'AC'),
(2, 'EO'),
(3, 'Other/Uknown'),
(4, 'Custom Blend'),
(5, 'Carrier');

DROP TABLE IF EXISTS `lids`;
CREATE TABLE `lids` (
  `id` int(11) NOT NULL,
  `style` varchar(255) COLLATE utf8_bin NOT NULL,
  `colour` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `label_printer_addr` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_model` varchar(225) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_size` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_font_size` int(11) DEFAULT 80,
  `currency` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `top_n` varchar(10) COLLATE utf8_bin NOT NULL,
  `heart_n` varchar(10) COLLATE utf8_bin NOT NULL,
  `base_n` varchar(10) COLLATE utf8_bin NOT NULL,
  `EDP` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `EDT` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `EDC` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Parfum` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `settings` (`id`, `label_printer_addr`, `label_printer_model`, `label_printer_size`, `label_printer_font_size`, `currency`, `top_n`, `heart_n`, `base_n`, `EDP`, `EDT`, `EDC`, `Parfum`) VALUES
(1, '1.2.3.4', 'QL-810W', '12', 70, '&pound;', '25', '50', '25', '20', '15', '4', '30');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `fullName` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `avatar` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;


ALTER TABLE `bottles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `formulas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `formulasMetaData`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `IFRALibrary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

ALTER TABLE `ingCategory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ingProfiles`
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `ingStrength`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ingSuppliers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ingTypes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `lids`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `bottles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `formulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `formulasMetaData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `IFRALibrary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingCategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingProfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingStrength`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingSuppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingTypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
