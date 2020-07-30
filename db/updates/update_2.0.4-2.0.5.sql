CREATE TABLE `pv_meta` (
  `id` int(11) NOT NULL,
  `schema_ver` varchar(255) COLLATE utf8_bin NOT NULL,
  `app_ver` varchar(255) COLLATE utf8_bin NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `pv_meta` (`id`, `schema_ver`, `app_ver`, `updated_at`) VALUES
(1, '2.0.5', '2.0.5', '2020-07-30 07:53:35');


ALTER TABLE `pv_meta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);


ALTER TABLE `pv_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

