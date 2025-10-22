<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <meta name="keywords" content="covoiturage, covoiturage écologique, trajet partagé, véhicule électrique, mobilité durable, transport responsable, EcoRide">
    <meta name="author" content="EcoRide">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://ecoride-ecologique.alwaysdata.net/php/index.php?page=home">

    <!-- Open Graph pour les réseaux sociaux -->
    <meta property="og:title" content="EcoRide - Voyagez ensemble, préservez la planète">
    <meta property="og:description" content="Plateforme de covoiturage écologique pour des trajets responsables et économiques">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ecoride-ecologique.alwaysdata.net/php/index.php?page=home">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="EcoRide - Voyagez ensemble, préservez la planète">
    <meta name="twitter:description" content="Plateforme de covoiturage écologique pour des trajets responsables et économiques">
    <meta name="twitter:image" content="https://ecoride-ecologique.alwaysdata.net/img/Accueil/banniere.png">

    <title>EcoRide - Voyagez ensemble, préservez la planète</title>
    <link rel="stylesheet" href="/css/style.css" />
    <!-- Preconnect pour charger plus vite -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <script type="module" src="/js/script.js" defer></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main id="contenu" class="accueil">
      <!-- H1 - BANNIERE -->
      <section class="banniere">
        <img src="/img/Accueil/banniere.png" alt="Voiture écologique verte" />
        <div class="banniere-texte">
          <h1 class="animation-texte">
            Voyagez ensemble,<br />Préservez la planète.
          </h1>
        </div>
      </section>
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

      <!-- FLECHE VERS LE BAS -->
      <div class="scroll-container">
        <a
          href="#ecoride"
          class="scroll-bas"
          aria-label="Aller à la section Ecoride"
          title="Aller à la section Ecoride"
          ><span class="material-symbols-outlined">output_circle</span></a
        >
      </div>
      <!-- 3 IMAGES AVEC TEXTES -->
      <section class="galerie" id="galerie">
        <h3>Le covoiturage, bien plus qu'un simple trajet</h3>
        <div class="card">
          <div class="carousel-container">
            <!-- Radio buttons cachés pour la navigation -->
            <input
              type="radio"
              name="carousel"
              id="slide1"
              class="carousel-radio"
              checked
            />
            <input
              type="radio"
              name="carousel"
              id="slide2"
              class="carousel-radio"
            />
            <input
              type="radio"
              name="carousel"
              id="slide3"
              class="carousel-radio"
            />

            <!-- Flèches de navigation simplifiées -->
            <label for="slide1" class="carousel-nav prev">‹</label>
            <label for="slide2" class="carousel-nav next">›</label>
            <label for="slide2" class="carousel-nav prev">‹</label>
            <label for="slide3" class="carousel-nav next">›</label>
            <label for="slide2" class="carousel-nav prev">‹</label>

            <div class="carousel-track">
              <figure class="carousel-slide">
                <img
                  src="/img/Accueil/img_liberte.jpg"
                  alt="Tête sur une fenêtre ouverte en montagne"
                  loading="lazy"
                />
                <figcaption>Respirez la liberté, partagez la route.</figcaption>
              </figure>
              <figure class="carousel-slide">
                <img
                  src="/img/Accueil/img_group.jpg"
                  alt="Groupe dans un véhicule"
                  loading="lazy"
                />
                <figcaption>
                  Un trajet, des rencontres, moins d'impact.
                </figcaption>
              </figure>
              <figure class="carousel-slide">
                <img
                  src="/img/Accueil/img_echange.jpg"
                  alt="Échange entre deux adultes sans un véhicule"
                  loading="lazy"
                />
                <figcaption>Connectés, complices, responsables.</figcaption>
              </figure>
            </div>

            <!-- Indicateurs cliquables -->
            <div class="carousel-indicators">
              <label for="slide1" class="indicator-label"></label>
              <label for="slide2" class="indicator-label"></label>
              <label for="slide3" class="indicator-label"></label>
            </div>
          </div>
        </div>
      </section>

      <!-- TEXTE DE PRESENTATION DE LA START-UP -->
      <section class="presentation card" id="ecoride">
        <h2>Découvrez EcoRide</h2>
        <p>
          Chez EcoRide, nous croyons qu’il est possible de se déplacer
          autrement, de façon plus responsable, plus humaine et plus
          économique.<br />
          Notre mission est simple : réduire l’impact environnemental des
          trajets en voiture en favorisant le covoiturage écologique, en
          particulier grâce à l’usage de véhicules électriques.<br />
          En connectant les voyageurs soucieux de l’environnement, nous
          construisons chaque jour une communauté engagée pour une mobilité
          durable.<br />
          Que vous soyez conducteur ou passager, EcoRide vous accompagne pour
          voyager ensemble, tout en préservant la planète.
        </p>
        <img
          src="/img/Accueil/img_startup.jpg"
          alt="L'équipe EcoRide en image"
          loading="lazy"
        />
      </section>
    </main>

    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
