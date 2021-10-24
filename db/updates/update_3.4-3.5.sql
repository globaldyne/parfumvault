CREATE TABLE `ingSafetyInfo` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingID` int(11) NOT NULL,
 `GHS` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `ingSafetyInfo` ADD UNIQUE(`id`); 
