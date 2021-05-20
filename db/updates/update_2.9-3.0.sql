CREATE TABLE `suppliers` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingSupplierID` int(11) NOT NULL,
 `ingID` int(11) NOT NULL,
 `supplierLink` varchar(255) COLLATE utf8_bin NOT NULL,
 `price` varchar(10) COLLATE utf8_bin NOT NULL,
 `size` float DEFAULT 10,
 `manufacturer` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

