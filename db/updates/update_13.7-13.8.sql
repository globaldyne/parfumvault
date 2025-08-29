DELETE FROM `system_settings` WHERE `system_settings`.`key_name` = 'SYSTEM_chkVersion';
UPDATE `system_settings` SET `value` = '#' WHERE `system_settings`.`key_name` = 'USER_terms_url';
UPDATE `system_settings` SET `value` = '#' WHERE `system_settings`.`key_name` = 'USER_privacy_url';