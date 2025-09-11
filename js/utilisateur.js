// Attente du chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  // ===========================================
  // DONNÉES UTILISATEUR (simulation localStorage)
  // ===========================================

  const userData = {
    prenom: "Paul",
    credits: 48,
    role: null,
    photo: null,
    vehicules: [],
    preferences: [],
    voyages: [],
  };

  // ===========================================
  // INITIALISATION DE LA PAGE
  // ===========================================

  function initialiserPage() {
    afficherInformationsUtilisateur();
    configurerPhotoProfil();
    configurerChoixRole();
    configurerFormulaireVehicule();
    configurerPreferences();
    configurerFormulaireVoyage();
    configurerGestionTrajets();
    configurerPlaceholders();
    initialiserEtoilesTrajetsPasses();
  }

  // ===========================================
  // AFFICHAGE DES INFORMATIONS UTILISATEUR
  // ===========================================

  function afficherInformationsUtilisateur() {
    const welcomeText = document.querySelector(".profil-credits h2");
    const creditsText = document.querySelector(".profil-credits p strong");

    if (welcomeText) {
      welcomeText.textContent = `Bienvenue ${userData.prenom}`;
    }

    if (creditsText) {
      creditsText.textContent = `Nombre de crédits : ${userData.credits}`;
    }
  }

  // ===========================================
  // GESTION PHOTO DE PROFIL
  // ===========================================

  function configurerPhotoProfil() {
    const profilPhoto = document.querySelector(".profil-photo");
    if (!profilPhoto) return;

    const iconePhoto = profilPhoto.querySelector(".material-symbols-outlined");

    profilPhoto.addEventListener("click", function () {
      const inputFile = document.createElement("input");
      inputFile.type = "file";
      inputFile.accept = "image/*";
      inputFile.style.display = "none";

      inputFile.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            let img = profilPhoto.querySelector("img");
            if (!img) {
              img = document.createElement("img");
              img.style.cssText = `
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid var(--vert-clair);
              `;
              profilPhoto.replaceChild(img, iconePhoto);
            }
            img.src = e.target.result;
            userData.photo = e.target.result;

            afficherNotification("Photo de profil mise à jour !", "success");
          };
          reader.readAsDataURL(file);
        }
      });

      document.body.appendChild(inputFile);
      inputFile.click();
      document.body.removeChild(inputFile);
    });
  }

  // ===========================================
  // CHOIX DU RÔLE
  // ===========================================

  function configurerChoixRole() {
    const boutons = document.querySelectorAll(".role-choice .button button");
    const sectionChauffeur = document.querySelector(".informations-chauffeur");
    const sectionVoyage = document.querySelector(".voyage");

    if (!boutons.length) return;

    boutons.forEach((bouton) => {
      bouton.addEventListener("click", function () {
        boutons.forEach((b) => b.classList.remove("selected"));
        this.classList.add("selected");

        const role = this.getAttribute("aria-label");
        userData.role = role;

        if (role === "chauffeur" || role === "duo") {
          if (sectionChauffeur) sectionChauffeur.style.display = "grid";
          if (sectionVoyage) sectionVoyage.style.display = "block";
        } else {
          if (sectionChauffeur) sectionChauffeur.style.display = "none";
          if (sectionVoyage) sectionVoyage.style.display = "none";
        }

        afficherNotification(`Rôle "${role}" sélectionné`, "success");
      });
    });
  }

  // ===========================================
  // FORMULAIRE VÉHICULE
  // ===========================================

  function configurerFormulaireVehicule() {
    const form = document.querySelector(".vehicule form");
    const btnAjouterVehicule = document.querySelector(".btn-ajouter-vehicule");

    if (!form) return;

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const vehicule = {
        id: Date.now(),
        marque: document.getElementById("marque")?.value || "",
        modele: document.getElementById("modele")?.value || "",
        couleur: document.getElementById("couleur")?.value || "",
        energie: document.getElementById("energie")?.value || "",
        immatriculation:
          document.getElementById("immatriculation")?.value || "",
        circulation: document.getElementById("circulation")?.value || "",
        places: document.getElementById("places")?.value || "",
      };

      if (
        !vehicule.marque ||
        !vehicule.modele ||
        !vehicule.couleur ||
        !vehicule.energie ||
        !vehicule.immatriculation ||
        !vehicule.circulation ||
        !vehicule.places
      ) {
        afficherNotification("Veuillez remplir tous les champs", "error");
        return;
      }

      userData.vehicules.push(vehicule);
      mettreAJourSelectVehicules();
      form.reset();
      afficherNotification("Véhicule ajouté avec succès !", "success");
    });

    if (btnAjouterVehicule) {
      btnAjouterVehicule.addEventListener("click", function () {
        form.reset();
        form.scrollIntoView({ behavior: "smooth" });
      });
    }
  }

  function mettreAJourSelectVehicules() {
    const select = document.querySelector("#choix-vehicule");
    if (!select) return;

    select.innerHTML =
      '<option value="">-- Sélectionnez un véhicule --</option>';

    userData.vehicules.forEach((vehicule) => {
      const option = document.createElement("option");
      option.value = vehicule.id;
      option.textContent = `${vehicule.marque} ${vehicule.modele} (${vehicule.immatriculation})`;
      select.appendChild(option);
    });
  }

  // ===========================================
  // PRÉFÉRENCES
  // ===========================================

  function configurerPreferences() {
    const preferences = document.querySelectorAll(
      '.preferences input[type="checkbox"]'
    );
    const btnAjouterPreference = document.querySelector(
      ".btn-ajouter-preference"
    );

    preferences.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const preference = this.name;

        if (this.checked) {
          if (!userData.preferences.includes(preference)) {
            userData.preferences.push(preference);
          }
        } else {
          const index = userData.preferences.indexOf(preference);
          if (index > -1) {
            userData.preferences.splice(index, 1);
          }
        }

        afficherNotification(
          `Préférence "${preference}" ${
            this.checked ? "ajoutée" : "supprimée"
          }`,
          "success"
        );
      });
    });

    if (btnAjouterPreference) {
      btnAjouterPreference.addEventListener("click", function () {
        const preference = prompt("Entrez votre préférence personnalisée :");
        if (preference && preference.trim()) {
          ajouterPreferencePersonnalisee(preference.trim());
        }
      });
    }
  }

  function ajouterPreferencePersonnalisee(preference) {
    const container = document.querySelector(".preferences-all");
    const btnAjouterPreference = document.querySelector(
      ".btn-ajouter-preference"
    );

    if (!container || !btnAjouterPreference) return;

    const newPreference = document.createElement("div");
    newPreference.className = "preferences";
    newPreference.innerHTML = `
      <input type="checkbox" id="${preference}" name="${preference}" checked>
      <label for="${preference}">
        <span class="material-symbols-outlined" aria-label="${preference}" role="img">add_circle</span>
        ${preference}
      </label>
    `;

    container.insertBefore(newPreference, btnAjouterPreference);

    const checkbox = newPreference.querySelector("input");
    checkbox.addEventListener("change", function () {
      if (this.checked) {
        if (!userData.preferences.includes(preference)) {
          userData.preferences.push(preference);
        }
      } else {
        const index = userData.preferences.indexOf(preference);
        if (index > -1) {
          userData.preferences.splice(index, 1);
        }
      }
    });

    userData.preferences.push(preference);
    afficherNotification(`Préférence "${preference}" ajoutée !`, "success");
  }

  // ===========================================
  // AUTOCOMPLÉTION AVEC API GÉOCODE
  // ===========================================

  async function rechercherVilles(query) {
    if (query.length < 2) return [];

    try {
      const url = `https://geo.api.gouv.fr/communes?nom=${encodeURIComponent(
        query
      )}&fields=nom,code,codesPostaux,population&boost=population&limit=10`;
      const response = await fetch(url);

      if (!response.ok) {
        throw new Error("Erreur lors de la récupération des données");
      }

      const villes = await response.json();
      return villes;
    } catch (error) {
      console.error("Erreur API:", error);
      return [];
    }
  }

  function creerListeSuggestions(villes, container, input) {
    console.log("creerListeSuggestions appelée avec:", villes, container);

    container.innerHTML = "";

    if (villes.length === 0) {
      container.style.display = "none";
      console.log("Aucune ville trouvée");
      return;
    }

    const liste = document.createElement("ul");
    liste.className = "suggestions-list";
    liste.style.cssText = `
      position: relative;
      margin: 0;
      padding: 0;
      list-style: none;
      background-color: var(--vert-fond-de-page);
      border: 3px solid var(--vert-clair);
      border-radius: 12px;
      overflow: hidden;
      max-height: 200px;
      overflow-y: auto;
    `;

    villes.forEach((ville, index) => {
      console.log(`Création suggestion ${index}:`, ville);

      const item = document.createElement("li");
      item.className = "suggestion-item";
      item.style.cssText = `
        padding: 0.75rem 1rem;
        cursor: pointer;
        color: var(--noir-primaire);
        transition: all 0.2s ease;
      `;

      const codePostal =
        ville.codesPostaux && ville.codesPostaux[0]
          ? ville.codesPostaux[0]
          : "";
      const texteAffiche = codePostal
        ? `${ville.nom} (${codePostal})`
        : ville.nom;
      item.textContent = texteAffiche;

      item.addEventListener("click", function () {
        console.log("Clic sur suggestion:", texteAffiche);
        input.value = texteAffiche;
        container.style.display = "none";
      });

      item.addEventListener("mouseenter", function () {
        const autresItems = liste.querySelectorAll(".suggestion-item");
        autresItems.forEach((autreItem) =>
          autreItem.classList.remove("active")
        );
        this.classList.add("active");
        this.style.cssText += `
          color: var(--vert-fonce);
          font-weight: 500;
          transform: translateX(2px);
        `;
      });

      item.addEventListener("mouseleave", function () {
        this.classList.remove("active");
        this.style.cssText = `
          padding: 0.75rem 1rem;
          cursor: pointer;
          color: var(--noir-primaire);
          transition: all 0.2s ease;
        `;
      });

      liste.appendChild(item);
    });

    container.appendChild(liste);
    container.style.display = "block";
    console.log("Liste créée et affichée, container:", container);
    console.log("Container display:", getComputedStyle(container).display);
    console.log(
      "Container visibility:",
      getComputedStyle(container).visibility
    );
  }

  function configurerAutocompletion(input, container) {
    if (!input || !container) return;

    let timeoutId;

    input.addEventListener("input", function () {
      const query = this.value.trim();

      if (timeoutId) {
        clearTimeout(timeoutId);
      }

      timeoutId = setTimeout(async function () {
        if (query.length < 2) {
          container.style.display = "none";
          return;
        }

        const villes = await rechercherVilles(query);
        creerListeSuggestions(villes, container, input);
      }, 300);
    });

    input.addEventListener("blur", function () {
      setTimeout(() => {
        container.style.display = "none";
      }, 200);
    });

    input.addEventListener("focus", function () {
      if (this.value.length >= 2) {
        rechercherVilles(this.value).then((villes) => {
          creerListeSuggestions(villes, container, input);
        });
      }
    });
  }

  // ===========================================
  // FORMULAIRE VOYAGE
  // ===========================================

  function configurerFormulaireVoyage() {
    const inputDepart = document.getElementById("depart");
    const inputArrivee = document.getElementById("arrivee");
    const btnAjouterVoyage = document.querySelector(".btn-ajouter-voyage");

    // Créer les conteneurs de suggestions
    if (inputDepart) {
      const voyageContainer = inputDepart.closest(".voyage");
      let suggestionsDepart = document.getElementById(
        "suggestions-depart-voyage"
      );

      if (!suggestionsDepart) {
        suggestionsDepart = document.createElement("div");
        suggestionsDepart.id = "suggestions-depart-voyage";
        suggestionsDepart.className = "suggestions";
        suggestionsDepart.style.display = "none";
        voyageContainer.appendChild(suggestionsDepart);
      }

      configurerAutocompletion(inputDepart, suggestionsDepart);
    }

    if (inputArrivee) {
      const voyageContainer = inputArrivee.closest(".voyage");
      let suggestionsArrivee = document.getElementById(
        "suggestions-arrivee-voyage"
      );

      if (!suggestionsArrivee) {
        suggestionsArrivee = document.createElement("div");
        suggestionsArrivee.id = "suggestions-arrivee-voyage";
        suggestionsArrivee.className = "suggestions";
        suggestionsArrivee.style.display = "none";
        voyageContainer.appendChild(suggestionsArrivee);
      }

      configurerAutocompletion(inputArrivee, suggestionsArrivee);
    }

    if (btnAjouterVoyage) {
      btnAjouterVoyage.addEventListener("click", function (e) {
        e.preventDefault();
        ajouterVoyage();
      });
    }
  }

  function configurerPlaceholders() {
    document.getElementById("depart").placeholder = "Départ";
    document.getElementById("arrivee").placeholder = "Destination";
    document.getElementById("fixer-prix").placeholder = "-- crédits";
  }

  function ajouterVoyage() {
    const depart = document.getElementById("depart")?.value || "";
    const arrivee = document.getElementById("arrivee")?.value || "";
    const prix = document.getElementById("fixer-prix")?.value || "";
    const vehiculeId = document.getElementById("choix-vehicule")?.value || "";

    if (!depart || !arrivee || !prix || !vehiculeId) {
      afficherNotification("Veuillez remplir tous les champs", "error");
      return;
    }

    if (userData.credits < 2) {
      afficherNotification("Crédits insuffisants (2 crédits requis)", "error");
      return;
    }

    const vehicule = userData.vehicules.find((v) => v.id == vehiculeId);

    const voyage = {
      id: Date.now(),
      depart: depart,
      arrivee: arrivee,
      prix: parseInt(prix),
      vehicule: vehicule,
      statut: "En attente",
      date: new Date().toISOString().split("T")[0],
      heure: "10h / 13h",
    };

    userData.voyages.push(voyage);
    userData.credits -= 2;

    // Réinitialiser le formulaire
    const formInputs = document.querySelectorAll(
      ".add-voyage input, .add-voyage select"
    );
    formInputs.forEach((input) => (input.value = ""));

    afficherInformationsUtilisateur();
    mettreAJourTableauTrajets();
    afficherNotification(
      "Voyage ajouté avec succès ! (2 crédits débités)",
      "success"
    );
  }

  // ===========================================
  // GESTION DES TRAJETS
  // ===========================================

  function configurerGestionTrajets() {
    mettreAJourTableauTrajets();

    document.addEventListener("click", function (e) {
      if (e.target.matches('button[aria-label*="Annuler"]')) {
        const row = e.target.closest("tr");
        if (row && row.dataset.voyageId) {
          annulerTrajet(row.dataset.voyageId);
        }
      }

      if (e.target.matches('button[aria-label*="Démarrer"]')) {
        const row = e.target.closest("tr");
        if (row && row.dataset.voyageId) {
          demarrerTrajet(row.dataset.voyageId);
        }
      }
    });
  }

  function mettreAJourTableauTrajets() {
    const tableauAVenir = document.querySelector(".a-venir tbody");
    if (!tableauAVenir) return;

    // Supprimer uniquement les trajets ajoutés par l'utilisateur (ceux avec data-voyage-id)
    const userRows = tableauAVenir.querySelectorAll("tr[data-voyage-id]");
    userRows.forEach((row) => row.remove());

    userData.voyages.forEach((voyage) => {
      const row = document.createElement("tr");
      row.dataset.voyageId = voyage.id;

      row.innerHTML = `
        <td>${formatDate(voyage.date)}</td>
        <td>${voyage.depart} --> ${voyage.arrivee}</td>
        <td>Vous-même</td>
        <td>${voyage.heure}</td>
        <td>${voyage.statut}</td>
        <td>${voyage.prix}</td>
        <td>
          ${
            voyage.statut === "En attente"
              ? '<button type="button" aria-label="Démarrer ce trajet">Démarrer</button>'
              : '<button type="button" aria-label="Annuler ce trajet">Annuler</button>'
          }
        </td>
      `;

      tableauAVenir.appendChild(row);
    });
  }

  function annulerTrajet(voyageId) {
    if (confirm("Êtes-vous sûr de vouloir annuler ce trajet ?")) {
      const voyage = userData.voyages.find((v) => v.id == voyageId);
      if (voyage) {
        voyage.statut = "Annulé";
        userData.credits += parseInt(voyage.prix);
        afficherInformationsUtilisateur();
        mettreAJourTableauTrajets();
        afficherNotification("Trajet annulé, crédits remboursés", "success");
      }
    }
  }

  function demarrerTrajet(voyageId) {
    const voyage = userData.voyages.find((v) => v.id == voyageId);
    if (voyage) {
      voyage.statut = "Confirmé";
      mettreAJourTableauTrajets();
      afficherNotification("Trajet démarré !", "success");
    }
  }

  // ===========================================
  // FONCTIONS UTILITAIRES
  // ===========================================

  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR");
  }

  function afficherNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      color: white;
      font-weight: 500;
      z-index: 10000;
      transform: translateX(400px);
      transition: transform 0.3s ease;
      max-width: 300px;
    `;

    switch (type) {
      case "success":
        notification.style.background =
          "linear-gradient(135deg, #10b981, #059669)";
        break;
      case "error":
        notification.style.background =
          "linear-gradient(135deg, #ef4444, #dc2626)";
        break;
      case "info":
      default:
        notification.style.background =
          "linear-gradient(135deg, #3b82f6, #2563eb)";
        break;
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    setTimeout(() => {
      notification.style.transform = "translateX(400px)";
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification);
        }
      }, 300);
    }, 3000);

    notification.addEventListener("click", () => {
      notification.style.transform = "translateX(400px)";
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification);
        }
      }, 300);
    });
  }

  // ===========================================
  // FERMETURE DES SUGGESTIONS EN CLIQUANT AILLEURS
  // ===========================================

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".voyage")) {
      const suggestionsDepart = document.getElementById(
        "suggestions-depart-voyage"
      );
      const suggestionsArrivee = document.getElementById(
        "suggestions-arrivee-voyage"
      );

      if (suggestionsDepart) suggestionsDepart.style.display = "none";
      if (suggestionsArrivee) suggestionsArrivee.style.display = "none";
    }
  });

  // ===========================================
  // GESTION DES ÉTOILES POUR TRAJETS PASSÉS
  // ===========================================

  function initialiserEtoilesTrajetsPasses() {
    // Sélectionner toutes les cellules d'avis dans le tableau des trajets passés
    const cellulesAvis = document.querySelectorAll(
      ".t-passes tbody tr td:last-child"
    );

    cellulesAvis.forEach((cellule, indexLigne) => {
      const etoiles = cellule.querySelectorAll(
        '.material-symbols-outlined[aria-hidden="true"]'
      );

      if (etoiles.length === 0) return; // Pas d'étoiles dans cette cellule

      let noteSelectionnee = 0;

      // ID unique pour cette ligne
      const ligneId = `trajet-passe-${indexLigne}`;

      // Fonction pour définir le nombre d'étoiles pleine
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

      // Fonction pour réinitialiser les étoiles de cette ligne
      function reinitialiserEtoilesLigne() {
        noteSelectionnee = 0;
        etoiles.forEach((etoile) => {
          etoile.textContent = "star";
          etoile.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
          etoile.style.setProperty("color", "var(--vert-fonce)", "important");
        });
      }

      // Initialiser les étoiles de cette ligne (toutes vides par défaut)
      reinitialiserEtoilesLigne();

      // Ajouter les événements pour chaque étoile
      etoiles.forEach((etoile, index) => {
        // Rendre les étoiles cliquables
        etoile.style.cursor = "pointer";

        // Gestion des clics
        etoile.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation(); // Éviter la propagation vers le parent

          console.log(
            `Clic sur étoile ${index + 1} de la ligne ${indexLigne + 1}`
          );

          if (noteSelectionnee === index + 1) {
            // Désélectionner si on clique sur la même étoile
            noteSelectionnee = 0;
            console.log(`Désélection ligne ${indexLigne + 1}`);
          } else {
            // Nouvelle sélection
            noteSelectionnee = index + 1;
            console.log(
              `Sélection de ${noteSelectionnee} étoiles pour ligne ${
                indexLigne + 1
              }`
            );
          }

          // Mise à jour de l'affichage pour cette ligne
          definirEtoiles(noteSelectionnee);

          // Optionnel : sauvegarder la note (localStorage, API, etc.)
          sauvegarderNote(indexLigne, noteSelectionnee);
        });

        // Effet de survol
        etoile.addEventListener("mouseenter", function () {
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
          // Restaurer l'état réel pour cette ligne
          definirEtoiles(noteSelectionnee);
        });
      });

      // Charger une note existante si elle existe
      const noteSauvegardee = chargerNote(indexLigne);
      if (noteSauvegardee > 0) {
        noteSelectionnee = noteSauvegardee;
        definirEtoiles(noteSelectionnee);
      }
    });
  }

  // ===========================================
  // FONCTIONS DE SAUVEGARDE/CHARGEMENT DES NOTES
  // ===========================================

  function sauvegarderNote(indexLigne, note) {
    // Exemple avec localStorage - adaptez selon vos besoins
    const notes = JSON.parse(
      localStorage.getItem("notes-trajets-passes") || "{}"
    );
    notes[indexLigne] = note;
    localStorage.setItem("notes-trajets-passes", JSON.stringify(notes));

    // Afficher une notification
    afficherNotification(
      note > 0
        ? `Note de ${note} étoile${note > 1 ? "s" : ""} enregistrée`
        : "Note supprimée",
      "success"
    );
  }

  function chargerNote(indexLigne) {
    const notes = JSON.parse(
      localStorage.getItem("notes-trajets-passes") || "{}"
    );
    return notes[indexLigne] || 0;
  }

  // ===========================================
  // FONCTION DE NOTIFICATION
  // ===========================================

  function afficherNotification(message, type = "info") {
    // Vérifier si la fonction existe déjà
    if (typeof window.afficherNotification === "function") {
      window.afficherNotification(message, type);
      return;
    }

    // Créer la notification si la fonction n'existe pas
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 10000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    max-width: 300px;
  `;

    switch (type) {
      case "success":
        notification.style.background =
          "linear-gradient(135deg, var(--vert-clair), var(--vert-fond-menu-footer))";
        notification.style.border = "2px solid var(--vert-fonce)";
        break;
      case "error":
        notification.style.background =
          "linear-gradient(135deg, #ef4444, #dc2626)";
        break;
      case "info":
      default:
        notification.style.background =
          "linear-gradient(135deg, #3b82f6, #2563eb)";
        break;
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    setTimeout(() => {
      notification.style.transform = "translateX(400px)";
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification);
        }
      }, 300);
    }, 3000);
  }

  // ===========================================
  // INITIALISER LA PAGE AU CHARGEMENT
  // ===========================================

  initialiserPage();
});
