ALTER TABLE `user_prefs` ADD `pref_tab` VARCHAR(255) NULL AFTER `pref_data`; 
ALTER TABLE `settings`
  DROP `label_printer_addr`,
  DROP `label_printer_model`,
  DROP `label_printer_size`,
  DROP `label_printer_font_size`;
