ALTER TABLE `formulas` CHANGE `quantity` `quantity` DECIMAL(10,4) NULL DEFAULT NULL; 
ALTER TABLE `makeFormula` CHANGE `quantity` `quantity` DECIMAL(10,4) NULL DEFAULT NULL; 
ALTER TABLE `makeFormula` CHANGE `overdose` `overdose` DOUBLE(10,4) NOT NULL DEFAULT '0.0000'; 
ALTER TABLE `formulasRevisions` CHANGE `quantity` `quantity` DECIMAL(10,4) NULL DEFAULT NULL; 