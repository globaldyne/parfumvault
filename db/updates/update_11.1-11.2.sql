TRUNCATE `IFRALibrary`;

ALTER TABLE `ingredients` CHANGE `cat2` `cat2` FLOAT NOT NULL DEFAULT '100', CHANGE `cat3` `cat3` FLOAT NOT NULL DEFAULT '100', CHANGE `cat4` `cat4` FLOAT NOT NULL DEFAULT '100', CHANGE `cat5A` `cat5A` FLOAT NOT NULL DEFAULT '100', CHANGE `cat5B` `cat5B` FLOAT NOT NULL DEFAULT '100', CHANGE `cat5C` `cat5C` FLOAT NOT NULL DEFAULT '100', CHANGE `cat5D` `cat5D` FLOAT NOT NULL DEFAULT '100', CHANGE `cat6` `cat6` FLOAT NOT NULL DEFAULT '100', CHANGE `cat7A` `cat7A` FLOAT NOT NULL DEFAULT '100', CHANGE `cat7B` `cat7B` FLOAT NOT NULL DEFAULT '100', CHANGE `cat8` `cat8` FLOAT NOT NULL DEFAULT '100', CHANGE `cat9` `cat9` FLOAT NOT NULL DEFAULT '100', CHANGE `cat10A` `cat10A` FLOAT NOT NULL DEFAULT '100', CHANGE `cat10B` `cat10B` FLOAT NOT NULL DEFAULT '100', CHANGE `cat11A` `cat11A` FLOAT NOT NULL DEFAULT '100', CHANGE `cat11B` `cat11B` FLOAT NOT NULL DEFAULT '100', CHANGE `cat12` `cat12` FLOAT NOT NULL DEFAULT '100';

ALTER TABLE `IFRALibrary` CHANGE `cat1` `cat1` FLOAT NULL DEFAULT '100', CHANGE `cat2` `cat2` FLOAT NULL DEFAULT '100', CHANGE `cat3` `cat3` FLOAT NULL DEFAULT '100', CHANGE `cat4` `cat4` FLOAT NULL DEFAULT '100', CHANGE `cat5A` `cat5A` FLOAT NULL DEFAULT '100', CHANGE `cat5B` `cat5B` FLOAT NULL DEFAULT '100', CHANGE `cat5C` `cat5C` FLOAT NULL DEFAULT '100', CHANGE `cat5D` `cat5D` FLOAT NULL DEFAULT '100', CHANGE `cat6` `cat6` FLOAT NULL DEFAULT '100', CHANGE `cat7A` `cat7A` FLOAT NULL DEFAULT '100', CHANGE `cat7B` `cat7B` FLOAT NULL DEFAULT '100', CHANGE `cat8` `cat8` FLOAT NULL DEFAULT '100', CHANGE `cat9` `cat9` FLOAT NULL DEFAULT '100', CHANGE `cat10A` `cat10A` FLOAT NULL DEFAULT '100', CHANGE `cat10B` `cat10B` FLOAT NULL DEFAULT '100', CHANGE `cat11A` `cat11A` FLOAT NULL DEFAULT '100', CHANGE `cat11B` `cat11B` FLOAT NULL DEFAULT '100', CHANGE `cat12` `cat12` FLOAT NULL DEFAULT '100';