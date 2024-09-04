-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2024 at 12:09 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam-framework`
--
CREATE DATABASE IF NOT EXISTS `exam-framework` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `exam-framework`;

-- --------------------------------------------------------

--
-- Table structure for table `affectation`
--

CREATE TABLE `affectation` (
  `id` int NOT NULL,
  `collaborateur_id` int NOT NULL,
  `restaurant_id` int NOT NULL,
  `fonction_id` int NOT NULL,
  `date_debut` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `date_fin` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affectation`
--

INSERT INTO `affectation` (`id`, `collaborateur_id`, `restaurant_id`, `fonction_id`, `date_debut`, `date_fin`) VALUES
(1, 1, 3, 4, '2024-08-28 07:42:00', NULL),
(2, 3, 3, 3, '2024-08-28 12:02:00', NULL),
(3, 3, 2, 2, '2024-03-04 08:06:00', '2024-08-22 15:25:00'),
(4, 3, 1, 2, '2024-08-28 10:01:00', NULL),
(5, 2, 3, 3, '2024-08-19 15:49:00', '2024-08-23 15:49:00'),
(6, 4, 3, 2, '2024-08-29 10:49:00', NULL),
(7, 3, 1, 1, '2024-08-19 11:24:00', '2024-08-27 11:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `collaborateur`
--

CREATE TABLE `collaborateur` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isadmin` tinyint(1) NOT NULL,
  `date_embauche` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_verified` tinyint(1) NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collaborateur`
--

INSERT INTO `collaborateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `isadmin`, `date_embauche`, `is_verified`, `photo`) VALUES
(1, 'mickael.durand@outlook.com', '[]', '$2y$13$Pfiw7HImmZbrosrO7ok50.BF9ZCLaYw1/OLdiJ3q8.1.Qzjuz9RjG', 'Durand', 'Mickaël', 1, '2024-08-19 10:16:00', 0, '66cf15e28b7cc.jpg'),
(2, 'bstaron@example.com', '[]', NULL, 'Staron', 'Blandine', 0, '2024-08-05 15:39:00', 1, '66d022a1eee37.jpg'),
(3, 'blucao@example.com', '[]', NULL, 'Lucao', 'Baptista', 0, '2024-08-12 15:41:00', 1, '66cf1886cbd5e.jpg'),
(4, 'cdelaistre@example.com', '[]', NULL, 'Delaistre', 'Corentin', 0, '2024-07-15 15:43:00', 1, '66d0228f9e532.jpg'),
(7, 'cmartin@example.com', '[]', NULL, 'Martin', 'Claire', 0, '2024-03-05 13:51:00', 1, '66d060cd96eab.jpg'),
(8, 'ldubois@example.com', '[]', NULL, 'Dubois', 'Lucas', 0, '2012-01-29 13:51:00', 1, '66d060ee6418c.jpg'),
(9, 'slefevre@example.com', '[]', NULL, 'Lefevre', 'Sophie', 0, '2021-06-07 13:52:00', 1, '66d0610f1147d.jpg'),
(10, 'tbernard@example.com', '[]', NULL, 'Bernard', 'Thomas', 0, '2023-05-29 13:52:00', 1, '66d0612da4173.jpg'),
(11, 'erichard@example.com', '[]', NULL, 'Richard', 'Emma', 0, '2024-01-10 13:53:00', 1, '66d06149a25de.jpg'),
(12, 'apetit@example.com', '[]', NULL, 'Petit', 'Antoine', 0, '1990-02-28 13:53:00', 1, '66d06166a3581.jpg'),
(13, 'jgarnier@example.com', '[]', NULL, 'Garnier', 'Julie', 0, '2025-01-06 13:54:00', 1, '66d061843e648.jpg'),
(14, 'aleroy@example.com', '[]', NULL, 'Leroy', 'Adrien', 0, '2022-09-05 13:55:00', 1, '66d061a0a384d.jpg'),
(15, 'cmoreau@example.com', '[]', NULL, 'Moreau', 'Chloé', 0, '2024-09-02 13:55:00', 1, '66d061bc4fb01.jpg'),
(16, 'mroux@example.com', '[]', NULL, 'Roux', 'Maxime', 0, '2015-06-01 13:55:00', 1, '66d061de2ce07.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240826074223', '2024-08-26 07:42:41', 354),
('DoctrineMigrations\\Version20240826075104', '2024-08-26 07:51:17', 36),
('DoctrineMigrations\\Version20240826081239', '2024-08-26 08:12:49', 187),
('DoctrineMigrations\\Version20240826094742', '2024-08-26 09:47:58', 73);

