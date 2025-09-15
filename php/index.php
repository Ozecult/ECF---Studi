<?php
// index.php
session_start();

// Page par défaut
$page = $_GET['page'] ?? 'home';

// Tableau des vues
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

// Vérifier si la page existe
if (isset($routes[$page])) {
    require "views/" . $routes[$page];
} else {
    echo "Page introuvable : $page";
}
