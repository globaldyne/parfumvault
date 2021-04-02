ALTER TABLE `formulasMetaData` ADD `isProtected` INT NOT NULL DEFAULT '0' AFTER `image`; 
ALTER TABLE `settings` ADD `multi_dim_perc` INT NOT NULL DEFAULT '0' AFTER `pubchem_view`; 
