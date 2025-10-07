// Attente du chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  // ===========================================
  // DONNÉES UTILISATEUR (chargées depuis la BDD)
  // ===========================================

  let userData = {
    id: null,
    prenom: "",
    credits: 0,
    role: null,
    photo: null,
    vehicules: [],
    preferences: [],
    voyages: [],
    trajets_a_venir: [],
    trajets_passes: [],
  };

  let currentRating = 0;
  let modalOpen = false;

  // ===========================================
  // GESTION BIO
  // ===========================================
  function configurerBio() {
    const saveBioBtn = document.getElementById("save-bio");
    const editBioBtn = document.getElementById("edit-bio-btn");
    const bioTextarea = document.getElementById("bio");
    const bioDisplay = document.getElementById("bio-display");
    const bioEdit = document.getElementById("bio-edit");
    const bioText = document.getElementById("bio-text");

    if (!saveBioBtn || !bioTextarea) return;

    // Fonction pour afficher la bio
    function afficherBio() {
      const bioValue = bioTextarea.value.trim(); // ✅ Lit directement depuis le textarea
      if (bioValue) {
        bioText.textContent = bioValue;
        bioDisplay.style.display = "block";
        bioEdit.style.display = "none";
        editBioBtn.style.display = "flex";
      } else {
        bioDisplay.style.display = "none";
        bioEdit.style.display = "flex";
        editBioBtn.style.display = "none";
      }
    }

    // ✅ Afficher l'état initial immédiatement
    afficherBio();

    // Clic sur le crayon pour éditer
    editBioBtn?.addEventListener("click", function () {
      bioDisplay.style.display = "none";
      bioEdit.style.display = "flex";
      editBioBtn.style.display = "none";
      bioTextarea.focus();
    });

    // Sauvegarde
    saveBioBtn.addEventListener("click", async function () {
      const bio = bioTextarea.value || "";

      try {
        const response = await fetch(
          "/ecoride/php/api/api-router.php?action=update-bio",
          {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `bio=${encodeURIComponent(bio)}`,
            credentials: "same-origin",
          }
        );

        const data = await response.json();
        if (data.success) {
          // ✅ Mettre à jour userData si disponible
          if (typeof userData !== "undefined") {
            userData.bio = bio;
          }
          afficherBio();
          afficherNotification("Bio mise à jour !", "success");
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        afficherNotification("Erreur lors de la sauvegarde", "error");
      }
    });
  }

  // ===========================================
  // RECHARGE DE CRÉDITS
  // ===========================================
  function configurerRechargeMredits() {
    const btnRecharger = document.getElementById("btn-recharger-credits");
    const modalRecharge = document.getElementById("modalRecharge");
    const closeRecharge = document.getElementById("closeRecharge");
    const formRecharge = document.getElementById("formRecharge");

    if (!btnRecharger || !modalRecharge) return;

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("open_recharge") === "1") {
      modalRecharge.style.display = "flex";
    }

    btnRecharger.addEventListener("click", function () {
      modalRecharge.style.display = "flex";
    });

    closeRecharge?.addEventListener("click", function () {
      modalRecharge.style.display = "none";
    });

    modalRecharge.addEventListener("click", function (e) {
      if (e.target === modalRecharge) {
        modalRecharge.style.display = "none";
      }
    });

    formRecharge?.addEventListener("submit", async function (e) {
      e.preventDefault();

      const pack = document.querySelector('input[name="pack"]:checked')?.value;
      if (!pack) return;

      try {
        const response = await fetch(
          "/ecoride/php/api/api-router.php?action=recharger-credits",
          {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `montant=${pack}`,
            credentials: "same-origin",
          }
        );

        const data = await response.json();

        if (data.success) {
          userData.credits = data.nouveaux_credits;
          await chargerDonneesUtilisateur();
          afficherInformationsUtilisateur();
          modalRecharge.style.display = "none";
          formRecharge.reset();
          afficherNotification(
            `${pack} crédits ajoutés avec succès !`,
            "success"
          );
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        afficherNotification("Erreur lors de la recharge", "error");
      }
    });
  }

  // ===========================================
  // INITIALISATION DE LA PAGE
  // ===========================================

  function initialiserPage() {
    // Vérifier si on est sur la page utilisateur avant de charger les données
    const currentPage = new URLSearchParams(window.location.search).get("page");
    const isUserPage =
      currentPage === "utilisateur" ||
      window.location.pathname.includes("utilisateur");

    if (!isUserPage) {
      return;
    }

    // Vérifier que les éléments nécessaires existent sur la page
    const profilCredits = document.querySelector(".profil-credits");
    if (!profilCredits) {
      return;
    }

    chargerDonneesUtilisateur()
      .then(() => {
        afficherInformationsUtilisateur();
        configurerPhotoProfil();
        configurerRechargeMredits();
        configurerChoixRole();
        configurerFormulaireVehicule();
        configurerPreferences();
        configurerFormulaireVoyage();
        configurerGestionTrajets();
        configurerPlaceholders();
        initialiserEtoilesTrajetsPasses();
        initializeAvisSystem();
        configurerModalParticipants();
        configurerModalActions();
        configurerBio();
      })
      .catch((error) => {
        console.error("Erreur lors du chargement des données:", error);
        afficherNotification("Erreur lors du chargement des données", "error");
      });
  }

  // ===========================================
  // CHARGEMENT DES DONNÉES DEPUIS LA BDD
  // ===========================================

  async function chargerDonneesUtilisateur() {
    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=get-user-data",
        {
          method: "GET",
          credentials: "include",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
      );

      if (!response.ok) {
        if (response.status === 401) {
          console.error("Erreur 401: Non authentifié");
          // Rediriger vers la page de connexion
          window.location.href =
            "/ecoride/php/index.php?page=connexion&redirect=" +
            encodeURIComponent(window.location.pathname);
          return;
        }

        let errorMessage = "Erreur réseau";
        try {
          const errorData = await response.json();
          errorMessage = errorData.message || errorMessage;
        } catch (e) {}

        throw new Error(`HTTP ${response.status}: ${errorMessage}`);
      }

      const data = await response.json();

      if (data.success) {
        userData = {
          ...userData,
          ...data.user,
          vehicules: data.vehicules || [],
          preferences: data.preferences || [],
          trajets_a_venir: data.trajets_a_venir || [],
          trajets_passes: data.trajets_passes || [],
        };

        // Mettre à jour le select des véhicules
        mettreAJourSelectVehicules();
        // Mettre à jour les tableaux de trajets
        mettreAJourTableauxTrajets();
      } else {
        throw new Error(data.message || "Erreur lors du chargement");
      }
    } catch (error) {
      console.error("Erreur complète dans chargerDonneesUtilisateur:", error);
      throw error;
    }
  }

  // ===========================================
  // AFFICHAGE DES INFORMATIONS UTILISATEUR
  // ===========================================

  function afficherInformationsUtilisateur() {
    const welcomeText = document.querySelector(".profil-credits h2");
    const creditsText = document.querySelector(".profil-credits p strong");
    const bioTextarea = document.getElementById("bio");

    if (welcomeText && userData.prenom) {
      welcomeText.textContent = `Bienvenue ${userData.prenom}`;
    }

    if (creditsText) {
      creditsText.textContent = `Nombre de crédits : ${userData.credits}`;
    }

    // ✅ Initialiser la bio
    if (bioTextarea && userData.bio) {
      bioTextarea.value = userData.bio;
    }
  }

  // ===========================================
  // GESTION PHOTO DE PROFIL
  // ===========================================

  function configurerPhotoProfil() {
    const profilPhoto = document.querySelector(".profil-photo");
    if (!profilPhoto) return;

    const iconePhoto = profilPhoto.querySelector(".material-symbols-outlined");

    // Afficher la photo existante si elle existe
    if (userData.photo_profil) {
      // Créer ou réutiliser l'élément img
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

      // Gérer les erreurs de chargement d'image
      img.onerror = function () {
        console.error(
          "Erreur de chargement de l'image:",
          userData.photo_profil
        );
        // Revenir à l'icône par défaut en cas d'erreur
        const defaultIcon = document.createElement("span");
        defaultIcon.className = "material-symbols-outlined";
        defaultIcon.setAttribute("aria-label", "Photo profil");
        defaultIcon.setAttribute("role", "img");
        defaultIcon.textContent = "account_circle";
        profilPhoto.replaceChild(defaultIcon, img);
      };

      // Ajouter un timestamp pour éviter le cache
      img.src = userData.photo_profil + "?t=" + Date.now();
    }

    profilPhoto.addEventListener("click", function () {
      const inputFile = document.createElement("input");
      inputFile.type = "file";
      inputFile.accept = "image/*";
      inputFile.style.display = "none";

      inputFile.addEventListener("change", async function (e) {
        const file = e.target.files[0];
        if (file) {
          // Validation de la taille
          if (file.size > 2 * 1024 * 1024) {
            afficherNotification("L'image ne doit pas dépasser 2MB", "error");
            return;
          }

          // Validation du type
          const allowedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/webp",
          ];
          if (!allowedTypes.includes(file.type)) {
            afficherNotification(
              "Format non supporté (JPG, PNG, WebP uniquement)",
              "error"
            );
            return;
          }

          try {
            const formData = new FormData();
            formData.append("photo", file);

            // Afficher un indicateur de chargement
            afficherNotification("Upload en cours...", "info");

            const response = await fetch(
              "/ecoride/php/api/api-router.php?action=upload-photo",
              {
                method: "POST",
                body: formData,
                credentials: "same-origin",
              }
            );

            const data = await response.json();

            if (data.success) {
              // Mettre à jour l'affichage
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
                // Remplacer l'icône par l'image
                const icon = profilPhoto.querySelector(
                  ".material-symbols-outlined"
                );
                if (icon) {
                  profilPhoto.replaceChild(img, icon);
                } else {
                  profilPhoto.appendChild(img);
                }
              }

              // Mettre à jour src avec un timestamp pour éviter le cache
              img.src = data.photo_url + "?t=" + Date.now();
              userData.photo_profil = data.photo_url;

              afficherNotification("Photo de profil mise à jour !", "success");
            } else {
              throw new Error(data.message || "Erreur lors de l'upload");
            }
          } catch (error) {
            console.error("Erreur upload photo:", error);
            afficherNotification(
              "Erreur lors de l'upload de la photo: " + error.message,
              "error"
            );
          }
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
    // Si la vue PHP a injecté des données (version intégrée), on les charge d'abord
    if (window.ecoRideData && window.ecoRideData.user) {
      userData = {
        ...userData,
        ...window.ecoRideData.user,
        vehicules: window.ecoRideData.vehicules || userData.vehicules,
        preferences: window.ecoRideData.preferences || userData.preferences,
        trajets_a_venir:
          window.ecoRideData.trajetsAVenir || userData.trajets_a_venir,
        trajets_passes:
          window.ecoRideData.trajetsPasses || userData.trajets_passes,
      };
    }

    const boutons = document.querySelectorAll(".role-choice .button button");
    const sectionChauffeur = document.querySelector(".informations-chauffeur");
    const sectionVoyage = document.querySelector(".voyage");

    if (!boutons || boutons.length === 0) return;

    // Helper : normalize truthy server flags
    const isTrue = (v) => v === true || v === 1 || v === "1" || v === "true";

    // Déterminer l'état initial à partir de userData
    const estChauffeur = isTrue(userData.est_chauffeur);
    const estPassager = isTrue(userData.est_passager);

    // Mettre à jour classes selected sur les boutons (initial)
    boutons.forEach((btn) => btn.classList.remove("selected"));
    if (estChauffeur && estPassager) {
      const btn = document.querySelector(
        '.role-choice .button button[aria-label="duo"]'
      );
      if (btn) btn.classList.add("selected");
      userData.role = "duo";
    } else if (estChauffeur) {
      const btn = document.querySelector(
        '.role-choice .button button[aria-label="chauffeur"]'
      );
      if (btn) btn.classList.add("selected");
      userData.role = "chauffeur";
    } else if (estPassager) {
      const btn = document.querySelector(
        '.role-choice .button button[aria-label="passager"]'
      );
      if (btn) btn.classList.add("selected");
      userData.role = "passager";
    } else {
      userData.role = null;
    }

    // Afficher/masquer sections selon l'état initial
    if (userData.role === "chauffeur" || userData.role === "duo") {
      if (sectionChauffeur) sectionChauffeur.style.display = "grid";
      if (sectionVoyage) sectionVoyage.style.display = "block";
    } else {
      if (sectionChauffeur) sectionChauffeur.style.display = "none";
      if (sectionVoyage) sectionVoyage.style.display = "none";
    }

    // Handler clics (buttons sont type="button", but preventDefault is harmless)
    boutons.forEach((bouton) => {
      bouton.addEventListener("click", async function (e) {
        e.preventDefault(); // sûr même si type="button"

        const role = this.getAttribute("aria-label");
        if (!role) return;

        // Désactiver boutons pour éviter doubles clics
        boutons.forEach((b) => (b.disabled = true));

        try {
          const response = await fetch(
            "/ecoride/php/api/api-router.php?action=update-role",
            {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `role=${encodeURIComponent(role)}`,
              credentials: "same-origin",
            }
          );

          const data = await response.json();

          if (!data.success)
            throw new Error(data.message || "Erreur mise à jour rôle");

          // Mise à jour visuelle
          boutons.forEach((b) => b.classList.remove("selected"));
          this.classList.add("selected");

          // Mettre à jour userData
          userData.role = role;
          switch (role) {
            case "chauffeur":
              userData.est_chauffeur = true;
              userData.est_passager = false;
              break;
            case "passager":
              userData.est_chauffeur = false;
              userData.est_passager = true;
              break;
            case "duo":
              userData.est_chauffeur = true;
              userData.est_passager = true;
              break;
          }

          // Afficher/masquer sections
          if (role === "chauffeur" || role === "duo") {
            if (sectionChauffeur) sectionChauffeur.style.display = "grid";
            if (sectionVoyage) sectionVoyage.style.display = "block";
            // si tu veux, recharger la liste de véhicules ici :
            // await chargerVehicules(); mettreAJourSelectVehicules();
          } else {
            if (sectionChauffeur) sectionChauffeur.style.display = "none";
            if (sectionVoyage) sectionVoyage.style.display = "none";
          }

          afficherNotification(`Rôle "${role}" sélectionné`, "success");
        } catch (err) {
          console.error("Erreur mise à jour rôle:", err);
          afficherNotification(
            "Erreur lors de la mise à jour du rôle",
            "error"
          );
        } finally {
          // Réactiver boutons
          boutons.forEach((b) => (b.disabled = false));
        }
      });
    });
  }

  // ===========================================
  // FORMULAIRE VÉHICULE
  // ===========================================

  function configurerFormulaireVehicule() {
    const form = document.querySelector("form.vehicule");
    const btnAjouterVehicule = document.querySelector(".btn-ajouter-vehicule");

    if (!form) {
      console.error("ERREUR: Formulaire véhicule non trouvé!");
      return;
    }

    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const response = await fetch(
          "/ecoride/php/api/api-router.php?action=add-vehicule",
          {
            method: "POST",
            body: formData,
            credentials: "same-origin",
          }
        );

        const data = await response.json();

        if (data.success) {
          // Recharger les véhicules
          await chargerVehicules();
          mettreAJourSelectVehicules();
          form.reset();
          afficherNotification("Véhicule ajouté avec succès !", "success");
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        console.error("Erreur ajout véhicule:", error);
        afficherNotification(
          error.message || "Erreur lors de l'ajout du véhicule",
          "error"
        );
      }
    });

    if (btnAjouterVehicule) {
      btnAjouterVehicule.addEventListener("click", function () {
        form.reset();
        form.scrollIntoView({ behavior: "smooth" });
      });
    }
  }

  async function chargerVehicules() {
    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=get-vehicules",
        {
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        userData.vehicules = data.vehicules;
      }
    } catch (error) {
      console.error("Erreur chargement véhicules:", error);
    }
  }

  function mettreAJourSelectVehicules() {
    const select = document.querySelector("#choix-vehicule");
    if (!select) return;

    select.innerHTML = '<option value="">-- Sélectionnez --</option>';

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

    // Cocher les préférences existantes
    userData.preferences.forEach((pref) => {
      const checkbox = document.querySelector(`input[name="${pref.type_nom}"]`);
      if (checkbox && pref.valeur === "accepte") {
        checkbox.checked = true;
      }
    });

    // Écouter les changements sur TOUTES les préférences
    preferences.forEach((checkbox) => {
      checkbox.addEventListener("change", async function () {
        await sauvegarderPreferences();
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

  async function sauvegarderPreferences() {
    try {
      const toutesCheckboxes = document.querySelectorAll(
        '.preferences input[type="checkbox"]'
      );
      const preferencesCochees = [];

      toutesCheckboxes.forEach((checkbox) => {
        if (checkbox.checked) {
          preferencesCochees.push(checkbox.name);
        }
      });

      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=update-preferences",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `preferences=${encodeURIComponent(
            JSON.stringify(preferencesCochees)
          )}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message);
      }

      await chargerDonneesUtilisateur();
      afficherNotification("Préférences enregistrées", "success");
    } catch (error) {
      console.error("Erreur sauvegarde préférences:", error);
      afficherNotification(
        "Erreur lors de la sauvegarde des préférences",
        "error"
      );
    }
  }

  async function ajouterPreferencePersonnalisee(preference) {
    try {
      // D'abord sauvegarder les préférences actuelles cochées
      const toutesCheckboxes = document.querySelectorAll(
        '.preferences input[type="checkbox"]'
      );
      const preferencesCochees = [];

      toutesCheckboxes.forEach((checkbox) => {
        if (checkbox.checked) {
          preferencesCochees.push(checkbox.name);
        }
      });

      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=add-custom-preference",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `preference=${encodeURIComponent(
            preference
          )}&existing_preferences=${encodeURIComponent(
            JSON.stringify(preferencesCochees)
          )}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        // Recharger les données utilisateur pour avoir la liste complète
        await chargerDonneesUtilisateur();

        // Recréer l'affichage complet des préférences
        window.location.reload(); // Plus simple pour tout rafraîchir

        afficherNotification(`Préférence "${preference}" ajoutée !`, "success");
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur ajout préférence:", error);
      afficherNotification("Erreur lors de l'ajout de la préférence", "error");
    }
  }

  // ===========================================
  // FORMULAIRE VOYAGE
  // ===========================================

  function configurerFormulaireVoyage() {
    const inputDepart = document.getElementById("depart");
    const inputArrivee = document.getElementById("arrivee");
    const btnAjouterVoyage = document.querySelector(".btn-ajouter-voyage");

    // Autocomplétion (code existant...)
    configurerAutocompletion(inputDepart, "suggestions-depart-voyage");
    configurerAutocompletion(inputArrivee, "suggestions-arrivee-voyage");

    if (btnAjouterVoyage) {
      btnAjouterVoyage.addEventListener("click", async function (e) {
        e.preventDefault();
        await ajouterVoyage();
      });
    }
  }

  async function ajouterVoyage() {
    const depart = document.getElementById("depart")?.value || "";
    const arrivee = document.getElementById("arrivee")?.value || "";
    const prix = document.getElementById("fixer-prix")?.value || "";
    const vehiculeId = document.getElementById("choix-vehicule")?.value || "";
    const dateDepart = document.getElementById("date-depart")?.value || ""; // ✅ Changé ici

    if (!depart || !arrivee || !prix || !vehiculeId || !dateDepart) {
      afficherNotification("Veuillez remplir tous les champs", "error");
      return;
    }

    if (userData.credits < 2) {
      afficherNotification("Crédits insuffisants (2 crédits requis)", "error");
      return;
    }

    try {
      const formData = new FormData();
      formData.append("depart", depart);
      formData.append("arrivee", arrivee);
      formData.append("fixer_prix", prix);
      formData.append("choix_vehicule", vehiculeId);
      formData.append("date_depart", dateDepart); // ✅ Utilise maintenant la vraie date

      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=create-trajet",
        {
          method: "POST",
          body: formData,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        userData.credits = data.nouveaux_credits;
        await chargerDonneesUtilisateur();

        // Réinitialiser le formulaire
        const formInputs = document.querySelectorAll(
          ".add-voyage input, .add-voyage select"
        );
        formInputs.forEach((input) => (input.value = ""));

        afficherInformationsUtilisateur();
        mettreAJourTableauxTrajets();
        afficherNotification(
          "Voyage ajouté avec succès ! (2 crédits débités)",
          "success"
        );
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur création trajet:", error);
      afficherNotification(
        error.message || "Erreur lors de la création du trajet",
        "error"
      );
    }
  }

  // ===========================================
  // GESTION DES TRAJETS
  // ===========================================

  function configurerGestionTrajets() {
    mettreAJourTableauxTrajets();

    document.addEventListener("click", async function (e) {
      if (e.target.matches(".btn-action")) {
        const action = e.target.dataset.action;
        const row = e.target.closest("tr");
        if (!row) return;

        const trajetId = row.dataset.trajetId;
        if (!trajetId) return;

        switch (action) {
          case "demarrer":
            await demarrerTrajet(trajetId);
            break;
          case "terminer":
            await terminerTrajet(trajetId);
            break;
          case "annuler":
            await annulerTrajet(trajetId);
            break;
        }
      }
    });
  }

  async function terminerTrajet(trajetId) {
    if (!confirm("Confirmer la fin de ce trajet ?")) {
      return;
    }

    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=terminer-trajet",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `trajet_id=${encodeURIComponent(trajetId)}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        await chargerDonneesUtilisateur();
        mettreAJourTableauxTrajets();
        afficherNotification("Trajet terminé !", "success");
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur:", error);
      afficherNotification(error.message || "Erreur", "error");
    }
  }

  function mettreAJourTableauxTrajets() {
    mettreAJourTableauAVenir();
    mettreAJourTableauPasses();
  }

  function mettreAJourTableauAVenir() {
    const tableauAVenir = document.querySelector(".a-venir tbody");
    if (!tableauAVenir) return;

    tableauAVenir.innerHTML = "";

    userData.trajets_a_venir.forEach((trajet) => {
      const row = document.createElement("tr");
      row.dataset.trajetId = trajet.id;

      const dateDepart = new Date(trajet.date_depart);
      const heureDepart = dateDepart.toLocaleTimeString("fr-FR", {
        hour: "2-digit",
        minute: "2-digit",
      });

      const nbParticipants = trajet.nb_participants || 0;
      const placesTotales =
        trajet.places_totales || trajet.places_disponibles || 0;

      row.innerHTML = `
      <td>${dateDepart.toLocaleDateString("fr-FR")}</td>
      <td>${trajet.adresse_depart} → ${trajet.adresse_arrivee}</td>
      <td>${trajet.conducteur}</td>
      <td>${heureDepart}</td>
      <td>${trajet.statut}</td>
      <td>${trajet.prix_par_passager}</td>
      <td>
        <button class="btn-voir-participants" data-trajet-id="${trajet.id}">
          <span class="material-symbols-outlined" style="font-size:1rem;">group</span>
          ${nbParticipants}/${placesTotales}
        </button>
      </td>
      <td>${getActionButton(trajet)}</td>
    `;

      tableauAVenir.appendChild(row);
    });
  }

  function mettreAJourTableauPasses() {
    const tableauPasses = document.querySelector(".t-passes tbody");
    if (!tableauPasses) return;

    tableauPasses.innerHTML = "";

    userData.trajets_passes.forEach((trajet) => {
      const row = document.createElement("tr");
      row.dataset.trajetId = trajet.id;

      const dateDepart = new Date(trajet.date_depart);
      const heureDepart = dateDepart.toLocaleTimeString("fr-FR", {
        hour: "2-digit",
        minute: "2-digit",
      });

      const nbParticipants = trajet.nb_participants || 0;
      const placesTotales =
        trajet.places_totales || trajet.places_disponibles || 0;

      row.innerHTML = `
      <td>${dateDepart.toLocaleDateString("fr-FR")}</td>
      <td>${trajet.adresse_depart} → ${trajet.adresse_arrivee}</td>
      <td>${trajet.conducteur}</td>
      <td>${heureDepart}</td>
      <td>${trajet.statut}</td>
      <td>${trajet.prix_par_passager}</td>
      <td>
        <button class="btn-voir-participants" data-trajet-id="${trajet.id}">
          <span class="material-symbols-outlined" style="font-size:1rem;">group</span>
          ${nbParticipants}/${placesTotales}
        </button>
      </td>
      <td>${getAvisButton(trajet)}</td>
    `;

      tableauPasses.appendChild(row);
    });
  }

  function getActionButton(trajet) {
    if (trajet.role_utilisateur === "chauffeur") {
      switch (trajet.statut) {
        case "planifie":
        case "en_cours":
          return `<button type="button" class="btn-action-modal" data-trajet-id="${trajet.id}" data-statut="${trajet.statut}" data-role="chauffeur">Actions</button>`;
        default:
          return "<span>-</span>";
      }
    } else {
      if (trajet.statut === "planifie" || trajet.statut === "en_cours") {
        return `<button type="button" class="btn-action-modal" data-trajet-id="${trajet.id}" data-statut="${trajet.statut}" data-role="passager">Actions</button>`;
      }
      return "<span>-</span>";
    }
  }

  function getAvisButton(trajet) {
    if (trajet.role_utilisateur === "chauffeur") {
      return '<span style="color: var(--noir-secondaire); font-style: italic;">-</span>';
    }

    if (trajet.statut === "annule") {
      return '<span style="color: var(--noir-secondaire); font-style: italic;">-</span>';
    }

    // Vérifier le statut de l'avis
    const avisStatut = trajet.avis_statut || trajet.statut_avis;

    if (avisStatut === "valide") {
      return '<button type="submit" data-status="avis_publie" class="btn-avis" disabled>✅ Avis publié</button>';
    }

    if (avisStatut === "refuse") {
      const motif = trajet.avis_motif_refus || "Aucun motif fourni";
      return `<button class="btn-avis-refuse" data-motif="${motif}">✗ Avis refusé</button>`;
    }

    if (avisStatut === "en_attente") {
      return '<button type="submit" data-status="en_validation" class="btn-avis" disabled>⏳ En validation</button>';
    }

    return '<button type="submit" data-status="en_attente_avis" class="btn-avis">🌟 Laisser un avis</button>';
  }

  async function annulerTrajet(trajetId) {
    if (!confirm("Êtes-vous sûr de vouloir annuler ce trajet ?")) {
      return;
    }

    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=annuler-trajet",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `trajet_id=${encodeURIComponent(trajetId)}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        // Recharger les données
        await chargerDonneesUtilisateur();
        mettreAJourTableauxTrajets();
        afficherInformationsUtilisateur();
        afficherNotification("Trajet annulé avec succès", "success");
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur annulation trajet:", error);
      afficherNotification(
        error.message || "Erreur lors de l'annulation",
        "error"
      );
    }
  }

  async function demarrerTrajet(trajetId) {
    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=demarrer-trajet",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `trajet_id=${encodeURIComponent(trajetId)}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        // Recharger les données
        await chargerDonneesUtilisateur();
        mettreAJourTableauxTrajets();
        afficherNotification("Trajet démarré !", "success");
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur démarrage trajet:", error);
      afficherNotification(
        error.message || "Erreur lors du démarrage",
        "error"
      );
    }
  }

  // ===========================================
  // AUTOCOMPLÉTION
  // ===========================================

  async function rechercherAdresses(query) {
    if (query.length < 3) return [];

    try {
      const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(
        query
      )}&limit=10`;
      const response = await fetch(url);

      if (!response.ok) throw new Error("Erreur API");

      const data = await response.json();

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

  function configurerAutocompletion(input, containerId) {
    if (!input) return;

    let container = document.getElementById(containerId);
    if (!container) {
      container = document.createElement("div");
      container.id = containerId;
      container.className = "suggestions";
      container.style.display = "none";
      input.parentNode.appendChild(container);
    }

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

        const adresses = await rechercherAdresses(query);
        creerListeSuggestions(adresses, container, input);
      }, 300);
    });

    input.addEventListener("blur", function () {
      setTimeout(() => {
        container.style.display = "none";
      }, 200);
    });
  }

  function creerListeSuggestions(adresses, container, input) {
    container.innerHTML = "";

    if (adresses.length === 0) {
      container.style.display = "none";
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

    adresses.forEach((adresse) => {
      const item = document.createElement("li");
      item.className = "suggestion-item";
      item.style.cssText = `
      padding: 0.75rem 1rem;
      cursor: pointer;
      color: var(--noir-primaire);
      transition: all 0.2s ease;
    `;

      item.textContent = adresse.nom; // Ex: "10 Rue de Rivoli, 75001 Paris"

      item.addEventListener("click", function () {
        input.value = adresse.nom;
        // Stocker les coordonnées GPS dans des attributs data
        input.dataset.latitude = adresse.latitude;
        input.dataset.longitude = adresse.longitude;
        container.style.display = "none";
      });

      item.addEventListener("mouseenter", function () {
        liste
          .querySelectorAll(".suggestion-item")
          .forEach((i) => i.classList.remove("active"));
        this.classList.add("active");
        this.style.cssText += `color: var(--vert-fonce); font-weight: 500; transform: translateX(2px);`;
      });

      item.addEventListener("mouseleave", function () {
        this.style.cssText = `padding: 0.75rem 1rem; cursor: pointer; color: var(--noir-primaire); transition: all 0.2s ease;`;
      });

      liste.appendChild(item);
    });

    container.appendChild(liste);
    container.style.display = "block";
  }

  function configurerPlaceholders() {
    if (document.getElementById("depart")) {
      document.getElementById("depart").placeholder = "Départ";
    }
    if (document.getElementById("arrivee")) {
      document.getElementById("arrivee").placeholder = "Destination";
    }
    if (document.getElementById("fixer-prix")) {
      document.getElementById("fixer-prix").placeholder = "-- crédits";
    }
  }

  // ===========================================
  // SYSTEM AVIS
  // ===========================================

  function initializeAvisSystem() {
    initializeModal();
    initializeStarRating();
    initializeFormValidation();

    // Événements pour les boutons d'avis (délégation d'événements)
    document.addEventListener("click", function (e) {
      if (e.target.matches('.btn-avis[data-status="en_attente_avis"]')) {
        const trajetId = e.target.closest("tr").dataset.trajetId;
        openAvisModal(trajetId);
      }
    });
  }

  function initializeModal() {
    const modal = document.getElementById("modalAvis");
    const closeBtn = document.getElementById("closeModal");
    const annulerBtn = document.getElementById("annulerAvis");

    [closeBtn, annulerBtn].forEach((btn) => {
      if (btn) {
        btn.addEventListener("click", closeAvisModal);
      }
    });

    if (modal) {
      modal.addEventListener("click", function (e) {
        if (e.target === modal) {
          closeAvisModal();
        }
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modalOpen) {
        closeAvisModal();
      }
    });
  }

  function initializeStarRating() {
    const stars = document.querySelectorAll(".star");

    stars.forEach((star, index) => {
      star.addEventListener("mouseenter", function () {
        highlightStars(index + 1);
      });

      star.addEventListener("click", function () {
        const rating = index + 1;
        setRating(rating);
      });
    });

    const starRating = document.getElementById("starRating");
    if (starRating) {
      starRating.addEventListener("mouseleave", function () {
        highlightStars(currentRating);
      });
    }
  }

  function initializeFormValidation() {
    const commentaire = document.getElementById("commentaire");
    const signalement = document.getElementById("signalement");
    const trajetRadios = document.querySelectorAll(
      'input[name="trajetReussi"]'
    );
    const soumettreBtn = document.getElementById("soumettreAvis");

    if (commentaire) {
      commentaire.addEventListener("input", function () {
        updateCharCounter("charCount", this.value.length);
        validateForm();
      });
    }

    if (signalement) {
      signalement.addEventListener("input", function () {
        updateCharCounter("signalementCharCount", this.value.length);
        validateForm();
      });
    }

    trajetRadios.forEach((radio) => {
      radio.addEventListener("change", function () {
        const signalementField = document.getElementById("signalementField");

        if (this.value === "non") {
          signalementField.classList.add("show");
          signalement.required = true;
        } else {
          signalementField.classList.remove("show");
          signalement.required = false;
          signalement.value = "";
          updateCharCounter("signalementCharCount", 0);
        }
        validateForm();
      });
    });

    if (soumettreBtn) {
      soumettreBtn.addEventListener("click", soumettreAvis);
    }
  }

  function highlightStars(count) {
    const stars = document.querySelectorAll(".star");
    stars.forEach((star, index) => {
      if (index < count) {
        star.classList.add("filled");
        star.classList.remove("empty");
      } else {
        star.classList.add("empty");
        star.classList.remove("filled");
      }
    });
  }

  function setRating(rating) {
    currentRating = rating;
    const noteInput = document.getElementById("note");
    if (noteInput) {
      noteInput.value = rating;
    }
    highlightStars(rating);
    validateForm();
  }

  function updateCharCounter(counterId, length) {
    const counter = document.getElementById(counterId);
    if (counter) {
      counter.textContent = length;
    }
  }

  function validateForm() {
    const note = document.getElementById("note")?.value;
    const trajetReussi = document.querySelector(
      'input[name="trajetReussi"]:checked'
    );
    const signalement = document.getElementById("signalement");
    const soumettreBtn = document.getElementById("soumettreAvis");

    let isValid = note && trajetReussi;

    if (trajetReussi && trajetReussi.value === "non") {
      isValid = isValid && signalement && signalement.value.trim().length > 0;
    }

    if (soumettreBtn) {
      soumettreBtn.disabled = !isValid;
    }
  }

  function openAvisModal(trajetId) {
    const modal = document.getElementById("modalAvis");
    const trajetIdInput = document.getElementById("trajetId");

    if (trajetIdInput) {
      trajetIdInput.value = trajetId;
    }

    if (modal) {
      modal.style.display = "flex";
      modalOpen = true;
      document.body.style.overflow = "hidden";
    }
  }

  function closeAvisModal() {
    const modal = document.getElementById("modalAvis");
    if (modal) {
      modal.style.display = "none";
    }
    modalOpen = false;
    document.body.style.overflow = "";
    resetForm();
  }

  function resetForm() {
    const form = document.getElementById("formAvis");
    if (form) {
      form.reset();
    }
    currentRating = 0;
    highlightStars(0);

    const signalementField = document.getElementById("signalementField");
    if (signalementField) {
      signalementField.classList.remove("show");
    }

    const signalement = document.getElementById("signalement");
    if (signalement) {
      signalement.required = false;
    }

    updateCharCounter("charCount", 0);
    updateCharCounter("signalementCharCount", 0);

    const soumettreBtn = document.getElementById("soumettreAvis");
    if (soumettreBtn) {
      soumettreBtn.disabled = true;
    }
  }

  async function soumettreAvis() {
    const trajetId = document.getElementById("trajetId")?.value;
    const note = document.getElementById("note")?.value;
    const commentaire = document.getElementById("commentaire")?.value;
    const trajetReussi = document.querySelector(
      'input[name="trajetReussi"]:checked'
    )?.value;
    const signalement = document.getElementById("signalement")?.value;

    if (!trajetId || !note || !trajetReussi) {
      afficherNotification(
        "Veuillez remplir tous les champs obligatoires",
        "error"
      );
      return;
    }

    try {
      const formData = new FormData();
      formData.append("trajet_id", trajetId);
      formData.append("note", note);
      formData.append("commentaire", commentaire || "");
      formData.append("trajet_reussi", trajetReussi);

      if (trajetReussi === "non" && signalement) {
        formData.append("signalement", signalement);
      }

      const soumettreBtn = document.getElementById("soumettreAvis");
      if (soumettreBtn) {
        soumettreBtn.disabled = true;
        soumettreBtn.textContent = "Envoi en cours...";
      }

      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=soumettre-avis",
        {
          method: "POST",
          body: formData,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        // Mettre à jour le bouton dans le tableau
        const btn = document.querySelector(
          `tr[data-trajet-id="${trajetId}"] .btn-avis`
        );
        if (btn) {
          btn.dataset.status = "en_validation";
          btn.textContent = "⏳ En validation";
          btn.disabled = true;
          btn.style.background = "linear-gradient(135deg, #757575, #616161)";
          btn.style.cursor = "not-allowed";
        }

        afficherNotification(
          "Votre avis a été soumis avec succès ! Il sera visible après validation.",
          "success"
        );
        closeAvisModal();
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error("Erreur soumission avis:", error);
      afficherNotification(
        error.message || "Erreur lors de la soumission de l'avis",
        "error"
      );

      const soumettreBtn = document.getElementById("soumettreAvis");
      if (soumettreBtn) {
        soumettreBtn.disabled = false;
        soumettreBtn.textContent = "Soumettre l'avis";
      }
    }
  }

  // ===========================================
  // GESTION DES ÉTOILES POUR TRAJETS PASSÉS
  // ===========================================

  function initialiserEtoilesTrajetsPasses() {
    const cellulesAvis = document.querySelectorAll(
      ".t-passes tbody tr td:last-child"
    );

    cellulesAvis.forEach((cellule, indexLigne) => {
      const etoiles = cellule.querySelectorAll(
        '.material-symbols-outlined[aria-hidden="true"]'
      );

      if (etoiles.length === 0) return;

      let noteSelectionnee = 0;

      function definirEtoiles(nombre) {
        etoiles.forEach((etoile, index) => {
          etoile.textContent = "star";
          if (index < nombre) {
            etoile.style.fontVariationSettings = "'FILL' 1, 'wght' 400";
          } else {
            etoile.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
          }
          etoile.style.setProperty("color", "var(--vert-fonce)", "important");
        });
      }

      function reinitialiserEtoilesLigne() {
        noteSelectionnee = 0;
        etoiles.forEach((etoile) => {
          etoile.textContent = "star";
          etoile.style.fontVariationSettings = "'FILL' 0, 'wght' 400";
          etoile.style.setProperty("color", "var(--vert-fonce)", "important");
        });
      }

      reinitialiserEtoilesLigne();

      etoiles.forEach((etoile, index) => {
        etoile.style.cursor = "pointer";

        etoile.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation();

          if (noteSelectionnee === index + 1) {
            noteSelectionnee = 0;
          } else {
            noteSelectionnee = index + 1;
          }

          definirEtoiles(noteSelectionnee);
          sauvegarderNote(indexLigne, noteSelectionnee);
        });

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
          definirEtoiles(noteSelectionnee);
        });
      });

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
    const notes = JSON.parse(
      localStorage.getItem("notes-trajets-passes") || "{}"
    );
    notes[indexLigne] = note;
    localStorage.setItem("notes-trajets-passes", JSON.stringify(notes));

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
      cursor: pointer;
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
    }, 4000);

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
  // FERMETURE DES SUGGESTIONS
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
  // GESTION MODAL PARTICIPANTS
  // ===========================================
  function configurerModalParticipants() {
    const modal = document.getElementById("modalParticipants");
    const closeBtn = document.getElementById("closeParticipants");

    if (!modal) return;

    // Ouvrir modal
    document.querySelectorAll(".btn-voir-participants").forEach((btn) => {
      btn.addEventListener("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const trajetId = this.dataset.trajetId;

        try {
          const response = await fetch(
            `/ecoride/php/api/api-router.php?action=get-participants&trajet_id=${trajetId}`,
            { credentials: "same-origin" }
          );
          const data = await response.json();

          if (data.success) {
            afficherParticipants(data.participants || []);
            modal.style.display = "flex";
          } else {
            afficherNotification(data.message || "Erreur", "error");
          }
        } catch (error) {
          console.error("Erreur:", error);
          afficherNotification("Erreur chargement participants", "error");
        }
      });
    });

    // Fermer modal
    closeBtn?.addEventListener("click", () => (modal.style.display = "none"));
    modal?.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });
  }
  // ===========================================
  // GESTION MODAL ACTIONS
  // ===========================================
  function configurerModalActions() {
    const modal = document.getElementById("modalActions");
    const closeBtn = document.getElementById("closeActions");
    const actionsContainer = document.getElementById("actionsContainer");

    if (!modal) return;

    // Ouvrir le modal
    document.addEventListener("click", function (e) {
      if (e.target.matches(".btn-action-modal")) {
        const trajetId = e.target.dataset.trajetId;
        const statut = e.target.dataset.statut;
        const role = e.target.dataset.role;

        // Construire les actions selon le rôle et statut
        let actionsHTML = "";

        if (role === "chauffeur") {
          if (statut === "planifie") {
            actionsHTML = `
            <button class="action-button demarrer" data-action="demarrer" data-trajet-id="${trajetId}">
              <span class="material-symbols-outlined">play_arrow</span>
              Démarrer le trajet
            </button>
            <button class="action-button annuler" data-action="annuler" data-trajet-id="${trajetId}">
              <span class="material-symbols-outlined">cancel</span>
              Annuler le trajet
            </button>
          `;
          } else if (statut === "en_cours") {
            actionsHTML = `
            <button class="action-button terminer" data-action="terminer" data-trajet-id="${trajetId}">
              <span class="material-symbols-outlined">check_circle</span>
              Terminer le trajet
            </button>
            <button class="action-button annuler" data-action="annuler" data-trajet-id="${trajetId}">
              <span class="material-symbols-outlined">cancel</span>
              Annuler le trajet
            </button>
          `;
          }
        } else {
          actionsHTML = `
          <button class="action-button annuler" data-action="annuler" data-trajet-id="${trajetId}">
            <span class="material-symbols-outlined">cancel</span>
            Annuler ma réservation
          </button>
        `;
        }

        actionsContainer.innerHTML = actionsHTML;
        modal.style.display = "flex";
      }
    });

    // Fermer le modal
    closeBtn?.addEventListener("click", () => (modal.style.display = "none"));
    modal?.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });

    // Gérer les actions
    actionsContainer.addEventListener("click", async function (e) {
      const button = e.target.closest(".action-button");
      if (!button) return;

      const action = button.dataset.action;
      const trajetId = button.dataset.trajetId;

      modal.style.display = "none";

      switch (action) {
        case "demarrer":
          await demarrerTrajet(trajetId);
          break;
        case "terminer":
          await terminerTrajet(trajetId);
          break;
        case "annuler":
          await annulerTrajet(trajetId);
          break;
      }
    });
  }

  function afficherParticipants(participants) {
    const container = document.getElementById("listeParticipants");

    if (participants.length === 0) {
      container.innerHTML = `
      <div class="empty-participants">
        <span class="material-symbols-outlined" style="font-size:3rem;color:var(--noir-secondaire);">person_off</span>
        <p>Aucun participant pour le moment</p>
      </div>
    `;
      return;
    }

    container.innerHTML = participants
      .map(
        (p) => `
    <div class="participant-item" onclick="window.location.href='/ecoride/php/index.php?page=details&user=${
      p.id
    }'">
      <img src="${p.photo || "/ecoride/assets/images/default-avatar.png"}" 
           alt="${p.prenom}" 
           class="participant-photo"
           onerror="this.src='/ecoride/assets/images/default-avatar.png'">
      <div class="participant-info">
        <div class="participant-nom">${p.prenom} ${p.nom}</div>
        <div class="participant-note">
          <span class="material-symbols-outlined" style="font-size:1rem;color:var(--vert-clair);">star</span>
          ${p.note_moyenne || "Nouveau"} ${
          p.nombre_avis ? `(${p.nombre_avis} avis)` : ""
        }
        </div>
      </div>
      <span class="material-symbols-outlined" style="color:var(--vert-fonce);">arrow_forward</span>
    </div>
  `
      )
      .join("");
  }

  // ===========================================
  // GESTION AVIS REFUSÉS
  // ===========================================
  document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-avis-refuse")) {
      const button = e.target.closest(".btn-avis-refuse");
      const motif = button.dataset.motif || "Aucun motif fourni";

      const modalOverlay = document.createElement("div");
      modalOverlay.className = "modal-overlay";
      modalOverlay.style.display = "flex";

      modalOverlay.innerHTML = `
        <div class="modal modal-refus-avis">
          <div class="modal-header">
            <h3>Avis refusé</h3>
            <button class="close-modal">&times;</button>
          </div>
          <div class="modal-body">
            <p style="color: var(--noir-secondaire); line-height: 1.6;">
              Votre avis pour ce trajet a été refusé par notre équipe de modération.
            </p>
            <div class="motif-refus-container">
              <strong>Motif du refus :</strong>
              <p>${motif}</p>
            </div>
          </div>
        </div>
      `;

      document.body.appendChild(modalOverlay);

      modalOverlay
        .querySelector(".close-modal")
        .addEventListener("click", () => {
          modalOverlay.remove();
        });

      modalOverlay.addEventListener("click", (e) => {
        if (e.target === modalOverlay) modalOverlay.remove();
      });
    }
  });

  // ===========================================
  // INITIALISER LA PAGE AU CHARGEMENT
  // ===========================================

  initialiserPage();
});
