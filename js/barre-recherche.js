// Attente du chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  const inputDepart = document.getElementById("depart");
  const inputDestination = document.getElementById("destination");
  const suggestionsDepart = document.getElementById("suggestions-depart");
  const suggestionsDestination = document.getElementById(
    "suggestions-destination"
  );
  const labelDepart = document.querySelector('label[for="depart"]');
  const labelDestination = document.querySelector('label[for="destination"]');

  // Fonction pour récuperer les villes avec l'API geo.api.gouv.fr
  // Fonction pour récupérer les adresses complètes avec l'API adresse
  async function rechercherAdresses(query) {
    if (query.length < 3) {
      return [];
    }

    try {
      const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(
        query
      )}&limit=10`;

      const response = await fetch(url);

      if (!response.ok) {
        throw new Error("Erreur lors de la récupération des données");
      }

      const data = await response.json();

      // Transformer les résultats pour correspondre au format attendu
      return data.features.map((feature) => ({
        nom: feature.properties.label, // Adresse complète
        codePostal: feature.properties.postcode,
        ville: feature.properties.city,
        latitude: feature.geometry.coordinates[1],
        longitude: feature.geometry.coordinates[0],
      }));
    } catch (error) {
      console.error("Erreur API:", error);
      return [];
    }
  }

  // FONCTION POUR CRÉER LA LISTE DE SUGGESTIONS
  function creerListeSuggestions(adresses, container, input) {
    container.innerHTML = "";

    if (adresses.length === 0) {
      container.style.display = "none";
      return;
    }

    const liste = document.createElement("ul");
    liste.className = "suggestions-list";

    adresses.forEach((adresse) => {
      const item = document.createElement("li");
      item.className = "suggestion-item";

      // Afficher l'adresse complète
      item.textContent = adresse.nom; // Ex: "10 Rue de Rivoli, 75001 Paris"

      item.addEventListener("click", function () {
        input.value = adresse.nom;
        // Stocker les coordonnées dans des champs cachés si besoin
        input.dataset.latitude = adresse.latitude;
        input.dataset.longitude = adresse.longitude;
        container.style.display = "none";
      });

      item.addEventListener("mouseenter", function () {
        const autresItems = liste.querySelectorAll(".suggestion-item");
        autresItems.forEach((autreItem) =>
          autreItem.classList.remove("active")
        );
        this.classList.add("active");
      });

      liste.appendChild(item);
    });

    container.appendChild(liste);
    container.style.display = "block";
  }

  // FONCTION POUR LES LABELS
  function gererAffichageLabel(input, label) {
    // texte du label
    const textLabel = label.querySelector(
      "span:not(.material-symbols-outlined)"
    );

    if (input.value.length > 0) {
      // masque du text label
      if (textLabel) {
        textLabel.style.display = "none";
      }
    } else {
      // On réaffiche le texte
      if (textLabel) {
        textLabel.style.display = "inline";
      }
    }
  }

  // FONCTION PRINCIPALE POUR INPUT
  function configurerAutocompletion(input, suggestionsContainer, label) {
    // VÉRIFIER QUE LES ÉLÉMENTS EXISTENT
    if (!input || !suggestionsContainer || !label) {
      console.warn("Éléments manquants pour autocomplétion");
      return;
    }

    // Variable pour stocker le timer
    let timeoutId;

    // ÉVÉNEMENT : QUAND ON TAPE DANS L'INPUT
    input.addEventListener("input", function () {
      const query = this.value.trim(); // récupère la valeur et enlève les espaces

      // affichage du label
      gererAffichageLabel(input, label);

      // annulation du timer précédent s'il existe pour éviter trop d'appels à l'API
      if (timeoutId) {
        clearTimeout(timeoutId);
      }

      // nouveau timer qui se déclenche après 300ms
      timeoutId = setTimeout(async function () {
        // si la recherche fait moins de 2 caractères, pas de suggestions
        if (query.length < 2) {
          suggestionsContainer.style.display = "none";
          return;
        }

        // On recherche les villes correspondantes
        const adresses = await rechercherAdresses(query);
        // Création de la liste de suggestions
        creerListeSuggestions(adresses, suggestionsContainer, input);
      }, 300);
    });

    // ÉVÉNEMENT : QUAND L'INPUT PERD LE FOCUS
    input.addEventListener("blur", function () {
      // Masque des suggestions après un petit délai
      setTimeout(() => {
        suggestionsContainer.style.display = "none";
      }, 200);

      // Affichage du label
      gererAffichageLabel(input, label);
    });

    // ÉVÉNEMENT : QUAND L'INPUT REÇOIT LE FOCUS
    input.addEventListener("focus", function () {
      gererAffichageLabel(input, label);

      // Si on a déjà saisi du texte, on relance la recherche
      if (this.value.length >= 2) {
        rechercherVilles(this.value).then((villes) => {
          creerListeSuggestions(villes, suggestionsContainer, input);
        });
      }
    });

    // GESTION DES TOUCHES DU CLAVIER
    input.addEventListener("keydown", function (e) {
      const suggestions =
        suggestionsContainer.querySelectorAll(".suggestion-item");
      const suggestionActive = suggestionsContainer.querySelector(
        ".suggestion-item.active"
      );

      // Si on n'a pas de suggestions, on ne fait rien
      if (suggestions.length === 0) return;

      switch (e.key) {
        case "ArrowDown":
          e.preventDefault();
          if (!suggestionActive) {
            suggestions[0].classList.add("active");
          } else {
            suggestionActive.classList.remove("active");
            const nextSuggestion = suggestionActive.nextElementSibling;
            if (nextSuggestion) {
              nextSuggestion.classList.add("active");
            } else {
              suggestions[0].classList.add("active");
            }
          }
          break;

        case "ArrowUp":
          e.preventDefault();
          if (!suggestionActive) {
            suggestions[suggestions.length - 1].classList.add("active");
          } else {
            suggestionActive.classList.remove("active");
            const prevSuggestion = suggestionActive.previousElementSibling;
            if (prevSuggestion) {
              prevSuggestion.classList.add("active");
            } else {
              suggestions[suggestions.length - 1].classList.add("active");
            }
          }
          break;

        case "Enter":
          if (suggestionActive) {
            e.preventDefault();
            suggestionActive.click();
          }
          break;

        case "Escape":
          suggestionsContainer.style.display = "none";
          break;
      }
    });
  }

  // INITIALISATION
  // Autocomplétion pour les deux champs
  configurerAutocompletion(inputDepart, suggestionsDepart, labelDepart);
  configurerAutocompletion(
    inputDestination,
    suggestionsDestination,
    labelDestination
  );

  if (inputDepart && suggestionsDepart && labelDepart) {
    configurerAutocompletion(inputDepart, suggestionsDepart, labelDepart);
  }

  if (inputDestination && suggestionsDestination && labelDestination) {
    configurerAutocompletion(
      inputDestination,
      suggestionsDestination,
      labelDestination
    );
  }

  // AFFICHAGE DES LABELS AU CHARGEMENT DE LA PAGE
  gererAffichageLabel(inputDepart, labelDepart);
  gererAffichageLabel(inputDestination, labelDestination);

  // CACHER LES SUGGESTIONS SI ON CLIQUE AILLEURS
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".choix")) {
      suggestionsDepart.style.display = "none";
      suggestionsDestination.style.display = "none";
    }
  });
});

// CALENDRIER
document.addEventListener("DOMContentLoaded", function () {
  const inputDate = document.getElementById("date");

  if (inputDate) {
    // Ajouter la classe pour le calendrier custom
    inputDate.classList.add("custom-date-input");

    // Créer le calendrier personnalisé
    createCustomCalendar();
  }
});

function createCustomCalendar() {
  const inputDate = document.getElementById("date");
  const choixDate = inputDate.parentElement;

  // Créer le conteneur du calendrier
  const calendar = document.createElement("div");
  calendar.className = "custom-calendar";
  calendar.innerHTML = `
    <div class="calendar-header">
      <button type="button" class="calendar-nav" id="prev-month">‹</button>
      <div class="calendar-month-year" id="month-year"></div>
      <button type="button" class="calendar-nav" id="next-month">›</button>
    </div>
    <div class="calendar-grid" id="calendar-grid">
      <div class="calendar-weekday">Lun</div>
      <div class="calendar-weekday">Mar</div>
      <div class="calendar-weekday">Mer</div>
      <div class="calendar-weekday">Jeu</div>
      <div class="calendar-weekday">Ven</div>
      <div class="calendar-weekday">Sam</div>
      <div class="calendar-weekday">Dim</div>
    </div>
  `;

  choixDate.appendChild(calendar);

  // Variables pour le calendrier
  let currentDate = new Date();
  let selectedDate = new Date();
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const maxDate = new Date();
  maxDate.setFullYear(today.getFullYear() + 1);
  maxDate.setHours(0, 0, 0, 0);

  // Initialiser avec la date d'aujourd'hui
  inputDate.value = formatDateForInput(today);

  // Éléments du calendrier
  const monthYearDisplay = calendar.querySelector("#month-year");
  const prevButton = calendar.querySelector("#prev-month");
  const nextButton = calendar.querySelector("#next-month");
  const calendarGrid = calendar.querySelector("#calendar-grid");

  // Fonctions utilitaires
  function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  }

  function formatDateForDisplay(date) {
    const months = [
      "Janvier",
      "Février",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Août",
      "Septembre",
      "Octobre",
      "Novembre",
      "Décembre",
    ];
    return `${months[date.getMonth()]} ${date.getFullYear()}`;
  }

  function isSameDay(date1, date2) {
    return (
      date1.getDate() === date2.getDate() &&
      date1.getMonth() === date2.getMonth() &&
      date1.getFullYear() === date2.getFullYear()
    );
  }

  function isDateDisabled(date) {
    return date < today || date > maxDate;
  }

  // Générer le calendrier
  function generateCalendar(year, month) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);

    // Commencer au lundi de la semaine
    const dayOfWeek = firstDay.getDay();
    const mondayOffset = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    startDate.setDate(startDate.getDate() - mondayOffset);

    monthYearDisplay.textContent = formatDateForDisplay(firstDay);

    // Effacer les jours précédents (sauf les en-têtes)
    const existingDays = calendarGrid.querySelectorAll(".calendar-day");
    existingDays.forEach((day) => day.remove());

    // Générer 42 jours (6 semaines)
    for (let i = 0; i < 42; i++) {
      const date = new Date(startDate);
      date.setDate(startDate.getDate() + i);

      const dayElement = document.createElement("div");
      dayElement.className = "calendar-day";
      dayElement.textContent = date.getDate();

      // Classes CSS selon le contexte
      if (date.getMonth() !== month) {
        dayElement.classList.add("other-month");
      }

      if (isSameDay(date, today)) {
        dayElement.classList.add("today");
      }

      if (isSameDay(date, selectedDate)) {
        dayElement.classList.add("selected");
      }

      if (isDateDisabled(date)) {
        dayElement.style.opacity = "0.3";
        dayElement.style.cursor = "not-allowed";
      } else {
        // Gestionnaire de clic
        dayElement.addEventListener("click", function () {
          if (!isDateDisabled(date)) {
            selectedDate = new Date(date);
            inputDate.value = formatDateForInput(selectedDate);

            // Mettre à jour l'affichage
            const allDays = calendarGrid.querySelectorAll(".calendar-day");
            allDays.forEach((d) => d.classList.remove("selected"));
            dayElement.classList.add("selected");

            // Fermer le calendrier
            calendar.classList.remove("show");
          }
        });
      }

      calendarGrid.appendChild(dayElement);
    }
  }

  // Gestionnaires d'événements pour la navigation
  prevButton.addEventListener("click", function () {
    currentDate.setMonth(currentDate.getMonth() - 1);
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  });

  nextButton.addEventListener("click", function () {
    currentDate.setMonth(currentDate.getMonth() + 1);
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  });

  // Ouvrir/fermer le calendrier
  inputDate.addEventListener("click", function (e) {
    e.preventDefault();

    // Synchroniser la date affichée avec la valeur de l'input
    if (inputDate.value) {
      const inputDateValue = new Date(inputDate.value + "T00:00:00");
      currentDate = new Date(inputDateValue);
      selectedDate = new Date(inputDateValue);
    }

    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    calendar.classList.add("show");
  });

  // Fermer le calendrier en cliquant ailleurs
  document.addEventListener("click", function (e) {
    if (!choixDate.contains(e.target)) {
      calendar.classList.remove("show");
    }
  });

  // Empêcher la fermeture en cliquant dans le calendrier
  calendar.addEventListener("click", function (e) {
    e.stopPropagation();
  });

  // Navigation au clavier
  inputDate.addEventListener("keydown", function (e) {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      inputDate.click();
    }
    if (e.key === "Escape") {
      calendar.classList.remove("show");
    }
  });

  // Initialiser le calendrier
  generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
}

// SELECT PASSAGERS PERSONNALISÉ
document.addEventListener("DOMContentLoaded", function () {
  const selectContainer = document.querySelector(
    '.choix[data-field="passagers"]'
  );

  if (!selectContainer) {
    return; // ⬅️ Pas de select sur cette page
  }

  const originalSelect = selectContainer.querySelector("select");

  if (originalSelect) {
    // Créer le bouton personnalisé
    const button = document.createElement("button");
    button.type = "button";
    button.className = "custom-select-button";
    button.innerHTML = `
      <span>1 passager</span>
      <span class="material-symbols-outlined">expand_more</span>
    `;

    // Créer le dropdown
    const dropdown = document.createElement("div");
    dropdown.className = "custom-select-dropdown";

    // Créer les options
    for (let i = 1; i <= 8; i++) {
      const option = document.createElement("div");
      option.className = "custom-select-option";
      option.textContent = `${i} passager${i > 1 ? "s" : ""}`;
      option.addEventListener("click", () => {
        button.querySelector("span").textContent = option.textContent;
        originalSelect.value = i;
        dropdown.classList.remove("show");
        button.classList.remove("open");
      });
      dropdown.appendChild(option);
    }

    // Ouvrir/fermer
    button.addEventListener("click", () => {
      const isOpen = dropdown.classList.contains("show");
      if (isOpen) {
        dropdown.classList.remove("show");
        button.classList.remove("open");
      } else {
        // Éviter le décalage visuel
        dropdown.style.display = "block";
        dropdown.classList.add("show");
        button.classList.add("open");
      }
    });

    // Fermer en cliquant ailleurs
    document.addEventListener("click", (e) => {
      if (!selectContainer.contains(e.target)) {
        dropdown.classList.remove("show");
        button.classList.remove("open");
      }
    });

    // Ajouter au DOM
    selectContainer.appendChild(button);
    selectContainer.appendChild(dropdown);
  }
});
