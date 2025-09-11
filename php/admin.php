<!DOCTYPE html>
<html lang="fr">
  <html lang="fr">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta
        name="description"
        content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
      />
      <title>EcoRide - Espace administrateur</title>
      <link rel="stylesheet" href="../css/style.css" />
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
      <main class="administrateur">
        <h1>Espace administrateur</h1>

        <!-- Créer un compte employé -->
        <section class="creation-employe">
          <h2>Créer un compte employé</h2>

          <!-- formulaire -->
          <form method="post" class="compte card" autocomplete="on">
            <fieldset>
              <legend>Créer un compte employé</legend>
              <!-- nom -->
              <div class="compte">
                <label for="nom"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Nom"
                    role="img"
                    >badge</span
                  >Nom :</label
                >
                <input type="text" name="nom" id="nom" required />
              </div>
              <!-- prénom -->
              <div class="compte">
                <label for="prenom"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Prénom"
                    role="img"
                    >person</span
                  >Prénom :</label
                >
                <input type="text" name="prenom" id="prenom" required />
              </div>
              <!-- email -->
              <div class="compte">
                <label for="email"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Email"
                    role="img"
                    >email</span
                  >Email :</label
                >
                <input type="email" name="email" id="email" required />
              </div>
              <!-- password -->
              <div class="compte">
                <label for="password"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Password"
                    role="img"
                    >lock</span
                  >Mot de passe :</label
                >
                <input type="password" name="password" id="password" required />
              </div>
              <!-- password2 -->
              <div class="compte">
                <label for="password2"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Password2"
                    role="img"
                    >lock_reset</span
                  >Confirmez le mot de passe :</label
                >
                <input
                  type="password"
                  name="password2"
                  id="password2"
                  required
                />
              </div>

              <button class="button" type="button" aria-label="creer">
                Créer le compte
              </button>
            </fieldset>
          </form>
        </section>

        <!-- Statistiques -->
        <section class="statistiques card">
          <h2>Statistiques</h2>

          <!-- graphique : Covoiturages du jour -->
          <div class="graphique" id="graph-covoiturages">
            <!-- Le graphique viendra ici -->
          </div>

          <!-- graphique : Crédits gagnés -->
          <div class="graphique" id="graph-credits">
            <!-- Le graphique viendra ici -->
          </div>

          <!-- total -->
          <p><strong>Total de crédits générés : 21936 crédits</strong></p>
        </section>

        <!-- Liste des utilisateurs -->
        <section class="liste-utilisateur card">
          <h2>Liste des utilisateurs</h2>
          <table class="user">
            <!-- colonnes -->
            <thead>
              <tr>
                <th scope="col">
                  <span
                    class="material-symbols-outlined"
                    aria-label="Utilisateur"
                    role="img"
                    >person</span
                  >Utilisateur
                </th>
                <th scope="col">
                  <span
                    class="material-symbols-outlined"
                    aria-label="Rôle"
                    role="img"
                    >assignment_ind</span
                  >Rôle
                </th>
                <th scope="col">
                  <span
                    class="material-symbols-outlined"
                    aria-label="Action"
                    role="img"
                    >touch_app</span
                  >Action
                </th>
              </tr>
            </thead>
            <!-- lignes -->
            <tbody>
              <!-- ligne 1 -->
              <tr>
                <td>Elisa</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 2 -->
              <tr>
                <td>Romain</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 3 -->
              <tr>
                <td><strong>Thomas</strong></td>
                <td><strong>Employé</strong></td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 4 -->
              <tr>
                <td>Benjamin</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 5 -->
              <tr>
                <td>Léa</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 6 -->
              <tr>
                <td>Paul</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 7 -->
              <tr>
                <td>Jennifer</td>
                <td>Conducteur</td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
              <!-- ligne 8 -->
              <tr>
                <td><strong>Jean</strong></td>
                <td><strong>Employé</strong></td>
                <td>
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- Version mobile -->
          <div class="user-mobile">
            <!-- Utilisateur 1 : Elisa -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Elisa</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 2 : Romain -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Romain</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 3 : Thomas -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value"><strong>Thomas</strong></div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value"><strong>Employé</strong></div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 4 : Benjamin -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Benjamin</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 5 : Léa -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Léa</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 6 : Paul -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Paul</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 7 : Jennifer -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value">Jennifer</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value">Conducteur</div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>

            <!-- Utilisateur 8 : Jean -->
            <div class="user-card">
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">person</span>
                  <span class="label">Utilisateur</span>
                </div>
                <div class="value"><strong>Jean</strong></div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">assignment_ind</span>
                  <span class="label">Rôle</span>
                </div>
                <div class="value"><strong>Employé</strong></div>
              </div>
              <div class="user-info">
                <div class="user-info-header">
                  <span class="material-symbols-outlined">touch_app</span>
                  <span class="label">Action</span>
                </div>
                <div class="value">
                  <button type="button" aria-label="Suspendre">
                    Suspendre
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <!-- FOOTER -->
      <?php require 'footer.php'; ?>
    </body>
  </html>
</html>
