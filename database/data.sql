-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 09 oct. 2025 à 14:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `reservation_id`, `trajet_id`, `evaluateur_id`, `evalue_id`, `note`, `commentaire`, `statut`, `date_validation`, `validateur_id`, `motif_refus`, `created_at`, `updated_at`) VALUES
(1, NULL, 5, 2, 2, 4, 'Très bonne expérience', 'valide', '2025-10-02 07:06:53', 2, NULL, '2025-09-30 06:30:35', '2025-10-02 07:06:53'),
(2, 9, 24, 7, 2, 5, 'Personne très agréable et prudente, je recommande.', 'valide', '2025-10-02 07:36:03', 2, NULL, '2025-10-02 04:06:34', '2025-10-02 07:36:03'),
(9, 19, 5, 7, 2, 5, 'Très bon trajet, conducteur ponctuel !', 'valide', '2025-10-02 07:43:02', 2, NULL, '2025-10-02 04:48:16', '2025-10-02 07:43:02'),
(10, 20, 15, 8, 2, 4, 'Très bon covoiturage, bonne ambiance dans la voiture et échanges agréables', 'refuse', '2025-10-02 07:42:48', 2, 'Non cohérent', '2025-10-02 04:48:16', '2025-10-02 07:42:48'),
(11, 21, 16, 9, 2, 5, 'Super trajet, conducteur ponctuel et sympa. Voiture propre, je recommande !', 'valide', '2025-10-02 07:43:12', 2, NULL, '2025-10-02 04:48:16', '2025-10-02 07:43:12'),
(22, 24, 26, 2, 8, 4, '', 'valide', '2025-10-03 07:58:38', 1, NULL, '2025-10-03 07:57:33', '2025-10-03 07:58:38'),
(23, 25, 27, 7, 8, 1, '', 'refuse', '2025-10-03 08:08:25', 1, '', '2025-10-03 08:05:45', '2025-10-03 08:08:25'),
(24, 27, 27, 11, 8, 4, 'Très bonne conductrice et sympathique.', 'valide', '2025-10-03 08:06:37', 1, NULL, '2025-10-03 08:06:10', '2025-10-03 08:06:37'),
(30, 28, 28, 9, 8, 1, '', 'refuse', '2025-10-03 08:38:58', 2, 'Avis non commenté', '2025-10-03 08:37:22', '2025-10-03 08:38:58'),
(31, 30, 28, 6, 8, 1, '', 'refuse', '2025-10-03 08:43:06', 2, 'Non commenté', '2025-10-03 08:40:06', '2025-10-03 08:43:06'),
(32, 31, 29, 7, 2, 1, '', 'refuse', '2025-10-06 03:51:01', 1, '', '2025-10-03 08:46:39', '2025-10-06 03:51:01'),
(33, 32, 29, 8, 2, 5, 'Très agréable et ponctuel !', 'valide', '2025-10-03 08:47:31', 2, NULL, '2025-10-03 08:47:14', '2025-10-03 08:47:31'),
(34, 34, 30, 7, 8, 1, '', 'refuse', '2025-10-06 03:50:59', 1, '', '2025-10-06 03:49:57', '2025-10-06 03:50:59'),
(35, 33, 30, 11, 8, 5, 'Ponctuelle et agréable.', 'valide', '2025-10-06 04:20:00', 1, NULL, '2025-10-06 03:50:26', '2025-10-06 04:20:00'),
(36, 35, 31, 8, 7, 5, 'Conductrice très agréable, le trajet s\'est très bien passé !', 'valide', '2025-10-06 04:34:04', 2, NULL, '2025-10-06 04:33:01', '2025-10-06 04:34:04'),
(37, 36, 31, 9, 7, 4, 'Je vous recommande.', 'valide', '2025-10-06 04:34:07', 2, NULL, '2025-10-06 04:33:46', '2025-10-06 04:34:07'),
(38, 37, 32, 10, 7, 4, '', 'valide', '2025-10-06 05:05:40', 2, NULL, '2025-10-06 04:55:49', '2025-10-06 05:05:40'),
(39, 43, 33, 12, 9, 4, 'Trajet agréable.', 'valide', '2025-10-06 08:11:45', 1, NULL, '2025-10-06 08:11:20', '2025-10-06 08:11:45');

--
-- Déchargement des données de la table `employes`
--

