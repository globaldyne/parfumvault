RENAME TABLE `allergens` TO `ingredient_compounds`;
ALTER TABLE `ingredient_compounds` ADD `GHS` TEXT NOT NULL DEFAULT '-' AFTER `toDeclare`; 
