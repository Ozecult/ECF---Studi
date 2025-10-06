// -------------------------
// UTILITAIRES GLOBAUX
// -------------------------
function qs(selector, root = document) {
  return root.querySelector(selector);
}
function qsa(selector, root = document) {
  return Array.from((root || document).querySelectorAll(selector));
}

// -------------------------
// VALIDATION CREATION EMPLOYE
// -------------------------
const champsEmploye = ["nom", "prenom", "email", "password", "password2"];

function validerChampEmploye(nomChamp) {
  const input = document.getElementById(nomChamp);
  if (!input) return false;

  let isValid = true;
  let message = "";
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
      const passwordVal = document.getElementById("password")?.value;
      if (!input.value) {
        message = "Veuillez confirmer le mot de passe";
        isValid = false;
      } else if (input.value !== passwordVal) {
        message = "Les mots de passe ne correspondent pas";
        isValid = false;
      }
      break;
  }

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
  if (password.length >= 8) score++;
  if (/[a-z]/.test(password)) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/\d/.test(password)) score++;
  if (/[!@#$%^&*]/.test(password)) score++;

  let message = "",
    classe = "";
  if (score <= 2) {
    message = "Mot de passe faible";
    classe = "strength-weak";
  } else if (score <= 4) {
    message = "Mot de passe moyen";
    classe = "strength-medium";
  } else {
    message = "Mot de passe fort";
    classe = "strength-strong";
  }

  let strengthDiv = qs(".password-strength-admin");
  if (!strengthDiv) {
    const passwordInput = document.getElementById("password");
    if (!passwordInput) return;
    strengthDiv = document.createElement("div");
    strengthDiv.className = "password-strength-admin";
    passwordInput.parentNode.appendChild(strengthDiv);
  }
  strengthDiv.textContent = message;
  strengthDiv.className = `password-strength-admin ${classe}`;
}

function verifierConfirmationMotDePasse() {
  const password = document.getElementById("password")?.value;
  const confirm = document.getElementById("password2")?.value;
  if (confirm && password) {
    clearTimeout(window.passwordAdminTimeout);
    window.passwordAdminTimeout = setTimeout(
      () => validerChampEmploye("password2"),
      500
    );
  }
}

async function validerFormulaireEmploye() {
  let isValid = true;
  champsEmploye.forEach((champ) => {
    if (!validerChampEmploye(champ)) isValid = false;
  });
  return isValid;
}

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
  if (errorDiv) errorDiv.style.display = "none";
}

function afficherMessageAdmin(message, type = "success") {
  let messageDiv = qs(".message-admin");
  if (!messageDiv) {
    messageDiv = document.createElement("div");
    messageDiv.className = "message-admin";
    const main = qs("main");
    if (main && main.firstElementChild) {
      main.insertBefore(
        messageDiv,
        main.firstElementChild.nextElementSibling || main.firstElementChild
      );
    } else if (document.body) {
      document.body.prepend(messageDiv);
    }
  }

  const styles =
    type === "success"
      ? `background: var(--vert-fond-structure); color: var(--vert-fonce); border: 1px solid var(--vert-clair);`
      : `background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;`;

  messageDiv.style.cssText = `padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: 500; ${styles}`;
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
  qsa(".field-error, .field-success").forEach((f) =>
    f.classList.remove("field-error", "field-success")
  );
  qsa('[id^="error-"][id$="-admin"]').forEach(
    (err) => (err.style.display = "none")
  );
  const s = qs(".password-strength-admin");
  if (s) {
    s.textContent = "";
    s.className = "password-strength-admin";
  }
}
// -------------------------
// GRAPHIQUES CHART.JS
// -------------------------
let chartCovoiturages = null;
let chartCredits = null;

function obtenirDonneesCovoiturages() {
  // Générer les 7 derniers jours
  const jours = [];
  const today = new Date();
  for (let i = 6; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(today.getDate() - i);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    jours.push(`${yyyy}-${mm}-${dd}`);
  }

  // Mapper les données backend
  const backend = (window.statsData && window.statsData.trajets_par_jour) || [];
  const map = {};
  backend.forEach((r) => {
    const dateStr = (r.date || "").slice(0, 10);
    map[dateStr] = parseInt(r.total || 0, 10) || 0;
  });

  const labels = jours.map((d) =>
    new Date(d + "T00:00:00").toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "short",
    })
  );
  const values = jours.map((d) => map[d] ?? 0);

  return { labels, values };
}

function obtenirDonneesCredits() {
  // build last 7 days array (YYYY-MM-DD)
  const jours = [];
  const today = new Date();
  for (let i = 6; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(today.getDate() - i);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    jours.push(`${yyyy}-${mm}-${dd}`);
  }

  // map backend data into an object { "YYYY-MM-DD": total }
  const backend = (window.statsData && window.statsData.credits_par_jour) || [];
  const map = {};
  backend.forEach((r) => {
    // adapter selon le format réel: r.date ou r.jour...
    const dateStr = (r.date || r.jour || "").slice(0, 10);
    map[dateStr] = parseInt(r.total || r.tot || 0, 10) || 0;
  });

  const labels = jours.map((d) =>
    new Date(d + "T00:00:00").toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "short",
    })
  );
  const values = jours.map((d) => map[d] ?? 0);

  return { labels, values };
}

