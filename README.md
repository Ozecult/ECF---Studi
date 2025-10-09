# ECF---EcoRide

# 🚗 EcoRide - Plateforme de Covoiturage Écologique

![PHP Version](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)
![Status](https://img.shields.io/badge/Status-Complete-success)
![License](https://img.shields.io/badge/License-Academic-green)

![EcoRide Banner](img/logo.svg)

Application web de covoiturage favorisant les véhicules électriques, développée dans le cadre de l'ECF - Titre Professionnel Développeur Web et Web Mobile (Studi 2025).

## 📋 Table des matières

- [À propos](#à-propos)
- [Captures d'écran](#captures-décran)
- [Technologies utilisées](#technologies-utilisées)
- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation locale](#installation-locale)
- [Structure du projet](#structure-du-projet)
- [Identifiants de test](#identifiants-de-test)
- [Utilisation](#utilisation)
- [Sécurité](#sécurité)
- [Déploiement](#déploiement)
- [Documentation](#documentation)
- [FAQ](#faq)
- [License](#license)
- [Auteur](#auteur)

## À propos

EcoRide est une plateforme de covoiturage qui encourage les déplacements écologiques en mettant en avant les véhicules électriques. L'application permet aux conducteurs de proposer des trajets et aux passagers de les réserver via un système de crédits.

**Objectifs du projet :**

- Réduire l'impact environnemental des déplacements
- Faciliter le covoiturage entre particuliers
- Favoriser l'utilisation de véhicules électriques
- Proposer une solution économique et transparente

## Captures d'écran

### Page d'accueil

![Page d'accueil EcoRide](docs/screenshots/home.png)
_Recherche de trajets et présentation des valeurs_

### Recherche de covoiturages

![Recherche](docs/screenshots/search.png)
_Filtres avancés et résultats en temps réel_

### Espace utilisateur

![Profil](docs/screenshots/profile.png)
_Gestion de profil, véhicules et trajets_

### Espace administrateur

![Dashboard admin](docs/screenshots/admin.png)
_Statistiques et gestion des utilisateurs_

## Technologies utilisées

### Front-end

- **HTML5** - Structure sémantique
- **CSS3** - Design responsive et moderne
- **JavaScript** - Interactions dynamiques
- **Chart.js** - Graphiques statistiques (espace admin)
- **Material Icons** - Iconographie

### Back-end

- **PHP 8.x** - Langage serveur
- **Architecture MVC** - Organisation du code
- **PDO** - Accès base de données

### Bases de données

- **MySQL 5.7+** - Données relationnelles (utilisateurs, trajets, réservations)
- **MongoDB** - Logs d'activité (via MongoDB Atlas)

### Outils

- **Composer** - Gestionnaire de dépendances PHP
- **Git/GitHub** - Versioning
- **Trello** - Gestion de projet (Kanban)
- **Figma** - Maquettes UI/UX

## Fonctionnalités

### Pour les visiteurs

- ✅ Consulter les trajets disponibles
- ✅ Rechercher un trajet (départ, arrivée, date)
- ✅ Filtrer par prix, durée, type de véhicule, note chauffeur
- ✅ Voir les détails d'un trajet (avis, véhicule, préférences conducteur)
- ✅ Créer un compte

### Pour les utilisateurs

- ✅ Réserver un trajet
- ✅ Devenir chauffeur et/ou passager
- ✅ Créer et publier un trajet
- ✅ Gérer ses véhicules
- ✅ Définir ses préférences (prédéfinies ou personnalisées)
- ✅ Voir l'historique de ses trajets
- ✅ Démarrer/terminer un trajet
- ✅ Annuler une réservation/trajet
- ✅ Laisser un avis sur un conducteur ou le signaler
- ✅ Recharger ses crédits

### Pour les employés

- ✅ Valider ou refuser les avis soumis
- ✅ Gérer les signalements de trajets
- ✅ Consulter les messages de contact

### Pour les administrateurs

- ✅ Créer des comptes employés
- ✅ Suspendre/réactiver des utilisateurs
- ✅ Visualiser les statistiques (trajets/jour, crédits générés)
- ✅ Accéder à l'espace employés

## Prérequis

- **PHP** >= 8.0
- **MySQL** >= 5.7 ou MariaDB
- **Composer** (gestionnaire de dépendances)
- **Serveur web** (Apache/Nginx) ou PHP built-in server
- _Optionnel :_ Compte MongoDB Atlas (pour les logs)

### Extensions PHP requises

```bash
php -m | grep -E 'pdo|mysql|mbstring|openssl|json'
```

## Installation locale

1. Cloner le projet

```bash
git clone https://github.com/Ozecult/ECF---Studi.git
cd ECF---Studi
```

2. Installer les dépendances

```bash
composer install
```

3. Configurer la base de données MySQL

Créer la base de données :

```sql
CREATE DATABASE ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importer la structure :

```bash
mysql -u root -p ecoride < database/schema.sql
```

Importer les données de test :

```bash
# Importer les données de test
mysql -u root -p ecoride < database/data.sql
```

4. Configurer l'application

Copier le fichier de configuration:

```bash
cp php/config/config.example.php php/config/config.php
```

Éditer php/config/config.php avec vos paramètres :

```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'ecoride',
    'username' => 'root',
    'password' => 'votre_mot_de_passe',
    'charset' => 'utf8mb4',
],
```

MongoDB (optionnel), si vous souhaitez activer les logs MongoDB :

```php
'mongodb' => [
    'uri' => 'mongodb+srv://username:password@cluster.mongodb.net/',
    'database' => 'ecoride'
],
```

5. Créer le dossier uploads

```bash
mkdir -p uploads/photos
chmod 755 uploads/photos
```

6. Démarrer le serveur

Option A : Serveur PHP intégré

```bash
php -S localhost:8000
```

Option B : XAMPP/MAMP/WAMP

- Placer le projet dans htdocs/ ou www/
- Accéder via http://localhost/ecoride/php/index.php

7. Accéder à l'application
   Ouvrir dans le navigateur :
   http://localhost:8000/php/index.php

## Structure du projet

```bash
ecoride/
├── css/
│ └── style.css # Styles globaux
├── js/
│ └── script.js # Scripts front-end
├── php/
│ ├── api/
│ │ └── api-router.php # Routeur API REST
│ ├── config/
│ │ ├── config.php # Configuration (git-ignored)
│ │ ├── config.example.php # Template config
│ │ ├── Database.php # Singleton MySQL
│ │ └── mongodb.php # Connexion MongoDB
│ ├── controllers/
│ │ ├── AdminController.php
│ │ ├── AuthController.php
│ │ ├── ContactController.php
│ │ ├── EmployeController.php
│ │ ├── TrajetController.php
│ │ └── UserController.php
│ ├── helpers/
│ │ └── EmailSimulator.php
│ ├── models/
│ │ ├── ActivityLog.php # Logs MongoDB
│ │ ├── Avis.php
│ │ ├── MessageContact.php
│ │ ├── Preference.php
│ │ ├── Signalement.php
│ │ ├── Trajet.php
│ │ ├── User.php
│ │ └── Vehicule.php
│ ├── views/
│ │ ├── admin.php
│ │ ├── connexion.php
│ │ ├── contact.php
│ │ ├── covoiturages.php
│ │ ├── details.php
│ │ ├── employe.php
│ │ ├── footer.php
│ │ ├── header.php
│ │ ├── home.php
│ │ ├── inscription.php
│ │ ├── mdp-oublie.php
│ │ ├── mentionslegales.php
│ │ ├── rechercher.php
│ │ └── utilisateur.php
│ └── index.php # Point d'entrée principal
├── img/ # Images statiques
├── uploads/
│ └── photos/ # Photos de profil utilisateurs
├── database/
│ ├── schema.sql # Structure BDD
│ └── data.sql # Données de test
├── docs/ # Documentation PDF
├── .gitignore
├── composer.json
└── README.md
```

## Identifiants de test

Admin -> 'administrateur@ecoride.fr', '@Admin1234!' -> Accès complet
Employés -> 'kevin.dupont@hotmail.fr', '@Employe2025!' -> Validation avis, gestion signalements et messages de contact
Utilisateurs -> Peut réserver des trajets :
'elisa.portemont@orange.fr', '@Elisa2025!'
'joelle.garcia@hotmail.fr', '@Joelle2025!'
'jessica.bore@gmail.com', '@Jessica2025!'
'maxime.bernard@gmail.com', '@Maxime2025!'
'thierry.marchand@gmail.com', '@Thierry2025!'
'francois.gaillant@gmail.com', '@Francois2025!'

## Utilisation

### Créer un trajet chauffeur :

1. Se connecter avec un compte utilisateur
2. Redirection automatique vers le profil
3. Sélectionner le rôle "Chauffeur" ou "Les deux"
4. Ajouter un véhicule (obligatoire)
5. Définir ses préférences
6. Dans la section "Ajouter un voyage" :
   - Adresse de départ
   - Adresse d'arrivée
   - Date et heure
   - Prix (en crédits)
   - Véhicule
7. Valider (coûts : 2 crédits de commission plateforme)

### Réserver un trajet (n'importe quel rôle peut le faire)

1. Rechercher un trajet (page recherche, covoiturages ou barre de recherche)
2. Filtrer les résultats si besoin
3. Cliquez sur "Détails"
4. Vérifier les informations (avis, préférences, véhicule)
5. Cliquez sur "Participer" si les crédits sont suffisants ou ajouter des crédits au compte
6. Le trajet réservé est consultable depuis son espace utilisateur

### Déroulement d'un trajet

1. Avant départ : Chauffeur et passager peuvent annuler (remboursement automatique hormis les deux crédits de commission)
2. Démarrage : Chauffeur clique sur "Démarrer le trajet" dans son espace (tableau trajets à venir)
3. Fin : Chauffeur clique sur "Terminer le trajet" -> crédits versés au chauffeur
4. Après : Passagers peuvent laisser un avis (validation requise par employé)

## Sécurité

### Mesures implémentées

- Mots de passe : Hashage Argon2ID
- SQL Injection : Prepared statements (PDO) sur 100% des requêtes
- XSS : htmlspecialchars() sur toutes les sorties utilisateur
- CSRF : Tokens uniques par session
  Sessions : Configuration sécurisée (httpOnly,secure en prod)
- Rate limiting: Blocage temporaire après 5 tentatives de connexion échouées
- Validation : Sanitization des entrées (email, pseudon etc.)

## Déploiement

### Environnement de production

Hébergeur : https://www.hostinger.com/special/000webhost
URL en ligne :

### Configuration de production

Dans config.php :

```php
'app' => [
    'env' => 'production',
    'debug' => false,
    'url' => 'https://votre-domaine.com',
],
'session' => [
    'cookie_secure' => true, // HTTPS uniquement
],
```

### Procédure de déploiement

1. Exporter le projet (sans /vendor/)
2. Uploader via FTP
3. Exécuter composer install sur le serveur
4. Créer la BDD et importer schema.sql puis data.sql
5. Configurer config.php avec les credentials serveur
6. Vérifier les permissions sur /uploads/

## Documentation

### Documentation complète disponible dans le dossier /docs/ :

- documentation_technique.pdf - Architecture, MCD, diagrammes UML
- manuel_utilisateur.pdf - Guide utilisateur avec captures d'écran
- charte_graphique.pdf - Palette de couleurs, typographie, maquettes
- gestion_projet.pdf - Méthodologie, Kanban Trello
  Lien Trello : https://trello.com/b/sWHWiAEd/ecoride

### Contexte académique

Ce projet a été développé dans le cadre de l'Évaluation en Cours de Formation (ECF) pour le Titre Professionnel Développeur Web et Web Mobile (Studi - 2025).

## FAQ

### Questions générales

**Q: Puis-je utiliser ce projet comme base pour mon propre site de covoiturage ?**  
Ce projet académique est protégé par le droit d'auteur. Vous pouvez consulter le code pour apprendre et vous en inspirer, mais l'utilisation commerciale ou la copie intégrale nécessitent une autorisation écrite. Contactez sandypodvin@gmail.com pour toute demande spécifique.

**Q: Le projet est-il en production ?**  
R: Il s'agit d'un projet académique. Une version de démonstration est disponible sur 000webhost (temps de chargement lent dû à l'hébergement gratuit).

**Q: MongoDB est-il obligatoire pour faire fonctionner l'application ?**  
R: Non, MongoDB est optionnel. Il sert uniquement à stocker les logs d'activité. Sans MongoDB, l'application fonctionne normalement (les logs sont simplement désactivés).

### Questions techniques

**Q: Les emails de réinitialisation de mot de passe fonctionnent-ils ?**  
R: Non, actuellement l'envoi d'emails est simulé via `error_log()`. En production, il faudrait implémenter PHPMailer ou un service SMTP.

**Q: Pourquoi utiliser à la fois MySQL et MongoDB ?**  
R: MySQL stocke les données structurées (utilisateurs, trajets, réservations), tandis que MongoDB stocke les logs non structurés (historique d'activité, audit). C'est une architecture polyglotte.

**Q: Comment ajouter un nouveau rôle utilisateur ?**  
R: Modifiez la table `roles` dans MySQL et ajoutez les permissions correspondantes dans `UserController.php`.

**Q: Le projet est-il responsive ?**  
R: Oui, l'application est entièrement responsive (mobile, tablette, desktop) selon la charte graphique.

### Installation et déploiement

**Q: J'ai une erreur "Connection refused" avec MongoDB**  
R: C'est normal si vous n'avez pas configuré MongoDB. Commentez la ligne `ActivityLog::log()` dans les contrôleurs ou configurez votre URI MongoDB Atlas.

**Q: Composer install échoue, que faire ?**  
R: Vérifiez que vous avez PHP >= 8.0 et les extensions requises. Exécutez `php -v` et `php -m`.

**Q: Comment changer le port du serveur PHP ?**  
R: Utilisez `php -S localhost:VOTRE_PORT` (ex: `php -S localhost:3000`).

### Sécurité

**Q: Les mots de passe sont-ils sécurisés ?**  
R: Oui, tous les mots de passe sont hashés avec Argon2ID (algorithme recommandé par l'OWASP). Ils ne sont jamais stockés en clair.

**Q: Y a-t-il une protection contre les injections SQL ?**  
R: Oui, 100% des requêtes utilisent des requêtes préparées (PDO prepared statements).

**Q: Le projet a-t-il été audité en sécurité ?**  
R: Il s'agit d'un projet académique. Les bonnes pratiques sont appliquées, mais un audit professionnel serait recommandé pour une mise en production réelle.

---

**Votre question n'est pas listée ?** Ouvrez une [issue sur GitHub](https://github.com/Ozecult/ECF---Studi/issues) !

## Auteur

PODVIN Sandy
Étudiant Développeur Web et Web Mobile - Studi
sandypodvin@gmail.com
GitHub : https://github.com/Ozecult/ECF---Studi

## License

**Projet académique - Tous droits réservés © 2025 PODVIN Sandy**

Ce code est mis à disposition publiquement à des fins de **portfolio** et de **démonstration de compétences** dans le cadre de l'ECF Studi 2025.

Utilisation éducative et consultation autorisées avec attribution. Usage commercial interdit sans autorisation écrite.

Pour toute question : sandypodvin@gmail.com

🐛 Problèmes connus

Envoi d'emails simulé via error_log() (à remplacer par PHPMailer en production)
MongoDB optionnel (fonctionne sans si non configuré)

🚧 Améliorations futures

Implémentation PHPMailer/SMTP
API REST complète (endpoints JSON)
Système de notifications push
Paiement Stripe pour achat de crédits
Application mobile
Système de messagerie interne
Export PDF des factures
Proposition de dates alternatives pour les résultats de covoiturages

⚠️ Note importante : Les identifiants de test fournis sont à usage exclusif de démonstration. En production, tous les mots de passe doivent être modifiés.
