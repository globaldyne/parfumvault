RENAME TABLE `lids` TO `accessories`;
ALTER TABLE `accessories` CHANGE `style` `name` VARCHAR(255) NOT NULL; 
ALTER TABLE `accessories` CHANGE `colour` `accessory` VARCHAR(255) NOT NULL; 
ALTER TABLE `accessories` CHANGE `supplier_link` `supplier_link` VARCHAR(255) NOT NULL;
ALTER TABLE `accessories` CHANGE `supplier` `supplier` VARCHAR(255) NOT NULL; 
