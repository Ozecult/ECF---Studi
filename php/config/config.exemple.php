<?php
// Configuration de la BDD
return [
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'dbname' => $_ENV['DB_NAME'] ?? 'ecoride',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? 'votre_mot_de_passe',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    // Configuration MongoDB
    'mongodb' => [
        'uri' => $_ENV['MONGO_URI'] ?? 'mongodb://localhost:27017',
        'database' => $_ENV['MONGO_DB'] ?? 'ecoride'
    ],

    // Configuration de l'application
    'app' => [
        'name' => 'EcoRide',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'timezone' => 'Europe/Paris',
        'locale' => 'fr',
        'key' => $_ENV['APP_KEY'] ?? '',
    ],
    // Configuration de sécurité
    'security' => [
        'password_hash_algo' => PASSWORD_ARGON2ID,
        'password_options' => [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ],
        'csrf_token_name' => 'csrf_token',
        'session_name' => 'ECORIDE_SESSION',
        'session_lifetime' => 7200, // 2 heures
        'remember_token_lifetime' => 2592000, // 30 jours
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutes
        'rate_limit_attempts' => 10,
        'rate_limit_window' => 3600, // 1 heure
    ],
    // Configuration des sessions
    'session' => [
        'cookie_httponly' => true,
        'cookie_secure' => false, // FALSE pour HTTP local
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax',
        'cookie_lifetime' => 0,
        'gc_maxlifetime' => 7200,
        'cookie_domain' => '', // Vide pour localhost
        'cookie_path' => '/', // Chemin racine
    ],
    // Configuration email
    'email' => [
        'smtp_host' => $_ENV['SMTP_HOST'] ?? '',
        'smtp_port' => $_ENV['SMTP_PORT'] ?? 587,
        'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
        'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
        'smtp_encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
        'from_address' => $_ENV['SMTP_FROM_ADDRESS'] ?? 'noreply@ecoride.com',
        'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'EcoRide',
    ],
    // Configuration des logs
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'file' => $_ENV['LOG_FILE'] ?? '../logs/ecoride.log',
        'max_files' => 10,
    ],
    // Configuration des uploads
    'upload' => [
        'max_size' => 2097152, // 2MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'photo_path' => '../uploads/photos/',
    ],
    // Configuration des API externes
    'apis' => [
        'google_maps_key' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? '',
        'stripe_public' => $_ENV['STRIPE_PUBLIC_KEY'] ?? '',
        'stripe_secret' => $_ENV['STRIPE_SECRET_KEY'] ?? '',
    ],
    // Routes et pages autorisées
    'routes' => [
        'default' => 'home',
        'allowed_pages' => [
            'admin',
            'connexion',
            'contact',
            'covoiturages',
            'details',
            'employe',
            'home',
            'inscription',
            'mdp-oublie',
            'mentionslegales',
            'rechercher',
            'utilisateur'
        ]
    ]
];