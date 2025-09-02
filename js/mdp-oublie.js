// Attendre que la page soit complètement chargée avant d'exécuter le script
document.addEventListener("DOMContentLoaded", function () {
  // === SÉLECTION DES ÉLÉMENTS ===
  const form = document.querySelector(".mdp-oublie"); // Le formulaire
  const btnReset = form.querySelector("button[type='submit']"); // Le bouton de soumission

  // === CRÉATION DU MESSAGE GLOBAL ===
  let messageGlobal = document.createElement("div");
  messageGlobal.className = "message-global";
  messageGlobal.style.display = "none";

  // Insérer message avant le formulaire dans la page
  document
    .querySelector("main")
    .insertBefore(messageGlobal, document.querySelector(".mdp-oublie"));

  // === PRÉ-REMPLISSAGE DE L'EMAIL ===
  // Récupérer l'email depuis l'URL si il y en a un venant de la page de connexion
  const urlParams = new URLSearchParams(window.location.search);
  const emailFromUrl = urlParams.get("email");
  if (emailFromUrl) {
    // Si un email est dans l'URL, le mettre dans le champ email
    document.getElementById("email").value = decodeURIComponent(emailFromUrl);
  }

  // === VALIDATION EN TEMPS RÉEL ===
  // Surveiller le champ email pour valider au fur et à mesure
  const emailInput = document.getElementById("email");
  if (emailInput) {
    // Validation quand l'utilisateur sort du champ
    emailInput.addEventListener("blur", () => validerEmail());

    // Validation pendant la saisie pour effacer les erreurs si l'utilisateur corrige
    emailInput.addEventListener("input", () => {
      const errorDiv = document.getElementById("error-email");
      // Si il y a une erreur affichée et que l'utilisateur tape, re-valider après un délai
      if (errorDiv && errorDiv.style.display === "block") {
        setTimeout(() => validerEmail(), 300);
      }
    });
  }

  // === GESTION DE LA SOUMISSION DU FORMULAIRE ===
  form.addEventListener("submit", async function (e) {
    // Empêcher le rechargement de la page
    e.preventDefault();

    // Valider le formulaire avant de l'envoyer
    if (validerEmail()) {
      // Si la validation passe, envoyer le formulaire
      await soumettreFormulaire();
    }
  });

  // === FONCTION DE VALIDATION EMAIL ===
  function validerEmail() {
    const input = document.getElementById("email");
    let errorDiv = document.getElementById("error-email");

    // Créer le div d'erreur s'il n'existe pas
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = "error-email";
      errorDiv.className = "error-text";
      input.parentNode.appendChild(errorDiv);
    }

    let isValid = true;
    let message = "";

    // Nettoyer l'état précédent (enlever les classes CSS d'erreur/succès)
    input.classList.remove("field-error", "field-success");
    errorDiv.style.display = "none";

    // Vérifier le format email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Vérifications par étapes
    if (!input.value.trim()) {
      // Si le champ est vide
      message = "L'adresse email est obligatoire";
      isValid = false;
    } else if (!emailRegex.test(input.value.trim())) {
      // Si le format email n'est pas valide
      message = "Veuillez saisir une adresse email valide";
      isValid = false;
    }

    // Appliquer le style selon le résultat
    if (isValid) {
      // Email valide : bordure verte
      input.classList.add("field-success");
    } else {
      // Email invalide : bordure rouge + message d'erreur
      input.classList.add("field-error");
      errorDiv.textContent = message;
      errorDiv.style.display = "block";
    }

    return isValid; // Retourner true si valide, false sinon
  }

  // === FONCTION DE SOUMISSION ===
  async function soumettreFormulaire() {
    // Désactiver le bouton pour éviter les double-clics
    btnReset.disabled = true;
    btnReset.textContent = "Envoi en cours...";

    // Récupérer les données du formulaire
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries()); // Convertir en objet JS

    try {
      // Simuler l'envoi à l'API (à remplacer par vraie API)
      const result = await simulerResetPassword(data);

      // Succès : afficher message vert
      afficherMessage(
        "Un email de réinitialisation a été envoyé à votre adresse. Vérifiez votre boîte mail et vos spams.",
        "success"
      );

      // Vider le formulaire
      form.reset();

      // Nettoyer les styles de validation
      document
        .getElementById("email")
        .classList.remove("field-success", "field-error");

      // Rediriger vers la page de connexion après 5 secondes
      setTimeout(() => {
        window.location.href = "Connexion.html";
      }, 5000);
    } catch (error) {
      // En cas d'erreur
      if (error.type === "email_not_found") {
        // Email non trouvé : afficher erreur sous le champ
        afficherErreurChamp("email", error.message);
      } else {
        // Autre erreur : message global
        afficherMessage(
          error.message || "Erreur lors de l'envoi. Veuillez réessayer.",
          "error"
        );
      }
    } finally {
      // Dans tous les cas, réactiver le bouton
      btnReset.disabled = false;
      btnReset.textContent = "Réinitialiser mon mot de passe";
    }
  }

  // === SIMULATION D'API (À REMPLACER PAR  VRAIE API) ===
  function simulerResetPassword(data) {
    return new Promise((resolve, reject) => {
      // Simuler un délai de réseau
      setTimeout(() => {
        // Simuler différents cas
        if (data.email === "inexistant@test.com") {
          // Email non trouvé dans la base
          reject({
            message: "Aucun compte associé à cette adresse email",
            type: "email_not_found",
          });
        } else if (Math.random() > 0.9) {
          // Simuler une erreur serveur occasionnelle (10% de chance)
          reject({
            message:
              "Erreur serveur. Veuillez réessayer dans quelques minutes.",
            type: "server_error",
          });
        } else {
          // Succès (90% du temps)
          resolve({
            success: true,
            message: "Email de réinitialisation envoyé",
          });
        }
      }, 1500); // Attendre 1.5 secondes pour simuler le réseau
    });
  }

  // === FONCTION POUR AFFICHER UNE ERREUR SOUS UN CHAMP ===
  function afficherErreurChamp(nomChamp, message) {
    const input = document.getElementById(nomChamp);
    let errorDiv = document.getElementById(`error-${nomChamp}`);

    // Créer le div d'erreur
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = `error-${nomChamp}`;
      errorDiv.className = "error-text";
      input.parentNode.appendChild(errorDiv);
    }

    if (input && errorDiv) {
      // Appliquer le style d'erreur
      input.classList.remove("field-success");
      input.classList.add("field-error");

      // Afficher le message
      errorDiv.textContent = message;
      errorDiv.style.display = "block";

      // Faire défiler jusqu'au champ et le mettre en focus
      input.scrollIntoView({ behavior: "smooth", block: "center" });
      input.focus();
    }
  }

  // === FONCTION POUR AFFICHER LES MESSAGES GLOBAUX ===
  function afficherMessage(message, type) {
    messageGlobal.textContent = message;
    messageGlobal.className = `message-global ${type}`; // "success" ou "error"
    messageGlobal.style.display = "block";

    // Auto-masquer après 8 secondes si succès
    if (type === "success") {
      setTimeout(() => {
        messageGlobal.style.display = "none";
      }, 8000);
    }

    // Faire défiler vers le message
    messageGlobal.scrollIntoView({ behavior: "smooth", block: "center" });
  }
});
