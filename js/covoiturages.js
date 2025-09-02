// Gestion du bouton toggle des filtres
document.addEventListener("DOMContentLoaded", function () {
  const filtresToggle = document.querySelector(".filtres-toggle");
  const filtresContent = document.querySelector(".filtres-content");
  const resetButton = document.querySelector('.filtres button[type="reset"]');
  const formFiltres = document.querySelector(".form-filtres");

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

      console.log("Filtres réinitialisés");
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
  const etoilesContainer = document.querySelector(".etoiles");
  if (etoilesContainer) {
    const etoiles = etoilesContainer.querySelectorAll(
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
      console.log("Étoiles réinitialisées");
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
        console.log("Clic sur étoile", index + 1);

        if (noteSelectionnee === index + 1) {
          // Désélectionner si on clique sur la même étoile
          noteSelectionnee = 0;
          console.log("Désélection");
        } else {
          // Nouvelle sélection
          noteSelectionnee = index + 1;
          console.log("Sélection de", noteSelectionnee, "étoiles");
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
