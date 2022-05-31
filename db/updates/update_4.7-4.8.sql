ALTER TABLE `users` DROP `username`;
TRUNCATE `users`;
ALTER TABLE `pv_online` DROP `id`, DROP `email`, DROP `password`;
