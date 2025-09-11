<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Détails du covoiturage</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"
      rel="stylesheet"
    />
    <script type="module" src="../js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require 'header.php'; ?>

    <!-- MAIN -->
    <main id="contenu" class="page-details">
      <!-- BARRE DE RECHERCHE -->
      <?php require 'barrerecherche.php'; ?>

      <!-- RESUME DU COVOITURAGE -->
      <div class="disposition">
        <!-- Card trajet -->
        <div class="info-user">
          <section class="card-profil card">
            <h2>Profil du conducteur</h2>

            <div class="profil">
              <div class="header-card">
                <div class="user-img">
                  <img
                    src="../img/Covoiturages/Elisa.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="100"
                    height="100"
                  />

                  <p class="pseudo">Elisa</p>
                </div>

                <div class="etoiles">
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                </div>
              </div>

              <p>
                Bonjour! Je suis une conductrice agréable et qui roule en toute
                sécurité.
              </p>

              <ul class="infos-trajets">
                <li>
                  <span
                    class="material-symbols-outlined"
                    aria-label="Cigarette"
                    role="img"
                    >smoke_free</span
                  >Non fumeur
                </li>
                <li>
                  <span
                    class="material-symbols-outlined"
                    aria-label="Chien"
                    role="img"
                    >sound_detection_dog_barking</span
                  >Animaux autorisés
                </li>
                <li>
                  <span
                    class="material-symbols-outlined"
                    aria-label="Enfant"
                    role="img"
                    >account_child</span
                  >Enfants acceptés
                </li>
                <li>
                  <span
                    class="material-icons"
                    aria-label="Voiture électrique"
                    role="img"
                    >electric_car</span
                  >Tesla model 3
                </li>
              </ul>
            </div>
          </section>

          <section class="avis card">
            <h2>Avis du conducteur</h2>
            <!-- Avis 1 -->
            <article class="avis-item card">
              <h3>Avis de Pierre</h3>

              <div class="user-img">
                <img
                  src="../img/Détails/profil1.png"
                  alt="Photo de profil"
                  class="photo-profil"
                  width="60"
                  height="60"
                />

                <div class="etoiles">
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                </div>
              </div>

              <p>
                Conductrice très ponctuelle et agréable. Le trajet s’est super
                bien passé, je recommande à 100 % !
              </p>
            </article>

            <!-- Avis 2 -->
            <article class="avis-item card" title="avis2">
              <h3>Avis de Jean</h3>
              <div class="user-img">
                <img
                  src="../img/Détails/profil2.png"
                  alt="Photo de profil"
                  class="photo-profil"
                  width="60"
                  height="60"
                />

                <div class="etoiles">
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                </div>
              </div>

              <p>
                Voiture propre, conduite souple, et très bon échange pendant le
                trajet. Merci encore !
              </p>
            </article>

            <!-- Avis 3 -->
            <article class="avis-item card" title="avis3">
              <h3>Avis de George</h3>

              <div class="user-img">
                <span
                  class="material-symbols-outlined"
                  aria-label="Photo profil"
                  role="img"
                  >account_circle</span
                >

                <div class="etoiles">
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                </div>
              </div>

              <p>
                Conductrice fiable et respectueuse des horaires. Trajet
                agréable, je me suis senti en confiance.
              </p>
            </article>

            <!-- Avis 4 -->
            <article class="avis-item card" title="avis4">
              <h3>Avis de Léna</h3>

              <div class="user-img">
                <img
                  src="../img/Détails/profil3.png"
                  alt="Photo de profil"
                  class="photo-profil"
                  width="60"
                  height="60"
                />

                <div class="etoiles">
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                  <span class="material-symbols-outlined" aria-hidden="true"
                    >star</span
                  >
                </div>
              </div>

              <p>
                Un covoiturage fluide, sans stress. Merci pour la conduite
                prudente et les échanges intéressants.
              </p>
            </article>
          </section>
        </div>

        <!-- RESERVATION -->
        <section class="reservation card">
          <h2>Réservation</h2>
          <!-- Résumé trajet -->
          <!-- Date trajet -->
          <div class="date-trajet">
            <span>Vendredi 30 mai 2025</span>
          </div>

          <div class="time-bar">
            <span class="heure-depart">06h00</span>
            <div class="barre-verte"></div>
            <span class="heure-arrivee">08h35</span>
          </div>

          <!-- Trajet -->
          <div class="trajet-infos">
            <div><strong>Lille :</strong> Gare Lille Europe</div>
            <div><strong>Paris :</strong> Gare de Lyon</div>
          </div>

          <div class="user-img">
            <img
              src="../img/Covoiturages/Elisa.png"
              alt="Photo de profil"
              class="photo-profil"
              width="100"
              height="100"
            />

            <p>Elisa</p>
          </div>

          <ul class="infos-trajets">
            <li>
              <span
                class="material-icons"
                aria-label="Véhicule électrique"
                role="img"
                >electric_car</span
              >Véhicule électrique !
            </li>
            <li>
              <span
                class="material-icons"
                aria-label="Prise électrique"
                role="img"
                >power_off</span
              >Pas besoin de recharge !
            </li>
          </ul>

          <!-- Bouton réservation -->
          <button class="bouton-validation" type="button">
            <span class="material-symbols-outlined">task_alt</span
            ><strong>Payer 16 crédits</strong>
          </button>
        </section>
      </div>
    </main>

    <!-- FOOTER -->
    <?php require 'footer.php'; ?>
  </body>
</html>