function creerGraphiqueCovoiturages() {
  const ctx = document.getElementById("graph-covoiturages");
  if (!ctx) return;

  const data = obtenirDonneesCovoiturages();

  if (chartCovoiturages) {
    chartCovoiturages.destroy();
  }

  chartCovoiturages = new Chart(ctx, {
    type: "line",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Nombre de trajets",
          data: data.values,
          borderColor: "rgb(74, 107, 74)",
          backgroundColor: "rgba(74, 107, 74, 0.1)",
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 7,
          pointBackgroundColor: "rgb(74, 107, 74)",
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      aspectRatio: 2,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: "rgba(74, 107, 74, 0.9)",
          padding: 12,
          titleFont: { size: 14, weight: "bold" },
          bodyFont: { size: 13 },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            color: "#4A6B4A",
            font: { size: 12 },
          },
          grid: {
            color: "rgba(74, 107, 74, 0.1)",
          },
        },
        x: {
          ticks: {
            color: "#4A6B4A",
            font: { size: 11 },
          },
          grid: {
            display: false,
          },
        },
      },
    },
  });
}

function creerGraphiqueCredits() {
  const ctx = document.getElementById("graph-credits");
  if (!ctx) return;

  const data = obtenirDonneesCredits();

  if (chartCredits) {
    chartCredits.destroy();
  }

  chartCredits = new Chart(ctx, {
    type: "bar",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Crédits générés",
          data: data.values,
          backgroundColor: "rgba(74, 107, 74, 0.7)",
          borderColor: "rgb(74, 107, 74)",
          borderWidth: 2,
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      aspectRatio: 2,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: "rgba(74, 107, 74, 0.9)",
          padding: 12,
          callbacks: {
            label: function (context) {
              return context.parsed.y + " crédits";
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: "#4A6B4A",
            font: { size: 12 },
          },
          grid: {
            color: "rgba(74, 107, 74, 0.1)",
          },
        },
        x: {
          ticks: {
            color: "#4A6B4A",
            font: { size: 11 },
          },
          grid: {
            display: false,
          },
        },
      },
    },
  });
}

function initStatistiques() {
  creerGraphiqueCovoiturages();
  creerGraphiqueCredits();
}

// -------------------------
// GESTION UTILISATEURS (DÉLÉGATION)
// -------------------------
function initDelegationGestionUtilisateurs() {
  const container =
    qs(".liste-utilisateur") || qs(".users-table") || qs(".user-mobile");
  if (!container) return;

  container.addEventListener("click", function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const aria = (btn.getAttribute("aria-label") || "").toLowerCase();
    const ligne = btn.closest("tr") || btn.closest(".user-card");
    const nomUtilisateur =
      ligne?.dataset?.nom ||
      (ligne?.querySelector &&
        ligne.querySelector("td:first-child, .value")?.textContent.trim()) ||
      "Utilisateur";

    if (aria.includes("suspendre")) handleSuspend(btn, ligne, nomUtilisateur);
    else if (aria.includes("reactiv") || aria.includes("réactiv"))
      handleReactivate(btn, ligne, nomUtilisateur);
  });
}

function handleSuspend(btn, ligne, nomUtilisateur) {
  if (!confirm(`Êtes-vous sûr de vouloir suspendre ${nomUtilisateur} ?`))
    return;
  btn.disabled = true;
  btn.textContent = "Suspension...";
  btn.style.backgroundColor = "var(--gris-clair)";

  setTimeout(() => {
    btn.disabled = false;
    btn.textContent = "Réactiver";
    btn.setAttribute("aria-label", "Réactiver");
    btn.style.backgroundColor = "var(--vert-clair)";
    if (ligne) ligne.style.opacity = "0.6";
    afficherMessageAdmin(
      `${nomUtilisateur} a été suspendu avec succès`,
      "warning"
    );
  }, 1000);
}

function handleReactivate(btn, ligne, nomUtilisateur) {
  if (!confirm(`Êtes-vous sûr de vouloir réactiver ${nomUtilisateur} ?`))
    return;
  btn.disabled = true;
  btn.textContent = "Réactivation...";
  btn.style.backgroundColor = "var(--noir-secondaire)";

  setTimeout(() => {
    btn.disabled = false;
    btn.textContent = "Suspendre";
    btn.setAttribute("aria-label", "Suspendre");
    btn.style.backgroundColor = "";
    if (ligne) ligne.style.opacity = "1";
    afficherMessageAdmin(
      `${nomUtilisateur} a été réactivé avec succès`,
      "success"
    );
  }, 1000);
}

