<?php
// index.php
ob_start();

// Configuration et autoloader
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';

$authController = new AuthController();
$userController = new UserController();

// Récupérer la page demandée
$page = $_GET['page'] ?? 'home';

// Tableau des vues simples
$routes = [
    'admin'           => 'admin.php',
    'barrerecherche'  => 'barrerecherche.php',
    'connexion'       => 'connexion.php',
    'contact'         => 'contact.php',
    'covoiturages'    => 'covoiturages.php',
    'details'         => 'details.php',
    'employe'         => 'employe.php',
    'footer'          => 'footer.php',
    'header'          => 'header.php',
    'home'            => 'home.php',
    'inscription'     => 'inscription.php',
    'mdp-oublie'      => 'mdp-oublie.php',
    'mentionslegales' => 'mentionslegales.php',
    'rechercher'      => 'rechercher.php',
    'utilisateur'     => 'utilisateur.php',
];

// Routage des pages spéciales avec logique
switch ($page) {
    case 'connexion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->handleLogin();
        } else {
            $authController->showLogin();
        }
        break;

    case 'inscription':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->handleRegister();
        } else {
            $authController->showRegister();
        }
        break;

    case 'utilisateur':
        if (!$authController->isLoggedIn()) {
            header('Location: /ecoride/php/index.php?page=connexion&redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        $userController->showDashboard();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'mdp-oublie':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->handleForgotPassword();
        } else {
            $authController->showForgotPassword();
        }
        break;

    case 'covoiturages':
    case 'rechercher':
    case 'details':
        if (!$authController->isLoggedIn()) {
            header('Location: /ecoride/php/index.php?page=connexion&redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        include __DIR__ . "/views/{$page}.php";
        break;

    case 'admin':
        // Vérifier que l'utilisateur est admin
        if (!$authController->isLoggedIn()) {
            header('Location: /ecoride/php/index.php?page=connexion');
            exit;
        }
    
        $user = $authController->getCurrentUser();
        if ($user['pseudo'] !== 'admin' && $user['email'] !== 'admin@ecoride.fr') {
            header('Location: /ecoride/php/index.php?page=utilisateur');
            exit;
        }
    
        include __DIR__ . '/views/admin.php';
        break;

    case 'employe':
        if (!$authController->isLoggedIn()) {
            header('Location: /ecoride/php/index.php?page=connexion');
            exit;
        }
        
        // Vérifier que c'est un employé
        if (!$authController->hasPermission('admin') && !$authController->hasPermission('employe')) {
            http_response_code(403);
            echo "<h1>403 — Accès refusé</h1>";
            exit;
        }
        
        require_once __DIR__ . '/controllers/EmployeController.php';
        $employeController = new EmployeController();
        $employeController->showDashboard();
        break;

    default:
        if (isset($routes[$page])) {
            include __DIR__ . '/views/' . $routes[$page];
        } else {
            include __DIR__ . '/views/home.php'; // fallback
        }
        break;
}