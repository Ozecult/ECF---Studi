document.addEventListener("DOMContentLoaded", function () {
  // ===== GESTION DU FORMULAIRE DE CRÉATION D'EMPLOYÉ =====
  const formEmploye = document.querySelector(".creation-employe form");
  const btnCreer = formEmploye?.querySelector('button[aria-label="creer"]');

  // Ajouter les événements de validation en temps réel
  const champsEmploye = ["nom", "prenom", "email", "password", "password2"];

  champsEmploye.forEach((champ) => {
    const input = document.getElementById(champ);
    if (input) {
      input.addEventListener("blur", () => validerChampEmploye(champ));
      input.addEventListener("input", () => {
        if (champ === "password") {
          verifierForceMotDePasse();
          const confirm = document.getElementById("password2");
          if (confirm.value) {
            verifierConfirmationMotDePasse();
          }
        }
        if (champ === "password2") {
          verifierConfirmationMotDePasse();
        }
      });
    }
  });

  // Gestion de la soumission du formulaire employé
  formEmploye?.addEventListener("submit", async function (e) {
    e.preventDefault();
    if (await validerFormulaireEmploye()) {
      await creerCompteEmploye();
    }
  });

  // ===== FONCTIONS DE VALIDATION =====
  function validerChampEmploye(nomChamp) {
    const input = document.getElementById(nomChamp);
    if (!input) return false;

    let isValid = true;
    let message = "";

    // Nettoyer les styles précédents
    input.classList.remove("field-error", "field-success");

    switch (nomChamp) {
      case "nom":
      case "prenom":
        if (!input.value.trim()) {
          message = `Le ${nomChamp} est obligatoire`;
          isValid = false;
        } else if (input.value.trim().length < 2) {
          message = `Le ${nomChamp} doit contenir au moins 2 caractères`;
          isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\-\s']+$/.test(input.value.trim())) {
          message = `Le ${nomChamp} ne doit contenir que des lettres`;
          isValid = false;
        }
        break;

      case "email":
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!input.value.trim()) {
          message = "L'email est obligatoire";
          isValid = false;
        } else if (!emailRegex.test(input.value.trim())) {
          message = "Format d'email invalide";
          isValid = false;
        }
        break;

      case "password":
        if (!input.value) {
          message = "Le mot de passe est obligatoire";
          isValid = false;
        } else if (input.value.length < 8) {
          message = "Minimum 8 caractères";
          isValid = false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(input.value)) {
          message =
            "Doit contenir au moins : 1 minuscule, 1 majuscule, 1 chiffre";
          isValid = false;
        }
        break;

      case "password2":
        const password = document.getElementById("password")?.value;
        if (!input.value) {
          message = "Veuillez confirmer le mot de passe";
          isValid = false;
        } else if (input.value !== password) {
          message = "Les mots de passe ne correspondent pas";
          isValid = false;
        }
        break;
    }

    // Appliquer les styles
    if (isValid) {
      input.classList.add("field-success");
      masquerErreurChamp(nomChamp);
    } else {
      input.classList.add("field-error");
      afficherErreurChamp(nomChamp, message);
    }

    return isValid;
  }

  function verifierForceMotDePasse() {
    const password = document.getElementById("password")?.value || "";
    let score = 0;
    let message = "";
    let classe = "";

    if (password.length >= 8) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[!@#$%^&*]/.test(password)) score++;

    switch (score) {
      case 0:
      case 1:
      case 2:
        message = "Mot de passe faible";
        classe = "strength-weak";
        break;
      case 3:
      case 4:
        message = "Mot de passe moyen";
        classe = "strength-medium";
        break;
      case 5:
        message = "Mot de passe fort";
        classe = "strength-strong";
        break;
    }

    // Créer ou mettre à jour l'indicateur de force
    let strengthDiv = document.querySelector(".password-strength-admin");
    if (!strengthDiv) {
      strengthDiv = document.createElement("div");
      strengthDiv.className = "password-strength-admin";
      const passwordInput = document.getElementById("password");
      passwordInput?.parentNode.appendChild(strengthDiv);
    }

    strengthDiv.textContent = message;
    strengthDiv.className = `password-strength-admin ${classe}`;
  }

  function verifierConfirmationMotDePasse() {
    const password = document.getElementById("password")?.value;
    const confirm = document.getElementById("password2")?.value;

    if (confirm && password && confirm.length > 0) {
      clearTimeout(window.passwordAdminTimeout);
      window.passwordAdminTimeout = setTimeout(() => {
        validerChampEmploye("password2");
      }, 500);
    }
  }

  async function validerFormulaireEmploye() {
    let isValid = true;
    champsEmploye.forEach((champ) => {
      if (!validerChampEmploye(champ)) {
        isValid = false;
      }
    });
    return isValid;
  }

  async function creerCompteEmploye() {
    if (!btnCreer || !formEmploye) return;

    btnCreer.disabled = true;
    btnCreer.textContent = "Création en cours...";

    const formData = new FormData(formEmploye);
    const data = Object.fromEntries(formData.entries());

    try {
      const result = await simulerCreationEmploye(data);
      afficherMessageAdmin("Compte employé créé avec succès !", "success");
      formEmploye.reset();
      resetFormulaireEmploye();

      // Ajouter le nouvel employé à la liste
      ajouterEmployeALaListe(data);
    } catch (error) {
      if (error.field === "email") {
        afficherErreurChamp("email", error.message);
      } else {
        afficherMessageAdmin(
          error.message || "Erreur lors de la création",
          "error"
        );
      }
    } finally {
      btnCreer.disabled = false;
      btnCreer.textContent = "Créer le compte";
    }
  }

  function simulerCreationEmploye(data) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        if (data.email === "admin@ecoride.fr") {
          reject({
            message: "Cet email est déjà utilisé",
            field: "email",
          });
        } else if (Math.random() > 0.9) {
          reject({ message: "Erreur serveur, veuillez réessayer" });
        } else {
          resolve({ success: true });
        }
      }, 1000);
    });
  }

  // ===== GESTION DES STATISTIQUES AMÉLIORÉES =====
  function initStatistiques() {
    creerWidgetStatistiques();
    ajouterSelecteurPeriode();
  }

  function creerWidgetStatistiques() {
    const sectionStats = document.querySelector(".statistiques");
    if (!sectionStats) return;

    // Supprimer les anciens graphiques s'ils existent
    const anciennesStats = sectionStats.querySelectorAll(".graphique");
    anciennesStats.forEach((graph) => {
      const p = graph.querySelector("p");
      if (p) {
        graph.innerHTML = "";
        graph.appendChild(p);
      }
    });

    // Créer les widgets de statistiques
    creerWidgetCovoiturages();
    creerWidgetCredits();
  }

  function ajouterSelecteurPeriode() {
    const sectionStats = document.querySelector(".statistiques");
    const h2 = sectionStats?.querySelector("h2");

    if (!h2 || document.querySelector(".selecteur-periode")) return;

    const selecteurDiv = document.createElement("div");
    selecteurDiv.className = "selecteur-periode";
    selecteurDiv.style.cssText =
      "margin: 15px 0; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;";

    selecteurDiv.innerHTML = `
      <label for="periode-select" style="font-weight: 500; color: var(--vert-fonce);">Période :</label>
      <select id="periode-select" style="padding: 8px 12px; border: 1px solid var(--vert-clair); border-radius: 4px; background: white; color: var(--noir-primaire);">
        <option value="jour">Aujourd'hui</option>
        <option value="semaine">Cette semaine</option>
        <option value="mois">Ce mois</option>
        <option value="trimestre">Ce trimestre</option>
      </select>
      <button id="actualiser-stats" style="padding: 8px 16px; background: var(--vert-clair); color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s;">
        Actualiser
      </button>
    `;

    h2.insertAdjacentElement("afterend", selecteurDiv);

    // Ajouter les effets hover avec CSS inline
    const btnActualiser = selecteurDiv.querySelector("#actualiser-stats");
    if (btnActualiser) {
      btnActualiser.addEventListener("mouseenter", () => {
        btnActualiser.style.backgroundColor = "var(--vert-fonce)";
      });
      btnActualiser.addEventListener("mouseleave", () => {
        btnActualiser.style.backgroundColor = "var(--vert-clair)";
      });
    }

    // Gérer le changement de période
    document
      .getElementById("periode-select")
      ?.addEventListener("change", changerPeriode);
    document
      .getElementById("actualiser-stats")
      ?.addEventListener("click", actualiserStatistiques);
  }

  function creerWidgetCovoiturages() {
    const container = document.getElementById("graph-covoiturages");
    if (!container) return;

    const data = obtenirDonneesCovoiturages("jour");
    creerWidget(container, data, "var(--vert-fonce)", "Covoiturages");
  }

  function creerWidgetCredits() {
    const container = document.getElementById("graph-credits");
    if (!container) return;

    const data = obtenirDonneesCredits("jour");
    creerWidget(container, data, "var(--vert-fonce)", "Crédits");
  }

  function creerWidget(container, data, couleur, type) {
    console.log("creerWidget appelée pour", type);
    const widgetDiv = document.createElement("div");
    widgetDiv.className = "widget-stats";

    // Styles responsive basés sur la taille de l'écran
    const isMobile = window.innerWidth <= 480;
    const isTablet = window.innerWidth <= 768;

    const paddingValue = isMobile ? "1rem" : isTablet ? "1.5rem" : "2rem";
    const minHeightValue = isMobile ? "120px" : isTablet ? "150px" : "180px";

    widgetDiv.style.cssText = `
    background: linear-gradient(135deg, var(--vert-fond-structure), var(--vert-fond-de-page));
    padding: ${paddingValue};
    margin: 0;
    position: relative;
    min-height: ${minHeightValue};
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    display: block;
  `;

    const total = data.values.reduce((sum, val) => sum + val, 0);
    const moyenne = Math.round(total / data.values.length);
    const maximum = Math.max(...data.values);

    // Layout responsive pour le contenu
    const headerLayout = isMobile ? "column" : "row";
    const gridColumns = isMobile ? "1fr" : isTablet ? "1fr 1fr" : "1fr 1fr 1fr";
    const fontSize = isMobile ? "20px" : "24px";
    const titleFontSize = isMobile ? "14px" : "16px";

    widgetDiv.innerHTML = `
    <div style="display: flex; flex-direction: ${headerLayout}; justify-content: space-between; align-items: center; margin-bottom: 15px; gap: ${
      isMobile ? "8px" : "0"
    };">
      <h3 style="margin: 0; color: ${couleur}; font-size: ${titleFontSize};">${
      container.querySelector("p")?.textContent || type
    }</h3>
      <span style="background: ${couleur}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
        Total: ${total}
      </span>
    </div>
    
    <div style="display: grid; grid-template-columns: ${gridColumns}; gap: ${
      isMobile ? "10px" : "15px"
    }; margin-bottom: 15px;">
      <div style="text-align: center;">
        <div style="font-size: ${fontSize}; font-weight: bold; color: ${couleur};">${
      data.values[data.values.length - 1]
    }</div>
        <div style="font-size: 12px; color: var(--noir-secondaire); text-transform: uppercase;">Aujourd'hui</div>
      </div>
      <div style="text-align: center;">
        <div style="font-size: ${fontSize}; font-weight: bold; color: ${couleur};">${moyenne}</div>
        <div style="font-size: 12px; color: var(--noir-secondaire); text-transform: uppercase;">Moyenne</div>
      </div>
      ${
        !isMobile || gridColumns === "1fr"
          ? `
      <div style="text-align: center;">
        <div style="font-size: ${fontSize}; font-weight: bold; color: ${couleur};">${maximum}</div>
        <div style="font-size: 12px; color: var(--noir-secondaire); text-transform: uppercase;">Maximum</div>
      </div>
      `
          : ""
      }
    </div>
    
    <div style="display: flex; gap: 2px; height: 6px; background: var(--vert-fond-de-page); border-radius: 3px; overflow: hidden; margin-bottom: 10px;">
      ${data.values
        .map(
          (val) => `
        <div style="flex: 1; background: ${couleur}; opacity: ${
            0.3 + (val / maximum) * 0.7
          };"></div>
      `
        )
        .join("")}
    </div>
    
    <div style="margin-top: 10px; font-size: ${
      isMobile ? "10px" : "11px"
    }; color: var(--noir-secondaire); text-align: center; line-height: 1.4;">
      ${data.labels.join(isMobile ? " | " : " • ")}
    </div>
  `;

    // Remplacer le contenu du container en préservant la largeur complète
    const p = container.querySelector("p");
    container.style.padding = "0";
    container.classList.add("has-widget-stats");
    container.innerHTML = "";
    if (p) container.appendChild(p);
    container.appendChild(widgetDiv);
  }

  // Fonction pour gérer le redimensionnement
  function handleResize() {
    // Réinitialiser les widgets lors du redimensionnement
    const periodeSelect = document.getElementById("periode-select");
    if (periodeSelect) {
      const periode = periodeSelect.value || "jour";

      // Recréer les widgets avec les nouvelles dimensions
      const covoituragesContainer =
        document.getElementById("graph-covoiturages");
      const creditsContainer = document.getElementById("graph-credits");

      if (covoituragesContainer) {
        const data = obtenirDonneesCovoiturages(periode);
        creerWidget(
          covoituragesContainer,
          data,
          "var(--vert-fonce)",
          "Covoiturages"
        );
      }

      if (creditsContainer) {
        const data = obtenirDonneesCredits(periode);
        creerWidget(creditsContainer, data, "var(--vert-fonce)", "Crédits");
      }
    }
  }

  // Ajouter l'écouteur de redimensionnement avec debounce
  let resizeTimeout;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(handleResize, 150);
  });

  // Fonction améliorée pour créer les widgets de statistiques
  function creerWidgetStatistiques() {
    const sectionStats = document.querySelector(".statistiques");
    if (!sectionStats) return;

    // S'assurer que la section utilise toute la largeur
    sectionStats.style.width = "100%";
    sectionStats.style.boxSizing = "border-box";

    // Supprimer les anciens graphiques s'ils existent
    const anciennesStats = sectionStats.querySelectorAll(".graphique");
    anciennesStats.forEach((graph) => {
      const p = graph.querySelector("p");
      if (p) {
        graph.innerHTML = "";
        graph.appendChild(p);
        // S'assurer que le container du graphique utilise toute la largeur
        graph.style.width = "100%";
        graph.style.boxSizing = "border-box";
      }
    });

    // Créer les widgets de statistiques
    creerWidgetCovoiturages();
    creerWidgetCredits();
  }

  // Fonction améliorée pour le changement de période
  function changerPeriode() {
    const periodeSelect = document.getElementById("periode-select");
    const periode = periodeSelect?.value;
    if (!periode) return;

    // Mettre à jour les widgets avec responsivité
    const covoituragesContainer = document.getElementById("graph-covoiturages");
    const creditsContainer = document.getElementById("graph-credits");

    if (covoituragesContainer) {
      const data = obtenirDonneesCovoiturages(periode);
      creerWidget(
        covoituragesContainer,
        data,
        "var(--vert-fonce)",
        "Covoiturages"
      );
    }

    if (creditsContainer) {
      const data = obtenirDonneesCredits(periode);
      creerWidget(creditsContainer, data, "var(--vert-fonce)", "Crédits");
    }

    // Mettre à jour le total général
    mettreAJourTotalCredits(periode);
  }

  function obtenirDonneesCovoiturages(periode) {
    const donnees = {
      jour: {
        labels: ["6h", "8h", "10h", "12h", "14h", "16h", "18h"],
        values: [2, 8, 3, 5, 4, 12, 6],
      },
      semaine: {
        labels: ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
        values: [12, 15, 8, 22, 18, 25, 14],
      },
      mois: {
        labels: ["S1", "S2", "S3", "S4"],
        values: [89, 94, 67, 78],
      },
      trimestre: {
        labels: ["Jan", "Fév", "Mar"],
        values: [328, 285, 342],
      },
    };
    return donnees[periode] || donnees["jour"];
  }

  function obtenirDonneesCredits(periode) {
    const donnees = {
      jour: {
        labels: ["6h", "8h", "10h", "12h", "14h", "16h", "18h"],
        values: [40, 160, 60, 100, 80, 240, 120],
      },
      semaine: {
        labels: ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
        values: [240, 300, 160, 440, 360, 500, 280],
      },
      mois: {
        labels: ["S1", "S2", "S3", "S4"],
        values: [1780, 1880, 1340, 1560],
      },
      trimestre: {
        labels: ["Jan", "Fév", "Mar"],
        values: [6560, 5700, 6840],
      },
    };
    return donnees[periode] || donnees["jour"];
  }

  function changerPeriode() {
    const periodeSelect = document.getElementById("periode-select");
    const periode = periodeSelect?.value;
    if (!periode) return;

    // Mettre à jour les widgets
    const covoituragesContainer = document.getElementById("graph-covoiturages");
    const creditsContainer = document.getElementById("graph-credits");

    if (covoituragesContainer) {
      const data = obtenirDonneesCovoiturages(periode);
      creerWidget(
        covoituragesContainer,
        data,
        "var(--vert-fonce)",
        "Covoiturages"
      );
    }

    if (creditsContainer) {
      const data = obtenirDonneesCredits(periode);
      creerWidget(creditsContainer, data, "var(--vert-fonce)", "Crédits");
    }

    // Mettre à jour le total général si nécessaire
    mettreAJourTotalCredits(periode);
  }

  function actualiserStatistiques() {
    const btn = document.getElementById("actualiser-stats");
    if (!btn) return;

    const texteOriginal = btn.textContent;
    btn.textContent = "Actualisation...";
    btn.disabled = true;
    btn.style.backgroundColor = "var(--noir-secondaire)";

    setTimeout(() => {
      changerPeriode(); // Recharger les données
      btn.textContent = texteOriginal;
      btn.disabled = false;
      btn.style.backgroundColor = "var(--vert-clair)";

      // Message de confirmation
      afficherMessageAdmin("Statistiques actualisées !", "success");
    }, 1000);
  }

  function mettreAJourTotalCredits(periode) {
    const totalElement = document.querySelector(".statistiques p strong");
    if (!totalElement) return;

    const totaux = {
      jour: "892",
      semaine: "2280",
      mois: "6560",
      trimestre: "19100",
    };

    const suffixes = {
      jour: "du jour",
      semaine: "de la semaine",
      mois: "du mois",
      trimestre: "du trimestre",
    };

    totalElement.textContent = `Total de crédits générés ${suffixes[periode]} : ${totaux[periode]} crédits`;
  }

  // ===== GESTION DE LA LISTE DES UTILISATEURS =====
  function initGestionUtilisateurs() {
    // Ajouter la recherche
    ajouterRechercheUtilisateurs();

    // Gérer les boutons de suspension
    const btnsSuspendre = document.querySelectorAll(
      'button[aria-label="Suspendre"]'
    );
    btnsSuspendre.forEach((btn, index) => {
      btn.addEventListener("click", (e) => suspendreUtilisateur(e, index));
    });
  }

  function ajouterRechercheUtilisateurs() {
    const section = document.querySelector(".liste-utilisateur");
    if (!section) return;

    const h2 = section.querySelector("h2");
    if (!h2) return;

    const searchDiv = document.createElement("div");
    searchDiv.className = "recherche-utilisateur";
    searchDiv.style.cssText = "margin: 15px 0; position: relative;";

    searchDiv.innerHTML = `
      <input type="text" id="recherche-user" placeholder="Rechercher un utilisateur..." 
             style="width: 100%; padding: 10px 40px 10px 15px; border: 1px solid var(--vert-clair); border-radius: 5px; background: var(--vert-fond-de-page); color: var(--noir-primaire);">
      <span class="material-symbols-outlined" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: var(--vert-fonce);">search</span>
    `;

    h2.insertAdjacentElement("afterend", searchDiv);

    // Gérer la recherche
    const inputRecherche = document.getElementById("recherche-user");
    inputRecherche?.addEventListener("input", (e) => {
      const terme = e.target.value.toLowerCase();
      filtrerUtilisateurs(terme);
    });
  }

  function filtrerUtilisateurs(terme) {
    // Filtrage du tableau desktop
    const lignes = document.querySelectorAll(".liste-utilisateur tbody tr");
    lignes.forEach((ligne) => {
      const nom = ligne.querySelector("td")?.textContent.toLowerCase() || "";
      ligne.style.display = nom.includes(terme) ? "" : "none";
    });

    // Filtrage des cartes mobile
    const cartes = document.querySelectorAll(".user-mobile .user-card");
    cartes.forEach((carte) => {
      const nom =
        carte.querySelector(".value")?.textContent.toLowerCase() || "";
      carte.style.display = nom.includes(terme) ? "" : "none";
    });
  }

  function suspendreUtilisateur(event, index) {
    const btn = event.target;
    const ligne = btn.closest("tr") || btn.closest(".user-card");
    const nomUtilisateur = ligne
      .querySelector("td:first-child, .value")
      ?.textContent.trim();

    if (confirm(`Êtes-vous sûr de vouloir suspendre ${nomUtilisateur} ?`)) {
      btn.disabled = true;
      btn.textContent = "Suspension...";
      btn.style.backgroundColor = "var(--noir-secondaire)";

      // Simulation de l'appel API
      setTimeout(() => {
        if (Math.random() > 0.1) {
          // 90% de succès
          btn.textContent = "Réactiver";
          btn.setAttribute("aria-label", "Réactiver");
          btn.style.backgroundColor = "var(--vert-clair)";
          ligne.style.opacity = "0.6";

          // Changer l'événement pour la réactivation
          btn.onclick = () => reactiverUtilisateur(btn, ligne, nomUtilisateur);

          afficherMessageAdmin(
            `${nomUtilisateur} a été suspendu avec succès`,
            "success"
          );
        } else {
          btn.textContent = "Suspendre";
          btn.disabled = false;
          btn.style.backgroundColor = "";
          afficherMessageAdmin("Erreur lors de la suspension", "error");
        }
      }, 1000);
    }
  }

  function reactiverUtilisateur(btn, ligne, nomUtilisateur) {
    if (confirm(`Êtes-vous sûr de vouloir réactiver ${nomUtilisateur} ?`)) {
      btn.disabled = true;
      btn.textContent = "Réactivation...";
      btn.style.backgroundColor = "var(--noir-secondaire)";

      setTimeout(() => {
        btn.textContent = "Suspendre";
        btn.setAttribute("aria-label", "Suspendre");
        btn.style.backgroundColor = "";
        ligne.style.opacity = "1";
        btn.disabled = false;

        // Remettre l'événement de suspension
        const tbody = btn.closest("tbody");
        const userMobile = btn.closest(".user-mobile");
        const container = tbody || userMobile;
        if (container) {
          const index = Array.from(container.children).indexOf(ligne);
          btn.onclick = (e) => suspendreUtilisateur(e, index);
        }

        afficherMessageAdmin(
          `${nomUtilisateur} a été réactivé avec succès`,
          "success"
        );
      }, 1000);
    }
  }

  function ajouterEmployeALaListe(data) {
    const tbody = document.querySelector(".liste-utilisateur tbody");
    const userMobile = document.querySelector(".user-mobile");

    if (tbody) {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td><strong>${data.prenom} ${data.nom}</strong></td>
        <td><strong>Employé</strong></td>
        <td><button type="button" aria-label="Suspendre" style="background: var(--vert-clair); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Suspendre</button></td>
      `;
      tbody.appendChild(tr);

      // Ajouter l'événement au nouveau bouton
      const nouveauBtn = tr.querySelector("button");
      if (nouveauBtn) {
        nouveauBtn.addEventListener("click", (e) => {
          const index = Array.from(tbody.children).indexOf(tr);
          suspendreUtilisateur(e, index);
        });
      }
    }

    if (userMobile) {
      const carte = document.createElement("div");
      carte.className = "user-card";
      carte.innerHTML = `
        <div class="user-info">
          <div class="user-info-header">
            <span class="material-symbols-outlined">person</span>
            <span class="label">Utilisateur</span>
          </div>
          <div class="value"><strong>${data.prenom} ${data.nom}</strong></div>
        </div>
        <div class="user-info">
          <div class="user-info-header">
            <span class="material-symbols-outlined">assignment_ind</span>
            <span class="label">Rôle</span>
          </div>
          <div class="value"><strong>Employé</strong></div>
        </div>
        <div class="user-info">
          <div class="user-info-header">
            <span class="material-symbols-outlined">touch_app</span>
            <span class="label">Action</span>
          </div>
          <div class="value">
            <button type="button" aria-label="Suspendre" style="background: var(--vert-clair); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Suspendre</button>
          </div>
        </div>
      `;
      userMobile.appendChild(carte);

      // Ajouter l'événement au nouveau bouton mobile
      const nouveauBtnMobile = carte.querySelector("button");
      if (nouveauBtnMobile) {
        nouveauBtnMobile.addEventListener("click", (e) => {
          const index = Array.from(userMobile.children).indexOf(carte);
          suspendreUtilisateur(e, index);
        });
      }
    }
  }

  // ===== FONCTIONS UTILITAIRES =====
  function afficherErreurChamp(nomChamp, message) {
    let errorDiv = document.querySelector(`#error-${nomChamp}-admin`);
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = `error-${nomChamp}-admin`;
      errorDiv.className = "error-text";
      errorDiv.style.cssText =
        "color: #f44336; font-size: 14px; margin-top: 5px; display: none;";
      const input = document.getElementById(nomChamp);
      input?.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = "block";
  }

  function masquerErreurChamp(nomChamp) {
    const errorDiv = document.querySelector(`#error-${nomChamp}-admin`);
    if (errorDiv) {
      errorDiv.style.display = "none";
    }
  }

  function afficherMessageAdmin(message, type) {
    let messageDiv = document.querySelector(".message-admin");
    if (!messageDiv) {
      messageDiv = document.createElement("div");
      messageDiv.className = "message-admin";
      const main = document.querySelector("main");
      if (main && main.firstElementChild) {
        main.insertBefore(
          messageDiv,
          main.firstElementChild.nextElementSibling || main.firstElementChild
        );
      }
    }

    const styles =
      type === "success"
        ? `background: var(--vert-fond-structure); color: var(--vert-fonce); border: 1px solid var(--vert-clair);`
        : `background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;`;

    messageDiv.style.cssText = `
      padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: 500;
      ${styles}
    `;

    messageDiv.textContent = message;
    messageDiv.style.display = "block";

    setTimeout(
      () => {
        messageDiv.style.display = "none";
      },
      type === "success" ? 5000 : 7000
    );
  }

  function resetFormulaireEmploye() {
    document
      .querySelectorAll(".field-error, .field-success")
      .forEach((field) => {
        field.classList.remove("field-error", "field-success");
      });

    document
      .querySelectorAll('[id^="error-"][id$="-admin"]')
      .forEach((error) => {
        error.style.display = "none";
      });

    const strengthDiv = document.querySelector(".password-strength-admin");
    if (strengthDiv) {
      strengthDiv.textContent = "";
      strengthDiv.className = "password-strength-admin";
    }
  }

  // ===== INITIALISATION =====
  initStatistiques();
  initGestionUtilisateurs();
});

