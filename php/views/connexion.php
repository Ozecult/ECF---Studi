<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Connexion</title>
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
    <main id="contenu" class="connexion">
      <h1>Je me connecte</h1>
      <section class="formulaire-connexion">
        <form
          method="post"
          id="form-connexion"
          class="form-connexion card"
          autocomplete="on"
        >
          <fieldset>
            <legend>Identifiants de connexion</legend>

            <!-- Email -->
            <div class="form-group card">
              <label for="email">
                <span
                  class="material-symbols-outlined"
                  aria-label="Email"
                  role="img"
                  >email</span
                >
                Email
              </label>
              <input
                type="email"
                name="email"
                id="email"
                required
                autocomplete="email"
                placeholder="votre@email.com"
              />
              <div class="error-text" id="error-email"></div>
            </div>

            <!-- Mot de passe -->
            <div class="form-group card">
              <label for="password">
                <span
                  class="material-symbols-outlined"
                  aria-label="Mot de passe"
                  role="img"
                  >lock</span
                >
                Mot de passe
              </label>
              <div class="password-toggle">
                <input
                  type="password"
                  name="password"
                  id="password"
                  required
                  autocomplete="current-password"
                  placeholder="Votre mot de passe"
                />
                <button
                  type="button"
                  class="password-toggle-btn"
                  id="toggle-password"
                  aria-label="Afficher/masquer le mot de passe"
                >
                  <span class="material-symbols-outlined">visibility</span>
                </button>
              </div>
              <div class="error-text" id="error-password"></div>
            </div>

            <!-- Se souvenir de moi -->
            <div class="form-group">
              <input type="checkbox" id="remember" name="remember" />
              <label for="remember">Se souvenir de moi</label>
            </div>

            <!-- Mot de passe oublié -->
            <div class="form-group">
              <p><a href="../php/index.php?page=mdp-oublie">Mot de passe oublié ?</a></p>
            </div>

            <!-- Bouton de connexion -->
            <div class="button card">
              <button type="submit" id="btn-connexion">Connexion</button>
            </div>

            <!-- Lien vers inscription -->
            <div
              class="form-group"
              style="text-align: center; margin-top: 15px"
            >
              <p>
                Pas encore de compte ?
                <a href="../php/index.php?page=inscription">Créer un compte</a>
              </p>
            </div>
          </fieldset>
        </form>
      </section>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
