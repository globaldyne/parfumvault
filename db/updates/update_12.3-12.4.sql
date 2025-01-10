CREATE TABLE update_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prev_ver VARCHAR(10) NOT NULL COMMENT 'Previous schema version',
    new_ver VARCHAR(10) NOT NULL COMMENT 'New schema version',
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tracks schema update history';

ALTER TABLE `users` ADD `role` INT NOT NULL DEFAULT '1' AFTER `email`, ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL AFTER `role`, ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `updated_at`; 