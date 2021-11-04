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

CREATE TABLE `formulasRevisions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `ingredient` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `ingredient_id` varchar(11) COLLATE utf8_bin DEFAULT NULL,
 `concentration` decimal(5,2) DEFAULT 100.00,
 `dilutant` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `quantity` decimal(8,3) DEFAULT NULL,
 `notes` varchar(255) COLLATE utf8_bin DEFAULT NULL,
 `exclude_from_summary` int(11) NOT NULL DEFAULT 0,
 `revision` int(11) NOT NULL,
 `revisionDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `formulasMetaData` ADD `revision` INT NOT NULL DEFAULT '0' AFTER `catClass`; 
ALTER TABLE `formulasMetaData` ADD `finalType` INT NOT NULL DEFAULT '100' AFTER `revision`;
UPDATE allergens SET percentage = CONVERT( if( percentage REGEXP '^[0-9]+$', percentage, '0' ), DECIMAL(8, 4) );
ALTER TABLE `allergens` CHANGE `percentage` `percentage` DECIMAL(8,4) NOT NULL; 