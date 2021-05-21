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

INSERT INTO suppliers (`ingID`,`ingSupplierID`,`supplierLink`,`size`,`price`,`manufacturer`) SELECT id,'1',supplier_link,ml,price,manufacturer FROM ingredients;
ALTER TABLE `ingredients` DROP `supplier`, DROP `supplier_link`, DROP `price`, DROP `ml`, DROP `manufacturer`;
ALTER TABLE `cart` DROP `supplier`, DROP `supplier_link`; 
ALTER TABLE `cart` ADD `ingID` INT NOT NULL AFTER `id`; 
