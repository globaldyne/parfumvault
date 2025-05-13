CREATE TABLE `ingredientLabels` ( 
  `id` INT NOT NULL AUTO_INCREMENT,
  `ingredient_id` INT NOT NULL,
  `label_name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

-- Insert data into ingredientLabels by splitting the odor field
INSERT INTO `ingredientLabels` (`ingredient_id`, `label_name`, `owner_id`)
SELECT 
  `id` AS `ingredient_id`,
  TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`odor`, ',', numbers.n), ',', -1)) AS `label_name`,
  `owner_id`
FROM 
  (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) numbers
CROSS JOIN `ingredients`
WHERE 
  CHAR_LENGTH(`odor`) - CHAR_LENGTH(REPLACE(`odor`, ',', '')) + 1 >= numbers.n;

-- Remove the odor column from the ingredients table
ALTER TABLE `ingredients` DROP COLUMN `odor`;

