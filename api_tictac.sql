-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 23 mars 2023 à 18:28
-- Version du serveur : 8.0.32-0ubuntu0.22.04.2
-- Version de PHP : 8.1.2-1ubuntu2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `api_tictac`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity`
--

CREATE TABLE `activity` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `activity`
--

INSERT INTO `activity` (`id`, `title`, `description`, `user_id`) VALUES
(3, 'Coding monTicTac', 'Ma première API Symfony', 3),
(4, 'Marche', 'Objectif au moins une heure par jour', 4),
(5, 'Cours Java Openclassrooms', 'Pour répondre aux offres d\'emploi', 3),
(6, 'Petits cours', 'Cours particulierset CLAS', 4),
(9, 'Formation OpenClassrooms', 'JAVA', 4),
(10, 'Formation OpenClassrooms', 'API REST', 4),
(14, 'Coding', 'API montictac', 4),
(15, 'Courses - achats', NULL, 4),
(16, 'Coding montictac', 'front Angular', 4);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `period`
--

CREATE TABLE `period` (
  `id` int NOT NULL,
  `activity_id` int NOT NULL,
  `start` datetime NOT NULL,
  `stop` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `period`
--

INSERT INTO `period` (`id`, `activity_id`, `start`, `stop`) VALUES
(3, 4, '2023-03-22 11:54:12', '2023-03-22 15:19:55'),
(5, 6, '2023-03-22 13:26:40', '2023-03-22 14:11:44'),
(6, 9, '2023-03-22 14:11:44', '2023-03-22 14:11:52'),
(7, 10, '2023-03-22 14:11:52', '2023-03-22 14:12:04'),
(8, 14, '2023-03-22 14:12:04', '2023-03-22 14:12:32'),
(9, 15, '2023-03-22 14:12:32', '2023-03-22 15:18:15'),
(10, 6, '2023-03-22 15:09:36', '2023-03-22 15:10:52'),
(11, 15, '2023-03-22 15:10:52', '2023-03-22 16:54:10'),
(12, 15, '2023-03-22 16:54:10', '2023-03-22 16:55:15'),
(13, 14, '2023-03-22 16:55:15', '2023-03-22 17:06:54'),
(14, 14, '2023-03-22 17:06:54', '2023-03-22 17:07:39'),
(15, 14, '2023-03-22 17:07:39', '2023-03-22 17:10:56'),
(16, 14, '2023-03-22 17:10:56', '2023-03-22 17:12:25'),
(17, 14, '2023-03-22 17:12:25', '2023-03-22 17:12:42'),
(18, 14, '2023-03-22 17:12:42', '2023-03-22 17:13:02'),
(19, 14, '2023-03-22 17:13:02', '2023-03-22 17:13:38'),
(20, 14, '2023-03-22 17:13:38', '2023-03-22 17:14:35'),
(21, 14, '2023-03-22 17:14:35', '2023-03-22 17:54:23');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(3, 'admin@montictac.fr', '[\"ROLE_ADMIN\"]', '$2y$13$2jL6eNb2amnVLBnZEzutn.mkun5kImebk95k7l5wa0ProEHUUcWXy'),
(4, 'sylvain@montictac.fr', '[\"ROLE_USER\"]', '$2y$13$SgoLZbuupw6dL.qJfGsjQutF3rkgpCQExqLwYVuY58EA4yhf37Ec2');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AC74095AA76ED395` (`user_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `period`
--
ALTER TABLE `period`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C5B81ECE81C06096` (`activity_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `period`
--
ALTER TABLE `period`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `FK_AC74095AA76ED396` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `period`
--
ALTER TABLE `period`
  ADD CONSTRAINT `FK_C5B81ECE81C06096` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
