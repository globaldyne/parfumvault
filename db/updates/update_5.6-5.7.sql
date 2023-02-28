ALTER TABLE `makeFormula` CHANGE `fid` `fid` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `makeFormula` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `makeFormula` CHANGE `ingredient` `ingredient` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `formulasMetaData` ADD `toDo` INT NOT NULL DEFAULT '0' AFTER `status`;
ALTER TABLE `formulasMetaData` ADD `rating` INT NOT NULL DEFAULT '0' AFTER `toDo`;
