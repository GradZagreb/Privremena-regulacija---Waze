-- Host: localhost
-- Generation Time: Feb 04, 2026 at 01:59 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `korisnik`
--

CREATE TABLE `korisnik` (
  `id` int NOT NULL,
  `osoba` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `razina` int NOT NULL,
  `slika` varchar(200) DEFAULT NULL,
  `napomena` text,
  `zadnjaPrijava` datetime DEFAULT NULL,
  `datumUnosa` datetime NOT NULL,
  `unio` int NOT NULL,
  `odobren` int NOT NULL DEFAULT '0',
  `datumOdobrenja` datetime DEFAULT NULL,
  `odobrio` int DEFAULT NULL,
  `aktivan` int NOT NULL DEFAULT '0',
  `aktivirao` int DEFAULT NULL,
  `datumAktivnosti` datetime DEFAULT NULL,
  `obrisan` int NOT NULL DEFAULT '0',
  `obrisao` int DEFAULT NULL,
  `datumBrisanja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `korisnik`
--

INSERT INTO `korisnik` (`id`, `osoba`, `username`, `password`, `salt`, `razina`, `slika`, `napomena`, `zadnjaPrijava`, `datumUnosa`, `unio`, `odobren`, `datumOdobrenja`, `odobrio`, `aktivan`, `aktivirao`, `datumAktivnosti`, `obrisan`, `obrisao`, `datumBrisanja`) VALUES
(1, 1, 'admin', '6aab4e60d2a37b8c666619d24c763ffddf4e9f5d1ee3ceed8fe71c02f27923669e257ce36d7073716e9fc55bfec60e7e9b143dc2c563084f36ef19bcd373d050', '4a90dd7c63797c5557ce700e80e824e0db93637049be46f63903c9ea28f1d399', 1, NULL, NULL, '2026-02-04 13:58:54', '2019-09-03 10:32:00', 1, 1, NULL, 1, 1, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `korisnikrazina`
--

CREATE TABLE `korisnikrazina` (
  `id` int NOT NULL,
  `naziv` varchar(50) COLLATE utf8mb4_croatian_ci NOT NULL,
  `obrisano` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `korisnikrazina`
--

INSERT INTO `korisnikrazina` (`id`, `naziv`, `obrisano`) VALUES
(1, 'Admin', 0),
(2, 'Grad Zagreb', 0),
(3, 'ZG ceste', 0);

-- --------------------------------------------------------

--
-- Table structure for table `korisniktoken`
--

CREATE TABLE `korisniktoken` (
  `id` int NOT NULL,
  `korisnik` int NOT NULL,
  `token` varchar(256) NOT NULL,
  `vrsta` int NOT NULL COMMENT '1-zapamti me, 2-registracija',
  `vrijediDo` datetime NOT NULL,
  `datumUnosa` datetime NOT NULL,
  `obrisano` int NOT NULL DEFAULT '0',
  `datumBrisanja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- --------------------------------------------------------

--
-- Table structure for table `osoba`
--

CREATE TABLE `osoba` (
  `id` int NOT NULL,
  `ime` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prezime` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `napomena` text COLLATE utf8mb4_general_ci,
  `datumUnosa` datetime NOT NULL,
  `unio` int NOT NULL,
  `uredio` int DEFAULT NULL,
  `datumUredjivanja` datetime DEFAULT NULL,
  `obrisana` int NOT NULL DEFAULT '0',
  `obrisao` int DEFAULT NULL,
  `datumBrisanja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `osoba`
--

INSERT INTO `osoba` (`id`, `ime`, `prezime`, `email`, `napomena`, `datumUnosa`, `unio`, `uredio`, `datumUredjivanja`, `obrisana`, `obrisao`, `datumBrisanja`) VALUES
(1, 'Pero', 'Perić', 'admin@aplikacija.com', NULL, '2026-02-03 11:05:00', 1, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zatvaranje`
--

CREATE TABLE `zatvaranje` (
  `id` int NOT NULL,
  `stranka` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci DEFAULT NULL,
  `lokacijaPocetak` varchar(100) COLLATE utf8mb4_croatian_ci NOT NULL,
  `lokacijaKraj` varchar(100) COLLATE utf8mb4_croatian_ci NOT NULL,
  `wazeUlica` varchar(50) COLLATE utf8mb4_croatian_ci NOT NULL,
  `vrijemeOd` datetime NOT NULL,
  `vrijemeDo` datetime NOT NULL,
  `klasa` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci DEFAULT NULL,
  `uzrok` int NOT NULL DEFAULT '1' COMMENT '1-radovi, 2-događaj',
  `smjer` int NOT NULL,
  `koordinate` text COLLATE utf8mb4_croatian_ci,
  `aktivno` int NOT NULL DEFAULT '0',
  `aktivirao` int DEFAULT NULL,
  `datumAktivacije` datetime DEFAULT NULL,
  `deaktivirao` int DEFAULT NULL,
  `datumDeaktivacije` datetime DEFAULT NULL,
  `unio` int NOT NULL,
  `datumUnosa` datetime NOT NULL,
  `obrisano` int NOT NULL DEFAULT '0',
  `obrisao` int DEFAULT NULL,
  `datumBrisanja` datetime DEFAULT NULL,
  `uredio` int DEFAULT NULL,
  `datumUredjivanja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Indexes for table `korisnik`
--
ALTER TABLE `korisnik`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `osoba` (`osoba`) USING BTREE,
  ADD KEY `unio` (`unio`),
  ADD KEY `aktivirao` (`aktivirao`),
  ADD KEY `obrisao` (`obrisao`),
  ADD KEY `odobrio` (`odobrio`),
  ADD KEY `razina` (`razina`);

--
-- Indexes for table `korisnikrazina`
--
ALTER TABLE `korisnikrazina`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisniktoken`
--
ALTER TABLE `korisniktoken`
  ADD PRIMARY KEY (`id`),
  ADD KEY `korisnik` (`korisnik`);

--
-- Indexes for table `osoba`
--
ALTER TABLE `osoba`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `zatvaranje`
--
ALTER TABLE `zatvaranje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unio` (`unio`),
  ADD KEY `obrisao` (`obrisao`),
  ADD KEY `uredio` (`uredio`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `korisnik`
--
ALTER TABLE `korisnik`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `korisnikrazina`
--
ALTER TABLE `korisnikrazina`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `korisniktoken`
--
ALTER TABLE `korisniktoken`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `osoba`
--
ALTER TABLE `osoba`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zatvaranje`
--
ALTER TABLE `zatvaranje`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `korisnik`
--
ALTER TABLE `korisnik`
  ADD CONSTRAINT `korisnik_ibfk_1` FOREIGN KEY (`osoba`) REFERENCES `osoba` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `korisnik_ibfk_2` FOREIGN KEY (`unio`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `korisnik_ibfk_3` FOREIGN KEY (`obrisao`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `korisnik_ibfk_4` FOREIGN KEY (`odobrio`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `korisnik_ibfk_5` FOREIGN KEY (`aktivirao`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `korisnik_ibfk_6` FOREIGN KEY (`razina`) REFERENCES `korisnikrazina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `zatvaranje`
--
ALTER TABLE `zatvaranje`
  ADD CONSTRAINT `zatvaranje_ibfk_1` FOREIGN KEY (`unio`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `zatvaranje_ibfk_2` FOREIGN KEY (`obrisao`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `zatvaranje_ibfk_3` FOREIGN KEY (`uredio`) REFERENCES `korisnik` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

