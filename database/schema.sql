-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mar. 07 oct. 2025 à 10:19
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

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `trajet_id` int(11) DEFAULT NULL,
  `evaluateur_id` int(11) NOT NULL,
  `evalue_id` int(11) NOT NULL,
  `note` int(11) NOT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `statut` enum('en_attente','valide','refuse') DEFAULT 'en_attente',
  `date_validation` timestamp NULL DEFAULT NULL,
  `validateur_id` int(11) DEFAULT NULL,
  `motif_refus` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `avis`
--
DELIMITER $$
CREATE TRIGGER `after_avis_validated` AFTER UPDATE ON `avis` FOR EACH ROW BEGIN
    IF NEW.statut = 'valide' AND OLD.statut != 'valide' THEN
        UPDATE utilisateurs u
        SET note_moyenne = (
            SELECT ROUND(AVG(a.note), 1)
            FROM avis a
            WHERE a.evalue_id = NEW.evalue_id AND a.statut = 'valide'
        ),
            nombre_avis = (
                SELECT COUNT(*)
                FROM avis a
                WHERE a.evalue_id = NEW.evalue_id AND a.statut = 'valide'
            )
        WHERE u.id = NEW.evalue_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `employes`
--

CREATE TABLE `employes` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `date_embauche` date NOT NULL,
  `statut` enum('actif','suspendu','inactif') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logs_activite`
--

CREATE TABLE `logs_activite` (
  `id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_affectee` varchar(100) DEFAULT NULL,
  `enregistrement_id` int(11) DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages_contact`
--

CREATE TABLE `messages_contact` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `statut` enum('nouveau','lu','traite','ferme') DEFAULT 'nouveau',
  `employe_assigne_id` int(11) DEFAULT NULL,
  `reponse` text DEFAULT NULL,
  `date_reponse` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type_notification` varchar(100) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `reference_id` int(11) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `preferences_types`
--

CREATE TABLE `preferences_types` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `est_predefinit` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `preferences_utilisateurs`
--

CREATE TABLE `preferences_utilisateurs` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `preference_type_id` int(11) NOT NULL,
  `valeur` enum('accepte','refuse','indifferent') NOT NULL DEFAULT 'indifferent',
  `preference_personnalisee` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `trajet_id` int(11) NOT NULL,
  `passager_id` int(11) NOT NULL,
  `nombre_places` int(11) DEFAULT 1 CHECK (`nombre_places` > 0),
  `prix_total` decimal(8,2) NOT NULL,
  `statut` enum('confirmee','annulee','terminee') DEFAULT 'confirmee',
  `date_reservation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_annulation` timestamp NULL DEFAULT NULL,
  `motif_annulation` text DEFAULT NULL,
  `a_valide_trajet` tinyint(1) DEFAULT 0,
  `commentaire_validation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `reservations`
