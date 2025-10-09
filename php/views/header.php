<?php
// S'assurer que authController est disponible
if (!isset($authController)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $authController = new AuthController();
}
$isLoggedIn = $authController->isLoggedIn();
$userName = '';
$isAdmin = false;

if ($isLoggedIn) {
    $currentUser = $authController->getCurrentUser();
    $userName = $currentUser['prenom'] ?? $currentUser['pseudo'] ?? 'Utilisateur';
    
    // Vérifier si l'utilisateur est admin
    if ($currentUser['pseudo'] === 'admin' || $currentUser['email'] === 'admin@ecoride.fr') {
        $isAdmin = true;
    }
}
?>
<header>
  <!-- MENU -->
  <div class="menu">
    <!-- Logo cliquable -->
    <a href="../php/index.php?page=home" class="logo hover-underline gradient-hover">
      <img src="/img/logo.svg" alt="Logo EcoRide" />
      <span>EcoRide</span>
    </a>
   
    <!-- Navigation principale -->
    <nav aria-label="Menu principal">
      <ul class="nav">
        <li class="nav-item">
          <a href="../php/index.php?page=covoiturages" class="hover-underline gradient-hover">Covoiturages</a>
        </li>
        <li class="nav-item">
          <a href="../php/index.php?page=contact" class="hover-underline gradient-hover">Contact</a>
        </li>
        <li class="nav-item">
          <a href="../php/index.php?page=rechercher" class="hover-underline gradient-hover">Rechercher</a>
        </li>
      </ul>
    </nav>
   
    <!-- Bouton profil avec menu déroulant -->
    <div class="bouton-profile">
      <button id="menu-deroulant">
        <span class="material-symbols-outlined burger-icon">menu</span>
        <span class="material-symbols-outlined" aria-label="menu-deroulant" role="img">account_circle</span>
        <?php if ($isLoggedIn): ?>
          <span class="user-name"><?= htmlspecialchars($userName) ?></span>
        <?php endif; ?>
      </button>
     
      <nav class="mobile-nav-menu" id="mobile-nav-menu">
        <ul>
          <li><a href="../php/index.php?page=covoiturages">Covoiturages</a></li>
          <li><a href="../php/index.php?page=contact">Contact</a></li>
          <li><a href="../php/index.php?page=rechercher">Rechercher</a></li>
        </ul>
      </nav>
     
      <div id="menu-profile" class="menu-profile">
        <?php if ($isLoggedIn): ?>
          <!-- Utilisateur connecté -->
          <?php
          // Vérifier les rôles
          $isAdmin = $authController->hasPermission('admin');
          $isEmploye = $authController->hasPermission('employe');
          ?>
          
          <?php if ($isAdmin): ?>
            <a href="../php/index.php?page=admin">Espace Admin</a>
            <a href="../php/index.php?page=employe">Espace Employé</a>
            <a href="../php/index.php?page=utilisateur">Mon profil</a>
          <?php elseif ($isEmploye): ?>
            <a href="../php/index.php?page=employe">Espace Employé</a>
            <a href="../php/index.php?page=utilisateur">Mon profil</a>
          <?php else: ?>
            <a href="../php/index.php?page=utilisateur">Mon profil</a>
          <?php endif; ?>
          
          <a href="../php/index.php?page=logout">Se déconnecter</a>
        <?php else: ?>
          <!-- Utilisateur non connecté -->
          <a href="../php/index.php?page=connexion">Se connecter</a>
          <a href="../php/index.php?page=inscription">S'inscrire</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>
