<header>
      <!-- MENU -->
      <div class="menu">
        <!-- Logo cliquable -->
        <a href="index.php" class="logo hover-underline gradient-hover"
          ><img src="../img/logo.svg" alt="Logo EcoRide" /><span
            >EcoRide</span
          ></a
        >

        <!-- Navigation principale -->
        <nav aria-label="Menu principal">
          <ul class="nav">
            <li class="nav-item">
              <a href="covoiturages.php" class="hover-underline gradient-hover"
                >Covoiturages</a
              >
            </li>
            <li class="nav-item">
              <a href="contact.php" class="hover-underline gradient-hover"
                >Contact</a
              >
            </li>
            <li class="nav-item">
              <a href="rechercher.php" class="hover-underline gradient-hover"
                >Rechercher</a
              >
            </li>
          </ul>
        </nav>

        <!-- Bouton profil avec menu déroulant -->
        <div class="bouton-profile">
          <button id="menu-deroulant">
            <span class="material-symbols-outlined burger-icon">menu</span>
            <span
              class="material-symbols-outlined"
              aria-label="menu-deroulant"
              role="img"
              >account_circle</span
            >
          </button>
          <nav class="mobile-nav-menu" id="mobile-nav-menu">
            <ul>
              <li><a href="covoiturages.php">Covoiturages</a></li>
              <li><a href="contact.php">Contact</a></li>
              <li><a href="rechercher.php">Rechercher</a></li>
            </ul>
          </nav>
          <div id="menu-profile" class="menu-profile">
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">S’inscrire</a>
          </div>
        </div>
      </div>
    </header>