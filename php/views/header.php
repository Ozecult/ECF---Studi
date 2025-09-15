<header>
      <!-- MENU -->
      <div class="menu">
        <!-- Logo cliquable -->
        <a href="../php/index.php?page=home" class="logo hover-underline gradient-hover"
          ><img src="/ecoride/img/logo.svg" alt="Logo EcoRide" /><span
            >EcoRide</span
          ></a
        >

        <!-- Navigation principale -->
        <nav aria-label="Menu principal">
          <ul class="nav">
            <li class="nav-item">
              <a href="../php/index.php?page=covoiturages" class="hover-underline gradient-hover"
                >Covoiturages</a
              >
            </li>
            <li class="nav-item">
              <a href="../php/index.php?page=contact" class="hover-underline gradient-hover"
                >Contact</a
              >
            </li>
            <li class="nav-item">
              <a href="../php/index.php?page=rechercher" class="hover-underline gradient-hover"
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
              <li><a href="../php/index.php?page=covoiturages">Covoiturages</a></li>
              <li><a href="../php/index.php?page=contact">Contact</a></li>
              <li><a href="../php/index.php?page=rechercher">Rechercher</a></li>
            </ul>
          </nav>
          <div id="menu-profile" class="menu-profile">
            <a href="../php/index.php?page=connexion">Se connecter</a>
            <a href="../php/index.php?page=inscription">S’inscrire</a>
          </div>
        </div>
      </div>
    </header>