// ===== STYLES CSS POUR LES COMPOSANTS DYNAMIQUES =====
// Ajouter les styles nécessaires pour le bon fonctionnement
const styles = document.createElement("style");
styles.textContent = `
  .field-error {
    border-color: #f44336 !important;
    background-color: #fff5f5 !important;
  }
  
  .field-success {
    border-color: var(--vert-clair) !important;
    background-color: var(--vert-fond-de-page) !important;
  }
  
  .password-strength-admin {
    font-size: 12px;
    margin-top: 5px;
    padding: 4px 8px;
    border-radius: 3px;
    transition: all 0.3s ease;
  }
  
  .password-strength-admin.strength-weak {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
  }
  
  .password-strength-admin.strength-medium {
    background-color: #fff3e0;
    color: #ef6c00;
    border: 1px solid #ffcc02;
  }
  
  .password-strength-admin.strength-strong {
    background-color: var(--vert-fond-structure);
    color: var(--vert-fonce);
    border: 1px solid var(--vert-clair);
  }
  
  .widget-stats:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
  }
  
  .selecteur-periode select:focus,
  .recherche-utilisateur input:focus {
    outline: none;
    border-color: var(--vert-fonce);
    box-shadow: 0 0 0 2px var(--vert-fond-structure);
  }
  
  .user-card {
    transition: all 0.3s ease;
  }
  
  .user-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .message-admin {
    animation: slideIn 0.3s ease-out;
  }
  
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  button:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
  }
  
  button:active {
    transform: translateY(0);
  }
  
  button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
  }
  
  .error-text {
    animation: fadeIn 0.3s ease-out;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-5px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @media (max-width: 768px) {
    .selecteur-periode {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 8px !important;
    }
    
    .selecteur-periode select,
    .selecteur-periode button {
      width: 100%;
    }
    
    .widget-stats {
      padding: 1rem !important;
      min-height: 150px !important;
    }
    
    .widget-stats > div:first-child {
      flex-direction: column;
      align-items: center !important;
      gap: 10px;
    }
  }
  
  @media (max-width: 480px) {
    .widget-stats {
      padding: 1rem !important;
      min-height: 120px !important;
    }
    
    .widget-stats h3 {
      font-size: 14px !important;
    }
  }
  
  /* Gérer le redimensionnement de la fenêtre */
  window.addEventListener('resize', function() {
    // Réinitialiser les widgets lors du redimensionnement
    setTimeout(() => {
      changerPeriode();
    }, 100);
  });
`;
document.head.appendChild(styles);
// Gérer le redimensionnement
window.addEventListener("resize", function () {
  setTimeout(() => changerPeriode(), 100);
});
