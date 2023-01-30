ALTER TABLE `suppliers` CHANGE `stock` `stock` FLOAT NOT NULL DEFAULT '0';

CREATE TABLE `perfumeTypes` (
 	`id` INT NOT NULL AUTO_INCREMENT, 
	`name` VARCHAR(255) NOT NULL, 
	`concentration` INT NOT NULL, 
	`description` VARCHAR(255) NOT NULL, 
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `perfumeTypes` (`id`, `name`, `concentration`, `description`) VALUES (NULL, 'EDP', '20', 'Eau de Parfum - Contains between 15 - 20% of formula concentration'), (NULL, 'EDT', '15', 'Eau de Toilette - Contains between 5 - 15% of formula concentration'), (NULL, 'EDC', '4', 'Eau de Cologne - Contains between 2 - 4% of formula concentration'), (NULL, 'Perfume', '30', 'Perfume - Contains between 20 - 30% of formula concentration');

ALTER TABLE `settings` DROP `EDP`, DROP `EDT`, DROP `EDC`, DROP `Parfum`;

ALTER TABLE `suppliers` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `stock`;
