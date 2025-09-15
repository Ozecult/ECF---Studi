<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Espace employé</title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"
      rel="stylesheet"
    />
    <script type="module" src="/ecoride/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main class="employe">
      <!-- Avis à valider -->
      <section class="choix-avis card">
        <h1>Avis à valider</h1>
        <!-- Avis 1 -->
        <article
          class="avis_a_valider card"
          data-user-id="123"
          data-avis-id="123"
        >
          <div class="ligne-profil">
            <img
              src="/ecoride/img/Covoiturages/Elisa.png"
              alt="Photo de profil d'Elisa"
              class="photo-profil"
              width="60"
              height="60"
            />
            <p class="pseudo">Elisa</p>
          </div>

          <div class="ligne-commentaire">
            <p>"Très bon trajet, conducteur ponctuel !"</p>
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

          <div class="boutons-avis">
            <button
              class="button"
              type="button"
              aria-label="Valider l’avis de Elisa"
              data-action="valider"
            >
              Valider
            </button>
            <button
              class="button"
              type="button"
              aria-label="Refuser l’avis de Elisa"
              data-action="refuser"
            >
              Refuser
            </button>
          </div>
        </article>

        <!-- Avis 2 -->
        <article
          class="avis_a_valider card"
          data-user-id="456"
          data-avis-id="456"
        >
          <div class="ligne-profil">
            <span
              class="material-symbols-outlined"
              aria-label="Icône profil"
              role="img"
              >account_circle</span
            >
            <p class="pseudo">Marc</p>
          </div>

          <div class="ligne-commentaire">
            <p>
              "Très bon covoiturage, bonne ambiance dans la voiture et échanges
              agréables"
            </p>
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

          <div class="boutons-avis">
            <button
              class="button"
              type="button"
              aria-label="Valider l’avis de Marc"
              data-action="valider"
            >
              Valider
            </button>
            <button
              class="button"
              type="button"
              aria-label="Refuser l’avis de Marc"
              data-action="refuser"
            >
              Refuser
            </button>
          </div>
        </article>

        <!-- Avis 3 -->
        <article
          class="avis_a_valider card"
          data-user-id="789"
          data-avis-id="789"
        >
          <div class="ligne-profil">
            <img
              src="/ecoride/img/Employe/Alexandre.png"
              alt="Photo de profil d'Alexandre"
              class="photo-profil"
              width="60"
              height="60"
            />
            <p class="pseudo">Alexandre</p>
          </div>

          <div class="ligne-commentaire">
            <p>
              "Super trajet, conducteur ponctuel et sympa. Voiture propre, je
              recommande !"
            </p>
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

          <div class="boutons-avis">
            <button
              class="button"
              type="button"
              aria-label="Valider l’avis d'Alexandre"
              data-action="valider"
            >
              Valider
            </button>
            <button
              class="button"
              type="button"
              aria-label="Refuser l’avis d'Alexandre"
              data-action="refuser"
            >
              Refuser
            </button>
          </div>
        </article>
      </section>

      <!-- Trajets signalés -->
      <section class="litiges card">
        <h2>Trajets signalés</h2>

        <table>
          <!-- colonnes -->
          <thead>
            <tr>
              <th scope="col">
                <span
                  class="material-symbols-outlined"
                  aria-label="Calendrier"
                  role="img"
                  >calendar_month</span
                >Date
              </th>
              <th scope="col">
                <span
                  class="material-symbols-outlined"
                  aria-label="Trajet"
                  role="img"
                  >route</span
                >Trajet
              </th>
              <th scope="col">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >Conducteur
              </th>
              <th scope="col">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >Passager
              </th>
              <th scope="col">
                <span
                  class="material-symbols-outlined"
                  aria-label="Commentaire"
                  role="img"
                  >chat</span
                >Motif
              </th>
            </tr>
          </thead>
          <!-- lignes -->
          <tbody>
            <!-- ligne 1 -->
            <tr>
              <td>04/06/2025</td>
              <td>n°175 : Lille --> Paris</td>
              <td>Elisa : elisadurez@gmail.com</td>
              <td>Marc : marc.scallone@orange.fr</td>
              <td>Comportement</td>
            </tr>
            <!-- ligne 2 -->
            <tr>
              <td>06/06/2025</td>
              <td>n°223 : Lyon --> Dijon</td>
              <td>Romain : romain.balland@wanadoo.fr</td>
              <td>Sophie : s.deprez@gmail.com</td>
              <td>Retard</td>
            </tr>
            <!-- ligne 3 -->
            <tr>
              <td>28/05/2025</td>
              <td>n°29 : Toulouse --> Agen</td>
              <td>Benjamin : benjaminprez@orange.fr</td>
              <td>Eric : ericmahieu@gmail.com</td>
              <td>Retard</td>
            </tr>
          </tbody>
        </table>
        <!-- VERSION 1024px -->
        <div class="litiges-mobile">
          <!-- Carte 1 -->
          <div class="trajet-carte">
            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Calendrier"
                  role="img"
                  >calendar_month</span
                >
                <span class="label">Date</span>
              </div>
              <div class="value">04/06/2025</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Trajet"
                  role="img"
                  >route</span
                >
                <span class="label">Trajet</span>
              </div>
              <div class="value">n°175 : Lille --> Paris</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Conducteur</span>
              </div>
              <div class="value">Elisa : elisadurez@gmail.com</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Passager</span>
              </div>
              <div class="value">Marc : marc.scallone@orange.fr</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Commentaire"
                  role="img"
                  >chat</span
                >
                <span class="label">Motif</span>
              </div>
              <div class="value">Comportement</div>
            </div>
          </div>

          <!-- Carte 2 -->
          <div class="trajet-carte">
            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Calendrier"
                  role="img"
                  >calendar_month</span
                >
                <span class="label">Date</span>
              </div>
              <div class="value">06/06/2025</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Trajet"
                  role="img"
                  >route</span
                >
                <span class="label">Trajet</span>
              </div>
              <div class="value">n°223 : Lyon --> Dijon</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Conducteur</span>
              </div>
              <div class="value">Romain : romain.balland@wanadoo.fr</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Passager</span>
              </div>
              <div class="value">Sophie : s.deprez@gmail.com</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Commentaire"
                  role="img"
                  >chat</span
                >
                <span class="label">Motif</span>
              </div>
              <div class="value">Retard</div>
            </div>
          </div>

          <!-- Carte 3 -->
          <div class="trajet-carte">
            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Calendrier"
                  role="img"
                  >calendar_month</span
                >
                <span class="label">Date</span>
              </div>
              <div class="value">28/05/2025</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Trajet"
                  role="img"
                  >route</span
                >
                <span class="label">Trajet</span>
              </div>
              <div class="value">n°29 : Toulouse --> Agen</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Conducteur</span>
              </div>
              <div class="value">Benjamin : benjaminprez@orange.fr</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Profil"
                  role="img"
                  >person</span
                >
                <span class="label">Passager</span>
              </div>
              <div class="value">Eric : ericmahieu@gmail.com</div>
            </div>

            <div class="trajet-info">
              <div class="trajet-info-header">
                <span
                  class="material-symbols-outlined"
                  aria-label="Commentaire"
                  role="img"
                  >chat</span
                >
                <span class="label">Motif</span>
              </div>
              <div class="value">Retard</div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
