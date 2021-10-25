CREATE TABLE `ingSafetyInfo` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ingID` int(11) NOT NULL,
 `GHS` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `ingSafetyInfo` ADD UNIQUE(`id`); 

CREATE TABLE `pictograms` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `code` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `pictograms` (`id`, `name`, `code`) VALUES (NULL, 'Explosive', '1'), (NULL, 'Flammable', '2'), (NULL, 'Oxidising', '3'), (NULL, 'Gas under pressure', '4'), (NULL, 'Corrosive', '5'), (NULL, 'Acute toxicity', '6'), (NULL, 'Health hazard/Hazardous to the ozone layer', '7'), (NULL, 'Serious health hazard', '8'), (NULL, 'Hazardous to the environment', '9'); 