INSERT INTO `employes` (`id`, `utilisateur_id`, `role_id`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-09-30', 'actif', '2025-09-30 07:40:44', '2025-09-30 07:40:44'),
(2, 6, 2, '2025-09-30', 'actif', '2025-09-30 12:14:52', '2025-09-30 12:14:52');

--
-- Déchargement des données de la table `logs_activite`
--

INSERT INTO `logs_activite` (`id`, `utilisateur_id`, `action`, `table_affectee`, `enregistrement_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"sandypodvin@gmail.com\",\"pseudo\":\"Ozecult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-18 12:38:34'),
(2, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\":\"dzofh@mail.com\",\"timestamp\":\"2025-09-18 14:42:11\",\"reason\":\"invalid_credentials\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-18 12:42:11'),
(3, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\": \"admin@ecoride.fr\", \"timestamp\": \"2025-09-30 08:32:25\", \"reason\": \"invalid_credentials\", \"resolved\": true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:32:25'),
(4, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\": \"admin@ecoride.fr\", \"timestamp\": \"2025-09-30 08:32:29\", \"reason\": \"invalid_credentials\", \"resolved\": true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:32:29'),
(5, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\": \"admin@ecoride.fr\", \"timestamp\": \"2025-09-30 08:33:17\", \"reason\": \"invalid_credentials\", \"resolved\": true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:33:17'),
(6, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\": \"admin@ecoride.fr\", \"timestamp\": \"2025-09-30 08:33:21\", \"reason\": \"invalid_credentials\", \"resolved\": true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:33:21'),
(7, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\":\"administrateur@ecoride.fr\",\"timestamp\":\"2025-09-30 08:35:52\",\"reason\":\"invalid_credentials\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:35:52'),
(8, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\":\"administrateur@ecoride.fr\",\"timestamp\":\"2025-09-30 08:37:05\",\"reason\":\"invalid_credentials\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:37:05'),
(9, NULL, 'failed_login', 'UTILISATEURS', NULL, '{\"email\":\"administrateur@ecoride.fr\",\"timestamp\":\"2025-09-30 08:44:03\",\"reason\":\"invalid_credentials\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 06:44:03'),
(10, NULL, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"kevin.dupont@hotmail.fr\",\"pseudo\":\"dupontk\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 08:32:05'),
(11, 6, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"kevin.dupont@hotmail.fr\",\"pseudo\":\"kevind\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-30 12:14:52'),
(12, 7, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"elisa.portemont@orange.fr\",\"pseudo\":\"Elisa\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-01 14:57:08'),
(13, 8, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"jessica.bore@gmail.com\",\"pseudo\":\"jessica.bore@gmail.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-02 04:09:34'),
(14, 9, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"maxime.bernard@gmail.com\",\"pseudo\":\"Max\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-02 04:12:21'),
(15, 10, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"thierry.marchand@gmail.com\",\"pseudo\":\"Thierry\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-02 04:15:46'),
(16, 11, 'user_created', 'UTILISATEURS', NULL, '{\"email\":\"joelle.garcia@hotmail.fr\",\"pseudo\":\"Jo\\u00eblle\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-02 04:19:37'),
(17, 1, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-06 07:25:11\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-06 05:25:11'),
(18, 6, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-06 07:30:41\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-06 05:30:41'),
(NULL, 7, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-06 15:39:29\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-06 13:39:29'),
(NULL, 9, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 09:47:45\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 07:47:45'),
(NULL, 8, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 09:57:24\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 07:57:24'),
(NULL, 9, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 09:58:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 07:58:25'),
(NULL, 8, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 09:59:12\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 07:59:12'),
(NULL, 8, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 10:07:33\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 08:07:33'),
(NULL, 12, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 10:12:49\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 08:12:49'),
(NULL, 8, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 10:26:16\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 08:26:16'),
(NULL, 7, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 11:54:02\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 09:54:02'),
(NULL, 8, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 11:56:41\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 09:56:41'),
(NULL, 6, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 12:09:54\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 10:09:54'),
(NULL, 1, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-07 12:11:04\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 10:11:04'),
(NULL, 2, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-09 10:01:30\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-09 08:01:30'),
(NULL, 1, 'successful_login', 'utilisateurs', NULL, '{\"timestamp\":\"2025-10-09 10:02:28\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-09 08:02:28');

--
-- Déchargement des données de la table `messages_contact`
--

INSERT INTO `messages_contact` (`id`, `prenom`, `nom`, `email`, `telephone`, `sujet`, `message`, `statut`, `employe_assigne_id`, `reponse`, `date_reponse`, `created_at`, `updated_at`) VALUES
(1, 'Sandy', 'Podvin', 'sandypodvin@gmail.com', '06 25 28 00 65', 'autre', 'Ceci est un test.', 'traite', 2, 'Bien reçu', '2025-10-06 05:33:33', '2025-10-06 05:30:10', '2025-10-06 05:33:33'),
(2, 'Emile', 'Dupont', 'emile.dupont@orange.fr', '06 25 26 27 28', 'question', 'Ceci est un autre test.', 'traite', 1, 'Bien reçu', '2025-10-06 08:49:44', '2025-10-06 08:49:25', '2025-10-06 08:49:44');

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `utilisateur_id`, `type_notification`, `titre`, `message`, `lu`, `reference_id`, `reference_type`, `created_at`) VALUES
(1, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 01/10/2025 a une nouvelle réservation.', 0, 2, NULL, '2025-10-01 10:18:30'),
(2, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 3, NULL, '2025-10-02 03:07:40'),
(3, 7, 'annulation', 'Trajet annulé', 'Le trajet prévu le 03/10/2025 a été annulé. Vos crédits ont été remboursés.', 0, 3, NULL, '2025-10-02 03:22:29'),
(4, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 5, NULL, '2025-10-02 03:36:24'),
(5, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 8, NULL, '2025-10-02 03:40:21'),
(6, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 9, NULL, '2025-10-02 03:42:12'),
(8, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 29/09/2025 a une nouvelle réservation.', 0, 19, NULL, '2025-10-02 04:45:53'),
(9, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 24/09/2025 a une nouvelle réservation.', 0, 20, NULL, '2025-10-02 04:45:53'),
(10, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 25/09/2025 a une nouvelle réservation.', 0, 21, NULL, '2025-10-02 04:45:53'),
(11, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 23, NULL, '2025-10-03 07:54:07'),
(12, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 24, NULL, '2025-10-03 07:55:11'),
(13, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 25, NULL, '2025-10-03 08:01:43'),
(14, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 26, NULL, '2025-10-03 08:02:32'),
(15, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 27, NULL, '2025-10-03 08:03:27'),
(16, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 28, NULL, '2025-10-03 08:21:04'),
(17, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 30, NULL, '2025-10-03 08:23:31'),
(18, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 31, NULL, '2025-10-03 08:44:26'),
(19, 2, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 03/10/2025 a une nouvelle réservation.', 0, 32, NULL, '2025-10-03 08:45:50'),
(20, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 33, NULL, '2025-10-06 03:22:43'),
(21, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 34, NULL, '2025-10-06 03:30:25'),
(22, 7, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 35, NULL, '2025-10-06 04:31:15'),
(23, 7, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 36, NULL, '2025-10-06 04:31:45'),
(24, 7, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 37, NULL, '2025-10-06 04:54:44'),
(25, 9, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 06/10/2025 a une nouvelle réservation.', 0, 43, NULL, '2025-10-06 08:08:16'),
(26, 9, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 44, NULL, '2025-10-07 07:57:54'),
(27, 9, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 44, NULL, '2025-10-07 07:57:54'),
(28, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 45, NULL, '2025-10-07 08:14:51'),
(29, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 45, NULL, '2025-10-07 08:14:51'),
(30, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 46, NULL, '2025-10-07 08:26:01'),
(31, 8, 'nouveau_trajet', 'Nouvelle réservation !', 'Votre trajet du 07/10/2025 a une nouvelle réservation.', 0, 46, NULL, '2025-10-07 08:26:01');

--
-- Déchargement des données de la table `preferences_types`
--

INSERT INTO `preferences_types` (`id`, `nom`, `description`, `est_predefinit`, `created_at`) VALUES
(1, 'fumeur', 'Acceptation des fumeurs dans le véhicule', 1, '2025-09-16 12:33:19'),
(2, 'animaux', 'Acceptation des animaux de compagnie', 1, '2025-09-16 12:33:19'),
(3, 'enfants', 'Acceptation des enfants', 1, '2025-09-16 12:33:19'),
(4, 'musique', 'Écoute de musique pendant le trajet', 1, '2025-09-16 12:33:19'),
(5, 'Pas de musique', 'Préférence personnalisée créée par un utilisateur', 0, '2025-09-28 13:24:50'),
(6, 'Pas de cigarette électronique', 'Préférence personnalisée créée par un utilisateur', 0, '2025-09-29 13:15:28'),
(7, 'Discussion', 'Préférence personnalisée créée par un utilisateur', 0, '2025-10-02 02:35:18');

--
-- Déchargement des données de la table `preferences_utilisateurs`
--

INSERT INTO `preferences_utilisateurs` (`id`, `utilisateur_id`, `preference_type_id`, `valeur`, `preference_personnalisee`, `created_at`, `updated_at`) VALUES
(74, 2, 1, 'accepte', NULL, '2025-09-30 06:14:47', '2025-09-30 06:14:47'),
(75, 2, 2, 'accepte', NULL, '2025-09-30 06:14:47', '2025-09-30 06:14:47'),
(76, 2, 4, 'accepte', NULL, '2025-09-30 06:14:47', '2025-09-30 06:14:47'),
(83, 7, 2, 'accepte', NULL, '2025-10-02 02:35:18', '2025-10-02 02:35:18'),
(84, 7, 3, 'accepte', NULL, '2025-10-02 02:35:18', '2025-10-02 02:35:18'),
(85, 7, 4, 'accepte', NULL, '2025-10-02 02:35:18', '2025-10-02 02:35:18'),
(86, 7, 7, 'accepte', 'Discussion', '2025-10-02 02:35:18', '2025-10-02 02:35:18'),
(90, 8, 1, 'accepte', NULL, '2025-10-02 04:11:32', '2025-10-02 04:11:32'),
(91, 8, 3, 'accepte', NULL, '2025-10-02 04:11:32', '2025-10-02 04:11:32'),
(92, 8, 4, 'accepte', NULL, '2025-10-02 04:11:32', '2025-10-02 04:11:32'),
(96, 9, 2, 'accepte', NULL, '2025-10-02 04:14:38', '2025-10-02 04:14:38'),
(97, 9, 3, 'accepte', NULL, '2025-10-02 04:14:38', '2025-10-02 04:14:38'),
(98, 9, 4, 'accepte', NULL, '2025-10-02 04:14:38', '2025-10-02 04:14:38'),
(103, 10, 1, 'accepte', NULL, '2025-10-02 04:17:18', '2025-10-02 04:17:18'),
(104, 10, 4, 'accepte', NULL, '2025-10-02 04:17:18', '2025-10-02 04:17:18'),
(106, 11, 3, 'accepte', NULL, '2025-10-02 04:20:55', '2025-10-02 04:20:55'),
(107, 11, 4, 'accepte', NULL, '2025-10-02 04:20:55', '2025-10-02 04:20:55');

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `trajet_id`, `passager_id`, `nombre_places`, `prix_total`, `statut`, `date_reservation`, `date_annulation`, `motif_annulation`, `a_valide_trajet`, `commentaire_validation`, `created_at`, `updated_at`) VALUES
(2, 23, 2, 1, 34.00, 'confirmee', '2025-10-01 10:18:30', NULL, NULL, 0, NULL, '2025-10-01 10:18:30', '2025-10-01 10:18:30'),
(9, 24, 7, 1, 42.00, 'confirmee', '2025-10-02 03:42:12', NULL, NULL, 0, NULL, '2025-10-02 03:42:12', '2025-10-02 03:42:12'),
(19, 5, 7, 1, 42.00, 'confirmee', '2025-10-02 04:45:53', NULL, NULL, 0, NULL, '2025-10-02 04:45:53', '2025-10-02 04:45:53'),
(20, 15, 8, 1, 42.00, 'confirmee', '2025-10-02 04:45:53', NULL, NULL, 0, NULL, '2025-10-02 04:45:53', '2025-10-02 04:45:53'),
(21, 16, 9, 1, 42.00, 'confirmee', '2025-10-02 04:45:53', NULL, NULL, 0, NULL, '2025-10-02 04:45:53', '2025-10-02 04:45:53'),
(23, 26, 8, 1, 42.00, 'confirmee', '2025-10-03 07:54:07', NULL, NULL, 0, NULL, '2025-10-03 07:54:07', '2025-10-03 07:54:07'),
(24, 26, 2, 1, 42.00, 'confirmee', '2025-10-03 07:55:11', NULL, NULL, 0, NULL, '2025-10-03 07:55:11', '2025-10-03 07:55:11'),
(25, 27, 7, 1, 41.00, 'confirmee', '2025-10-03 08:01:43', NULL, NULL, 0, NULL, '2025-10-03 08:01:43', '2025-10-03 08:01:43'),
(26, 27, 1, 1, 41.00, 'confirmee', '2025-10-03 08:02:32', NULL, NULL, 0, NULL, '2025-10-03 08:02:32', '2025-10-03 08:02:32'),
(27, 27, 11, 1, 41.00, 'confirmee', '2025-10-03 08:03:27', NULL, NULL, 0, NULL, '2025-10-03 08:03:27', '2025-10-03 08:03:27'),
(28, 28, 9, 1, 45.00, 'confirmee', '2025-10-03 08:21:04', NULL, NULL, 0, NULL, '2025-10-03 08:21:04', '2025-10-03 08:21:04'),
(30, 28, 6, 1, 45.00, 'confirmee', '2025-10-03 08:23:31', NULL, NULL, 0, NULL, '2025-10-03 08:23:31', '2025-10-03 08:23:31'),
(31, 29, 7, 1, 38.00, 'confirmee', '2025-10-03 08:44:26', NULL, NULL, 0, NULL, '2025-10-03 08:44:26', '2025-10-03 08:44:26'),
(32, 29, 8, 1, 38.00, 'confirmee', '2025-10-03 08:45:50', NULL, NULL, 0, NULL, '2025-10-03 08:45:50', '2025-10-03 08:45:50'),
(33, 30, 11, 1, 45.00, 'confirmee', '2025-10-06 03:22:43', NULL, NULL, 0, NULL, '2025-10-06 03:22:43', '2025-10-06 03:22:43'),
(34, 30, 7, 1, 45.00, 'confirmee', '2025-10-06 03:30:25', NULL, NULL, 0, NULL, '2025-10-06 03:30:25', '2025-10-06 03:30:25'),
(35, 31, 8, 1, 38.00, 'confirmee', '2025-10-06 04:31:15', NULL, NULL, 0, NULL, '2025-10-06 04:31:15', '2025-10-06 04:31:15'),
(36, 31, 9, 1, 38.00, 'confirmee', '2025-10-06 04:31:45', NULL, NULL, 0, NULL, '2025-10-06 04:31:45', '2025-10-06 04:31:45'),
(37, 32, 10, 1, 22.00, 'confirmee', '2025-10-06 04:54:44', NULL, NULL, 0, NULL, '2025-10-06 04:54:44', '2025-10-06 04:54:44'),
(43, 33, 12, 1, 42.00, 'confirmee', '2025-10-06 08:08:16', NULL, NULL, 0, NULL, '2025-10-06 08:08:16', '2025-10-06 08:08:16'),
(44, 34, 8, 1, 47.00, 'terminee', '2025-10-07 07:57:54', NULL, NULL, 1, NULL, '2025-10-07 07:57:54', '2025-10-07 07:58:41'),
(46, 35, 12, 1, 46.00, 'terminee', '2025-10-07 08:26:01', NULL, NULL, 1, NULL, '2025-10-07 08:26:01', '2025-10-07 08:26:32');

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'administrateur', 'Accès complet à toutes les fonctionnalités', '2025-09-16 12:31:26', '2025-09-16 12:31:26'),
(2, 'employe', 'Gestion des avis et signalements', '2025-09-16 12:31:26', '2025-09-16 12:31:26'),
(3, 'utilisateur', 'Personne ayant crée un compte', '2025-09-16 12:32:26', '2025-09-16 12:32:26');

--
-- Déchargement des données de la table `signalements`
--

INSERT INTO `signalements` (`id`, `trajet_id`, `signaleur_id`, `signale_id`, `type_signalement`, `description`, `statut`, `demande_suspension`, `employe_assigne_id`, `commentaire_employe`, `date_resolution`, `created_at`, `updated_at`) VALUES
(1, 5, 7, 2, 'comportement', 'Le conducteur était impoli', '', 0, NULL, NULL, NULL, '2025-10-02 04:48:16', '2025-10-02 04:48:16'),
(2, 15, 8, 2, 'autre', 'Le conducteur a eu 30 minutes de retard', '', 0, NULL, NULL, NULL, '2025-10-02 04:48:16', '2025-10-06 06:43:50'),
(3, 16, 9, 2, 'comportement', 'Musique trop forte pendant le trajet', '', 0, NULL, NULL, NULL, '2025-10-02 04:48:16', '2025-10-02 04:48:16'),
(4, 5, 7, 2, 'comportement', 'Le conducteur était impoli', 'ferme', 0, NULL, 'RAS', '2025-10-02 08:19:22', '2025-10-02 06:55:39', '2025-10-02 08:19:22'),
(5, 15, 8, 2, 'securite', 'Le conducteur a eu 30 minutes de retard', 'resolu', 0, 1, 'Non confirmé par les autres passagers', '2025-10-02 13:36:40', '2025-10-02 06:55:39', '2025-10-02 13:36:40'),
(6, 16, 9, 2, 'comportement', 'Musique trop forte pendant le trajet', 'resolu', 0, 2, 'Plusieurs fois le même signalement et pas de réponse de l\'utilisateur Sandy malgré mes multiples mails.', '2025-10-02 08:21:43', '2025-10-02 06:55:39', '2025-10-02 12:49:35'),
(7, 29, 7, 2, 'comportement', 'Retard abusif', '', 0, NULL, NULL, NULL, '2025-10-03 08:46:39', '2025-10-03 08:46:39'),
(8, 30, 7, 8, 'comportement', 'Retard abusif.', 'resolu', 0, 1, 'Le deuxième passager ne s\'est pas plein de retard.', '2025-10-06 03:52:08', '2025-10-06 03:49:57', '2025-10-06 03:52:08');

--
-- Déchargement des données de la table `trajets`
--

INSERT INTO `trajets` (`id`, `chauffeur_id`, `vehicule_id`, `adresse_depart`, `adresse_arrivee`, `date_depart`, `prix_par_passager`, `commission_plateforme`, `places_disponibles`, `places_totales`, `est_ecologique`, `statut`, `description`, `date_creation`, `date_demarrage`, `date_arrivee_reelle`, `created_at`, `updated_at`) VALUES
(5, 2, 1, 'Mériel (95630)', 'Lille (59000)', '2025-09-29 18:10:00', 28.00, 2.00, 3, 4, 1, 'termine', '', '2025-09-29 13:23:38', '2025-09-29 13:40:40', '2025-09-29 13:40:46', '2025-09-29 13:23:38', '2025-10-02 04:45:53'),
(6, 2, 1, 'Mériel (95630)', 'Lille (59000)', '2025-09-30 12:00:00', 24.00, 2.00, 4, 4, 1, 'annule', '', '2025-09-29 13:41:49', '2025-09-29 14:05:50', NULL, '2025-09-29 13:41:49', '2025-09-29 14:06:03'),
(7, 2, 1, 'Mériel (95630)', 'Lille (59000)', '2025-09-29 18:00:00', 25.00, 2.00, 4, 4, 1, 'annule', '', '2025-09-29 14:08:24', NULL, NULL, '2025-09-29 14:08:24', '2025-09-29 14:11:09'),
(15, 2, 1, '10 Rue de Rivoli, Paris', '5 Place Bellecour, Lyon', '2025-09-24 08:00:00', 25.00, 2.00, 2, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-24 04:00:00', '2025-10-02 04:45:53'),
(16, 2, 1, '15 Avenue des Champs, Paris', '12 Rue Victor Hugo, Lyon', '2025-09-25 14:00:00', 30.00, 2.00, 1, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-25 10:00:00', '2025-10-02 04:45:53'),
(17, 2, 1, '8 Cours Lafayette, Lyon', '25 Boulevard Haussmann, Paris', '2025-09-26 09:00:00', 28.00, 2.00, 3, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-26 05:00:00', '2025-09-30 13:38:45'),
(18, 2, 1, '20 Rue du Louvre, Paris', '10 Place du Général de Gaulle, Lille', '2025-09-27 07:00:00', 35.00, 2.00, 2, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-27 03:00:00', '2025-09-30 13:38:45'),
(19, 2, 1, '5 La Canebière, Marseille', '2 Place du Capitole, Toulouse', '2025-09-28 11:00:00', 40.00, 2.00, 3, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-28 07:00:00', '2025-09-30 13:38:45'),
(20, 2, 1, '1 Rue Alsace Lorraine, Toulouse', '3 Quai Saint-Antoine, Lyon', '2025-09-29 16:00:00', 32.00, 2.00, 2, 4, 1, 'termine', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-29 12:00:00', '2025-09-30 13:38:45'),
(21, 2, 1, '30 Rue de la Paix, Paris', '8 Vieux Port, Marseille', '2025-09-30 13:00:00', 45.00, 2.00, 3, 4, 1, 'en_cours', NULL, '2025-09-30 12:51:16', NULL, NULL, '2025-09-30 09:00:00', '2025-09-30 13:38:45'),
(22, 2, 1, '25 Rue du Haut Val Mary 95630 Mériel', '73 Rue de la Botte d&#039;Or 59480 Illies', '2025-10-01 14:00:00', 36.00, 2.00, 4, 4, 1, 'termine', '', '2025-09-30 13:55:18', '2025-09-30 13:55:39', '2025-09-30 13:55:44', '2025-09-30 13:55:18', '2025-09-30 13:55:44'),
(23, 2, 1, '74 Rue Jean Moulin 80000 Amiens', '87 Rue Victor Hugo 92600 Asnières-sur-Seine', '2025-10-01 14:00:00', 34.00, 2.00, 3, 4, 1, 'planifie', '', '2025-10-01 07:27:18', NULL, NULL, '2025-10-01 07:27:18', '2025-10-01 10:18:30'),
(24, 2, 1, 'Paris 16e Arrondissement', 'Rue du Bac 76000 Rouen', '2025-10-03 09:00:00', 42.00, 2.00, 2, 4, 1, 'termine', '', '2025-10-01 12:17:20', '2025-10-02 04:05:20', '2025-10-02 04:05:33', '2025-10-01 12:17:20', '2025-10-02 04:05:33'),
(25, 8, 3, '10 Rue Georges Braque 80080 Amiens', '4 Rue de Frépillon 95540 Méry-sur-Oise', '2025-10-03 15:00:00', 42.00, 2.00, 4, 4, 0, 'annule', '', '2025-10-03 07:15:45', '2025-10-03 07:52:45', NULL, '2025-10-03 07:15:45', '2025-10-03 07:52:53'),
(26, 8, 3, '10 Rue Georges Braque 80080 Amiens', '4 Rue de Frépillon 95540 Méry-sur-Oise', '2025-10-03 10:00:00', 42.00, 2.00, 2, 4, 0, 'termine', '', '2025-10-03 07:53:37', '2025-10-03 07:55:50', '2025-10-03 07:57:11', '2025-10-03 07:53:37', '2025-10-03 07:57:11'),
(27, 8, 3, '10 Rue Vion 80090 Amiens', '14 Avenue Gaston Bourry 95740 Frépillon', '2025-10-03 10:15:00', 41.00, 2.00, 1, 4, 0, 'termine', '', '2025-10-03 08:01:05', '2025-10-03 08:03:50', '2025-10-03 08:04:00', '2025-10-03 08:01:05', '2025-10-03 08:04:00'),
(28, 8, 3, '10 Rue Wasse 80090 Amiens', '14 Rue Copin 95540 Méry-sur-Oise', '2025-10-03 10:30:00', 45.00, 2.00, 0, 4, 0, 'termine', '', '2025-10-03 08:20:24', '2025-10-03 08:29:26', '2025-10-03 08:29:33', '2025-10-03 08:20:24', '2025-10-03 08:29:33'),
(29, 2, 1, 'Rue du Haut Val Mary 95630 Mériel', 'Route d’Estaires 59480 La Bassée', '2025-10-03 10:50:00', 38.00, 2.00, 0, 4, 1, 'termine', '', '2025-10-03 08:43:56', '2025-10-03 08:46:09', '2025-10-03 08:46:15', '2025-10-03 08:43:56', '2025-10-03 08:46:15'),
(30, 8, 3, '14 Rue de Meriel 95290 L\'Isle-Adam', '27 Rue Henri 59330 Hautmont', '2025-10-06 06:30:00', 45.00, 2.00, 0, 4, 0, 'termine', '', '2025-10-06 03:20:55', '2025-10-06 03:38:21', '2025-10-06 03:49:18', '2025-10-06 03:20:55', '2025-10-06 05:00:39'),
(31, 7, 2, 'Rue de Mormal 59530 Hecq', 'Route d’Estaires 59480 La Bassée', '2025-10-06 06:45:00', 38.00, 2.00, 0, 4, 1, 'termine', '', '2025-10-06 04:30:41', '2025-10-06 04:32:21', '2025-10-06 04:32:25', '2025-10-06 04:30:41', '2025-10-06 04:32:25'),
(32, 7, 2, 'Rue de la Botte d\'Or 59480 Illies', 'Rue Fernand 59490 Somain', '2025-10-06 07:00:00', 22.00, 2.00, 2, 4, 1, 'termine', '', '2025-10-06 04:38:22', '2025-10-06 04:55:26', '2025-10-06 04:55:35', '2025-10-06 04:38:22', '2025-10-06 05:00:39'),
(33, 9, 4, 'Rue du Bel-Air 95630 Mériel', 'Rue de la Gare 59160 Lille', '2025-10-10 10:30:00', 42.00, 2.00, 1, 3, 0, 'termine', '', '2025-10-06 07:07:07', '2025-10-06 08:09:08', '2025-10-06 08:09:18', '2025-10-06 07:07:07', '2025-10-06 08:09:18'),
(34, 9, 4, 'Rue de la Gare 75019 Paris', 'Rue de la Gare 59160 Lille', '2025-10-07 10:30:00', 47.00, 2.00, 2, 3, 0, 'termine', '', '2025-10-07 07:49:24', '2025-10-07 07:58:32', '2025-10-07 07:58:41', '2025-10-07 07:49:24', '2025-10-07 07:58:41'),
(35, 8, 3, 'Place de la Gare (Lille) 59800 Lille', 'Quai de la Gare 75013 Paris', '2025-10-07 10:30:00', 46.00, 2.00, 3, 4, 0, 'termine', '', '2025-10-07 08:08:26', '2025-10-07 08:26:23', '2025-10-07 08:26:32', '2025-10-07 08:08:26', '2025-10-07 08:26:32'),
(36, 7, 2, 'Lille', 'Versailles', '2025-10-08 10:00:00', 46.00, 2.00, 4, 4, 1, 'planifie', '', '2025-10-07 09:56:32', NULL, NULL, '2025-10-07 09:56:32', '2025-10-07 09:56:32');

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `utilisateur_id`, `type_transaction`, `montant`, `solde_avant`, `solde_apres`, `motif`, `reference_id`, `description`, `created_at`) VALUES
(1, 1, 'credit', 50, 20, 70, 'completion_trajet', 1, NULL, '2025-09-24 08:00:00'),
(2, 1, 'credit', 60, 70, 130, 'completion_trajet', 2, NULL, '2025-09-25 14:00:00'),
(3, 1, 'credit', 56, 130, 186, 'completion_trajet', 3, NULL, '2025-09-26 09:00:00'),
(4, 1, 'credit', 70, 186, 256, 'completion_trajet', 4, NULL, '2025-09-27 07:00:00'),
(5, 1, 'credit', 80, 256, 336, 'completion_trajet', 5, NULL, '2025-09-28 11:00:00'),
(6, 1, 'credit', 64, 336, 400, 'completion_trajet', 6, NULL, '2025-09-29 16:00:00'),
(7, 1, 'recharge', 50, 20, 70, 'completion_trajet', 1, NULL, '2025-09-24 08:00:00'),
(8, 1, 'credit', 60, 70, 130, 'completion_trajet', 2, NULL, '2025-09-25 14:00:00'),
(9, 1, 'credit', 56, 130, 186, 'completion_trajet', 3, NULL, '2025-09-26 09:00:00'),
(10, 1, 'recharge', 70, 186, 256, 'completion_trajet', 4, NULL, '2025-09-27 07:00:00'),
(11, 1, 'credit', 80, 256, 336, 'completion_trajet', 5, NULL, '2025-09-28 11:00:00'),
(12, 1, 'credit', 64, 336, 400, 'completion_trajet', 6, NULL, '2025-09-29 16:00:00'),
(13, 2, 'debit', 34, 40, 6, 'reservation', 2, NULL, '2025-10-01 10:18:30'),
(14, 7, 'debit', 42, 60, 18, 'reservation', 3, NULL, '2025-10-02 03:07:40'),
(15, 7, 'debit', 42, 60, 18, 'reservation', 5, NULL, '2025-10-02 03:36:24'),
(16, 7, 'debit', 42, 60, 18, 'reservation', 8, NULL, '2025-10-02 03:40:21'),
(17, 7, 'debit', 42, 60, 18, 'reservation', 9, NULL, '2025-10-02 03:42:12'),
(19, 7, 'debit', 42, 150, 108, 'reservation', 19, NULL, '2025-10-02 04:45:53'),
(20, 8, 'debit', 42, 150, 108, 'reservation', 20, NULL, '2025-10-02 04:45:53'),
(21, 9, 'debit', 42, 150, 108, 'reservation', 21, NULL, '2025-10-02 04:45:53'),
(22, 1, 'recharge', 20, 1040, 1060, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-03 07:28:09'),
(23, 1, 'recharge', 20, 1060, 1080, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-03 07:28:17'),
(24, 8, 'debit', 42, 106, 64, 'reservation', 23, NULL, '2025-10-03 07:54:07'),
(25, 2, 'debit', 42, 150, 108, 'reservation', 24, NULL, '2025-10-03 07:55:11'),
(26, 7, 'debit', 41, 108, 67, 'reservation', 25, NULL, '2025-10-03 08:01:43'),
(27, 1, 'debit', 41, 1080, 1039, 'reservation', 26, NULL, '2025-10-03 08:02:32'),
(28, 11, 'recharge', 50, 20, 70, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-03 08:03:26'),
(29, 11, 'debit', 41, 70, 29, 'reservation', 27, NULL, '2025-10-03 08:03:27'),
(30, 9, 'debit', 45, 108, 63, 'reservation', 28, NULL, '2025-10-03 08:21:04'),
(31, 9, 'debit', 45, 108, 63, 'reservation', NULL, 'Réservation trajet #28', '2025-10-03 08:21:04'),
(32, 8, 'credit', 45, 60, 105, 'reservation', NULL, 'Réservation reçue trajet #28', '2025-10-03 08:21:04'),
(33, 9, 'recharge', 50, 18, 68, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-03 08:22:46'),
(34, 6, 'debit', 45, 150, 105, 'reservation', 30, NULL, '2025-10-03 08:23:31'),
(35, 6, 'debit', 45, 150, 105, 'reservation', NULL, 'Réservation trajet #28', '2025-10-03 08:23:31'),
(36, 8, 'credit', 45, 105, 150, 'reservation', NULL, 'Réservation reçue trajet #28', '2025-10-03 08:23:31'),
(37, 7, 'debit', 38, 67, 29, 'reservation', 31, NULL, '2025-10-03 08:44:26'),
(38, 7, 'debit', 38, 67, 29, 'reservation', NULL, 'Réservation trajet #29', '2025-10-03 08:44:26'),
(39, 2, 'credit', 38, 106, 144, 'reservation', NULL, 'Réservation reçue trajet #29', '2025-10-03 08:44:26'),
(40, 8, 'debit', 38, 150, 112, 'reservation', 32, NULL, '2025-10-03 08:45:50'),
(41, 8, 'debit', 38, 150, 112, 'reservation', NULL, 'Réservation trajet #29', '2025-10-03 08:45:50'),
(42, 2, 'credit', 38, 144, 182, 'reservation', NULL, 'Réservation reçue trajet #29', '2025-10-03 08:45:50'),
(43, 11, 'recharge', 20, 29, 49, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-06 03:22:39'),
(44, 11, 'debit', 45, 49, 4, 'reservation', 33, NULL, '2025-10-06 03:22:43'),
(45, 11, 'debit', 45, 49, 4, 'reservation', NULL, 'Réservation trajet #30', '2025-10-06 03:22:43'),
(46, 8, 'credit', 45, 72, 117, 'reservation', NULL, 'Réservation reçue trajet #30', '2025-10-06 03:22:43'),
(47, 11, 'recharge', 50, -41, 9, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-06 03:29:35'),
(48, 7, 'recharge', 50, -9, 41, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-06 03:29:51'),
(49, 7, 'recharge', 20, 41, 61, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-06 03:30:18'),
(50, 7, 'debit', 45, 61, 16, 'reservation', 34, NULL, '2025-10-06 03:30:25'),
(51, 7, 'debit', 45, 61, 16, 'reservation', NULL, 'Réservation trajet #30', '2025-10-06 03:30:25'),
(52, 8, 'credit', 45, 117, 162, 'reservation', NULL, 'Réservation reçue trajet #30', '2025-10-06 03:30:25'),
(53, 7, 'recharge', 50, -29, 21, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-06 03:30:33'),
(54, 8, 'debit', 38, 162, 124, 'reservation', 35, NULL, '2025-10-06 04:31:15'),
(55, 8, 'debit', 38, 162, 124, 'reservation', NULL, 'Réservation trajet #31', '2025-10-06 04:31:15'),
(56, 7, 'credit', 38, 19, 57, 'reservation', NULL, 'Réservation reçue trajet #31', '2025-10-06 04:31:15'),
(57, 9, 'debit', 38, 68, 30, 'reservation', 36, NULL, '2025-10-06 04:31:45'),
(58, 9, 'debit', 38, 68, 30, 'reservation', NULL, 'Réservation trajet #31', '2025-10-06 04:31:45'),
(59, 7, 'credit', 38, 57, 95, 'reservation', NULL, 'Réservation reçue trajet #31', '2025-10-06 04:31:45'),
(60, 9, 'recharge', 20, -8, 12, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-06 04:31:52'),
(61, 10, 'debit', 22, 150, 128, 'reservation', 37, NULL, '2025-10-06 04:54:44'),
(62, 10, 'debit', 22, 150, 128, 'reservation', NULL, 'Réservation trajet #32', '2025-10-06 04:54:44'),
(63, 7, 'credit', 22, 93, 115, 'reservation', NULL, 'Réservation reçue trajet #32', '2025-10-06 04:54:44'),
(64, 12, 'recharge', 50, 20, 70, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-06 07:08:42'),
(65, 12, 'debit', 42, 70, 28, 'reservation', 43, NULL, '2025-10-06 08:08:16'),
(66, 12, 'debit', 42, 70, 28, 'reservation', NULL, 'Réservation trajet #33', '2025-10-06 08:08:16'),
(67, 9, 'credit', 42, 10, 52, 'reservation', NULL, 'Réservation reçue trajet #33', '2025-10-06 08:08:16'),
(68, 9, 'credit', 45, 50, 95, 'completion_trajet', 44, NULL, '2025-10-07 07:58:41'),
(69, 12, 'recharge', 20, -14, 6, 'reservation', NULL, 'Achat de 20 crédits', '2025-10-07 08:14:01'),
(70, 12, 'recharge', 50, 6, 56, 'reservation', NULL, 'Achat de 50 crédits', '2025-10-07 08:14:15'),
(71, 12, 'debit', 46, 56, 10, 'reservation', 45, NULL, '2025-10-07 08:14:51'),
(72, 12, 'debit', 46, 56, 10, 'reservation', 46, NULL, '2025-10-07 08:26:01'),
(73, 8, 'credit', 44, 84, 128, 'completion_trajet', 46, NULL, '2025-10-07 08:26:32');

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `mot_de_passe`, `pseudo`, `photo_profil`, `bio`, `credits`, `note_moyenne`, `nombre_avis`, `est_chauffeur`, `est_passager`, `statut`, `token_reset_password`, `token_reset_expires_at`, `email_verifie`, `token_verification`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'Sandy', 'Podvin', 'admin@ecoride.fr', '$argon2id$v=19$m=65536,t=4,p=1$ZUVEWUViMzkzRzE0dXNDbA$VclTTavJfwiiK4NxkUyI0oFNXd1+lMXYOCKGFsPIfCg', 'admin', NULL, NULL, 1039, 0.0, 0, 1, 1, 'actif', NULL, NULL, 1, NULL, '2025-10-09 08:02:28', '2025-09-30 06:43:30', '2025-10-09 08:02:28'),
(2, 'Sandy', 'Podvin', 'sandypodvin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$RGNYYlBXUW93VkdVdnNiUg$GwDkABszvhyF4TzUAOf02BB/d/+LiMADatkVzr8quCk', 'Ozecult', '/ecoride/uploads/photos/2_1759308971.jpg', 'J\'aime rencontrer des gens lors de mes voyages !', 182, 4.8, 5, 1, 1, 'actif', NULL, NULL, 1, '2437a3b88609510290bb12a5458dc3525da4af7d9ef01a95c17e4cc26ce68035', '2025-10-09 08:01:30', '2025-09-18 12:38:34', '2025-10-09 08:01:30'),
(6, 'Kevin', 'Dupont', 'kevin.dupont@hotmail.fr', '$argon2id$v=19$m=65536,t=4,p=3$Z0M2N1k5YnpCYlJQRkNaUw$B6DXalTK5VsMtueMKGAvbZzkOSXhVp/Su1b/apPOERQ', 'kevind', NULL, NULL, 60, 0.0, 0, 0, 1, 'actif', NULL, NULL, 1, '6ea5c6324ed4d8db8a87064e58cf0898a0827ef9979a490f4f33de4bcf840d3a', '2025-10-07 10:09:54', '2025-09-30 12:14:52', '2025-10-07 10:09:54'),
(7, 'Elisa', 'Portemont', 'elisa.portemont@orange.fr', '$argon2id$v=19$m=65536,t=4,p=3$S21WSEdlbFAuZjR0WDZLMw$O80cYsxwBfAXQUu0FM8Exv07Fx9W68K0RelawhPQRWw', 'Elisa', '/ecoride/uploads/photos/7_1759372283.png', 'Bonjour! Je suis une conductrice agréable et qui roule en toute sécurité.', 113, 4.3, 3, 1, 1, 'actif', NULL, NULL, 1, 'b80464a2ff91df185eebe39b06ce9adc6f57383690d12ae5478e4c7084ac76eb', '2025-10-07 09:54:02', '2025-10-01 14:57:08', '2025-10-07 09:56:32'),
(8, 'Jessica', 'Bore', 'jessica.bore@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$YWR6dFQyNG1Jb080a2tGRw$zF5g1h68IivIU7t31ORHN3TI9Jmf3mYqDWCvNI1TRIs', 'jessica.bore@gmail.com', '/ecoride/uploads/photos/8_1759378193.png', 'Prudente et ponctuelle, j\'aime les échanges lors de mes voyages.', 128, 4.3, 3, 1, 1, 'actif', NULL, NULL, 1, '4d0ddc98262a91cb737b1726eb8d5e7b3d20278efb13751fe382ed9fd3a053bf', '2025-10-07 09:56:41', '2025-10-02 04:09:34', '2025-10-07 09:56:41'),
(9, 'Maxime', 'Bernard', 'maxime.bernard@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$cFN3RDZMSUp4MkhPYUU3aQ$tKnZB0/O1rDs/wq9Hp4q930a3JjzDVOsvx1mQy5HmIc', 'Max', '/ecoride/uploads/photos/9_1759378355.png', 'Conducteur habitué et prudent, j’aime la route et je privilégie la sécurité et la ponctualité. Ambiance conviviale à bord, musique douce ou discussion selon l’envie des passagers.', 95, 4.0, 1, 1, 1, 'actif', NULL, NULL, 1, '8f0225874f3a43cf6f825a0c6be368d72080cb528b15a3376f0ec46b72cb8d77', '2025-10-07 07:58:25', '2025-10-02 04:12:21', '2025-10-07 07:58:41'),
(10, 'Thierry', 'Marchand', 'thierry.marchand@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$ZGF1TlNKM1U5bVgyZVp0VQ$XX1aZjl/DToG6rIpn4VvjwWrbUTIeUttCKukPJVEF4k', 'Thierry', '/ecoride/uploads/photos/10_1759378561.png', 'Trajets réguliers, voiture confortable et bien entretenue. Je suis ponctuel et flexible, et j’apprécie un covoiturage agréable et respectueux.', 106, 0.0, 0, 1, 1, 'actif', NULL, NULL, 1, '67bf5c0502420b59b33e08f24bc51924a23a66afa243cda1917697dccee1e4ca', '2025-10-06 08:09:42', '2025-10-02 04:15:46', '2025-10-06 08:09:42'),
(11, 'Joëlle', 'Garcia', 'joelle.garcia@hotmail.fr', '$argon2id$v=19$m=65536,t=4,p=3$ZURjTGdDZkozV3k0ek5HOQ$1g1b9FPWKyCO0L8KqR9jQDoOa+PAQdlAUi0usTxvyrw', 'Joëlle', '/ecoride/uploads/photos/11_1759378794.png', 'Conductrice attentive et ponctuelle, je propose des trajets dans une ambiance agréable et détendue. La sécurité et le confort sont ma priorité, et je m’adapte volontiers aux préférences de mes passagers (discussion, musique ou tranquillité).', 9, 0.0, 0, 1, 0, 'actif', NULL, NULL, 1, 'aebce56b772adf155269c08f36c0960eeab52e0a057d253cb0b34edfb16ca346', '2025-10-06 04:20:15', '2025-10-02 04:19:37', '2025-10-06 04:20:15'),
(12, 'François', 'Gaillant', 'francois.gaillant@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$cUtmOWhtNWJDZG1vd1Vwdw$EgH88MsAALROANaRPYy4IhecMDkZv2Hp4oNBXx+i9Ug', 'François', NULL, NULL, 10, 0.0, 0, 0, 1, 'actif', NULL, NULL, 1, 'ee4973b12991d9b0b426445755c77d3bb60fb8282b2559ef05c01ca3e50b0747', '2025-10-07 08:12:49', '2025-10-06 07:08:28', '2025-10-07 08:26:01');

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `utilisateur_id`, `marque`, `modele`, `couleur`, `energie`, `immatriculation`, `premiere_mise_en_circulation`, `nombre_places_disponibles`, `est_actif`, `created_at`, `updated_at`) VALUES
(1, 2, 'Tesla', 'Y', 'Bleue', 'electrique', 'GG-386-YY', '2024-12-12', 4, 1, '2025-09-28 13:38:52', '2025-09-28 13:38:52'),
(2, 7, 'Renault', 'Megane E-tech', 'Grise', 'electrique', 'AB-123-CD', '2024-10-02', 4, 1, '2025-10-02 02:34:53', '2025-10-02 02:34:53'),
(3, 8, 'Toyota', 'Prius', 'Blanche', 'hybride', 'CD-456-EF', '2024-05-10', 4, 1, '2025-10-02 04:11:26', '2025-10-02 04:11:26'),
(4, 9, 'Peugeot', '307', 'Grise', 'thermique', 'AA-789-DF', '2022-04-05', 3, 1, '2025-10-02 04:14:24', '2025-10-02 04:14:24'),
(5, 10, 'Renault', 'Kadjar', 'Rouge', 'thermique', 'AZ-214-BC', '2022-10-02', 4, 1, '2025-10-02 04:17:12', '2025-10-02 04:17:12'),
(6, 11, 'Tesla', '3', 'Blanche', 'electrique', 'ZZ-145-FD', '2025-05-05', 4, 1, '2025-10-02 04:20:53', '2025-10-02 04:20:53');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
