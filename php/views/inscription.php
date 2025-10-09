<?php
// inscription.php

// Générer le CSRF token si inexistant
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Inscription</title>
    <link rel="stylesheet" href="/css/style.css" />
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

    <!-- MAIN-->
    <main id="contenu" class="inscription">
      <h1>Je m’inscris et bénéficie de 20 crédits !</h1>

      <section class="formulaire-inscription">
        <h2>Formulaire d'inscription</h2>
        <!-- Formulaire -->
        <form
          method="post"
          id="form-inscription"
          class="form-inscription card"
          autocomplete="on"
        >
          <fieldset>
            <legend>Vos identifiants</legend>
            <!-- Prénom -->
            <div class="form-group card">
              <label for="prenom">
                <span
                  class="material-symbols-outlined"
                  aria-label="Prénom"
                  role="img"
                  >person</span
                >
                Entrez votre prénom
              </label>
              <input
                type="text"
                name="prenom"
                id="prenom"
                required
                autocomplete="given-name"
              />
              <div class="error-text" id="error-prenom"></div>
            </div>

            <!-- Nom -->
            <div class="form-group card">
              <label for="nom">
                <span
                  class="material-symbols-outlined"
                  aria-label="Nom"
                  role="img"
                  >badge</span
                >
                Entrez votre nom
              </label>
              <input
                type="text"
                name="nom"
                id="nom"
                required
                autocomplete="family-name"
              />
              <div class="error-text" id="error-nom"></div>
            </div>
            <!-- Pseudo -->
            <div class="form-group card">
              <label for="pseudo">
                <span
                  class="material-symbols-outlined"
                  aria-label="Pseudo"
                  role="img"
                  >assignment_ind</span
                >
                Entrez votre pseudo
              </label>
              <input
                type="text"
                name="pseudo"
                id="pseudo"
                required
              />
              <div class="error-text" id="error-pseudo"></div>
            </div>
            <!-- email -->
            <div class="form-group card">
              <label for="email"
                ><span
                  class="material-symbols-outlined"
                  aria-label="Email"
                  role="img"
                  >email</span
                >Entrez votre adresse mail</label
              >
              <input
                type="email"
                name="email"
                id="email"
                required
                autocomplete="email"
              />
              <div class="error-text" id="error-email"></div>
            </div>

            <!-- Mot de passe -->
            <div class="form-group card">
              <label for="password"
                ><span
                  class="material-symbols-outlined"
                  aria-label="Mot de passe"
                  role="img"
                  >lock</span
                >Entrez votre mot de passe</label
              >
              <input
                type="password"
                name="password"
                id="password"
                required
                minlength="8"
                autocomplete="new-password"
              />
              <div class="password-strength" id="password-strength"></div>
              <div class="error-text" id="error-password"></div>
            </div>

            <!-- Vérif mot de passe -->
            <div class="form-group card">
              <label for="password2"
                ><span
                  class="material-symbols-outlined"
                  aria-label="Confirmer mot de passe"
                  role="img"
                  >lock_reset</span
                >Confirmez votre mot de passe</label
              >
              <input
                type="password"
                name="password2"
                id="password2"
                required
                autocomplete="new-password"
              />
              <div class="error-text" id="error-password2"></div>
            </div>

            <!-- Accepter les CGU -->
            <div class="form-group">
              <input
                type="checkbox"
                id="cgu"
                name="cgu"
                aria-required="true"
                required
              />
              <label for="cgu"
                >J'accepte les&nbsp;<a href="../php/index.php?page=mentionslegales"
                  >CGU</a
                ></label
              >
              <div class="error-text" id="error-cgu"></div>
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES) ?>">

            <!-- Boutton s'inscrire -->
            <div class="button card">
              <button type="submit" id="btn-inscription">S'inscrire</button>
            </div>
            <a href="../php/index.php?page=connexion">Déjà un compte ?</a>
          </fieldset>
        </form>
        <aside class="info-rgpd">
          <p>
            <em
              >En vous inscrivant, vous acceptez nos mentions légales. Les
              informations collectées par EcoRide sont traitées dans le but de
              créer de votre compte, gérer votre réservation, l'utilisation et
              l'amélioration de nos services et pour assurer la sécurité de
              notre plateforme. Vous avez des droits concernant vos données
              personnelles et vous pouvez les exercer en contactant EcoRide par
              le biais de notre mail de contact.</em
            >
          </p>
        </aside>
      </section>
    </main>

    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
