SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `allergens`;
CREATE TABLE `allergens` (
  `id` int(11) NOT NULL,
  `ing` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `cas` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `percentage` varchar(255) COLLATE utf8_bin  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `batchIDHistory`;
CREATE TABLE `batchIDHistory` (
  `id` varchar(50) COLLATE utf8_bin NOT NULL,
  `fid` varchar(255) COLLATE utf8_bin NOT NULL,
  `pdf` varchar(255) COLLATE utf8_bin NOT NULL,
  `created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(225) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `web` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `formulas`;
CREATE TABLE `formulas` (
  `id` int(11) NOT NULL,
  `fid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ingredient` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ingredient_id` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `concentration` decimal(5,2) DEFAULT 100.00,
  `dilutant` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` decimal(8,3) DEFAULT NULL,
  `exclude_from_summary` INT NOT NULL DEFAULT '0', 
  `notes` varchar(11) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `makeFormula`;
CREATE TABLE `makeFormula` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `ingredient` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `quantity` decimal(8,2) DEFAULT NULL,
 `toAdd` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `cart` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `quantity` varchar(255) COLLATE utf8_bin NOT NULL,
 `purity` varchar(255) COLLATE utf8_bin NOT NULL,
 `ingID` INT NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `formulasMetaData`;
CREATE TABLE `formulasMetaData` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `product_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `fid` varchar(255) COLLATE utf8_bin NOT NULL,
  `profile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sex` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `isProtected` INT NULL DEFAULT '0',
  `defView` INT NULL DEFAULT '1'
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
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `image` LONGBLOB NULL
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
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `INCI` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `strength` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `purity` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `cas` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `reach` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `FEMA` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `SDS` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `tenacity` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `chemical_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `formula` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `flash_point` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `appearance` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ml` FLOAT(5) NULL DEFAULT '10',
  `solvent` VARCHAR(255) DEFAULT NULL, 
  `odor` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `allergen` int(11) DEFAULT NULL,
  `flavor_use` int(10) DEFAULT NULL,
  `soluble` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `logp` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat1` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat2` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat3` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat4` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat5A` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat5B` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat5C` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat5D` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat6` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat7A` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat7B` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat8` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat9` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat10A` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat10B` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat11A` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat11B` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cat12` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `manufacturer` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `impact_top` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `impact_heart` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `impact_base` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `usage_type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `noUsageLimit` INT NULL DEFAULT '0',  
  `isPrivate` INT NULL DEFAULT '0',
  `molecularWeight` VARCHAR(255) NULL,
  `physical_state` INT NULL DEFAULT '0',
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `platform` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price_tag_start` text COLLATE utf8_bin DEFAULT NULL,
  `price_tag_end` text COLLATE utf8_bin DEFAULT NULL,
  `add_costs` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price_per_size` INT NOT NULL DEFAULT '0', 
  `notes` text COLLATE utf8_bin NOT NULL,
  `min_ml` INT NOT NULL DEFAULT '0', 
  `min_gr` INT NOT NULL DEFAULT '0'
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
(5, 'Carrier'),
(6, 'Solvent');

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

DROP TABLE IF EXISTS `pv_meta`;
CREATE TABLE `pv_meta` (
  `id` int(11) NOT NULL,
  `schema_ver` varchar(255) COLLATE utf8_bin NOT NULL,
  `app_ver` varchar(255) COLLATE utf8_bin NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `pv_meta` (`id`, `schema_ver`, `app_ver`, `updated_at`) VALUES
(1, '2.0.6', '2.0.6', '2020-07-30 07:53:35');

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
  `Parfum` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `chem_vs_brand` int(11) NOT NULL,
  `grp_formula` int(11) DEFAULT NULL,
  `brandName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `brandAddress` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `brandEmail` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `brandPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `brandLogo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `pubChem` int(11) DEFAULT NULL,
  `chkVersion` int(11) DEFAULT NULL,
  `qStep` INT(5) NOT NULL DEFAULT '2',
  `pubchem_view` VARCHAR(4) NOT NULL DEFAULT '2d',
  `mUnit` VARCHAR(10) NOT NULL DEFAULT 'ml',
  `multi_dim_perc` INT NOT NULL DEFAULT '0', 
  `defCatClass` VARCHAR(255) NOT NULL DEFAULT 'cat4' 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `settings` (`id`, `label_printer_addr`, `label_printer_model`, `label_printer_size`, `label_printer_font_size`, `currency`, `top_n`, `heart_n`, `base_n`, `EDP`, `EDT`, `EDC`, `Parfum`, `chem_vs_brand`, `grp_formula`, `brandName`, `brandAddress`, `brandEmail`, `brandPhone`, `brandLogo`) VALUES
(1, '1.2.3.4', 'QL-810W', '12', 70, '&pound;', '25', '50', '25', '20', '15', '4', '30', 0, 1, 'My Brand Name', 'My Address', 'info@mybrand.com', '123456', NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `fullName` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `avatar` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

CREATE TABLE `pv_online` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(255) COLLATE utf8_bin NOT NULL,
 `password` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `IFRACategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `type` int(11) NOT NULL COMMENT '1=Standard, 2=Custom',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `IFRACategories` (`id`, `name`, `description`, `type`) VALUES
(1, '1', 'Toys, Lip products of all types (solid and liquid lipsticks, balms, clear or colored, etc).', 1),
(2, '2', 'Deodorant and antiperspirant products of all types (sprays, roll-on, stick, under-arm and body, etc).', 1),
(3, '3', 'Hydroalcoholic products (EdT and fine fragrance range) applied to recently shaved skin (3A and 3B), Eye products of all types including eye cream, mens facial creams and balms (3C), and tampons (3D).', 1),
(4, '4', 'Hydroalcoholic products (including scent strips) (EdT and fine fragrance range) applied to unshaved skin, ingredients of perfume kits, hair styling aids and hair sprays, hair deodorants, body creams, foot care products.', 1),
(5, '5A', 'Body lotion products applied to the body using the hands (palms), primarily leave-on', 1),
(6, '5B', 'Face moisturizer products applied to the face using the hands (palms), primarily leave-on', 1),
(7, '5C', 'Hand cream products applied to the hands using the hands (palms), primarily leave-on', 1),
(8, '5D', 'Baby Creams, baby Oils and baby talc', 1),
(9, '6', 'Products with oral and lip exposure', 1),
(10, '7A', 'Rinse-off products applied to the hair with some hand contact', 1),
(11, '7B', 'Leave-on products applied to the hair with some hand contact', 1),
(12, '8', 'Products with significant anogenital exposure', 1),
(13, '9', 'Products with body and hand exposure, primarily rinse off', 1),
(14, '10A', 'Household care excluding aerosol products (excluding aerosol/spray products)', 1),
(15, '10B', 'Household aerosol/spray products', 1),
(16, '11A', 'Products with intended skin contact but minimal transfer of fragrance to skin from inert substrate without UV exposure', 1),
(17, '11B', 'Products with intended skin contact but minimal transfer of fragrance to skin from inert substrate with potential UV exposure', 1),
(18, '12', 'Products not intended for direct skin contact, minimal or insignificant transfer to skin', 1);

CREATE TABLE `suppliers` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingSupplierID` int(11) NOT NULL,
 `ingID` int(11) NOT NULL,
 `supplierLink` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `price` varchar(10) COLLATE utf8_bin DEFAULT NULL,
 `size` float DEFAULT 10,
 `manufacturer` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `preferred` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `batchIDHistory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bottles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `customers`
  ADD UNIQUE KEY `id` (`id`);

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

ALTER TABLE `pv_meta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `allergens`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bottles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers`
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

ALTER TABLE `pv_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `allergens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
