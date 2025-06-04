-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 04, 2025 at 06:18 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbca25`
--

-- --------------------------------------------------------

--
-- Table structure for table `Acces_log`
--

CREATE TABLE `Acces_log` (
  `idAcces` int(11) NOT NULL,
  `Date_heure_entree` datetime DEFAULT NULL,
  `Resultat_tentative` varchar(45) DEFAULT NULL,
  `Date_heure_sortie` datetime DEFAULT NULL,
  `Presence` tinyint(4) DEFAULT NULL,
  `Etat_porte` tinyint(4) DEFAULT NULL,
  `UID` varchar(50) NOT NULL,
  `IdUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Carte`
--

CREATE TABLE `Carte` (
  `idCarte` int(11) NOT NULL,
  `RFID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Carte`
--

INSERT INTO `Carte` (`idCarte`, `RFID`) VALUES
(1, '215121'),
(2, '442542412'),
(3, '59756708130'),
(4, '288366429724'),
(88, '4323FA86'),
(89, '4323FA86'),
(90, '3053188703');

-- --------------------------------------------------------

--
-- Table structure for table `Connect_log_admin`
--

CREATE TABLE `Connect_log_admin` (
  `idconnexion` int(11) NOT NULL,
  `HeureConnexion` datetime DEFAULT NULL,
  `idAdmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Connect_log_admin`
--

INSERT INTO `Connect_log_admin` (`idconnexion`, `HeureConnexion`, `idAdmin`) VALUES
(1, '2025-04-09 14:00:33', 1),
(2, '2025-04-09 14:05:20', 75),
(3, '2025-04-09 14:09:08', 1),
(4, '2025-04-09 14:26:10', 1),
(5, '2025-04-09 14:26:29', 1),
(6, '2025-04-09 14:27:24', 1),
(7, '2025-04-09 14:28:35', 1),
(8, '2025-04-09 14:28:55', 1),
(9, '2025-04-09 14:37:59', 1),
(10, '2025-04-09 14:46:55', 1),
(11, '2025-04-09 14:52:18', 1),
(12, '2025-04-09 14:53:21', 1),
(13, '2025-04-09 15:03:53', 1),
(14, '2025-04-09 15:15:27', 1),
(15, '2025-04-09 15:32:18', 1),
(16, '2025-04-09 16:52:09', 1),
(17, '2025-04-09 18:06:09', 1),
(18, '2025-04-09 18:08:20', 1),
(19, '2025-04-09 20:49:26', 1),
(20, '2025-04-09 23:13:03', 1),
(21, '2025-04-11 15:22:42', 1),
(22, '2025-04-11 15:40:38', 1),
(23, '2025-04-11 21:42:13', 1),
(24, '2025-04-12 15:08:48', 1),
(25, '2025-04-12 15:38:28', 91),
(26, '2025-04-14 15:52:09', 1),
(27, '2025-04-14 16:07:03', 1),
(28, '2025-04-14 16:43:10', 1),
(29, '2025-04-14 16:44:41', 1),
(30, '2025-05-23 13:09:53', 136),
(31, '2025-05-23 17:34:06', 136),
(32, '2025-05-23 17:39:54', 137),
(33, '2025-05-26 08:39:39', 1),
(34, '2025-05-26 09:06:08', 136),
(35, '2025-05-26 10:48:53', 136),
(36, '2025-05-26 13:35:29', 136),
(37, '2025-05-26 13:43:03', 137),
(38, '2025-05-26 13:49:20', 137),
(39, '2025-05-26 15:01:40', 1),
(40, '2025-05-26 15:35:25', 1),
(41, '2025-05-26 15:39:33', 137),
(42, '2025-05-26 15:47:44', 137),
(43, '2025-05-26 16:32:32', 1),
(44, '2025-05-26 16:33:08', 136),
(45, '2025-05-26 16:37:01', 137),
(46, '2025-05-26 21:38:03', 136),
(47, '2025-05-27 14:47:15', 1),
(48, '2025-05-27 16:48:13', 137),
(49, '2025-05-27 17:34:10', 136),
(50, '2025-05-28 10:18:49', 137),
(51, '2025-05-28 20:28:11', 136),
(52, '2025-05-30 20:07:04', 136),
(53, '2025-06-04 09:44:51', 136),
(54, '2025-06-04 10:34:54', 1),
(55, '2025-06-04 10:40:59', 1),
(56, '2025-06-04 10:45:02', 137),
(57, '2025-06-04 12:00:49', 136);

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `idUser` int(11) NOT NULL,
  `Nom` varchar(45) DEFAULT NULL,
  `Prenom` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Tel` int(11) DEFAULT NULL,
  `Motif` varchar(45) DEFAULT NULL,
  `Date_debut` datetime DEFAULT NULL,
  `Date_fin` datetime DEFAULT NULL,
  `Fonction` varchar(45) DEFAULT NULL,
  `Verifier` tinyint(4) DEFAULT NULL,
  `SuperUser` tinyint(4) DEFAULT NULL,
  `Identifiant` varchar(45) DEFAULT NULL,
  `Mdp` varchar(255) DEFAULT NULL,
  `Mail_envoye` tinyint(1) DEFAULT NULL,
  `Mail_verif` tinyint(1) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `idCarte` int(11) DEFAULT NULL,
  `date_demande` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`idUser`, `Nom`, `Prenom`, `Email`, `Tel`, `Motif`, `Date_debut`, `Date_fin`, `Fonction`, `Verifier`, `SuperUser`, `Identifiant`, `Mdp`, `Mail_envoye`, `Mail_verif`, `token`, `idCarte`, `date_demande`) VALUES
