<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Covoiturages</title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
      rel="stylesheet"
    />
    <script type="module" src="/ecoride/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main id="contenu" class="covoiturages">
      <!-- BARRE DE RECHERCHE -->
      <?php require __DIR__ . '/barrerecherche.php'; ?>

      <!--TITRE -->
      <h1>Résultats disponibles</h1>

      <div class="zone-principale">
        <!-- FILTRES -->
        <aside class="filtres">
          <button class="filtres-toggle">Filtres</button>

          <div class="filtres-content" id="filtres-content">
            <h3>Filtres <button type="reset">Tout effacer</button></h3>

            <form method="get" class="form-filtres card">
              <!-- Horaire de départ -->
              <fieldset>
                <legend>Horaire de départ</legend>
                <div class="horaire card">
                  <div class="field-group">
                    <input type="checkbox" id="nuit" name="nuit" value="N" />
                    <label for="nuit">Nuit (00h-06h)</label>
                  </div>

                  <div class="field-group">
                    <input type="checkbox" id="matin" name="matin" value="M" />
                    <label for="matin">Matin (06h-12h)</label>
                  </div>

                  <div class="field-group">
                    <input type="checkbox" id="am" name="am" value="A" />
                    <label for="am">Après-midi (12h-18h)</label>
                  </div>

                  <div class="field-group">
                    <input type="checkbox" id="soir" name="soir" value="S" />
                    <label for="soir">Soir (18h-00h)</label>
                  </div>
                </div>
              </fieldset>

              <!-- Type de motorisation -->
              <fieldset>
                <legend>Type de motorisation</legend>
                <div class="moteur card">
                  <div class="field-group">
                    <input type="checkbox" id="elec" name="elec" value="E" />
                    <label for="elec">Électrique</label>
                  </div>

                  <div class="field-group">
                    <input type="checkbox" id="therm" name="therm" value="T" />
                    <label for="therm">Thermique</label>
                  </div>
                </div>
              </fieldset>

              <!--Durée du trajet -->
              <fieldset>
                <legend>Durée du trajet</legend>
                <div class="time card">
                  <div class="range-container">
                    <span>0 min</span>
                    <input
                      type="range"
                      id="duree"
                      name="duree"
                      min="0"
                      max="480"
                      step="15"
                      value="0"
                    />
                    <label for="duree"></label>
                    <span>8h</span>
                  </div>
                </div>
              </fieldset>

              <!-- Prix du voyage -->
              <fieldset>
                <legend>Prix du voyage en crédits</legend>
                <div class="price card">
                  <div class="range-container">
                    <span>0</span>
                    <input
                      type="range"
                      id="prix"
                      name="prix"
                      min="0"
                      max="50"
                      step="1"
                      value="0"
                    />
                    <label for="prix"></label>
                    <span>50</span>
                  </div>
                </div>
              </fieldset>

              <!-- Note covoitureur-->
              <fieldset>
                <legend>Note covoitureur</legend>
                <div class="etoiles card">
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
              </fieldset>

              <div class="button-search">
                <button type="submit" class="btn-recherche">Rechercher</button>
              </div>
            </form>
          </div>
        </aside>

        <!-- RESULTATS COVOITURAGES-->
        <section class="resultats">
          <h3>Résultats de covoiturage</h3>

          <div class="resultats-container">
            <!-- Résultat 1 -->
            <article class="card-covoit card">
              <div class="card-header">
                <!-- Conducteur et étoiles -->
                <div class="container-info">
                  <!-- Photo et détails -->
                  <div class="conducteur-info">
                    <h3>Elisa</h3>
                    <div class="etoiles-card">
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                    </div>
                    <a href="../php/index.php?page=details" class="details">Détails</a>
                  </div>
                  <!-- photo -->
                  <img
                    src="/ecoride/img/Covoiturages/Elisa.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="80"
                    height="80"
                  />
                </div>
                <!-- Zone infos trajets -->
                <div class="card-main">
                  <!-- Date trajet -->
                  <div class="date-trajet">
                    <span>Vendredi 30 mai 2025</span>
                  </div>
                  <!-- Barre de temps -->
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
                </div>
              </div>
              <!-- Informations du bas -->
              <div class="card-footer">
                <div class="card-footer-left">
                  <div class="footer-item">
                    <span class="material-icons">electric_car</span>
                    <span>Véhicule électrique !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">power_off</span>
                    <span>Pas besoin de recharge !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">group</span>
                    <span>Place(s) restante(s) : 2</span>
                  </div>
                </div>

                <div class="prix-final">16 crédits</div>
              </div>
            </article>

            <!-- Résultat 2 -->
            <article class="card-covoit card">
              <div class="card-header">
                <!-- Conducteur et étoiles -->
                <div class="container-info">
                  <!-- Photo et détails -->
                  <div class="conducteur-info">
                    <h3>Marc</h3>
                    <div class="etoiles-card">
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                    </div>
                    <a href="../php/index.php?page=details" class="details">Détails</a>
                  </div>
                  <!-- photo -->
                  <img
                    src="/ecoride/img/Covoiturages/Marc.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="80"
                    height="80"
                  />
                </div>
                <!-- Zone infos trajets -->
                <div class="card-main">
                  <!-- Date trajet -->
                  <div class="date-trajet">
                    <span>Vendredi 30 mai 2025</span>
                  </div>
                  <!-- Barre de temps -->
                  <div class="time-bar">
                    <span class="heure-depart">06h35</span>
                    <div class="barre-verte"></div>
                    <span class="heure-arrivee">09h10</span>
                  </div>
                  <!-- Trajet -->
                  <div class="trajet-infos">
                    <div><strong>Lille :</strong> Gare Lille Europe</div>
                    <div><strong>Paris :</strong> Gare de Lyon</div>
                  </div>
                </div>
              </div>
              <!-- Informations du bas -->
              <div class="card-footer">
                <div class="card-footer-left">
                  <div class="footer-item">
                    <span class="material-icons">local_gas_station</span>
                    <span>Véhicule thermique</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">group</span>
                    <span>Place(s) restante(s) : 1</span>
                  </div>
                </div>

                <div class="prix-final">16 crédits</div>
              </div>
            </article>

            <!-- Résultat 3 -->
            <article class="card-covoit card">
              <div class="card-header">
                <!-- Conducteur et étoiles -->
                <div class="container-info">
                  <!-- Photo et détails -->
                  <div class="conducteur-info">
                    <h3>Jennifer</h3>
                    <div class="etoiles-card">
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                    </div>
                    <a href="../php/index.php?page=details" class="details">Détails</a>
                  </div>
                  <!-- photo -->
                  <img
                    src="/ecoride/img/Covoiturages/Jennifer.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="80"
                    height="80"
                  />
                </div>
                <!-- Zone infos trajets -->
                <div class="card-main">
                  <!-- Date trajet -->
                  <div class="date-trajet">
                    <span>Vendredi 30 mai 2025</span>
                  </div>
                  <!-- Barre de temps -->
                  <div class="time-bar">
                    <span class="heure-depart">06h45</span>
                    <div class="barre-verte"></div>
                    <span class="heure-arrivee">09h23</span>
                  </div>
                  <!-- Trajet -->
                  <div class="trajet-infos">
                    <div><strong>Lille :</strong> Gare Lille Europe</div>
                    <div><strong>Paris :</strong> Gare de Lyon</div>
                  </div>
                </div>
              </div>
              <!-- Informations du bas -->
              <div class="card-footer">
                <div class="card-footer-left">
                  <div class="footer-item">
                    <span class="material-icons">local_gas_station</span>
                    <span>Véhicule thermique</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">group</span>
                    <span>Place(s) restante(s) : 1</span>
                  </div>
                </div>

                <div class="prix-final">17 crédits</div>
              </div>
            </article>

            <!-- Résultat 4 -->
            <article class="card-covoit card">
              <div class="card-header">
                <!-- Conducteur et étoiles -->
                <div class="container-info">
                  <!-- Photo et détails -->
                  <div class="conducteur-info">
                    <h3>Paul</h3>
                    <div class="etoiles-card">
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                    </div>
                    <a href="../php/index.php?page=details" class="details">Détails</a>
                  </div>
                  <!-- photo -->
                  <img
                    src="/ecoride/img/Covoiturages/Paul.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="80"
                    height="80"
                  />
                </div>
                <!-- Zone infos trajets -->
                <div class="card-main">
                  <!-- Date trajet -->
                  <div class="date-trajet">
                    <span>Vendredi 30 mai 2025</span>
                  </div>
                  <!-- Barre de temps -->
                  <div class="time-bar">
                    <span class="heure-depart">07h15</span>
                    <div class="barre-verte"></div>
                    <span class="heure-arrivee">09h57</span>
                  </div>
                  <!-- Trajet -->
                  <div class="trajet-infos">
                    <div><strong>Lille :</strong> Gare Lille Europe</div>
                    <div><strong>Paris :</strong> Gare de Lyon</div>
                  </div>
                </div>
              </div>
              <!-- Informations du bas -->
              <div class="card-footer">
                <div class="card-footer-left">
                  <div class="footer-item">
                    <span class="material-icons">electric_car</span>
                    <span>Véhicule électrique !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">power_off</span>
                    <span>Pas besoin de recharge !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">group</span>
                    <span>Place(s) restante(s) : 3</span>
                  </div>
                </div>

                <div class="prix-final">17 crédits</div>
              </div>
            </article>

            <!-- Résultat 5 -->
            <article class="card-covoit card">
              <div class="card-header">
                <!-- Conducteur et étoiles -->
                <div class="container-info">
                  <!-- Photo et détails -->
                  <div class="conducteur-info">
                    <h3>Léa</h3>
                    <div class="etoiles-card">
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                      <span class="material-symbols-outlined">star</span>
                    </div>
                    <a href="../php/index.php?page=details" class="details">Détails</a>
                  </div>
                  <!-- photo -->
                  <img
                    src="/ecoride/img/Covoiturages/Léa.png"
                    alt="Photo de profil"
                    class="photo-profil"
                    width="80"
                    height="80"
                  />
                </div>
                <!-- Zone infos trajets -->
                <div class="card-main">
                  <!-- Date trajet -->
                  <div class="date-trajet">
                    <span>Vendredi 30 mai 2025</span>
                  </div>
                  <!-- Barre de temps -->
                  <div class="time-bar">
                    <span class="heure-depart">08h00</span>
                    <div class="barre-verte"></div>
                    <span class="heure-arrivee">10h47</span>
                  </div>
                  <!-- Trajet -->
                  <div class="trajet-infos">
                    <div><strong>Lille :</strong> Gare Lille Europe</div>
                    <div><strong>Paris :</strong> Gare de Lyon</div>
                  </div>
                </div>
              </div>
              <!-- Informations du bas -->
              <div class="card-footer">
                <div class="card-footer-left">
                  <div class="footer-item">
                    <span class="material-icons">electric_car</span>
                    <span>Véhicule électrique !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">power_off</span>
                    <span>Pas besoin de recharge !</span>
                  </div>

                  <div class="footer-separator"></div>

                  <div class="footer-item">
                    <span class="material-icons">group</span>
                    <span>Place(s) restante(s) : 2</span>
                  </div>
                </div>

                <div class="prix-final">18 crédits</div>
              </div>
            </article>
          </div>
        </section>
      </div>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
