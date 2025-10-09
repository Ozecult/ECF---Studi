// Gestion du bouton toggle des filtres
document.addEventListener("DOMContentLoaded", function () {
  const filtresToggle = document.querySelector(".filtres-toggle");
  const filtresContent = document.querySelector(".filtres-content");
  const resetButton = document.querySelector('.filtres button[type="reset"]');
  const formFiltres = document.querySelector(".form-filtres");
  const formRecherche = document.querySelector(".recherche form");
  const resultatsContainer = document.querySelector(".resultats-container");

  // RECHERCHE DE TRAJETS AVEC FILTRES

  if (formRecherche && resultatsContainer) {
    resultatsContainer.innerHTML =
      '<p style="text-align:center; padding:2rem; color:var(--noir-secondaire);">Utilisez la barre de recherche pour trouver un covoiturage</p>';

    // Fonction pour récupérer tous les paramètres de recherche et filtres
    function getSearchParams() {
      const params = {
        depart: document.getElementById("depart").value,
        destination: document.getElementById("destination").value,
        date: document.getElementById("date").value,
        passagers: document.getElementById("passagers").value,
      };

      // Ajouter les filtres si le formulaire existe
      if (formFiltres) {
        // Horaires
        if (document.getElementById("nuit")?.checked) params.horaire_nuit = "1";
        if (document.getElementById("matin")?.checked)
          params.horaire_matin = "1";
        if (document.getElementById("am")?.checked) params.horaire_am = "1";
        if (document.getElementById("soir")?.checked) params.horaire_soir = "1";

        // Motorisation
        if (document.getElementById("elec")?.checked) params.electrique = "1";
        if (document.getElementById("therm")?.checked) params.thermique = "1";

        // Prix max
        const prixMax = document.getElementById("prix")?.value;
        if (prixMax && prixMax > 0) params.prix_max = prixMax;

        // Durée max
        const dureeMax = document.getElementById("duree")?.value;
        if (dureeMax && dureeMax > 0) params.duree_max = dureeMax;
      }

      return params;
    }

    // Fonction de recherche
    async function effectuerRecherche() {
      const params = getSearchParams();

      if (!params.depart || !params.destination || !params.date) {
        alert("Veuillez remplir tous les champs de recherche");
        return;
      }

      resultatsContainer.innerHTML =
        '<p style="text-align:center; padding:2rem;">Recherche en cours...</p>';

      try {
        const urlParams = new URLSearchParams(params);
        const response = await fetch(
          `/php/api/api-router.php?action=search-trajets&${urlParams}`
        );
        const data = await response.json();

        if (data.success && data.trajets.length > 0) {
          afficherResultats(data.trajets);
        } else {
          resultatsContainer.innerHTML =
            '<p style="text-align:center; padding:2rem;">Aucun trajet trouvé pour cette recherche.</p>';
        }
      } catch (error) {
        console.error("Erreur:", error);
        resultatsContainer.innerHTML =
          '<p style="text-align:center; padding:2rem; color:red;">Erreur lors de la recherche</p>';
      }
    }

    // Soumettre la recherche principale
    formRecherche.addEventListener("submit", async function (e) {
      e.preventDefault();
      effectuerRecherche();
    });

    // Soumettre avec les filtres
    if (formFiltres) {
      formFiltres.addEventListener("submit", function (e) {
        e.preventDefault();
        effectuerRecherche();
      });
    }
  }

  // API CALCUL TEMPS DE TRAJET
  async function calculerHeureArrivee(trajet, elementHeureArrivee) {
    const API_KEY =
      "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImNlNzQwOWM0YWU2NjQyMTQ4OTYwYzliODc1OWQxZDRkIiwiaCI6Im11cm11cjY0In0=";

    try {
      // 1. Géocoder les adresses
      const [geocodeDepart, geocodeArrivee] = await Promise.all([
        fetch(
          `https://api.openrouteservice.org/geocode/search?api_key=${API_KEY}&text=${encodeURIComponent(
            trajet.adresse_depart
          )}`
        ),
        fetch(
          `https://api.openrouteservice.org/geocode/search?api_key=${API_KEY}&text=${encodeURIComponent(
            trajet.adresse_arrivee
          )}`
        ),
      ]);

      const [dataDepart, dataArrivee] = await Promise.all([
        geocodeDepart.json(),
        geocodeArrivee.json(),
      ]);

      const coordDepart = dataDepart.features[0].geometry.coordinates;
      const coordArrivee = dataArrivee.features[0].geometry.coordinates;

      // 2. Calculer l'itinéraire
      const route = await fetch(
        `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${API_KEY}`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            coordinates: [coordDepart, coordArrivee],
          }),
        }
      );

      const routeData = await route.json();
      const dureeSecondes = routeData.routes[0].summary.duration;
      const dureeMinutes = Math.round(dureeSecondes / 60);

      // 3. Calculer l'heure d'arrivée
      const dateDepart = new Date(trajet.date_depart);
      const arriveeDate = new Date(dateDepart.getTime() + dureeMinutes * 60000);
      const heureArrivee = arriveeDate.toLocaleTimeString("fr-FR", {
        hour: "2-digit",
        minute: "2-digit",
      });

      elementHeureArrivee.textContent = heureArrivee;
    } catch (error) {
      console.error("Erreur calcul itinéraire:", error);
      elementHeureArrivee.textContent = "~1h30"; // Fallback
    }
  }

  function afficherResultats(trajets) {
    resultatsContainer.innerHTML = trajets
      .map((trajet) => {
        const dateDepart = new Date(trajet.date_depart);
        const heureDepart = dateDepart.toLocaleTimeString("fr-FR", {
          hour: "2-digit",
          minute: "2-digit",
        });

        const noteArrondie = Math.round(trajet.note_moyenne);
        const etoiles = Array(5)
          .fill(0)
          .map(
            (_, i) =>
              `<span class="material-symbols-outlined ${
                i < noteArrondie ? "star-filled" : "star-empty"
              }">star</span>`
          )
          .join("");

        const isElectrique = trajet.energie === "electrique";

        return `
    <article class="card-covoit card" data-trajet-id="${trajet.id}">
      <div class="card-header">
        <div class="container-info">
          <div class="conducteur-info">
            <h3>${trajet.conducteur_prenom}</h3>
            <div class="etoiles-card">${etoiles}</div>
            <a href="../php/index.php?page=details&trajet=${
              trajet.id
            }" class="details">Détails</a>
          </div>
          ${
            trajet.photo_profil
              ? `<img src="${trajet.photo_profil}" alt="Photo" class="photo-profil" width="80" height="80">`
              : '<span class="material-symbols-outlined" style="font-size:80px;">account_circle</span>'
          }
        </div>
        <div class="card-main">
          <div class="date-trajet">
            <span>${dateDepart.toLocaleDateString("fr-FR", {
              weekday: "long",
              day: "numeric",
              month: "long",
              year: "numeric",
            })}</span>
          </div>
          <div class="time-bar">
            <span class="heure-depart">${heureDepart}</span>
            <div class="barre-verte"></div>
            <span class="heure-arrivee" data-trajet-id="${
              trajet.id
            }">Calcul...</span>
          </div>
          <div class="trajet-infos">
            <div><strong>Départ :</strong> ${trajet.adresse_depart}</div>
            <div><strong>Arrivée :</strong> ${trajet.adresse_arrivee}</div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="card-footer-left">
          <div class="footer-item">
            <span class="material-icons">${
              isElectrique ? "electric_car" : "local_gas_station"
            }</span>
            <span>${
              isElectrique ? "Véhicule électrique !" : "Véhicule thermique"
            }</span>
          </div>
          <div class="footer-separator"></div>
          <div class="footer-item">
            <span class="material-icons">group</span>
            <span>Place(s) restante(s) : ${trajet.places_disponibles}</span>
          </div>
        </div>
        <div class="prix-final">${trajet.prix_par_passager} crédits</div>
      </div>
    </article>
  `;
      })
      .join("");

    // Calculer les heures d'arrivée en parallèle APRÈS l'affichage
    trajets.forEach((trajet) => {
      const elementHeureArrivee = document.querySelector(
        `.heure-arrivee[data-trajet-id="${trajet.id}"]`
      );
      if (elementHeureArrivee) {
        calculerHeureArrivee(trajet, elementHeureArrivee);
      }
    });
  }

  // Toggle des filtres en responsive
  if (filtresToggle && filtresContent) {
    filtresToggle.addEventListener("click", function () {
      filtresContent.classList.toggle("hidden");

      // Changer le texte du bouton selon l'état
      if (filtresContent.classList.contains("hidden")) {
        filtresToggle.textContent = "☰ Filtres";
      } else {
        filtresToggle.textContent = "✕ Masquer les filtres";
      }
    });

    // Masquer les filtres par défaut sur mobile
    function checkScreenSize() {
      if (window.innerWidth <= 1024) {
        filtresContent.classList.add("hidden");
        filtresToggle.textContent = "☰ Filtres";
      } else {
        filtresContent.classList.remove("hidden");
      }
    }

    // Vérifier la taille d'écran au chargement et au redimensionnement
    checkScreenSize();
    window.addEventListener("resize", checkScreenSize);
  }

  // Gestion du bouton "Effacer les filtres"
  if (resetButton && formFiltres) {
    resetButton.addEventListener("click", function (e) {
      e.preventDefault();

      // Réinitialiser toutes les checkboxes
      const checkboxes = formFiltres.querySelectorAll('input[type="checkbox"]');
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false;
      });

      // Réinitialiser les range sliders
      const rangeInputs = formFiltres.querySelectorAll('input[type="range"]');
      rangeInputs.forEach((range) => {
        range.value = range.min || 0;
        range.dispatchEvent(new Event("input"));
      });

      // Réinitialiser les étoiles
      reinitialiserEtoiles();
    });
  }

  // Gestion des range sliders
  const rangeInputs = document.querySelectorAll(
    '.time input[type="range"], .price input[type="range"]'
  );
  rangeInputs.forEach((range) => {
    const container = range.closest(".time, .price");
    const valueDisplay = container.querySelector("span");

    if (valueDisplay) {
      function updateDisplay() {
        let value = range.value;

        if (container.classList.contains("time")) {
          const hours = Math.floor(value / 60);
          const minutes = value % 60;
          valueDisplay.textContent =
            hours.toString().padStart(2, "0") +
            ":" +
            minutes.toString().padStart(2, "0");
        } else if (container.classList.contains("price")) {
          if (value == 0) {
            valueDisplay.textContent = "0";
          } else {
            valueDisplay.textContent = value;
          }
        }
      }

      updateDisplay();
      range.addEventListener("input", updateDisplay);
    }
  });

  // GESTION DES ÉTOILES
  const etoilesFiltre = document.querySelector(".filtres .etoiles");
  if (etoilesFiltre) {
    const etoiles = etoilesFiltre.querySelectorAll(
      ".material-symbols-outlined"
    );
    let noteSelectionnee = 0;

    // Fonction pour réinitialiser les étoiles
    window.reinitialiserEtoiles = function () {
      noteSelectionnee = 0;
      etoiles.forEach((etoile) => {
        // Utiliser "star" comme icône de base
        etoile.textContent = "star";
        // Étoile vide avec FILL 0
        etoile.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
        etoile.style.setProperty("color", "var(--vert-fonce)", "important");
      });
    };

    // Fonction pour définir le nombre d'étoiles pleines
    function definirEtoiles(nombre) {
      etoiles.forEach((etoile, index) => {
        etoile.textContent = "star";
        if (index < nombre) {
          // Étoile pleine
          etoile.style.fontVariationSettings = "'FILL' 1, 'wght' 400";
        } else {
          // Étoile vide
          etoile.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
        }
        etoile.style.setProperty("color", "var(--vert-fonce)", "important");
      });
    }

    // Initialiser les étoiles
    reinitialiserEtoiles();

    // Gestion des clics
    etoiles.forEach((etoile, index) => {
      etoile.addEventListener("click", function (e) {
        e.preventDefault();

        if (noteSelectionnee === index + 1) {
          // Désélectionner si on clique sur la même étoile
          noteSelectionnee = 0;
        } else {
          // Nouvelle sélection
          noteSelectionnee = index + 1;
        }

        // Mise à jour de l'affichage
        definirEtoiles(noteSelectionnee);
      });

      // Effet de survol
      etoile.addEventListener("mouseenter", function () {
        // Prévisualiser la sélection en vert clair
        etoiles.forEach((e, i) => {
          e.textContent = "star";
          if (i <= index) {
            e.style.fontVariationSettings = "'FILL' 1, 'wght' 400";
            e.style.setProperty("color", "var(--vert-clair)", "important");
          } else {
            e.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
            e.style.setProperty("color", "var(--vert-fonce)", "important");
          }
        });
      });

      etoile.addEventListener("mouseleave", function () {
        // Restaurer l'état réel
        definirEtoiles(noteSelectionnee);
      });
    });
  }
});
