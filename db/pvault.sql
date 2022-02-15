SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `allergens`;
CREATE TABLE `allergens` (
  `id` int(11) NOT NULL,
  `ing` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `cas` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `ec` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `percentage` DECIMAL(8,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `batchIDHistory`;
CREATE TABLE `batchIDHistory` (
  `id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `fid` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `pdf` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `bottles`;
CREATE TABLE `bottles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `ml` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `price` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `height` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `width` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `diameter` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(225) COLLATE utf8_general_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `web` varchar(255) COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `formulas`;
CREATE TABLE `formulas` (
  `id` int(11) NOT NULL,
  `fid` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `ingredient` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `ingredient_id` varchar(11) COLLATE utf8_general_ci DEFAULT NULL,
  `concentration` decimal(5,2) DEFAULT 100.00,
  `dilutant` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `quantity` decimal(8,4) DEFAULT NULL,
  `exclude_from_summary` INT NOT NULL DEFAULT '0', 
  `exclude_from_calculation` INT NOT NULL DEFAULT '0', 
  `notes` varchar(11) COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `makeFormula`;
CREATE TABLE `makeFormula` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `ingredient` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `quantity` decimal(8,2) DEFAULT NULL,
 `toAdd` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `cart` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `quantity` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `purity` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `ingID` INT NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `formulasMetaData`;
CREATE TABLE `formulasMetaData` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `fid` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `sex` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `isProtected` INT NULL DEFAULT '0',
  `defView` INT NULL DEFAULT '1',
  `catClass` VARCHAR(10) NULL,
  `revision` INT NOT NULL DEFAULT '0',
  `finalType` INT NOT NULL DEFAULT '100',
  `isMade` INT NOT NULL DEFAULT '0',
  `madeOn` DATETIME NULL DEFAULT NULL,
  `customer_id` INT NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL,
  `image` LONGBLOB NULL,
  `colorKey` VARCHAR(255) NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `ingProfiles`;
CREATE TABLE `ingProfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingProfiles` (`id`, `name`, `notes`) VALUES
(1, 'Top', 'Top Note'),
(2, 'Base', 'Base Note'),
(4, 'Heart', 'Heart Note');

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `INCI` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `strength` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `category` int(10) NOT NULL DEFAULT '1',
  `purity` varchar(11) COLLATE utf8_general_ci DEFAULT NULL,
  `cas` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `einecs` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `reach` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `FEMA` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `price` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
  `tenacity` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `chemical_name` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `flash_point` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `appearance` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8_general_ci DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `ml` FLOAT(5) NULL DEFAULT '10',
  `solvent` VARCHAR(255) DEFAULT NULL, 
  `odor` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `allergen` int(11) DEFAULT NULL,
  `flavor_use` int(10) DEFAULT NULL,
  `soluble` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `logp` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `cat1` double NOT NULL DEFAULT 100,
  `cat2` double NOT NULL DEFAULT 100,
  `cat3` double NOT NULL DEFAULT 100,
  `cat4` double NOT NULL DEFAULT 100,
  `cat5A` double NOT NULL DEFAULT 100,
  `cat5B` double NOT NULL DEFAULT 100,
  `cat5C` double NOT NULL DEFAULT 100,
  `cat5D` double NOT NULL DEFAULT 100,
  `cat6` double NOT NULL DEFAULT 100,
  `cat7A` double NOT NULL DEFAULT 100,
  `cat7B` double NOT NULL DEFAULT 100,
  `cat8` double NOT NULL DEFAULT 100,
  `cat9` double NOT NULL DEFAULT 100,
  `cat10A` double NOT NULL DEFAULT 100,
  `cat10B` double NOT NULL DEFAULT 100,
  `cat11A` double NOT NULL DEFAULT 100,
  `cat11B` double NOT NULL DEFAULT 100,
  `cat12` double NOT NULL DEFAULT 100,
  `manufacturer` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `impact_top` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
  `impact_heart` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
  `impact_base` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
  `usage_type` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `noUsageLimit` INT NULL DEFAULT '0',  
  `isPrivate` INT NULL DEFAULT '0',
  `molecularWeight` VARCHAR(255) NULL,
  `physical_state` INT NULL DEFAULT '1',
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `ingStrength`;
CREATE TABLE `ingStrength` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingStrength` (`id`, `name`) VALUES
(1, 'Medium'),
(2, 'Low'),
(3, 'High');

DROP TABLE IF EXISTS `ingSuppliers`;
CREATE TABLE `ingSuppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `platform` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `price_tag_start` text COLLATE utf8_general_ci DEFAULT NULL,
  `price_tag_end` text COLLATE utf8_general_ci DEFAULT NULL,
  `add_costs` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `price_per_size` INT NOT NULL DEFAULT '0', 
  `notes` text COLLATE utf8_general_ci NOT NULL,
  `min_ml` INT NOT NULL DEFAULT '0', 
  `min_gr` INT NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `ingTypes`;
CREATE TABLE `ingTypes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingTypes` (`id`, `name`) VALUES
(1, 'AC'),
(2, 'EO'),
(3, 'Other/Uknown'),
(4, 'Custom Blend'),
(5, 'Carrier'),
(6, 'Solvent'),
(7, 'Base');

DROP TABLE IF EXISTS `lids`;
CREATE TABLE `lids` (
  `id` int(11) NOT NULL,
  `style` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `colour` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `pv_meta`;
CREATE TABLE `pv_meta` (
  `id` int(11) NOT NULL,
  `schema_ver` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `app_ver` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `pv_meta` (`id`, `schema_ver`, `app_ver`, `updated_at`) VALUES
(1, '2.0.6', '2.0.6', '2020-07-30 07:53:35');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `label_printer_addr` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `label_printer_model` varchar(225) COLLATE utf8_general_ci DEFAULT NULL,
  `label_printer_size` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `label_printer_font_size` int(11) DEFAULT 80,
  `currency` varchar(40) COLLATE utf8_general_ci DEFAULT NULL,
  `top_n` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `heart_n` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `base_n` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `EDP` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `EDT` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `EDC` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `Parfum` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `chem_vs_brand` int(11) NOT NULL,
  `grp_formula` int(11) DEFAULT NULL,
  `brandName` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `brandAddress` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `brandEmail` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `brandPhone` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `brandLogo` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `pubChem` int(11) DEFAULT NULL,
  `chkVersion` int(11) DEFAULT NULL,
  `qStep` INT(5) NOT NULL DEFAULT '2',
  `pubchem_view` VARCHAR(4) NOT NULL DEFAULT '2d',
  `mUnit` VARCHAR(10) NOT NULL DEFAULT 'ml',
  `multi_dim_perc` INT NOT NULL DEFAULT '0', 
  `defCatClass` VARCHAR(255) NOT NULL DEFAULT 'cat4',
  `defIngView` INT NOT NULL DEFAULT '1', 
  `api` INT NOT NULL DEFAULT '0',
  `api_key` VARCHAR(255) NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `settings` (`id`, `label_printer_addr`, `label_printer_model`, `label_printer_size`, `label_printer_font_size`, `currency`, `top_n`, `heart_n`, `base_n`, `EDP`, `EDT`, `EDC`, `Parfum`, `chem_vs_brand`, `grp_formula`, `brandName`, `brandAddress`, `brandEmail`, `brandPhone`, `brandLogo`) VALUES
(1, '1.2.3.4', 'QL-810W', '12', 70, '&pound;', '25', '50', '25', '20', '15', '4', '30', 0, 1, 'My Brand Name', 'My Address', 'info@mybrand.com', '123456', NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `fullName` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

CREATE TABLE `pv_online` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `password` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `enabled` INT NOT NULL DEFAULT '0', 
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `IFRACategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `description` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `type` int(11) NOT NULL COMMENT '1=Standard, 2=Custom',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `colorKey` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `rgb` varchar(255) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


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
 `supplierLink` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `price` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
 `size` float DEFAULT 10,
 `manufacturer` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `preferred` int(11) NOT NULL DEFAULT 0,
 `batch` VARCHAR(255) NULL,
 `purchased` DATE NULL,
 `mUnit` VARCHAR(255) NULL, 
 `stock` INT NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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

CREATE TABLE `documents` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ownerID` int(11) NOT NULL,
 `type` int(11) NOT NULL,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `notes` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `docData` longblob NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `colorKey` (`id`, `name`, `rgb`) VALUES
(1, 'Cyan', '0, 255, 255, 0.8'),
(2, 'Azure', '240, 255, 255, 0.8'),
(3, 'Beige', '245, 245, 220, 0.8'),
(4, 'Brown', '165, 42, 42, 0.8'),
(5, 'Black', '0, 0, 0, 0.8'),
(6, 'Blue', '0, 0, 255, 0.8'),
(7, 'Dark Blue', '0, 0, 139, 0.8'),
(8, 'Dark Cyan', '0, 139, 139, 0.8'),
(9, 'Dark Green', '0, 100, 0, 0.8'),
(10, 'Dark Grey', '169, 169, 169, 0.8'),
(11, 'Dark Khaki', '189, 183, 107, 0.8'),
(12, 'Dark Orange', '255, 140, 0, 0.8'),
(13, 'Dark Orchid', '153, 50, 204, 0.8'),
(14, 'Dark Salmon', '233, 150, 122, 0.8'),
(15, 'Magenta', '255, 0, 255, 0.8'),
(16, 'Gold', '255, 215, 0, 0.8'),
(17, 'Green', '109, 135, 59, 0.8'),
(18, 'Khaki', '240, 230, 140, 0.8'),
(19, 'Light Blue', '173, 216, 230, 0.8'),
(20, 'Light Cyan', '224, 255, 255, 0.8'),
(21, 'Light Grey', '211, 211, 211, 0.8'),
(22, 'Light Green', '144, 238, 144, 0.8'),
(23, 'Light Pink', '255, 182, 193, 0.8'),
(24, 'Light Yellow', '255, 255, 224, 0.8'),
(25, 'Lime', '0, 255, 0, 0.8'),
(26, 'Navy', '0, 0, 128, 0.8'),
(27, 'Purple', '128, 0, 128, 0.8'),
(28, 'Olive', '128, 128, 0, 0.8'),
(29, 'Orange', '255, 165, 0, 0.8'),
(30, 'Red', '255, 0, 0, 0.8'),
(31, 'Pink', '255, 192, 203, 0.8'),
(32, 'Silver', '192, 192, 192, 0.8'),
(33, 'Yellow', '255, 255, 0, 0.8'),
(34, 'White', '255, 255, 255, 0.8'),
(35, 'Gourmand', '219, 184, 119, 0.8'),
(36, 'Oud', '173, 26, 26, 0.8'),
(37, 'Citrus', '222, 212, 31, 0.8'),
(38, 'Balsamic', '206, 169, 122, 0.8'),
(39, 'Spices', '228, 27, 24, 0.8'),
(40, 'Chypre', '199, 186, 171, 0.8'),
(41, 'Caramel', '217, 62, 21, 0.8'),
(42, 'Coffee', '31, 9, 10, 0.8'),
(43, 'Vanilla', '217, 183, 117, 0.8'),
(44, 'Leathery', '117, 92, 82, 0.8'),
(45, 'Flowery Notes', '40, 130, 185, 0.8'),
(46, 'Ambery', '224, 162, 121, 0.8'),
(47, 'Animalic', '89, 75, 69, 0.8'),
(48, 'Mint', '69, 172, 52, 0.8'),
(49, 'Mossy', '22, 74, 9, 0.8'),
(50, 'Aromatic', '180, 214, 149, 0.8'),
(51, 'Aldehydic', '126, 174, 191, 0.8'),
(52, 'Woody', '92, 60, 9, 0.8'),
(53, 'Alcohol', '178, 52, 16, 0.8'),
(54, 'Tea', '189, 214, 132, 0.8'),
(55, 'Fruity', '240, 132, 8, 0.8'),
(56, 'Sweet', '136, 136, 136, 0.8');

ALTER TABLE `colorKey`
  ADD PRIMARY KEY (`id`);
  
CREATE TABLE `ingSafetyInfo` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingID` int(11) NOT NULL,
 `GHS` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
ALTER TABLE `ingSafetyInfo` ADD UNIQUE(`id`); 

CREATE TABLE `pictograms` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `code` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `pictograms` (`id`, `name`, `code`) VALUES (NULL, 'Explosive', '1'), (NULL, 'Flammable', '2'), (NULL, 'Oxidising', '3'), (NULL, 'Gas under pressure', '4'), (NULL, 'Corrosive', '5'), (NULL, 'Acute toxicity', '6'), (NULL, 'Health hazard/Hazardous to the ozone layer', '7'), (NULL, 'Serious health hazard', '8'), (NULL, 'Hazardous to the environment', '9'); 

CREATE TABLE `formulasRevisions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `ingredient` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `ingredient_id` varchar(11) COLLATE utf8_general_ci DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `quantity` decimal(8,4) DEFAULT NULL,
 `notes` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
 `exclude_from_summary` int(11) NOT NULL DEFAULT 0,
 `revision` int(11) NOT NULL,
 `revisionDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formula_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` int(11) NOT NULL,
 `change_made` text COLLATE utf8_general_ci NOT NULL,
 `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 `user` varchar(255) COLLATE utf8_general_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formulaCategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `cname` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `type` varchar(255) COLLATE utf8_general_ci NOT NULL,
 `colorKey` VARCHAR(255) NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `formulaCategories` (`id`, `name`, `cname`, `type`) VALUES (NULL, 'Oriental', 'oriental', 'profile'), (NULL, 'Woody', 'woody', 'profile'), (NULL, 'Floral', 'floral', 'profile'), (NULL, 'Fresh', 'fresh', 'profile'), (NULL, 'Unisex', 'unisex', 'sex'), (NULL, 'Men', 'men', 'sex'), (NULL, 'Women', 'women', 'sex');

CREATE TABLE `synonyms` ( `id` INT NOT NULL , `ing` VARCHAR(255) NOT NULL, `cid` INT(10) NULL DEFAULT NULL , `synonym` VARCHAR(255) NOT NULL , `source` VARCHAR(255) NULL DEFAULT NULL ) ENGINE = InnoDB;

ALTER TABLE `synonyms` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);