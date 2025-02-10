SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `ingredient_compounds` (
  `id` int(11) NOT NULL,
  `ing` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cas` varchar(255) DEFAULT NULL,
  `ec` varchar(255) DEFAULT NULL,
  `min_percentage` DECIMAL(8,4) NOT NULL,
  `max_percentage` DECIMAL(8,4) NOT NULL,
  `toDeclare` INT NOT NULL DEFAULT '0',
  `GHS` TEXT NOT NULL DEFAULT '-',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `batchIDHistory` (
  `id` varchar(255) NOT NULL,
  `fid` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `pdf` LONGBLOB NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `bottles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ml` DOUBLE NOT NULL,
  `price` DOUBLE NOT NULL,
  `height` DOUBLE NOT NULL,
  `width` DOUBLE NOT NULL,
  `diameter` DOUBLE NOT NULL,
  `weight` DOUBLE NOT NULL, 
  `supplier` varchar(255) DEFAULT NULL,
  `supplier_link` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pieces` int(11) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL, 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(225) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `owner_id` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL, 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formulas` (
  `id` int(11) NOT NULL,
  `fid` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `ingredient` varchar(255) DEFAULT NULL,
  `ingredient_id` int(11) NULL DEFAULT NULL,
  `concentration` decimal(5,2) DEFAULT 100.00,
  `dilutant` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,4) DEFAULT NULL,
  `exclude_from_summary` INT NOT NULL DEFAULT '0', 
  `exclude_from_calculation` INT NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `makeFormula` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) DEFAULT NULL,
 `name` varchar(255) NOT NULL,
 `ingredient` varchar(255) DEFAULT NULL,
 `ingredient_id` INT NOT NULL,
 `replacement_id` INT NOT NULL DEFAULT '0', 
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) DEFAULT NULL,
 `quantity` decimal(10,4) DEFAULT NULL,
 `overdose` double(10,4) NOT NULL DEFAULT 0.0000,
 `originalQuantity` double(8,4) DEFAULT NULL,
 `notes` MEDIUMTEXT NULL,
 `skip` INT NOT NULL DEFAULT '0', 
 `toAdd` int(11) NOT NULL,
 `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `cart` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `quantity` varchar(255) NOT NULL,
 `purity` varchar(255) NOT NULL,
 `ingID` INT NOT NULL,
 `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formulasMetaData` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `fid` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT 'unisex',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isProtected` INT NULL DEFAULT '0',
  `defView` INT NULL,
  `catClass` VARCHAR(10) NULL,
  `revision` INT NOT NULL DEFAULT '0',
  `finalType` INT NOT NULL DEFAULT '100',
  `isMade` INT NOT NULL DEFAULT '0',
  `madeOn` DATETIME NULL DEFAULT NULL,
  `scheduledOn` DATETIME NULL DEFAULT NULL,
  `customer_id` INT NOT NULL DEFAULT '0',
  `status` INT NOT NULL DEFAULT '0',
  `toDo` INT NOT NULL DEFAULT '0',
  `rating` INT NOT NULL DEFAULT '0',
  `src` int(11) NOT NULL DEFAULT 0 COMMENT '0 = pvLocal, 1 = pvMarket',
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `IFRALibrary` (
  `id` int(11) NOT NULL,
  `ifra_key` varchar(255) DEFAULT NULL,
  `image` longblob DEFAULT NULL,
  `amendment` varchar(255) DEFAULT NULL,
  `prev_pub` varchar(255) DEFAULT NULL,
  `last_pub` varchar(255) DEFAULT NULL,
  `deadline_existing` varchar(255) DEFAULT NULL,
  `deadline_new` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cas` varchar(255) DEFAULT NULL,
  `cas_comment` text DEFAULT NULL,
  `synonyms` text DEFAULT NULL,
  `formula` varchar(255) DEFAULT NULL,
  `flavor_use` text DEFAULT NULL,
  `prohibited_notes` text DEFAULT NULL,
  `restricted_photo_notes` text DEFAULT NULL,
  `restricted_notes` text DEFAULT NULL,
  `specified_notes` text DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `risk` varchar(255) DEFAULT NULL,
  `contrib_others` text DEFAULT NULL,
  `contrib_others_notes` text DEFAULT NULL,
  `cat1` float NOT NULL DEFAULT 100,
  `cat2` float NOT NULL DEFAULT 100,
  `cat3` float NOT NULL DEFAULT 100,
  `cat4` float NOT NULL DEFAULT 100,
  `cat5A` float NOT NULL DEFAULT 100,
  `cat5B` float NOT NULL DEFAULT 100,
  `cat5C` float NOT NULL DEFAULT 100,
  `cat5D` float NOT NULL DEFAULT 100,
  `cat6` float NOT NULL DEFAULT 100,
  `cat7A` float NOT NULL DEFAULT 100,
  `cat7B` float NOT NULL DEFAULT 100,
  `cat8` float NOT NULL DEFAULT 100,
  `cat9` float NOT NULL DEFAULT 100,
  `cat10A` float NOT NULL DEFAULT 100,
  `cat10B` float NOT NULL DEFAULT 100,
  `cat11A` float NOT NULL DEFAULT 100,
  `cat11B` float NOT NULL DEFAULT 100,
  `cat12` float NOT NULL DEFAULT 100,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `ingCategory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `image` LONGBLOB NULL,
  `colorKey` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `ingProfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingProfiles` (`id`, `name`, `notes`) VALUES
(1, 'Top', 'Top Note'),
(2, 'Heart', 'Heart Note'),
(3, 'Base', 'Base Note'),
(4, 'Solvent', 'Solvents and Carriers');

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `INCI` varchar(255) DEFAULT NULL,
  `type` varchar(255)  DEFAULT NULL,
  `strength` varchar(255)  DEFAULT NULL,
  `category` int(10) NOT NULL DEFAULT '1',
  `purity` varchar(11)  DEFAULT NULL,
  `cas` varchar(255)  DEFAULT NULL,
  `einecs` varchar(255)  DEFAULT NULL,
  `reach` varchar(255)  DEFAULT NULL,
  `FEMA` varchar(255)  DEFAULT NULL,
  `tenacity` varchar(255)  DEFAULT NULL,
  `chemical_name` varchar(255)  DEFAULT NULL,
  `formula` varchar(255)  DEFAULT NULL,
  `flash_point` varchar(255)  DEFAULT NULL,
  `appearance` varchar(255)  DEFAULT NULL,
  `rdi` INT NOT NULL DEFAULT '0',
  `notes` text  DEFAULT NULL,
  `profile` varchar(255)  DEFAULT NULL,
  `solvent` VARCHAR(255) DEFAULT NULL, 
  `odor` varchar(255)  DEFAULT NULL,
  `allergen` int(11) DEFAULT NULL,
  `flavor_use` int(10) DEFAULT NULL,
  `soluble` varchar(255)  DEFAULT NULL,
  `logp` varchar(255)  DEFAULT NULL,
  `cat1` float NOT NULL DEFAULT 100,
  `cat2` float NOT NULL DEFAULT 100,
  `cat3` float NOT NULL DEFAULT 100,
  `cat4` float NOT NULL DEFAULT 100,
  `cat5A` float NOT NULL DEFAULT 100,
  `cat5B` float NOT NULL DEFAULT 100,
  `cat5C` float NOT NULL DEFAULT 100,
  `cat5D` float NOT NULL DEFAULT 100,
  `cat6` float NOT NULL DEFAULT 100,
  `cat7A` float NOT NULL DEFAULT 100,
  `cat7B` float NOT NULL DEFAULT 100,
  `cat8` float NOT NULL DEFAULT 100,
  `cat9` float NOT NULL DEFAULT 100,
  `cat10A` float NOT NULL DEFAULT 100,
  `cat10B` float NOT NULL DEFAULT 100,
  `cat11A` float NOT NULL DEFAULT 100,
  `cat11B` float NOT NULL DEFAULT 100,
  `cat12` float NOT NULL DEFAULT 100,
  `impact_top` varchar(10)  DEFAULT NULL,
  `impact_heart` varchar(10)  DEFAULT NULL,
  `impact_base` varchar(10)  DEFAULT NULL,
  `usage_type` varchar(255)  DEFAULT NULL,
  `noUsageLimit` INT NULL DEFAULT '0',
  `byPassIFRA` INT NULL DEFAULT '0',
  `isPrivate` INT NULL DEFAULT '0',
  `molecularWeight` VARCHAR(255) NULL,
  `physical_state` INT NULL DEFAULT '1',
  `cid` INT NULL,
  `shelf_life` INT NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `ingStrength` (
  `id` int(11) NOT NULL,
  `name` varchar(255)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingStrength` (`id`, `name`) VALUES
(1, 'Medium'),
(2, 'Low'),
(3, 'High'),
(4, 'Extreme');

CREATE TABLE `ingSuppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `po` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `price_tag_start` text DEFAULT NULL,
  `price_tag_end` text DEFAULT NULL,
  `add_costs` varchar(255) DEFAULT NULL,
  `price_per_size` INT NOT NULL DEFAULT '0', 
  `notes` text  DEFAULT NULL,
  `min_ml` INT NOT NULL DEFAULT '0', 
  `min_gr` INT NOT NULL DEFAULT '0',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `ingTypes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ingTypes` (`id`, `name`) VALUES
(1, 'AC'),
(2, 'EO'),
(3, 'Other/Uknown'),
(4, 'Custom Blend'),
(5, 'Carrier'),
(6, 'Solvent'),
(7, 'Base');

CREATE TABLE `inventory_accessories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `accessory` varchar(255) NOT NULL,
  `price` DOUBLE DEFAULT 0,
  `supplier` varchar(255) NOT NULL,
  `supplier_link` varchar(255) NOT NULL,
  `pieces` int(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `pv_meta` (
  `schema_ver` varchar(255) NOT NULL,
  `app_ver` varchar(255) NOT NULL,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE update_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prev_ver VARCHAR(10) NOT NULL COMMENT 'Previous schema version',
    new_ver VARCHAR(10) NOT NULL COMMENT 'New schema version',
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tracks schema update history';


CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `provider` INT NOT NULL DEFAULT '1' COMMENT '1=Local,2=SSO', 
  `role` INT NOT NULL,
  `isActive` INT NOT NULL DEFAULT '1',
  `country` VARCHAR(255) NULL DEFAULT NULL,
  `isAPIActive` INT NOT NULL DEFAULT '0',
  `API_key` VARCHAR(255) NULL DEFAULT NULL,
  `isVerified` INT NOT NULL,
  `token` VARCHAR(255) NULL, 
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `password_resets` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `email` varchar(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL , 
    `expiry` TIMESTAMP NOT NULL , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8mb3 COLLATE utf8_general_ci; 


CREATE TABLE `IFRACategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `description` varchar(255) DEFAULT NULL,
 `type` int(11) NOT NULL COMMENT '1=Standard, 2=Custom',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `colorKey` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rgb` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


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
 `supplierLink` varchar(255) DEFAULT NULL,
 `price` DOUBLE DEFAULT 0,
 `size` float DEFAULT 10,
 `manufacturer` varchar(255) DEFAULT NULL,
 `preferred` int(11) NOT NULL DEFAULT 0,
 `batch` VARCHAR(255) NULL,
 `purchased` DATE NULL,
 `mUnit` VARCHAR(255) NULL, 
 `stock` decimal(10,3) NOT NULL,
 `status` INT NOT NULL DEFAULT '1' COMMENT '1 = Available\r\n2 = Limited Availability\r\n3 = Not available', 
 `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `supplier_sku` VARCHAR(255) NULL, 
 `internal_sku` VARCHAR(255) NULL,
 `storage_location` VARCHAR(255) NULL,
 `owner_id` VARCHAR(255) NOT NULL,
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

ALTER TABLE `formulasMetaData` ADD PRIMARY KEY (`id`);
ALTER TABLE `formulasMetaData` ADD UNIQUE(`id`);
ALTER TABLE `formulasMetaData` ADD UNIQUE(`fid`);

ALTER TABLE `IFRALibrary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

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

ALTER TABLE `inventory_accessories` ADD PRIMARY KEY (`id`);


ALTER TABLE `ingredient_compounds`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bottles` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

ALTER TABLE `ingSuppliers` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingTypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `inventory_accessories` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `ingredient_compounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `documents` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ownerID` int(11) NOT NULL,
 `type` int(11) NOT NULL,
 `name` varchar(255) NOT NULL,
 `notes` varchar(255) DEFAULT NULL,
 `docData` longblob NOT NULL,
 `isBatch` INT NOT NULL DEFAULT '0', 
 `isSDS` INT NOT NULL DEFAULT '0', 
 `created_at` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
 `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `ingSafetyInfo` ADD UNIQUE(`id`); 
ALTER TABLE ingSafetyInfo ADD CONSTRAINT unique_ghs_ingid UNIQUE (GHS, ingID);


CREATE TABLE `pictograms` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `code` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `pictograms` (`id`, `name`, `code`) VALUES (NULL, 'Explosive', '1'), (NULL, 'Flammable', '2'), (NULL, 'Oxidising', '3'), (NULL, 'Gas under pressure', '4'), (NULL, 'Corrosive', '5'), (NULL, 'Acute toxicity', '6'), (NULL, 'Health hazard/Hazardous to the ozone layer', '7'), (NULL, 'Serious health hazard', '8'), (NULL, 'Hazardous to the environment', '9'); 

CREATE TABLE `formulasRevisions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) DEFAULT NULL,
 `name` varchar(255) NOT NULL,
 `ingredient` varchar(255) DEFAULT NULL,
 `ingredient_id` int(11) NULL DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) DEFAULT NULL,
 `quantity` decimal(10,4) DEFAULT NULL,
 `notes` varchar(255) DEFAULT NULL,
 `exclude_from_summary` int(11) NOT NULL DEFAULT 0,
 `revision` int(11) NOT NULL,
 `revisionDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 `revisionMethod` VARCHAR(255) DEFAULT NULL,
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formula_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` int(11) NOT NULL,
 `ing_id` INT NOT NULL DEFAULT '0', 
 `change_made` text NOT NULL,
 `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 `user` varchar(255) NOT NULL,
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `formulaCategories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `cname` varchar(255) NOT NULL,
 `type` varchar(255) NOT NULL,
 `colorKey` VARCHAR(255) NULL DEFAULT NULL,
 `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `owner_id` VARCHAR(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `formulaCategories` (`id`, `name`, `cname`, `type`) VALUES (NULL, 'Oriental', 'oriental', 'profile'), (NULL, 'Woody', 'woody', 'profile'), (NULL, 'Floral', 'floral', 'profile'), (NULL, 'Fresh', 'fresh', 'profile'), (NULL, 'Unisex', 'unisex', 'gender'), (NULL, 'Men', 'men', 'gender'), (NULL, 'Women', 'women', 'gender');

CREATE TABLE `synonyms` (
	`id` INT NOT NULL,
	`ing` VARCHAR(255) NOT NULL, 
	`cid` INT(10) NULL DEFAULT NULL, 
	`synonym` VARCHAR(255) NOT NULL, 
	`source` VARCHAR(255) NULL DEFAULT NULL, 
	`created_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`owner_id` VARCHAR(255) NOT NULL
) ENGINE = InnoDB;

ALTER TABLE `synonyms` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);

CREATE TABLE `ingReplacements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ing_id` INT NOT NULL,
  `ing_name` varchar(255) NOT NULL,
  `ing_cas` varchar(255) NOT NULL,
  `ing_rep_id` INT NOT NULL,
  `ing_rep_name` varchar(255) NOT NULL,
  `ing_rep_cas` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `owner_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `perfumeTypes` (
 	`id` INT NOT NULL AUTO_INCREMENT, 
	`name` VARCHAR(255) NOT NULL, 
	`concentration` INT NOT NULL, 
	`description` VARCHAR(255) NOT NULL, 
	`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`owner_id` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `perfumeTypes` (`id`, `name`, `concentration`, `description`) VALUES (NULL, 'EDP', '20', 'Eau de Parfum - Contains between 15 - 20% of formula concentration'), (NULL, 'EDT', '15', 'Eau de Toilette - Contains between 5 - 15% of formula concentration'), (NULL, 'EDC', '4', 'Eau de Cologne - Contains between 2 - 4% of formula concentration'), (NULL, 'Perfume', '30', 'Perfume - Contains between 20 - 30% of formula concentration');

CREATE TABLE `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` varchar(255) NOT NULL,
  `owner_id` VARCHAR(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `templates` (`id`, `name`, `content`, `created_at`, `updated_at`, `description`, `owner_id`) VALUES (NULL, 'IFRA Document Template', '<!doctype html>
<html lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
 <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
 <link href="/css/ifraCert.css" rel="stylesheet">
</head>

<body>
 <div>
 <p style="margin-bottom: 0.63in"><img src="%LOGO%" width="200px" /></p>
 </div>
 <h1 class="western"><font face="Arial, sans-serif"><span style="font-style: normal">CERTIFICATE OF CONFORMITY OF FRAGRANCE MIXTURES WITH IFRA STANDARDS</span></font><br>
 </h1>
 <p align=center style="widows: 0; orphans: 0"><font face="Helvetica 65 Medium, Arial Narrow, sans-serif"><font size=4><b><font face="Arial, sans-serif"><font size=2 style="font-size: 11pt"><u>This Certificate assesses the conformity of a fragrance mixture with IFRA Standards and provides restrictions for use as necessary. It is based only on those materials subject to IFRA Standards for the toxicity endpoint(s) described in each Standard. </u></font></font></b></font></font>
 </p>
 <p align=center style="widows: 0; orphans: 0"><br>
 </p>
 <hr size="1">
 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>CERTIFYING PARTY:</b></u></font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_NAME%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_ADDRESS%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_EMAIL%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_PHONE%</font></p>


 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>CERTIFICATE DELIVERED TO: </b></u></font>
 </p>
 <p class="western"><font face="Arial, sans-serif"><span ><b>Customer: </b></span></font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_NAME%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_ADDRESS%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_EMAIL%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_WEB%</font></p>

 <p class="western"><br>
 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>SCOPE OF THE CERTIFICATE:</b></u></font></p>
 <p class="western"><font face="Arial, sans-serif"><span >Product: <B>%PRODUCT_NAME%</b></span></font></p>
 <p class="western">Size:<strong> %PRODUCT_SIZE%ml</strong></p>
 <p class="western">Concentration: <strong>%PRODUCT_CONCENTRATION%%</strong></p>
 <hr size="1"><br>
 <font face="Arial, sans-serif"><span ><U><B>COMPULSORY INFORMATION:</b></u></span></font>
 <p class="western" style="margin-right: -0.12in">
 <font face="Arial, sans-serif"><span >We certify that the above mixture is in compliance with the Standards of the INTERNATIONAL FRAGRANCE ASSOCIATION (IFRA), up to and including the <strong>%IFRA_AMENDMENT%</strong> Amendment to the IFRA Standards (published </span><b>%IFRA_AMENDMENT_DATE%</span></b>),
 provided it is used in the following</span></font> <font face="Arial, sans-serif"><span >category(ies)
 at a maximum concentration level of:</span></font></p>
 <p class="western" style="margin-right: -0.12in"> </p>
 <table class="table table-stripped">
 <tr>
 <th bgcolor="#d9d9d9"><strong>IFRA Category</strong></th>
 <th bgcolor="#d9d9d9"><strong>Description</strong></th>
 <th bgcolor="#d9d9d9"><strong>Level of use (%)*</strong></th>
 </tr>
 <tr>
 <td align="center">%IFRA_CAT_LIST%</td>
 </tr>
 </table>
 <p class="western" style="margin-right: -0.12in"><font face="Arial, sans-serif"><I>*Actual use level or maximum use level at 100% concentration</I></font> </p>
 <p class="western" style="margin-right: -0.12in">
 <font face="Arial, sans-serif"><span >For other kinds of, application or use at higher concentration levels, a new evaluation may be needed; please contact </span></font><font face="Arial, sans-serif"><b>%BRAND_NAME%</b></font><font face="Arial, sans-serif"><span >.
 </span></font></p>
 <p class="western" style="margin-right: -0.12in"><font face="Arial, sans-serif"><span >Information about presence and concentration of fragrance ingredients subject to IFRA Standards in the fragrance mixture </span></font><font face="Arial, sans-serif"><B>%PRODUCT_NAME%</b></font><font face="Arial, sans-serif"><span> is as follows:</span></font></p>
 <p class="western" style="margin-right: -0.12in"> </p>
 <table class="table table-stripped">
 <tr>
 <th width="22%" bgcolor="#d9d9d9"><strong>Material(s) under the scope of IFRA Standards:</strong></th>
 <th width="12%" bgcolor="#d9d9d9"><strong>CAS number(s):</strong></th>
 <th width="28%" bgcolor="#d9d9d9"><strong>Recommendation (%) from IFRA Standard:</strong></th>
 <th width="19%" bgcolor="#d9d9d9"><strong>Concentration (%) in finished product:</strong></th>
 <th width="19%" bgcolor="#d9d9d9">Risk</th>
 </tr>
 %IFRA_MATERIALS_LIST%
 </table>
 <p> </p>
 <p><font face="Arial, sans-serif"><span >Signature </span></font><font face="Arial, sans-serif"><span><i>(If generated electronically, no signature)</i></span></font></p>
 <p><font face="Arial, sans-serif"><span >Date: </span></font><strong>%CURRENT_DATE%</strong></p>
 </p>
 <div>
 <p style="margin-right: 0in; margin-top: 0.08in">
 <font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span><u>Disclaimer</u>:
 </span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate provides restrictions for use of the specified product based only on those materials restricted by IFRA Standards for the toxicity endpoint(s) described in each Standard.</span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate does not provide certification of a comprehensive safety assessment of all product constituents.</span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span> This certificate is the responsibility of the fragrance supplier issuing it. It has not been prepared or endorsed by IFRA in anyway. </span></font></font>
 </p>
 </div>
</body>
</html>', current_timestamp(), current_timestamp(), 'The default IFRA document template', 1);

INSERT INTO `templates` (`id`, `name`, `content`, `created_at`, `updated_at`, `description`, `owner_id`) VALUES
(9, 'SDS Example template', '<!doctype html>\n<html lang=\"en\">\n\n<head>\n    <link href=\"/css/bootstrap.min.css\" rel=\"stylesheet\">\n    <link href=\"/css/bootstrap-icons/font/bootstrap-icons.min.css\" rel=\"stylesheet\" type=\"text/css\">\n    <link href=\"/css/fontawesome-free/css/all.min.css\" rel=\"stylesheet\">\n    <link href=\"/css/regulatory.css\" rel=\"stylesheet\">\n</head>\n\n<body>\n    <div class=\"container\">\n        <div class=\"sds\">\n            <div class=\"sds-company text-inverse fw-bold\">\n                <img src=\"%LOGO%\" class=\"img-thumbnail float-start\">\n            </div>\n            <div class=\"sds-date\">\n                <div class=\"fw-bold\">%SDS_PRODUCT_NAME%</div>\n                <small>Language %SDS_LANGUAGE%</small>\n                <div class=\"date small\">%CURRENT_DATE%</div>\n                <div class=\"sds-detail\">According to Regulation (EC) No. 1907/2006 (amended by Regulation (EU) No. 2020/878)</div>\n            </div>\n            <div id=\"section-1\">\n                <div class=\"sds-header\">\n                    <div class=\"sds-to\">\n                        <h4>1. Identification of the substance/mixture and of the company/undertaking</h4>\n                    </div>\n                </div>\n                <div class=\"sds-content mt-2\">\n                    <div class=\"mb-4\">\n                        <div class=\"fw-bold\">1.1 Product identifier</div>\n                        <div class=\"mt-2\">\n                            <div class=\"fw-bold\">Trade name/designation</div>\n                            <div>%SDS_PRODUCT_NAME%</div>\n                        </div>\n                    </div>\n                    <div class=\"mb-4\">\n                        <div class=\"fw-bold\">1.2 Relevant identified uses of the substance or mixture and uses advised against</div>\n                        <div class=\"mt-2\">\n                            <div class=\"fw-bold\">Relevant identified uses</div>\n                            <div>%SDS_PRODUCT_USE%</div>\n                        </div>\n                        <div class=\"mt-2\">\n                            <div class=\"fw-bold\">Uses advised against</div>\n                            <div>%SDS_PRODUCT_ADA%</div>\n                        </div>\n                    </div>\n\n                    <div class=\"mb-4\">\n                        <div class=\"fw-bold\">1.3 Details of the supplier of the safety data sheet</div>\n                        <div class=\"mt-2\">\n                            <div class=\"fw-bold\">Supplier</div>\n                            <div>%SDS_SUPPLIER_NAME%</div>\n                            <div>%SDS_SUPPLIER_ADDRESS%, %SDS_SUPPLIER_COUNTRY%, %SDS_SUPPLIER_PO%</div>\n                            <div>%SDS_SUPPLIER_EMAIL%</div>\n                            <div>%SDS_SUPPLIER_PHONE%</div>\n                            <div>%SDS_SUPPLIER_WEB%</div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n            <div id=\"section-2\">\n                <div class=\"sds-header\">\n                    <div class=\"sds-to\">\n                        <h4>2. Hazards identification</h4>\n                    </div>\n                </div>\n                <div class=\"alert alert-info mt-4\"><i class=\"fa-solid fa-info mx-2\"></i>\n                    2.2 Labeling\n                    <p>\n                        <span class=\"me-3\"><i class=\"fa fa-fw fa-lg mt-2\"></i>Label elements according to the regulation (EC) n°1272/2008 (CLP) and its amendments</span>\n                    </p>\n                </div>\n                <div class=\"sds-content mt-2\">\n                    %GHS_LABEL_LIST%\n                </div>\n            </div>\n            <div id=\"section-3\">\n                <div class=\"sds-header\">\n                    <div class=\"sds-to\">\n                        <h4>3. Composition/information on ingredients</h4>\n                    </div>\n                </div>\n                <div class=\"alert alert-info mt-4\"><i class=\"fa-solid fa-info mx-2\"></i>\n                    In accordance with the product knowledge, no nanomaterials have been identified. The mixture does not contain any substances classified as Substances of Very High Concern (SVHC) by the European Chemicals Agency (ECHA) under article 57 of REACH: http://echa.europa.eu/en/candidate-list-table</div>\n                <div class=\"sds-content mt-2\">\n                    <div class=\"d-flex flex-wrap\">\n                       <table width=\"100%\" class=\"table table-sds\">\n                          <tbody>\n                             <th>Name</th>\n                             <th>CAS</th>\n                             <th>EINES</th>\n                             <th>Min percentage</th>\n                             <th>Max percentage</th>\n                             <th>GHS</th>\n                             %CMP_MATERIALS_LIST%\n                         </tbody>\n                     </table>\n                 </div>\n             </div>\n         </div>\n        <div id=\"section-4\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>4. First aid measures</h4>\n                </div>\n            </div>\n            <div class=\"alert alert-info mt-4\"><i class=\"fa-solid fa-info mx-2\"></i>\n                Description of first aid measures\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">General information</div>\n                    <div>%FIRST_AID_GENERAL%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Following inhalation</div>\n                    <div>%FIRST_AID_INHALATION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Following skin contact</div>\n                    <div>%FIRST_AID_SKIN%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Following eye contact</div>\n                    <div>%FIRST_AID_EYE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Following ingestion</div>\n                    <div>%FIRST_AID_INGESTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Self-protection of the first aider</div>\n                    <div>%FIRST_AID_SELF_PROTECTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Most important symptoms and effects, both acute and delayed</div>\n                    <div>%FIRST_AID_SYMPTOMS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Notes for the doctor</div>\n                    <div>%FIRST_AID_DR_NOTES%</div>\n                </div>\n            </div>\n        </div>\n        <div id=\"section-5\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>5. Firefighting measures</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Suitable extinguishing media</div>\n                    <div>%FIRE_SUIT_MEDIA%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Unsuitable extinguishing media</div>\n                    <div>%FIRE_NONSUIT_MEDIA%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Special hazards arising from the substance or mixture</div>\n                    <div>%FIRE_SPECIAL_HAZARDS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Advice for firefighters</div>\n                    <div>%FIRE_ADVICE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%FIRE_OTHER_INFO%</div>\n                </div>\n            </div>\n        </div>\n        <div id=\"section-6\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>6. Accidental release measures</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Personal precautions, protective equipment and emergency procedures</div>\n                    <div>%ACC_REL_PERSONAL_CAUTIONS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Environmental precautions</div>\n                    <div>%ACC_REL_ENV_CAUTIONS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Methods and material for containment and cleaning up</div>\n                    <div>%ACC_REL_CLEANING%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Reference to other sections</div>\n                    <div>%ACC_REL_REFERENCES%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%ACC_REL_OTHER_INFO%</div>\n                </div>\n            </div>\n        </div>\n        <div id=\"section-7\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>7. Handling and Storage</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Precautions for safe handling</div>\n                    <div>%HS_PROTECTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Advices on general occupational hygiene</div>\n                    <div>%HS_HYGIENE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Conditions for safe storage, including any incompatibilities</div>\n                    <div>%HS_SAFE_STORE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Advice on joint storage</div>\n                    <div>%HS_JOINT_STORE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Specific end uses</div>\n                    <div>%HS_SPECIFIC_USES%</div>\n                </div>\n            </div>\n        </div>\n        <div id=\"section-8\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>8. Exposure controls/personal protection</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Occupational exposure limits</div>\n                    <div>%EXPOSURE_OCC_LIMIT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Biological limit values</div>\n                    <div>%EXPOSURE_BIO_LIMIT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Exposure limits at intended use</div>\n                    <div>%EXPOSURE_USE_LIMIT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Remarks</div>\n                    <div>%EXPOSURE_OTHER_REM%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Eye/face protection</div>\n                    <div>%EXPOSURE_FACE_PROTECTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Skin protection</div>\n                    <div>%EXPOSURE_SKIN_PROTECTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Respiratory protection</div>\n                    <div>%EXPOSURE_RESP_PROTECTION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Environmental exposure controls</div>\n                    <div>%EXPOSURE_ENV_EXPOSURE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Consumer exposure controls</div>\n                    <div>%EXPOSURE_CONS_EXPOSURE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%EXPOSURE_OTHER_INFO%</div>\n                </div>\n            </div>\n        </div>\n        <div id=\"section-9\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>9. Physical and chemical Properties</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Physical state</div>\n                    <div>%PHYSICAL_STATE%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Color</div>\n                    <div>%COLOR%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Odor</div>\n                    <div>%ODOR%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Odor threshold</div>\n                    <div>%ODOR_THRESHOLD%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">pH</div>\n                    <div>%PH%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Melting/Freezing point</div>\n                    <div>%MELTING_POINT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Boiling point</div>\n                    <div>%BOILING_POINT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Flash point</div>\n                    <div>%FLASH_POINT%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Evaporation rate</div>\n                    <div>%EVAPORATION_RATE%</div>\n                </div> \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Water solubility</div>\n                    <div>%SOLUBILITY%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Partition coefficient, n-octanol/water (log Pow)</div>\n                    <div>%LOGP%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Auto-inflammability temperature</div>\n                    <div>%AUTO_INFL_TEMP%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Decomposition temperature</div>\n                    <div>%DECOMP_TEMP%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Viscosity</div>\n                    <div>%VISCOSITY%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Explosive properties</div>\n                    <div>%EXPLOSIVE_PROPERTIES%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Oxidising properties</div>\n                    <div>%OXIDISING_PROPERTIES%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Solubility in other Solvents</div>\n                    <div>%SOLVENTS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Particle characteristics</div>\n                    <div>%PARTICLE_CHARACTERISTICS%</div>\n                </div>                \n            </div>\n        </div>\n        <div id=\"section-10\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>10. Stability and Reactivity</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Reactivity</div>\n                    <div>%STABILLITY_REACTIVITY%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Chemical stability</div>\n                    <div>%STABILLITY_CHEMICAL%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Possibility of hazardous reactions</div>\n                    <div>%STABILLITY_REACTIONS%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Conditions to avoid</div>\n                    <div>%STABILLITY_AVOID%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Incompatible materials</div>\n                    <div>%STABILLITY_INCOMPATIBILITY%</div>\n                </div>             \n            </div>           \n        </div>\n        <div id=\"section-11\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>11. Toxicological information</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Acute oral toxicity</div>\n                    <div>%TOXICOLOGICAL_ACUTE_ORAL%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Acute dermal toxicity</div>\n                    <div>%TOXICOLOGICAL_ACUTE_DERMAL%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Acute inhalation toxicity</div>\n                    <div>%TOXICOLOGICAL_ACUTE_INHALATION%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Skin corrosion/irritation</div>\n                    <div>%TOXICOLOGICAL_SKIN%</div>\n                </div>\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Serious eye damage/irritation</div>\n                    <div>%TOXICOLOGICAL_EYE%</div>\n                </div>  \n\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Skin sensitisation</div>\n                    <div>%TOXICOLOGICAL_SENSITISATION%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Specific target organ toxicity (repeated exposure)</div>\n                    <div>%TOXICOLOGICAL_ORGAN_REPEATED%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Specific target organ toxicity (single exposure)</div>\n                    <div>%TOXICOLOGICAL_ORGAN_SINGLE%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Carcinogenicity</div>\n                    <div>%TOXICOLOGICAL_CARCINOGENCITY%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Reproductive toxicity</div>\n                    <div>%TOXICOLOGICAL_REPRODUCTIVE%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Germ cell mutagenicity</div>\n                    <div>%TOXICOLOGICAL_CELL_MUTATION%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Sensitisation to the respiratory tract</div>\n                    <div>%TOXICOLOGICAL_RESP_TRACT%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%TOXICOLOGICAL_OTHER_INFO%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Information on other hazards</div>\n                    <div>%TOXICOLOGICAL_OTHER_HAZARDS%</div>\n                </div>\n            </div>  \n        </div>\n\n        <div id=\"section-12\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>12. Ecological information</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Toxicity</div>\n                    <div>%ECOLOGICAL_TOXICITY%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Persistence and degradability</div>\n                    <div>%ECOLOGICAL_PERSISTENCE%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Bioaccumulative potential</div>\n                    <div>%ECOLOGICAL_BIOACCUMULATIVE%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Mobility in soil</div>\n                    <div>%ECOLOGICAL_SOIL_MOBILITY%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Results of PBT and vPvB assessment</div>\n                    <div>%ECOLOGICAL_PBT_VPVB%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Endocrine disrupting properties</div>\n                    <div>%ECOLOGICAL_ENDOCRINE_PROPERTIES%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Other adverse effects</div>\n                    <div>%ECOLOGICAL_OTHER_ADV_EFFECTS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional ecotoxicological information</div>\n                    <div>%ECOLOGICAL_ADDITIONAL_ECOTOXICOLOGICAL_INFO%</div>\n                </div>\n            </div>  \n        </div>\n        <div id=\"section-13\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>13. Disposal considerations</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\">\n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Product/Packaging disposal</div>\n                    <div>%DISPOSAL_PRODUCT%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Remark</div>\n                    <div>%DISPOSAL_REMARKS%</div>\n                </div>  \n            </div>  \n        </div>\n\n        <div id=\"section-14\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>14. Transport information</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\"> \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">UN number</div>\n                    <div>%TRANSPORT_UN_NUMBER%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">UN proper shipping name</div>\n                    <div>%TRANSPORT_SHIPPING_NAME%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Transport hazard class(es)</div>\n                    <div>%TRANSPORT_HAZARD_CLASS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Packing group</div>\n                    <div>%TRANSPORT_PACKING_GROUP%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Environmental hazards</div>\n                    <div>%TRANSPORT_ENV_HAZARDS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Special precautions for user</div>\n                    <div>%TRANSPORT_PRECAUTIONS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Bulk shipping according to IMO instruments</div>\n                    <div>%TRANSPORT_BULK_SHIPPING%</div>\n                </div>\n            </div>  \n        </div>    \n        <div id=\"section-15\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>15. Regulatory information</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\"> \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Safety, health and environmental regulations/legislation specific for the substance or mixture</div>\n                    <div>%LEGISLATION_SAFETY%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">EU legislation</div>\n                    <div>%LEGISLATION_EU%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Chemical Safety Assessment</div>\n                    <div>%LEGISLATION_CHEMICAL_SAFETY_ASSESSMENT%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%LEGISLATION_OTHER_INFO%</div>\n                </div>\n            </div>  \n        </div>\n\n\n        <div id=\"section-16\">\n            <div class=\"sds-header\">\n                <div class=\"sds-to\">\n                    <h4>16. Other information</h4>\n                </div>\n            </div>\n            <div class=\"sds-content mt-2\"> \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Indication of changes</div>\n                    <div>%ADD_INFO_CHANGES%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Abbreviations and acronyms</div>\n                    <div>%ADD_INFO_ACRONYMS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Key literature references and sources for data</div>\n                    <div>%ADD_INFO_REFERENCES%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">The classification of the mixture is in accordance with the evaluation method described in HazCom 2012</div>\n                    <div>%ADD_INFO_HAZCOM%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">The classification of the mixture is in accordance with the evaluation method described in the GHS</div>\n                    <div>%ADD_INFO_GHS%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Training advice</div>\n                    <div>%ADD_INFO_TRAINING%</div>\n                </div>  \n                <div class=\"mb-2\">\n                    <div class=\"fw-bold\">Additional information</div>\n                    <div>%ADD_INFO_OTHER%</div>\n                </div>\n            </div>  \n        </div>\n\n        <div class=\"sds-note\"><span class=\"text-center mb-3 fw-bold\">%BRAND_NAME%</span><br> \n            Creation date: %CURRENT_DATE%\n        </div>\n        <div class=\"sds-note alert alert-warning mt-4\"><i class=\"fa-solid fa-info mx-2\"></i>\n            %SDS_DISCLAIMER%\n        </div>\n        <div class=\"sds-footer\">\n            <p class=\"text-center mb-3 fw-bold\">\n            %BRAND_NAME%\n            </p>\n            <p class=\"text-center\">\n                <span class=\"me-3\"><i class=\"fa fa-fw fa-lg fa-globe mx-2\"></i>www.perfumersvault.com</span>\n            </p>\n        </div>\n    </div>\n</div>\n</body>\n</html>\n', '2024-06-22 10:23:11', '2024-06-27 08:47:38', 'This is an example template',1);


CREATE TABLE `formulasTags` ( 
	`id` INT NOT NULL AUTO_INCREMENT, 
	`formula_id` INT NOT NULL, 
	`tag_name` VARCHAR(255) NOT NULL,
	`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`owner_id` VARCHAR(255) NOT NULL,
	UNIQUE (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_prefs` ( 
	`pref_name` VARCHAR(255) NOT NULL,
	`pref_data` LONGTEXT NOT NULL,
	`pref_tab` VARCHAR(255) NULL,
	`created_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL,
	`owner_id` VARCHAR(255) NOT NULL 
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 


CREATE TABLE `inventory_compounds` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `description` TEXT NOT NULL , `batch_id` VARCHAR(255) NOT NULL DEFAULT '-' , `size` DOUBLE NOT NULL DEFAULT '0' , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `owner_id` VARCHAR(255) NOT NULL , `location` VARCHAR(255) NOT NULL , `label_info` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 

ALTER TABLE `inventory_compounds` ADD UNIQUE(`name`);

CREATE TABLE `sds_data` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`product_name` VARCHAR(255) NOT NULL, 
	`product_use` VARCHAR(255) NOT NULL,
	`country` VARCHAR(255) NOT NULL DEFAULT 'United Kingdom', 
	`language` VARCHAR(255) NOT NULL DEFAULT 'English', 
	`product_type` VARCHAR(255) NOT NULL DEFAULT 'Substance', 
	`state_type` VARCHAR(255) NOT NULL DEFAULT 'Liquid', 
	`supplier_id` INT NOT NULL, 
	`docID` INT NOT NULL, 
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
	`updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL,
	`owner_id` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 

CREATE TABLE `ingredient_safety_data` (
	`id` INT NOT NULL AUTO_INCREMENT,
  `ingID` int(11) NOT NULL,
  `first_aid_general` longtext DEFAULT NULL,
  `first_aid_inhalation` longtext DEFAULT NULL,
  `first_aid_skin` longtext DEFAULT NULL,
  `first_aid_eye` longtext DEFAULT NULL,
  `first_aid_ingestion` longtext DEFAULT NULL,
  `first_aid_self_protection` longtext DEFAULT NULL,
  `first_aid_symptoms` mediumtext DEFAULT NULL,
  `first_aid_dr_notes` mediumtext DEFAULT NULL,
  `firefighting_suitable_media` mediumtext DEFAULT NULL,
  `firefighting_non_suitable_media` mediumtext DEFAULT NULL,
  `firefighting_special_hazards` mediumtext DEFAULT NULL,
  `firefighting_advice` mediumtext DEFAULT NULL,
  `firefighting_other_info` mediumtext DEFAULT NULL,
  `accidental_release_per_precautions` mediumtext DEFAULT NULL,
  `accidental_release_env_precautions` mediumtext DEFAULT NULL,
  `accidental_release_cleaning` mediumtext DEFAULT NULL,
  `accidental_release_refs` mediumtext DEFAULT NULL,
  `accidental_release_other_info` mediumtext DEFAULT NULL,
  `handling_protection` mediumtext DEFAULT NULL,
  `handling_hygiene` mediumtext DEFAULT NULL,
  `handling_safe_storage` mediumtext DEFAULT NULL,
  `handling_joint_storage` mediumtext DEFAULT NULL,
  `handling_specific_uses` mediumtext DEFAULT NULL,
  `exposure_occupational_limits` mediumtext DEFAULT NULL,
  `exposure_biological_limits` mediumtext DEFAULT NULL,
  `exposure_intented_use_limits` mediumtext DEFAULT NULL,
  `exposure_other_remarks` mediumtext DEFAULT NULL,
  `exposure_face_protection` mediumtext DEFAULT NULL,
  `exposure_skin_protection` mediumtext DEFAULT NULL,
  `exposure_respiratory_protection` mediumtext DEFAULT NULL,
  `exposure_env_exposure` mediumtext DEFAULT NULL,
  `exposure_consumer_exposure` mediumtext DEFAULT NULL,
  `exposure_other_info` mediumtext DEFAULT NULL,
  `stabillity_reactivity` mediumtext DEFAULT NULL,
  `stabillity_chemical` mediumtext DEFAULT NULL,
  `stabillity_reactions` mediumtext DEFAULT NULL,
  `stabillity_avoid` mediumtext DEFAULT NULL,
  `stabillity_incompatibility` mediumtext DEFAULT NULL,
  `toxicological_acute_oral` mediumtext DEFAULT NULL,
  `toxicological_acute_dermal` mediumtext DEFAULT NULL,
  `toxicological_acute_inhalation` mediumtext DEFAULT NULL,
  `toxicological_skin` mediumtext DEFAULT NULL,
  `toxicological_eye` mediumtext DEFAULT NULL,
  `toxicological_sensitisation` mediumtext DEFAULT NULL,
  `toxicological_organ_repeated` mediumtext DEFAULT NULL,
  `toxicological_organ_single` mediumtext DEFAULT NULL,
  `toxicological_carcinogencity` mediumtext DEFAULT NULL,
  `toxicological_reproductive` mediumtext DEFAULT NULL,
  `toxicological_cell_mutation` mediumtext DEFAULT NULL,
  `toxicological_resp_tract` mediumtext DEFAULT NULL,
  `toxicological_other_info` mediumtext DEFAULT NULL,
  `toxicological_other_hazards` mediumtext DEFAULT NULL,
  `ecological_toxicity` mediumtext DEFAULT NULL,
  `ecological_persistence` mediumtext DEFAULT NULL,
  `ecological_bioaccumulative` mediumtext DEFAULT NULL,
  `ecological_soil_mobility` mediumtext DEFAULT NULL,
  `ecological_PBT_vPvB` mediumtext DEFAULT NULL,
  `ecological_endocrine_properties` mediumtext DEFAULT NULL,
  `ecological_other_adv_effects` mediumtext DEFAULT NULL,
  `ecological_additional_ecotoxicological_info` mediumtext DEFAULT NULL,
  `disposal_product` mediumtext DEFAULT NULL,
  `disposal_remarks` mediumtext DEFAULT NULL,
  `transport_un_number` mediumtext DEFAULT NULL,
  `transport_shipping_name` mediumtext DEFAULT NULL,
  `transport_hazard_class` mediumtext DEFAULT NULL,
  `transport_packing_group` mediumtext DEFAULT NULL,
  `transport_env_hazards` mediumtext DEFAULT NULL,
  `transport_precautions` mediumtext DEFAULT NULL,
  `transport_bulk_shipping` mediumtext DEFAULT NULL,
  `odor_threshold` text DEFAULT NULL,
  `pH` text DEFAULT NULL,
  `melting_point` text DEFAULT NULL,
  `boiling_point` text DEFAULT NULL,
  `flash_point` text DEFAULT NULL,
  `evaporation_rate` text DEFAULT NULL,
  `solubility` text DEFAULT NULL,
  `auto_infl_temp` text DEFAULT NULL,
  `decomp_temp` text DEFAULT NULL,
  `viscosity` text DEFAULT NULL,
  `explosive_properties` mediumtext DEFAULT NULL,
  `oxidising_properties` mediumtext DEFAULT NULL,
  `particle_chars` mediumtext DEFAULT NULL,
  `flammability` mediumtext DEFAULT NULL,
  `logP` varchar(255) DEFAULT NULL,
  `soluble` varchar(255) DEFAULT NULL,
  `color` text DEFAULT NULL,
  `low_flammability_limit` text DEFAULT NULL,
  `vapour_pressure` text DEFAULT NULL,
  `vapour_density` text DEFAULT NULL,
  `relative_density` text DEFAULT NULL,
  `pcp_other_info` mediumtext DEFAULT NULL,
  `pcp_other_sec_info` mediumtext DEFAULT NULL,
  `legislation_safety` mediumtext DEFAULT NULL,
  `legislation_eu` mediumtext DEFAULT NULL,
  `legislation_chemical_safety_assessment` mediumtext DEFAULT NULL,
  `legislation_other_info` mediumtext DEFAULT NULL,
  `add_info_changes` mediumtext DEFAULT NULL,
  `add_info_acronyms` mediumtext DEFAULT NULL,
  `add_info_references` mediumtext DEFAULT NULL,
  `add_info_HazCom` mediumtext DEFAULT NULL,
  `add_info_GHS` mediumtext DEFAULT NULL,
  `add_info_training` mediumtext DEFAULT NULL,
  `add_info_other` mediumtext DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `system_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `key_name` VARCHAR(255) NOT NULL ,
    `value` LONGTEXT NOT NULL , 
    `slug` VARCHAR(255) NOT NULL , 
    `type` VARCHAR(255) NOT NULL , 
    `description` VARCHAR(255) NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `system_settings` ( `key_name`, `value`, `slug`, `type`, `description`) VALUES
('SYSTEM_chkVersion', '1', 'Check for updates', 'checkbox', 'Check for updates'),
('SYSTEM_pubChem', '1', 'Enable PubChem', 'checkbox', 'Enable or disable pubChem integration'),
('SYSTEM_server_url', '', 'Server URL', 'text', 'This is your Perfumers Vault installation server URL.'),
('INTEGRATIONS_enable', '0', 'Enable integrations', 'checkbox', 'Enable or disable integrations'),
('USER_selfRegister', '0', 'Enable user registration', 'checkbox', 'Enable or disable user self registration'),
('USER_terms_url', 'https://www.perfumersvault.com/terms-of-service', 'Terms and Conditions', 'text', 'Point this to your web site that hosts the terms and conditions info for users'),
('USER_privacy_url', 'https://www.perfumersvault.com/privacy-policy', 'Privacy Policy URL', 'text', 'Point this to your web site that hosts the privacy policy info for users'),
('LIBRARY_enable', '1', 'Enanle PV Library', 'checkbox', 'Enable or disable PV Library'),
('LIBRARY_apiurl', 'https://library.perfumersvault.com/api-data/api.php', 'Library API URL', 'text', 'Library API URL'),
('announcements', '', 'Announcement', 'textarea', 'Add here any announcement for your users when login'),
('EMAIL_isEnabled', '0', 'Enable email', 'checkbox', 'Enable or disable email functions, like user welcome email when register, password reset, email confirmation etc'),
('EMAIL_smtp_host', '', 'SMPT Host', 'text', 'This is your smtp email server ip or hostname'),
('EMAIL_smtp_port', '', 'SMTP Port', 'text', 'Optional, Defaults to 25'),
('EMAIL_from', '', 'From', 'text', 'This is the From address'),
('EMAIL_from_display_name', 'Perfumers Vault', 'From display name', 'text', 'A user-friendly name for the \'From\' address (optional).'),
('EMAIL_smtp_user', '', 'Username', 'text', 'Optional field, use only if your email server requires authentication'),
('EMAIL_smtp_pass', '', 'Password', 'password', 'Optional field, use only if your email server requires authentication'),
('EMAIL_smtp_secure', '0', 'Enable SSL', 'checkbox', 'Enable secure connection if your server supports it');


CREATE TABLE `user_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `key_name` VARCHAR(255) NOT NULL ,
    `value` LONGTEXT NOT NULL , 
    `owner_id` VARCHAR(255) NOT NULL ,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `branding` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `brandName` VARCHAR(255) NULL , 
    `brandAddress` VARCHAR(255) NULL , 
    `brandEmail` VARCHAR(255) NULL , 
    `brandPhone` VARCHAR(255) NULL , 
    `brandLogo` LONGBLOB NULL , 
    `owner_id` VARCHAR(255) NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `sdsSettings` ( 
  `id` INT NOT NULL AUTO_INCREMENT , 
  `sds_disclaimer` TEXT NOT NULL DEFAULT 'PLEASE ADD A PROPER DISCLAIMER MESSAGE' , 
  `owner_id` VARCHAR(255) NOT NULL , 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `integrations_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `key_name` VARCHAR(255) NOT NULL ,
    `value` LONGTEXT NOT NULL , 
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `session_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` varchar(255) NOT NULL,
  `remaining_time` decimal(10,2) NOT NULL,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `formulasMetaData` ADD INDEX(`owner_id`);
ALTER TABLE `formulas` ADD INDEX(`owner_id`);
ALTER TABLE `formulas` ADD INDEX(`fid`);
ALTER TABLE `formulas` ADD INDEX(`ingredient`);
ALTER TABLE `ingredients` ADD INDEX(`owner_id`);
ALTER TABLE `ingredients` ADD INDEX(`name`);