// -------------------------
// RECHERCHE UTILISATEURS
// -------------------------
function ajouterRechercheUtilisateurs() {
  const section = qs(".liste-utilisateur");
  if (!section) return;
  const h2 = section.querySelector("h2");
  if (!h2) return;

  if (qs(".recherche-utilisateur")) return; // déjà ajoutée

  const searchDiv = document.createElement("div");
  searchDiv.className = "recherche-utilisateur";
  searchDiv.style.cssText = "margin:15px 0;position:relative;";
  searchDiv.innerHTML = `
    <input type="text" id="recherche-user" placeholder="Rechercher un utilisateur..." style="width:100%;padding:10px 40px 10px 15px;border:1px solid var(--vert-clair);border-radius:5px;background:var(--vert-fond-de-page);color:var(--noir-primaire);">
    <span class="material-symbols-outlined" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--vert-fonce);">search</span>
  `;

  h2.insertAdjacentElement("afterend", searchDiv);
  const inputRecherche = qs("#recherche-user");
  inputRecherche?.addEventListener("input", (e) =>
    filtrerUtilisateurs(e.target.value.toLowerCase())
  );
}

function filtrerUtilisateurs(terme) {
  const lignes = qsa(".liste-utilisateur tbody tr");
  lignes.forEach((ligne) => {
    const nom = (ligne.querySelector("td")?.textContent || "").toLowerCase();
    ligne.style.display = nom.includes(terme) ? "" : "none";
  });
  const cartes = qsa(".user-mobile .user-card");
  cartes.forEach((c) => {
    const nom = (c.querySelector(".value")?.textContent || "").toLowerCase();
    c.style.display = nom.includes(terme) ? "" : "none";
  });
}

// -------------------------
// GESTION FORM CREATION EMPLOYE (INIT)
// -------------------------
function initCreationEmploye() {
  const formEmploye = qs(".creation-employe form");
  if (!formEmploye) return;

  // Events de validation en temps réel uniquement
  champsEmploye.forEach((champ) => {
    const input = document.getElementById(champ);
    if (!input) return;
    input.addEventListener("blur", () => validerChampEmploye(champ));
    input.addEventListener("input", () => {
      if (champ === "password") {
        verifierForceMotDePasse();
        if (document.getElementById("password2")?.value)
          verifierConfirmationMotDePasse();
      }
      if (champ === "password2") verifierConfirmationMotDePasse();
    });
  });

  // Validation avant soumission, mais on laisse le formulaire se soumettre normalement
  formEmploye.addEventListener("submit", async function (e) {
    if (!(await validerFormulaireEmploye())) {
      e.preventDefault(); // Bloquer seulement si validation échoue
    }
    // Sinon, laisser le formulaire se soumettre normalement vers l'API
  });
}

// -------------------------
// INIT GLOBALE (appelée à la fin)
// -------------------------
function initGestionUtilisateurs() {
  ajouterRechercheUtilisateurs();
}

function initAll() {
  try {
    initCreationEmploye();
    initStatistiques();
    initGestionUtilisateurs();
    initDelegationGestionUtilisateurs();
  } catch (e) {
    console.error("Erreur initAll:", e);
  }
}

// -------------------------
// STYLES DYNAMIQUES (injection seule)
// -------------------------
(function injectStyles() {
  const styles = document.createElement("style");
  styles.textContent = `
    .field-error{border-color:#f44336!important;background-color:#fff5f5!important}
    .field-success{border-color:var(--vert-clair)!important;background-color:var(--vert-fond-de-page)!important}
    .password-strength-admin{font-size:12px;margin-top:5px;padding:4px 8px;border-radius:3px;transition:all 0.3s}
    .password-strength-admin.strength-weak{background:#ffebee;color:#c62828;border:1px solid #ffcdd2}
    .password-strength-admin.strength-medium{background:#fff3e0;color:#ef6c00;border:1px solid #ffcc02}
    .password-strength-admin.strength-strong{background:var(--vert-fond-structure);color:var(--vert-fonce);border:1px solid var(--vert-clair)}
    .widget-stats:hover{transform:translateY(-2px);box-shadow:0 4px 8px rgba(0,0,0,0.15);transition:all 0.3s}
    .selecteur-periode select:focus,.recherche-utilisateur input:focus{outline:none;border-color:var(--vert-fonce);box-shadow:0 0 0 2px var(--vert-fond-structure)}
    .user-card{transition:all 0.3s}
    .user-card:hover{transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,0.1)}
    .message-admin{animation:slideIn 0.3s ease-out}
    @keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
    button:hover{transform:translateY(-1px);transition:all 0.2s ease}button:active{transform:translateY(0)}button:disabled{opacity:0.6;cursor:not-allowed;transform:none!important}
    .error-text{animation:fadeIn 0.3s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
    @media(max-width:768px){.selecteur-periode{flex-direction:column;align-items:flex-start!important;gap:8px!important}.selecteur-periode select,.selecteur-periode button{width:100%}.widget-stats{padding:1rem!important;min-height:150px!important}.widget-stats>div:first-child{flex-direction:column;align-items:center!important;gap:10px}}
    @media(max-width:480px){.widget-stats{padding:1rem!important;min-height:120px!important}.widget-stats h3{font-size:14px!important}}
  `;
  document.head.appendChild(styles);
})();

// -------------------------
// LANCEMENT
// -------------------------
document.addEventListener("DOMContentLoaded", initAll);

// EOF
