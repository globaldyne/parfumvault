ALTER TABLE `makeFormula` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`; 
ALTER TABLE `suppliers` CHANGE `updated_at_at` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP; 
ALTER TABLE `pv_meta` CHANGE `updated_at_at` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP; 
ALTER TABLE `user_prefs` CHANGE `owner_id` `owner_id` VARCHAR(255) NOT NULL; 

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `fid` varchar(255) NOT NULL,
  `owner_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE orders (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` VARCHAR(255) NULL,
    `reference_number` VARCHAR(255) NULL,
    `supplier` VARCHAR(255) NOT NULL,
    `currency` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `tax` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `placed` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `received` DATETIME NULL,
    `notes` TEXT NULL,
    `attachments` longblob DEFAULT NULL,
    `owner_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE order_items (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` VARCHAR(255) NULL,
    `material` VARCHAR(255) NOT NULL,
    `size` VARCHAR(255) NOT NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `quantity` DECIMAL(10, 2) NOT NULL,
    `lot` VARCHAR(255) NOT NULL,
    `owner_id` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

