<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Rechercher</title>
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
    <main id="contenu" class="search">
      <!-- BARRE DE RECHERCHE -->
      <?php require __DIR__ . '/barrerecherche.php'; ?>

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
