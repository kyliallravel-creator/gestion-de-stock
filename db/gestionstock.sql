-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 08:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestionstock`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_random_products` ()   BEGIN
  DECLARE i INT DEFAULT 1;
  DECLARE total INT DEFAULT 2000;
  DECLARE cat_count INT;
  DECLARE rand_cat INT;
  DECLARE nom_base VARCHAR(100);
  DECLARE descr TEXT;
  DECLARE qte INT;
  DECLARE exp DATE;

  SELECT COUNT(*) INTO cat_count FROM categorie;

  WHILE i <= total DO
    SET rand_cat = FLOOR(1 + RAND() * cat_count);
    SET qte = FLOOR(10 + RAND() * 990);
    SET exp = DATE_ADD(CURDATE(), INTERVAL FLOOR(RAND() * 1095) DAY); -- jusqu'à 3 ans

    -- 🔸 Choix aléatoire du nom parmi une grande liste
    SET nom_base = ELT(
      FLOOR(1 + RAND() * 150),
      'Pomme','Banane','Orange','Mangue','Fraise','Raisin','Kiwi','Pastèque','Ananas','Poire',
      'Tomate','Carotte','Concombre','Courgette','Aubergine','Poivron','Pomme de terre','Oignon','Ail','Chou',
      'Yaourt','Lait','Beurre','Crème','Fromage','Camembert','Mozzarella','Café','Thé','Chocolat',
      'Jus de fruit','Eau minérale','Coca-Cola','Pepsi','Bière','Vin rouge','Vin blanc','Whisky','Rhum','Vodka',
      'Pain','Croissant','Baguette','Biscuit','Gâteau','Chips','Céréales','Riz','Pâtes','Farine',
      'Huile','Sel','Sucre','Poivre','Épices','Ketchup','Moutarde','Mayonnaise','Sauce soja','Vinaigre',
      'Savon','Shampoing','Dentifrice','Gel douche','Crème hydratante','Déodorant','Rasoir','Serviette','Lingette','Coton',
      'Ordinateur','Clavier','Souris','Écran','Téléphone','Tablette','Casque audio','Caméra','Chargeur','Disque dur',
      'Chaussure','T-shirt','Jean','Veste','Robe','Pull','Chapeau','Ceinture','Montre','Sac à main',
      'Livre','Stylo','Cahier','Feutre','Agenda','Cartable','Lampe','Bougie','Cadre photo','Rideau',
      'Tondeuse','Perceuse','Tournevis','Marteau','Clé à molette','Scie','Peinture','Clou','Vis','Pince',
      'Balle de tennis','Ballon de foot','Casquette','Raquette','Gant de boxe','Tapis de yoga','Altère','Vélo','Tente','Sac de couchage',
      'Croquette chien','Litière chat','Gamelle','Brosse','Collier','Panier animal','Jouet chien','Shampoing animal','Friandise','Arbre à chat',
      'Rouge à lèvres','Parfum','Mascara','Fond de teint','Crayon à yeux','Crème visage','Vernis à ongle','Gel capillaire','Peigne','Brosse cheveux'
    );

    SET descr = CONCAT('Produit : ', nom_base, ' - qualité premium n°', i);

    INSERT INTO produit (nom, description, date_expiration, quantite_stock, id_categorie, created_at, updated_at)
    VALUES (CONCAT(nom_base, ' - ', i), descr, exp, qte, rand_cat, NOW(), NOW());

    SET i = i + 1;
  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`, `created_at`, `updated_at`) VALUES
(51, 'Alimentation', '2025-10-08 11:21:45', '2025-10-08 11:21:45'),
(52, 'Boissons', '2025-10-08 11:21:45', '2025-10-08 11:21:45'),
(53, 'Hygiène', '2025-10-08 11:21:45', '2025-10-08 11:21:45'),
(54, 'Électronique', '2025-10-08 11:21:45', '2025-10-08 11:21:45'),
(55, 'Papeterie', '2025-10-08 11:21:45', '2025-10-08 11:21:45'),
(56, 'alcool', '2025-10-08 11:31:51', '2025-10-08 11:31:51'),
(58, 'livre', '2025-10-08 13:50:24', '2025-10-08 13:50:24');

-- --------------------------------------------------------

--
-- Table structure for table `gerer`
--

CREATE TABLE `gerer` (
  `id_produit` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `id_journal` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_concernee` varchar(50) DEFAULT NULL,
  `id_enregistrement` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_action` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal`
--

