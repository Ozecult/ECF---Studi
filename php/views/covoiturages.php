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
    <link rel="stylesheet" href="/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/js/script.js"></script>
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
            <!-- Les résultats s'afficheront ici dynamiquement -->
          </div>
        </section>
      </div>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