--
DELIMITER $$
CREATE TRIGGER `after_new_reservation` AFTER INSERT ON `reservations` FOR EACH ROW BEGIN
    -- Notification au chauffeur
    INSERT INTO notifications (utilisateur_id, type_notification, titre, message, reference_id)
    SELECT t.chauffeur_id, 'nouveau_trajet',
           'Nouvelle réservation !',
           CONCAT('Votre trajet du ', DATE_FORMAT(t.date_depart, '%d/%m/%Y'), ' a une nouvelle réservation.'),
           NEW.id
    FROM trajets t WHERE t.id = NEW.trajet_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_reservation_insert` AFTER INSERT ON `reservations` FOR EACH ROW BEGIN
    -- 1. DÉBITER LES CRÉDITS DU PASSAGER IMMÉDIATEMENT
    UPDATE utilisateurs
    SET credits = credits - NEW.prix_total
    WHERE id = NEW.passager_id;

    -- 2. Enregistrer la transaction de débit
    INSERT INTO transactions (utilisateur_id, type_transaction, montant, solde_avant, solde_apres, motif, reference_id)
    SELECT NEW.passager_id, 'debit', NEW.prix_total,
           credits + NEW.prix_total, credits, 'reservation', NEW.id
    FROM utilisateurs WHERE id = NEW.passager_id;

    -- 3. Réduire les places disponibles
    UPDATE trajets
    SET places_disponibles = places_disponibles - NEW.nombre_places
    WHERE id = NEW.trajet_id;
    
    -- 4. Notification au chauffeur
    INSERT INTO notifications (utilisateur_id, type_notification, titre, message, reference_id)
    SELECT t.chauffeur_id, 'nouveau_trajet',
           'Nouvelle réservation !',
           CONCAT('Votre trajet du ', DATE_FORMAT(t.date_depart, '%d/%m/%Y'), ' a une nouvelle réservation.'),
           NEW.id
    FROM trajets t WHERE t.id = NEW.trajet_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_reservation_validated` AFTER UPDATE ON `reservations` FOR EACH ROW BEGIN
    IF NEW.a_valide_trajet = TRUE AND OLD.a_valide_trajet = FALSE THEN
        -- Créditer le chauffeur (prix - commission)
        UPDATE utilisateurs u
            JOIN trajets t ON t.chauffeur_id = u.id
        SET u.credits = u.credits + (NEW.prix_total - t.commission_plateforme)
        WHERE t.id = NEW.trajet_id;

        -- Enregistrer la transaction pour le chauffeur
        INSERT INTO transactions (utilisateur_id, type_transaction, montant, solde_avant, solde_apres, motif, reference_id)
        SELECT t.chauffeur_id, 'credit', (NEW.prix_total - t.commission_plateforme),
               u.credits - (NEW.prix_total - t.commission_plateforme), u.credits, 'completion_trajet', NEW.id
        FROM trajets t
                 JOIN utilisateurs u ON u.id = t.chauffeur_id
        WHERE t.id = NEW.trajet_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_reservation_insert` BEFORE INSERT ON `reservations` FOR EACH ROW BEGIN
    DECLARE user_credits INT;
    SELECT credits INTO user_credits FROM utilisateurs WHERE id = NEW.passager_id;

    -- Vérifier que le passager a assez de crédits
    IF user_credits < NEW.prix_total THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Crédits insuffisants pour cette réservation';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `signalements`
--

CREATE TABLE `signalements` (
  `id` int(11) NOT NULL,
  `trajet_id` int(11) NOT NULL,
  `signaleur_id` int(11) NOT NULL,
  `signale_id` int(11) NOT NULL,
  `type_signalement` enum('comportement','securite','vehicule','itineraire','autre') NOT NULL,
  `description` text NOT NULL,
  `statut` enum('nouveau','en_cours','resolu','ferme') DEFAULT 'nouveau',
  `demande_suspension` tinyint(1) DEFAULT 0,
  `employe_assigne_id` int(11) DEFAULT NULL,
  `commentaire_employe` text DEFAULT NULL,
  `date_resolution` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trajets`
--

