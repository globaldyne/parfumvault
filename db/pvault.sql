--
-- Database: `pvault`
--

-- --------------------------------------------------------

--
-- Table structure for table `formulas`
--

CREATE TABLE `formulas` (
  `id` int(11) NOT NULL,
  `fid` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ingredient` varchar(255) COLLATE utf8_bin NOT NULL,
  `ingredient_id` varchar(11) COLLATE utf8_bin NOT NULL,
  `concentration` int(5) NOT NULL DEFAULT 100,
  `quantity` varchar(10) COLLATE utf8_bin NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ingCategory`
--

CREATE TABLE `ingCategory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ingCategory`
--

INSERT INTO `ingCategory` (`id`, `name`, `notes`) VALUES
(1, 'Amber', 'Ambery note'),
(2, 'Woody', 'Woody note'),
(3, 'Floral', 'Floral, Rose');

-- --------------------------------------------------------

--
-- Table structure for table `ingProfiles`
--

CREATE TABLE `ingProfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ingProfiles`
--

INSERT INTO `ingProfiles` (`id`, `name`, `notes`) VALUES
(1, 'Base', 'Base Note'),
(2, 'Top', 'Top Note'),
(3, 'Medium', 'Medium Note');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `strength` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `IFRA` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cas` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `SDS` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `supplier_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `price` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `tenacity` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `chemical_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `flash_point` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `appearance` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` text COLLATE utf8_bin DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ml` int(5) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ingStrength`
--

CREATE TABLE `ingStrength` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ingSuppliers`
--

CREATE TABLE `ingSuppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `notes` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ingTypes`
--

CREATE TABLE `ingTypes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_addr` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_model` varchar(225) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_size` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `label_printer_font_size` int(11) DEFAULT 80,
  `currency` varchar(40) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `logo`, `label_printer_addr`, `label_printer_model`, `label_printer_size`, `label_printer_font_size`, `currency`) VALUES
(1, NULL, '', '', '12', 70, '&pound;');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `Name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `formulas`
--
ALTER TABLE `formulas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingCategory`
--
ALTER TABLE `ingCategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingProfiles`
--
ALTER TABLE `ingProfiles`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `ingStrength`
--
ALTER TABLE `ingStrength`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingSuppliers`
--
ALTER TABLE `ingSuppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingTypes`
--
ALTER TABLE `ingTypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `formulas`
--
ALTER TABLE `formulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingCategory`
--
ALTER TABLE `ingCategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ingProfiles`
--
ALTER TABLE `ingProfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingStrength`
--
ALTER TABLE `ingStrength`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingSuppliers`
--
ALTER TABLE `ingSuppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingTypes`
--
ALTER TABLE `ingTypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