-- --------------------------------------------------------

--
-- Table structure for table `fonction`
--

CREATE TABLE `fonction` (
  `id` int NOT NULL,
  `intitule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fonction`
--

INSERT INTO `fonction` (`id`, `intitule`) VALUES
(1, 'Manager'),
(2, 'Equipier polyvalent'),
(3, 'Formateur'),
(4, 'Directeur');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE `restaurant` (
  `id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`id`, `nom`, `adresse`, `code_postal`, `ville`, `photo`) VALUES
(1, 'WkDo Saint-Etienne', '1 rue de la Terrasse', '42000', 'Saint-Etienne', '66cc51b84bc73.jpg'),
(2, 'WkDo Argenteuil', '55 Rue Paul Vaillant Couturier', '95100', 'ARGENTEUIL', '66cc52386fad3.jpg'),
(3, 'WkDo Colombes', '41 Rue du Bournard', '92700', 'COLOMBES', '66cc527859592.png'),
(7, 'WkDo Loudun', 'Restaurant WkDonald\'s', '86200', 'LOUDUN', '66d05d2f7dc11.jpg'),
(8, 'WkDo Saint Junien', 'Avenue Nelson Mandela', '87200', 'SAINT JUNIEN', '66d05d637b62b.jpg'),
(9, 'WkDo Pontchateau', 'C.Cial La Cadivais', '44160', 'PONTCHATEAU', '66d05d926e589.png'),
(10, 'WkDo Figeac', 'Restaurant McDonald\'s de Figeac', '46100', 'FIGEAC', '66d05dbea7401.jpg'),
(11, 'WkDo Hennebont', 'ZAC de la Gardeloupe', '56700', 'HENNEBONT', '66d05ddd99143.jpg'),
(12, 'WkDo Gaillac', 'McDonald\'s', '81600', 'GAILLAC', '66d05e07b8d83.jpg'),
(13, 'WcDo Ploermel', 'Rue de Ronsouze', '56800', 'PLOERMEL', '66d05e3216249.png'),
(14, 'WkDo Strasbourg', '10 place de la Gare', '67000', 'STRASBOURG', '66d05e5ccb182.jpg'),
(15, 'WkDo Prades', 'McDonald\'s Centre commercial Super U, N116', '66500', 'PRADES', '66d05e84eeb8a.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affectation`
--
ALTER TABLE `affectation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F4DD61D3A848E3B1` (`collaborateur_id`),
  ADD KEY `IDX_F4DD61D3B1E7706E` (`restaurant_id`),
  ADD KEY `IDX_F4DD61D357889920` (`fonction_id`);

--
-- Indexes for table `collaborateur`
--
ALTER TABLE `collaborateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `fonction`
--
ALTER TABLE `fonction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affectation`
--
ALTER TABLE `affectation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `collaborateur`
--
ALTER TABLE `collaborateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `fonction`
--
ALTER TABLE `fonction`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `affectation`
--
ALTER TABLE `affectation`
  ADD CONSTRAINT `FK_F4DD61D357889920` FOREIGN KEY (`fonction_id`) REFERENCES `fonction` (`id`),
  ADD CONSTRAINT `FK_F4DD61D3A848E3B1` FOREIGN KEY (`collaborateur_id`) REFERENCES `collaborateur` (`id`),
  ADD CONSTRAINT `FK_F4DD61D3B1E7706E` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
