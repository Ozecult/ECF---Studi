-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mar. 07 oct. 2025 à 10:21
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
-- Déchargement des données de la table `employes`
--

INSERT INTO `employes` (`id`, `utilisateur_id`, `role_id`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-09-30', 'actif', '2025-09-30 07:40:44', '2025-09-30 07:40:44'),
(2, 6, 2, '2025-09-30', 'actif', '2025-09-30 12:14:52', '2025-09-30 12:14:52');

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'administrateur', 'Accès complet à toutes les fonctionnalités', '2025-09-16 12:31:26', '2025-09-16 12:31:26'),
(2, 'employe', 'Gestion des avis et signalements', '2025-09-16 12:31:26', '2025-09-16 12:31:26'),
(3, 'utilisateur', 'Personne ayant crée un compte', '2025-09-16 12:32:26', '2025-09-16 12:32:26');

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `mot_de_passe`, `pseudo`, `photo_profil`, `bio`, `credits`, `note_moyenne`, `nombre_avis`, `est_chauffeur`, `est_passager`, `statut`, `token_reset_password`, `token_reset_expires_at`, `email_verifie`, `token_verification`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'Sandy', 'Podvin', 'admin@ecoride.fr', '$argon2id$v=19$m=65536,t=4,p=1$ZUVEWUViMzkzRzE0dXNDbA$VclTTavJfwiiK4NxkUyI0oFNXd1+lMXYOCKGFsPIfCg', 'admin', NULL, NULL, 1039, 0.0, 0, 1, 1, 'actif', NULL, NULL, 1, NULL, '2025-10-06 08:11:28', '2025-09-30 06:43:30', '2025-10-06 08:11:28'),
(2, 'Sandy', 'Podvin', 'sandypodvin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$RGNYYlBXUW93VkdVdnNiUg$GwDkABszvhyF4TzUAOf02BB/d/+LiMADatkVzr8quCk', 'Ozecult', '/ecoride/uploads/photos/2_1759308971.jpg', 'J\'aime rencontrer des gens lors de mes voyages !', 182, 4.8, 5, 1, 1, 'actif', NULL, NULL, 1, '2437a3b88609510290bb12a5458dc3525da4af7d9ef01a95c17e4cc26ce68035', '2025-10-03 08:45:59', '2025-09-18 12:38:34', '2025-10-03 08:47:31'),
(6, 'Kevin', 'Dupont', 'kevin.dupont@hotmail.fr', '$argon2id$v=19$m=65536,t=4,p=3$Z0M2N1k5YnpCYlJQRkNaUw$B6DXalTK5VsMtueMKGAvbZzkOSXhVp/Su1b/apPOERQ', 'kevind', NULL, NULL, 60, 0.0, 0, 0, 1, 'actif', NULL, NULL, 1, '6ea5c6324ed4d8db8a87064e58cf0898a0827ef9979a490f4f33de4bcf840d3a', '2025-10-06 05:30:41', '2025-09-30 12:14:52', '2025-10-06 05:30:41'),
(7, 'Elisa', 'Portemont', 'elisa.portemont@orange.fr', '$argon2id$v=19$m=65536,t=4,p=3$S21WSEdlbFAuZjR0WDZLMw$O80cYsxwBfAXQUu0FM8Exv07Fx9W68K0RelawhPQRWw', 'Elisa', '/ecoride/uploads/photos/7_1759372283.png', 'Bonjour! Je suis une conductrice agréable et qui roule en toute sécurité.', 115, 4.3, 3, 1, 1, 'actif', NULL, NULL, 1, 'b80464a2ff91df185eebe39b06ce9adc6f57383690d12ae5478e4c7084ac76eb', '2025-10-06 13:39:29', '2025-10-01 14:57:08', '2025-10-06 13:39:29'),
(8, 'Jessica', 'Bore', 'jessica.bore@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$YWR6dFQyNG1Jb080a2tGRw$zF5g1h68IivIU7t31ORHN3TI9Jmf3mYqDWCvNI1TRIs', 'jessica.bore@gmail.com', '/ecoride/uploads/photos/8_1759378193.png', 'Prudente et ponctuelle, j\'aime les échanges lors de mes voyages.', 84, 4.3, 3, 1, 1, 'actif', NULL, NULL, 1, '4d0ddc98262a91cb737b1726eb8d5e7b3d20278efb13751fe382ed9fd3a053bf', '2025-10-07 08:07:33', '2025-10-02 04:09:34', '2025-10-07 08:08:26'),
(9, 'Maxime', 'Bernard', 'maxime.bernard@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$cFN3RDZMSUp4MkhPYUU3aQ$tKnZB0/O1rDs/wq9Hp4q930a3JjzDVOsvx1mQy5HmIc', 'Max', '/ecoride/uploads/photos/9_1759378355.png', 'Conducteur habitué et prudent, j’aime la route et je privilégie la sécurité et la ponctualité. Ambiance conviviale à bord, musique douce ou discussion selon l’envie des passagers.', 95, 4.0, 1, 1, 1, 'actif', NULL, NULL, 1, '8f0225874f3a43cf6f825a0c6be368d72080cb528b15a3376f0ec46b72cb8d77', '2025-10-07 07:58:25', '2025-10-02 04:12:21', '2025-10-07 07:58:41'),
(10, 'Thierry', 'Marchand', 'thierry.marchand@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$ZGF1TlNKM1U5bVgyZVp0VQ$XX1aZjl/DToG6rIpn4VvjwWrbUTIeUttCKukPJVEF4k', 'Thierry', '/ecoride/uploads/photos/10_1759378561.png', 'Trajets réguliers, voiture confortable et bien entretenue. Je suis ponctuel et flexible, et j’apprécie un covoiturage agréable et respectueux.', 106, 0.0, 0, 1, 1, 'actif', NULL, NULL, 1, '67bf5c0502420b59b33e08f24bc51924a23a66afa243cda1917697dccee1e4ca', '2025-10-06 08:09:42', '2025-10-02 04:15:46', '2025-10-06 08:09:42'),
(11, 'Joëlle', 'Garcia', 'joelle.garcia@hotmail.fr', '$argon2id$v=19$m=65536,t=4,p=3$ZURjTGdDZkozV3k0ek5HOQ$1g1b9FPWKyCO0L8KqR9jQDoOa+PAQdlAUi0usTxvyrw', 'Joëlle', '/ecoride/uploads/photos/11_1759378794.png', 'Conductrice attentive et ponctuelle, je propose des trajets dans une ambiance agréable et détendue. La sécurité et le confort sont ma priorité, et je m’adapte volontiers aux préférences de mes passagers (discussion, musique ou tranquillité).', 9, 0.0, 0, 1, 0, 'actif', NULL, NULL, 1, 'aebce56b772adf155269c08f36c0960eeab52e0a057d253cb0b34edfb16ca346', '2025-10-06 04:20:15', '2025-10-02 04:19:37', '2025-10-06 04:20:15'),
(12, 'François', 'Gaillant', 'francois.gaillant@gmail.com', '$argon2id$v=19$m=65536,t=4,p=3$cUtmOWhtNWJDZG1vd1Vwdw$EgH88MsAALROANaRPYy4IhecMDkZv2Hp4oNBXx+i9Ug', 'François', NULL, NULL, 56, 0.0, 0, 0, 1, 'actif', NULL, NULL, 1, 'ee4973b12991d9b0b426445755c77d3bb60fb8282b2559ef05c01ca3e50b0747', '2025-10-07 08:12:49', '2025-10-06 07:08:28', '2025-10-07 08:15:01');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