(1, 'jojo', 'jojo', 'raphael.rodriguez74@gmail.com', 142521452, NULL, NULL, NULL, 'Admin', NULL, 1, 'jojo', '$2y$10$ayUJzg/GVZlxB4yoqA0voO6w/Ga.FvliThApn9kr3MNO.UMHQfk9m', 0, 0, '', 4, '2025-05-06 11:29:39'),
(91, 'Aydogdu', 'Hamza Akif', 'hamza.aydogdu@icloud.com', 789554436, NULL, NULL, NULL, 'Admin', NULL, 1, 'haykif', '$2y$10$V0/T10Bt2ej6ruB.djaV5.mZGJvC/J2rofkdWVIWfZPp5m2wMIK5.', NULL, NULL, NULL, NULL, '2025-05-06 11:29:39'),
(94, 'Martin', 'Sophie', 'wenagi5363@anlocc.com', 655781345, 'Inspection des lieux', '2025-04-17 00:00:00', '2025-04-17 00:00:00', 'Responsable RH', 1, NULL, NULL, NULL, 1, 1, '0926872ca366b0012725990c2d6b67ed34483f9eb2c39dd5d241f07edbd40106617eaa52e3473d153b0862da9f9ac481cf4f', NULL, '2025-05-06 11:29:39'),
(95, 'Durand', 'Maxime', 'yeyat29780@anlocc.com', 623987654, 'Installation d’équipement', '2025-04-18 00:00:00', '2025-04-19 00:00:00', 'Ingénieur', 1, NULL, NULL, NULL, 1, 1, 'bebfe1f847051ca36b78142ed741cd30f81ba9711fb813e0c213852d5246cc2d8a0f277ce2638fc41823438f4199b4f26aca', NULL, '2025-05-06 11:29:39'),
(114, 'Joestar', 'Jonathan', 'lemay23523@deusa7.com', 623154852, 'Maintenance Technique', '2025-05-14 00:00:00', '2025-05-20 00:00:00', 'Etudiant', 1, NULL, NULL, NULL, 1, 1, 'a313752a99b2ba6774bd944eaa731ffd57b0634fb183c0b970d2c1f6a66a75a451c1856f39c4a556005dcb89c5758d23fa06', NULL, '2025-05-13 17:43:53'),
(115, 'jojo', 'palas', 'zombidie74@gmail.com', 612151412, NULL, NULL, NULL, 'Admin', NULL, 1, 'palas', '$2y$10$hb7F2U6ONCps8tXkHggOVumROLu6dqR6JJhOI5ST5b0cug1li6Aca', NULL, NULL, NULL, NULL, '2025-05-13 17:51:05'),
(116, 'kjûzfvks', 'asdlfjbalsdf', 'fjhgbsf@ajhsdfv.com', 777777777, 'aslkdjfhlajksdhf', '2025-05-22 00:00:00', '2025-05-15 00:00:00', 'asdjhfls', NULL, NULL, NULL, NULL, 1, 0, '48c7826efa0250379c9e817be9be9649e0f66a0923485defcaff4b6a80aed9f984d716714a774dd7239da528ce0918616d35', NULL, '2025-05-13 18:02:04'),
(118, 'LePen', 'Jean-Marie', 'pinoy99409@benznoi.com', 455555559, 'Il manque des Nazis, où est Hitler?', '2025-05-21 00:00:00', '2025-06-27 00:00:00', 'Fascho', NULL, NULL, NULL, NULL, 1, 0, '34c165ee45b7673620866d2af42a29d530fe56226d2c0ba975d44f0fa0bea432828eb333a6ce47da58bb718ab15d980b442a', NULL, '2025-05-13 20:07:50'),
(124, 'Giovanna', 'Giorno', 'meheme9061@magpit.com', 625951548, 'Maintenance Informatique', '2025-05-20 00:00:00', '2025-05-28 00:00:00', 'Etudiant', NULL, NULL, NULL, NULL, 1, 0, 'bb99498850894a6605f5f07218c2e590c2984448a6500a238ed1e365dee5a75aeb77b4b85c7691014d099b446066b3411bb3', NULL, '2025-05-20 11:42:11'),
(136, 'Hamza', 'Aydogdu', 'h.aykif@caramail.com', 554886694, NULL, NULL, NULL, 'Admin', NULL, 1, 'haykif1', '$2y$10$aXOeVQQdA7nI5Qoeul04Q.KAgw8h7iI75dqMqH326XvgiSvhV29OO', NULL, NULL, NULL, NULL, '2025-05-22 23:28:54'),
(137, 'Rodriguez-Glise', 'Raphaël', 'ceyiv87902@leabro.com', 463558245, NULL, NULL, NULL, 'Admin', NULL, 1, 'rafa', '$2y$10$K/AjJcFsInhnIc3FrIWmjuwFB6FBjn9bVZcUs68xu/ca.UXp7IZDm', NULL, NULL, NULL, NULL, '2025-05-23 17:37:50'),
(146, 'Nigga', 'neger', 'jojocpourlesgays@yahoo.fr', 667666766, 'parce que', '0001-01-01 00:00:00', '9999-09-09 00:00:00', 'Bufflage professionnel', NULL, NULL, NULL, NULL, 1, 0, 'ffbfa48b51b277532fd7c0db1ce80abd463451e03b4b6e1361a5b7f1d2462166f4c8e528aa3be48e0302407db62f1bf09a30', NULL, '2025-06-04 11:02:51'),
(147, 'Nigga', 'neger', 'clovismondon@charles-poncet.net', 667666766, 'parce que', '0001-01-01 00:00:00', '9999-09-09 00:00:00', 'Bufflage professionnel', NULL, NULL, NULL, NULL, 1, 1, '0d01ffa4143a271354289a43cad900a08b050534dcf2518aab478b9fdbd90efae9ae040fdcf544c6142f64613d48fbfc00d6', NULL, '2025-06-04 11:05:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Acces_log`
--
ALTER TABLE `Acces_log`
  ADD PRIMARY KEY (`idAcces`),
  ADD KEY `fk_Acess_log_ Inscription user1_idx` (`IdUser`);

--
-- Indexes for table `Carte`
--
ALTER TABLE `Carte`
  ADD PRIMARY KEY (`idCarte`);

--
-- Indexes for table `Connect_log_admin`
--
ALTER TABLE `Connect_log_admin`
  ADD PRIMARY KEY (`idconnexion`),
  ADD KEY `fk_Connect_log_admin_User_idx` (`idAdmin`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`idUser`),
  ADD KEY `fk_idCarte` (`idCarte`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Acces_log`
--
ALTER TABLE `Acces_log`
  MODIFY `idAcces` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=755;

--
-- AUTO_INCREMENT for table `Carte`
--
ALTER TABLE `Carte`
  MODIFY `idCarte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `Connect_log_admin`
--
ALTER TABLE `Connect_log_admin`
  MODIFY `idconnexion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Acces_log`
--
ALTER TABLE `Acces_log`
  ADD CONSTRAINT `fk_Acess_log_ Inscription user1` FOREIGN KEY (`IdUser`) REFERENCES `User` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Connect_log_admin`
--
ALTER TABLE `Connect_log_admin`
  ADD CONSTRAINT `fk_Connect_log_admin_WhiteList1` FOREIGN KEY (`idAdmin`) REFERENCES `User` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `fk_idCarte` FOREIGN KEY (`idCarte`) REFERENCES `Carte` (`idCarte`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
