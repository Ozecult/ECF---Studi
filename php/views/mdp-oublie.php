<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Un oubli ?</title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/ecoride/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main class="password">
      <h1>Je réinitialise mon mot de passe</h1>

      <form
        action="index.php?page=home"
        class="mdp-oublie card"
        method="post"
        autocomplete="on"
      >
        <div class="form-group card">
          <label for="email"
            ><span
              class="material-symbols-outlined"
              aria-label="Email"
              role="img"
              >email</span
            >Entrez votre adresse email :</label
          >
          <input type="email" name="email" id="email" required />
          <div id="error-email" class="error-text" style="display: none"></div>
        </div>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES) ?>">

        <div class="button card">
          <button type="submit">Réinitialiser mon mot de passe</button>
        </div>
      </form>
      <!-- CARDS TEXTE -->
      <section class="valeurs">
        <div class="card">
          <span class="material-icons" aria-label="Mains serrées" role="img"
            >handshake</span
          >
          <h3>Simple, humain et responsable</h3>
          <p>
            Une plateforme pensée pour des trajets entre personnes de confiance,
            engagées dans une démarche solidaire. L’interface va à l’essentiel :
            trouver un trajet, échanger facilement, voyager sereinement.
          </p>
        </div>
        <div class="card">
          <span class="material-icons" aria-label="Balance" role="img"
            >balance</span
          >
          <h3>Économique et sans surprises</h3>
          <p>
            Partage équitable des frais entre conducteurs et passagers. Pas de
            commissions opaques, pas de publicité invasive : tout est
            transparent pour favoriser la confiance et l’accessibilité.
          </p>
        </div>
        <div class="card">
          <span class="material-icons" aria-label="Ecologique" role="img"
            >volunteer_activism</span
          >
          <h3>Écologique par essence</h3>
          <p>
            EcoRide met en avant les véhicules électriques, mais accepte aussi
            les thermiques pour maximiser les trajets partagés. Chaque trajet
            évite des émissions grâce au covoiturage, et vous pouvez suivre
            votre impact carbone évité.
          </p>
        </div>
      </section>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