INSERT INTO `journal` (`id_journal`, `action`, `table_concernee`, `id_enregistrement`, `id_utilisateur`, `date_action`) VALUES
(1, 'Création produit: TANGUI', 'produit', 232, 6, '2025-10-08 11:22:22'),
(2, 'entrée qte:1 produit:TANGUI', 'mouvement_stock', 1, 6, '2025-10-08 11:22:32'),
(3, 'Création catégorie: alcool', 'categorie', 56, 6, '2025-10-08 11:31:51'),
(4, 'Création catégorie: livre', 'categorie', 57, 6, '2025-10-08 13:41:01'),
(5, 'Création produit: li', 'produit', 233, 6, '2025-10-08 13:41:21'),
(6, 'Création produit: livre histoire', 'produit', 234, 6, '2025-10-08 13:42:16'),
(7, 'entrée qte:6 produit:livre histoire', 'mouvement_stock', 2, 6, '2025-10-08 13:43:02'),
(8, 'Suppression catégorie', 'categorie', 57, 6, '2025-10-08 13:44:37'),
(9, 'Création catégorie: livre', 'categorie', 58, 6, '2025-10-08 13:50:24'),
(10, 'Création produit: livre histoire', 'produit', 235, 6, '2025-10-08 13:51:03'),
(11, 'entrée qte:5 produit:livre histoire', 'mouvement_stock', 3, 6, '2025-10-08 13:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `mouvement_stock`
--

CREATE TABLE `mouvement_stock` (
  `id_mouvement` int(11) NOT NULL,
  `type_mouvement` varchar(20) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) NOT NULL CHECK (`quantite` > 0),
  `id_utilisateur` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `date_mouvement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mouvement_stock`
--

INSERT INTO `mouvement_stock` (`id_mouvement`, `type_mouvement`, `id_produit`, `quantite`, `id_utilisateur`, `description`, `date_mouvement`) VALUES
(1, 'entrée', 232, 1, 6, NULL, '2025-10-08 11:22:32'),
(2, 'entrée', 234, 6, 6, NULL, '2025-10-08 13:43:02'),
(3, 'entrée', 235, 5, 6, NULL, '2025-10-08 13:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `quantite_stock` int(11) NOT NULL DEFAULT 0 CHECK (`quantite_stock` >= 0),
  `id_categorie` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`id_produit`, `nom`, `description`, `date_expiration`, `quantite_stock`, `id_categorie`, `created_at`, `updated_at`) VALUES
(232, 'TANGUI', NULL, '2025-10-07', 301, 52, '2025-10-08 11:22:22', '2025-10-08 11:22:32'),
(233, 'li', NULL, NULL, 0, 56, '2025-10-08 13:41:21', '2025-10-08 13:41:21'),
(234, 'livre histoire', NULL, '2035-10-03', 11, NULL, '2025-10-08 13:42:16', '2025-10-08 13:43:02'),
(235, 'livre histoire', NULL, '2035-10-03', 10, 58, '2025-10-08 13:51:03', '2025-10-08 13:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `role_utilisateur`
--

CREATE TABLE `role_utilisateur` (
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_utilisateur`
--

INSERT INTO `role_utilisateur` (`role`) VALUES
('admin'),
('utilisateur');

-- --------------------------------------------------------

--
-- Table structure for table `type_mouvement_stock`
--

CREATE TABLE `type_mouvement_stock` (
  `type_mouvement` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_mouvement_stock`
--

INSERT INTO `type_mouvement_stock` (`type_mouvement`) VALUES
('entrée'),
('sortie');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `identifiant` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `identifiant`, `mot_de_passe`, `role`, `created_at`, `updated_at`) VALUES
(6, 'talla', '12345', '$2y$10$WmCnxVIm2bGxzR5sHAQ1R.JHAuZa4zXtKYa9g/m8mXozgVS9yfnoy', 'utilisateur', '2025-08-14 09:58:26', '2025-08-14 09:58:26'),
(7, 'yan', '2222', '$2y$10$pJVJsd6Hy5OdfiKoevH/peEXr62OgQGuatLRF2Smr.TDsdmox9euu', 'utilisateur', '2025-08-14 10:37:13', '2025-08-14 10:37:13'),
(8, 'daniel', 'XT', '$2y$10$S7JkZ66fTWUWvlocGLMyzO6cc96oOeeI7j2s65iBefMBIc1QoD1ay', 'utilisateur', '2025-08-14 10:51:09', '2025-08-14 10:51:09'),
(9, 'ZEUS', '9876', '$2y$10$pbpXF9XFDP.K1nuE3POg.OxFxs2jBYrP8dx8prYy9XN04PWwwuBIK', 'utilisateur', '2025-10-08 09:39:15', '2025-10-08 09:39:15'),
(10, 'talla', '1234', '$2y$10$D.xH60X4K80kk/ShmuRAoe.Iy6/nGrhcbgRyFjSDa.u.ABYgl8aZi', 'utilisateur', '2025-10-08 11:23:25', '2025-10-08 11:23:25'),
(11, 'talla', '2006', '$2y$10$IiGkMP9I14CoowlLOzhenurpC4/nsTdjfn12FQ9AeXvDi0CET6mRm', 'utilisateur', '2025-11-09 12:59:18', '2025-11-09 12:59:18'),
(12, 'ra', '123456', '$2y$10$wEMxcIDX61E3k6uHYLjIh.bpbm9Bc9jujiZZ1QzOJlMiFWxUzBK9m', 'utilisateur', '2025-11-09 13:00:44', '2025-11-09 13:00:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `gerer`
--
ALTER TABLE `gerer`
  ADD PRIMARY KEY (`id_produit`,`id_utilisateur`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id_journal`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD PRIMARY KEY (`id_mouvement`),
  ADD KEY `type_mouvement` (`type_mouvement`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Indexes for table `role_utilisateur`
--
ALTER TABLE `role_utilisateur`
  ADD PRIMARY KEY (`role`);

--
-- Indexes for table `type_mouvement_stock`
--
ALTER TABLE `type_mouvement_stock`
  ADD PRIMARY KEY (`type_mouvement`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `identifiant` (`identifiant`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `journal`
--
ALTER TABLE `journal`
  MODIFY `id_journal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  MODIFY `id_mouvement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gerer`
--
ALTER TABLE `gerer`
  ADD CONSTRAINT `gerer_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gerer_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Constraints for table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD CONSTRAINT `mouvement_stock_ibfk_1` FOREIGN KEY (`type_mouvement`) REFERENCES `type_mouvement_stock` (`type_mouvement`),
  ADD CONSTRAINT `mouvement_stock_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mouvement_stock_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Constraints for table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role_utilisateur` (`role`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
