RENAME TABLE `lids` TO `inventory_accessories`;
ALTER TABLE `inventory_accessories` CHANGE `style` `name` VARCHAR(255) NOT NULL; 
ALTER TABLE `inventory_accessories` CHANGE `colour` `accessory` VARCHAR(255) NOT NULL; 
ALTER TABLE `inventory_accessories` CHANGE `supplier_link` `supplier_link` VARCHAR(255) NOT NULL;
ALTER TABLE `inventory_accessories` CHANGE `supplier` `supplier` VARCHAR(255) NOT NULL; 
ALTER TABLE `inventory_accessories` ADD UNIQUE(`name`);
ALTER TABLE `inventory_compounds` ADD UNIQUE(`name`);
ALTER TABLE `bottles` ADD UNIQUE(`name`);
ALTER TABLE `customers` ADD UNIQUE(`name`);
ALTER TABLE `ingSuppliers` ADD UNIQUE(`name`);
ALTER TABLE `formulasMetaData` CHANGE `sex` `gender` VARCHAR(255) NULL DEFAULT 'unisex'; 
UPDATE `formulaCategories` SET type = 'gender' WHERE type = 'sex';