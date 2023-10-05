ALTER TABLE `suppliers` CHANGE `stock` `stock` DECIMAL(10,3) NOT NULL;
ALTER TABLE `ingReplacements` ADD `ing_id` INT NOT NULL AFTER `id`;
ALTER TABLE `ingReplacements` ADD `ing_rep_id` INT NOT NULL AFTER `ing_cas`;

UPDATE ingReplacements SET ingReplacements.ing_id = (SELECT ingredients.id FROM ingredients WHERE ingredients.name = ingReplacements.ing_name);


UPDATE ingReplacements SET ingReplacements.ing_rep_id = (SELECT ingredients.id FROM ingredients WHERE ingredients.name = ingReplacements.ing_rep_name);

