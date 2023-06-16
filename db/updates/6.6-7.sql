DROP TABLE `pv_online`;
ALTER TABLE `formulasRevisions` ADD `revisionMethod` VARCHAR(255) NULL AFTER `revisionDate`;
ALTER TABLE `suppliers` ADD `status` INT NOT NULL DEFAULT '1' COMMENT '1 = Available\r\n2 = Limited Availability\r\n3 = Not available' AFTER `stock`; 