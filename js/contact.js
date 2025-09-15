// Attendre que la page soit complètement chargée
document.addEventListener("DOMContentLoaded", function () {
  // === SÉLECTION DES ÉLÉMENTS ===
  const form = document.querySelector(".form-contact"); // Le formulaire de contact
  const btnSubmit = form.querySelector("button[type='submit']"); // Le bouton envoyer

  // === CRÉATION DU MESSAGE GLOBAL ===
  let messageGlobal = document.createElement("div");
  messageGlobal.className = "message-global";
  messageGlobal.style.display = "none";

  // Insérer avant le formulaire
  document
    .querySelector("main")
    .insertBefore(messageGlobal, document.querySelector(".form-contact"));

  // === LISTE DES CHAMPS À VALIDER ===
  // Tableau contenant tous les champs qui nécessitent une validation
  const champs = ["email", "tel", "prenom", "nom", "sujet", "champtext"];

  // === VALIDATION EN TEMPS RÉEL ===
  // Pour chaque champ, ajout d'écouteurs d'événements
  champs.forEach((champ) => {
    const input = document.getElementById(champ);
    if (input) {
      // Validation quand l'utilisateur quitte le champ
      input.addEventListener("blur", () => validerChamp(champ));

      // Validation pendant la saisie pour corriger les erreurs en temps réel
      input.addEventListener("input", () => {
        const errorDiv = document.getElementById(`error-${champ}`);
        if (errorDiv && errorDiv.style.display === "block") {
          // Re-valider après un court délai pour ne pas spam
          setTimeout(() => validerChamp(champ), 300);
        }
      });
    }
  });

  // === COMPTEUR DE CARACTÈRES POUR LE TEXTAREA ===
  const textarea = document.getElementById("champtext");
  if (textarea) {
    // Créer et ajouter le compteur de caractères
    const counter = document.createElement("div");
    counter.className = "character-counter";
    counter.style.textAlign = "right";
    counter.style.fontSize = "0.875em";
    counter.style.color = "var(--noir-secondaire)";
    counter.style.marginTop = "5px";

    // Insérer après le textarea
    textarea.parentNode.appendChild(counter);

    // Mettre à jour le compteur à chaque frappe
    textarea.addEventListener("input", function () {
      const remaining = 800 - this.value.length;
      counter.textContent = `${this.value.length}/800 caractères`;

      // Changer la couleur si proche de la limite
      if (remaining < 50) {
        counter.style.color = "#dc3545";
      } else if (remaining < 100) {
        counter.style.color = "var(--vert-fonce";
      } else {
        counter.style.color = "var(--noir-secondaire)";
      }
    });

    // Initialiser le compteur
    textarea.dispatchEvent(new Event("input"));
  }

  // === SOUMISSION DU FORMULAIRE ===
  form.addEventListener("submit", async function (e) {
    e.preventDefault(); // Empêcher le rechargement de page

    // Valider tous les champs avant envoi
    if (await validerFormulaire()) {
      await soumettreFormulaire();
    }
  });

  // === FONCTION DE VALIDATION PAR CHAMP ===
  function validerChamp(nomChamp) {
    const input = document.getElementById(nomChamp);
    let errorDiv = document.getElementById(`error-${nomChamp}`);

    // Créer le div d'erreur s'il n'existe pas
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = `error-${nomChamp}`;
      errorDiv.className = "error-text";
      input.parentNode.appendChild(errorDiv);
    }

    let isValid = true;
    let message = "";

    // Nettoyer l'état précédent
    input.classList.remove("field-error", "field-success");
    errorDiv.style.display = "none";

    // Validation selon le type de champ
    switch (nomChamp) {
      case "email":
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!input.value.trim()) {
          message = "L'adresse email est obligatoire";
          isValid = false;
        } else if (!emailRegex.test(input.value.trim())) {
          message = "Veuillez saisir une adresse email valide";
          isValid = false;
        }
        break;

      case "tel":
        // Regex pour numéro français
        const telRegex = /^(?:(?:\+33|0)[1-9](?:[0-9]{8}))$/;
        // Nettoyer le numéro (enlever espaces, points, tirets)
        const cleanTel = input.value.replace(/[\s\.\-]/g, "");
        if (!input.value.trim()) {
          message = "Le numéro de téléphone est obligatoire";
          isValid = false;
        } else if (!telRegex.test(cleanTel)) {
          message = "Veuillez saisir un numéro de téléphone valide";
          isValid = false;
        }
        break;

      case "prenom":
        if (!input.value.trim()) {
          message = "Le prénom est obligatoire";
          isValid = false;
        } else if (input.value.trim().length < 2) {
          message = "Le prénom doit contenir au moins 2 caractères";
          isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(input.value.trim())) {
          message = "Le prénom ne doit contenir que des lettres";
          isValid = false;
        }
        break;

      case "nom":
        if (!input.value.trim()) {
          message = "Le nom de famille est obligatoire";
          isValid = false;
        } else if (input.value.trim().length < 2) {
          message = "Le nom doit contenir au moins 2 caractères";
          isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(input.value.trim())) {
          message = "Le nom ne doit contenir que des lettres";
          isValid = false;
        }
        break;

      case "sujet":
        if (!input.value) {
          message = "Veuillez sélectionner un sujet";
          isValid = false;
        }
        break;

      case "champtext":
        if (!input.value.trim()) {
          message = "La description est obligatoire";
          isValid = false;
        } else if (input.value.trim().length < 10) {
          message = "La description doit contenir au moins 10 caractères";
          isValid = false;
        } else if (input.value.length > 800) {
          message = "La description ne peut pas dépasser 800 caractères";
          isValid = false;
        }
        break;
    }

    // Appliquer le style selon le résultat
    if (isValid) {
      input.classList.add("field-success");
    } else {
      input.classList.add("field-error");
      errorDiv.textContent = message;
      errorDiv.style.display = "block";
    }

    return isValid;
  }

  // === VALIDATION COMPLÈTE DU FORMULAIRE ===
  async function validerFormulaire() {
    let isValid = true;

    // Valider tous les champs un par un
    champs.forEach((champ) => {
      if (!validerChamp(champ)) {
        isValid = false;
      }
    });

    // Si un champ n'est pas valide, faire défiler vers le premier champ en erreur
    if (!isValid) {
      const firstError = document.querySelector(".field-error");
      if (firstError) {
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        firstError.focus();
      }
    }

    return isValid;
  }

  // === FONCTION DE SOUMISSION ===
  async function soumettreFormulaire() {
    // Désactiver le bouton pendant l'envoi
    btnSubmit.disabled = true;
    btnSubmit.textContent = "Envoi en cours...";

    // Récupérer toutes les données du formulaire
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      // Simuler l'envoi (remplacer par API)
      const result = await simulerEnvoiContact(data);

      // Succès
      afficherMessage(
        "Votre message a été envoyé avec succès ! Notre équipe vous répondra dans les plus brefs délais.",
        "success"
      );

      // Réinitialiser le formulaire
      form.reset();
      resetFormulaire();

      // Redirection après 5 secondes
      setTimeout(() => {
        window.location.href = "../php/index.php?page=home";
      }, 5000);
    } catch (error) {
      // En cas d'erreur
      afficherMessage(
        error.message ||
          "Erreur lors de l'envoi du message. Veuillez réessayer.",
        "error"
      );
    } finally {
      // Réactiver le bouton
      btnSubmit.disabled = false;
      btnSubmit.textContent = "Soumettre";
    }
  }

  // === SIMULATION D'API ===
  function simulerEnvoiContact(data) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        // Simuler différents cas selon l'email
        if (data.email === "spam@test.com") {
          reject({
            message: "Cette adresse email est bloquée",
            type: "blocked_email",
          });
        } else if (Math.random() > 0.95) {
          // 5% de chance d'erreur serveur
          reject({
            message:
              "Erreur serveur. Veuillez réessayer dans quelques minutes.",
            type: "server_error",
          });
        } else {
          // Succès 95% du temps
          resolve({
            success: true,
            message: "Message envoyé avec succès",
            ticketId:
              "TICKET-" + Math.random().toString(36).substr(2, 9).toUpperCase(),
          });
        }
      }, 2000); // Simuler 2 secondes de traitement
    });
  }

  // === FONCTION POUR RÉINITIALISER LE FORMULAIRE ===
  function resetFormulaire() {
    // Enlever toutes les classes de validation
    document
      .querySelectorAll(".field-error, .field-success")
      .forEach((field) => {
        field.classList.remove("field-error", "field-success");
      });

    // Masquer tous les messages d'erreur
    document.querySelectorAll(".error-text").forEach((error) => {
      error.style.display = "none";
    });

    // Réinitialiser le compteur de caractères
    const counter = document.querySelector(".character-counter");
    if (counter) {
      counter.textContent = "0/800 caractères";
      counter.style.color = "var(--noir-secondaire)";
    }

    // Masquer le message global
    messageGlobal.style.display = "none";
  }

  // === FONCTION POUR AFFICHER LES MESSAGES GLOBAUX ===
  function afficherMessage(message, type) {
    messageGlobal.textContent = message;
    messageGlobal.className = `message-global ${type}`;
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

  // === FORMATAGE AUTOMATIQUE DU TÉLÉPHONE ===
  const telInput = document.getElementById("tel");
  if (telInput) {
    telInput.addEventListener("input", function (e) {
      // Enlever tout ce qui n'est pas un chiffre
      let value = e.target.value.replace(/\D/g, "");

      // Formatter automatiquement
      if (value.length > 0) {
        if (value.startsWith("0")) {
          // Numéro français
          value = value.replace(
            /(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/,
            "$1 $2 $3 $4 $5"
          );
        }
      }

      // Limiter à 14 caractères
      if (value.length > 14) {
        value = value.slice(0, 14);
      }

      e.target.value = value;
    });
  }
});