CREATE TABLE `trajets` (
  `id` int(11) NOT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `vehicule_id` int(11) NOT NULL,
  `adresse_depart` text NOT NULL,
  `adresse_arrivee` text NOT NULL,
  `date_depart` datetime NOT NULL,
  `prix_par_passager` decimal(8,2) NOT NULL CHECK (`prix_par_passager` >= 0),
  `commission_plateforme` decimal(8,2) DEFAULT 2.00,
  `places_disponibles` int(11) NOT NULL CHECK (`places_disponibles` >= 0),
  `places_totales` int(11) NOT NULL,
  `est_ecologique` tinyint(1) DEFAULT 0,
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `description` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_demarrage` timestamp NULL DEFAULT NULL,
  `date_arrivee_reelle` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_date` date GENERATED ALWAYS AS (cast(`created_at` as date)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `trajets`
--
DELIMITER $$
CREATE TRIGGER `after_trajet_cancelled` AFTER UPDATE ON `trajets` FOR EACH ROW BEGIN
    IF NEW.statut = 'annule' AND OLD.statut != 'annule' THEN
        -- Rembourser UNIQUEMENT les passagers qui avaient payé
        -- (car maintenant on ne débite plus à la réservation)
        -- Donc on ne fait rien ici pour les crédits
        
        -- Marquer les réservations comme annulées
        UPDATE reservations SET statut = 'annulee' WHERE trajet_id = NEW.id;

        -- Notifier les passagers
        INSERT INTO notifications (utilisateur_id, type_notification, titre, message, reference_id)
        SELECT r.passager_id, 'annulation',
               'Trajet annulé',
               CONCAT('Le trajet prévu le ', DATE_FORMAT(NEW.date_depart, '%d/%m/%Y'), ' a été annulé.'),
               r.id
        FROM reservations r WHERE r.trajet_id = NEW.id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_trajets_est_ecologique` BEFORE INSERT ON `trajets` FOR EACH ROW BEGIN
    DECLARE v_energie VARCHAR(50);
    SELECT energie INTO v_energie FROM vehicules WHERE id = NEW.vehicule_id;
    IF v_energie = 'electrique' THEN
        SET NEW.est_ecologique = TRUE;
    ELSE
        SET NEW.est_ecologique = FALSE;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type_transaction` enum('debit','credit','recharge') NOT NULL,
  `montant` int(11) NOT NULL CHECK (`montant` > 0),
  `solde_avant` int(11) NOT NULL,
  `solde_apres` int(11) NOT NULL,
  `motif` enum('reservation','annulation','completion_trajet','bonus_inscription','commission','remboursement') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_date` date GENERATED ALWAYS AS (cast(`created_at` as date)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_remember_tokens`
--

CREATE TABLE `user_remember_tokens` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `pseudo` varchar(100) DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `credits` int(11) DEFAULT 20,
  `note_moyenne` decimal(2,1) DEFAULT 0.0,
  `nombre_avis` int(11) DEFAULT 0,
  `est_chauffeur` tinyint(1) DEFAULT 0,
  `est_passager` tinyint(1) DEFAULT 1,
  `statut` enum('actif','suspendu','inactif') DEFAULT 'actif',
  `token_reset_password` varchar(255) DEFAULT NULL,
  `token_reset_expires_at` timestamp NULL DEFAULT NULL,
  `email_verifie` tinyint(1) DEFAULT 0,
  `token_verification` varchar(255) DEFAULT NULL,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `marque` varchar(100) NOT NULL,
  `modele` varchar(100) NOT NULL,
  `couleur` varchar(50) NOT NULL,
  `energie` enum('electrique','thermique','hybride','gpl') NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `premiere_mise_en_circulation` date NOT NULL,
  `nombre_places_disponibles` int(11) NOT NULL CHECK (`nombre_places_disponibles` between 1 and 8),
  `est_actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_stats_daily`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_stats_daily` (
`date` date
,`nb_trajets` bigint(21)
,`credits_gagnes` decimal(43,3)
);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_avis_reservation` (`reservation_id`,`evaluateur_id`),
  ADD UNIQUE KEY `unique_avis_trajet` (`trajet_id`,`evaluateur_id`),
  ADD KEY `validateur_id` (`validateur_id`),
  ADD KEY `idx_reservation` (`reservation_id`),
  ADD KEY `idx_evaluateur` (`evaluateur_id`),
  ADD KEY `idx_evalue` (`evalue_id`),
  ADD KEY `idx_note` (`note`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date_creation` (`created_at`);

--
-- Index pour la table `employes`
--
ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`);

--
-- Index pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date_creation` (`created_at`),
  ADD KEY `idx_employe` (`employe_assigne_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_lu` (`lu`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `preferences_types`
--
ALTER TABLE `preferences_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `preferences_utilisateurs`
--
ALTER TABLE `preferences_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_preference` (`utilisateur_id`,`preference_type_id`),
  ADD KEY `preference_type_id` (`preference_type_id`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_valeur` (`valeur`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reservation` (`trajet_id`,`passager_id`),
  ADD KEY `idx_trajet` (`trajet_id`),
  ADD KEY `idx_passager` (`passager_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date_reservation` (`date_reservation`),
  ADD KEY `idx_historique_passager` (`passager_id`,`created_at`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trajet` (`trajet_id`),
  ADD KEY `idx_signaleur` (`signaleur_id`),
  ADD KEY `idx_signale` (`signale_id`),
  ADD KEY `idx_type` (`type_signalement`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_employe` (`employe_assigne_id`);

--
-- Index pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicule_id` (`vehicule_id`),
  ADD KEY `idx_chauffeur` (`chauffeur_id`),
  ADD KEY `idx_date_depart` (`date_depart`),
  ADD KEY `idx_prix` (`prix_par_passager`),
  ADD KEY `idx_places_disponibles` (`places_disponibles`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_est_ecologique` (`est_ecologique`),
  ADD KEY `idx_historique_chauffeur` (`chauffeur_id`,`created_at`),
  ADD KEY `idx_stats_trajets` (`created_date`,`statut`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_type` (`type_transaction`),
  ADD KEY `idx_motif` (`motif`),
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `idx_reference` (`reference_id`),
  ADD KEY `idx_stats_credits` (`created_date`,`motif`,`montant`);

--
-- Index pour la table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilisateur_token` (`utilisateur_id`,`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_pseudo` (`pseudo`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_est_chauffeur` (`est_chauffeur`),
  ADD KEY `idx_note_moyenne` (`note_moyenne`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `immatriculation` (`immatriculation`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_energie` (`energie`),
  ADD KEY `idx_immatriculation` (`immatriculation`),
  ADD KEY `idx_est_actif` (`est_actif`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employes`
--
ALTER TABLE `employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `preferences_types`
--
ALTER TABLE `preferences_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `preferences_utilisateurs`
--
ALTER TABLE `preferences_utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `signalements`
--
ALTER TABLE `signalements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trajets`
--
ALTER TABLE `trajets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure de la vue `v_stats_daily`
--
DROP TABLE IF EXISTS `v_stats_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stats_daily`  AS SELECT cast(`trajets`.`created_at` as date) AS `date`, count(0) AS `nb_trajets`, sum(case when `trajets`.`statut` <> 'annule' then `trajets`.`prix_par_passager` * (`trajets`.`places_totales` - `trajets`.`places_disponibles`) * 0.1 else 0 end) AS `credits_gagnes` FROM `trajets` GROUP BY cast(`trajets`.`created_at` as date) ORDER BY cast(`trajets`.`created_at` as date) DESC ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`evaluateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `avis_ibfk_3` FOREIGN KEY (`evalue_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `avis_ibfk_4` FOREIGN KEY (`validateur_id`) REFERENCES `employes` (`id`),
  ADD CONSTRAINT `avis_ibfk_5` FOREIGN KEY (`trajet_id`) REFERENCES `trajets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employes`
--
ALTER TABLE `employes`
  ADD CONSTRAINT `employes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employes_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  ADD CONSTRAINT `messages_contact_ibfk_1` FOREIGN KEY (`employe_assigne_id`) REFERENCES `employes` (`id`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `preferences_utilisateurs`
--
ALTER TABLE `preferences_utilisateurs`
  ADD CONSTRAINT `preferences_utilisateurs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `preferences_utilisateurs_ibfk_2` FOREIGN KEY (`preference_type_id`) REFERENCES `preferences_types` (`id`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`trajet_id`) REFERENCES `trajets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`passager_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD CONSTRAINT `signalements_ibfk_1` FOREIGN KEY (`trajet_id`) REFERENCES `trajets_old` (`id`),
  ADD CONSTRAINT `signalements_ibfk_2` FOREIGN KEY (`signaleur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `signalements_ibfk_3` FOREIGN KEY (`signale_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `signalements_ibfk_4` FOREIGN KEY (`employe_assigne_id`) REFERENCES `employes` (`id`);

--
-- Contraintes pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD CONSTRAINT `trajets_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `trajets_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`);

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  ADD CONSTRAINT `user_remember_tokens_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
