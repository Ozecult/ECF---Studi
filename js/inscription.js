document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-inscription");
  const btnInscription = document.getElementById("btn-inscription");

  // Créer un élément pour les messages globaux
  let messageGlobal = document.createElement("div");
  messageGlobal.className = "message-global";
  messageGlobal.style.display = "none";
  document
    .querySelector("main")
    .insertBefore(
      messageGlobal,
      document.querySelector(".formulaire-inscription")
    );

  // Validation en temps réel - AJOUT DES NOUVEAUX CHAMPS
  const champs = ["prenom", "nom", "email", "password", "password2"];

  champs.forEach((champ) => {
    const input = document.getElementById(champ);
    if (input) {
      input.addEventListener("blur", () => validerChamp(champ));
      input.addEventListener("input", () => {
        if (champ === "password") {
          verifierForceMotDePasse();
          const confirm = document.getElementById("password2");
          if (confirm && confirm.value) {
            verifierConfirmationMotDePasse();
          }
        }
        if (champ === "password2") {
          verifierConfirmationMotDePasse();
        }
      });
    }
  });

  // Soumission du formulaire
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    if (await validerFormulaire()) {
      await soumettreFormulaire();
    }
  });

  // Validation - AJOUT DE LA VALIDATION POUR NOM ET PRENOM
  function validerChamp(nomChamp) {
    const input = document.getElementById(nomChamp);
    const errorDiv = document.getElementById(`error-${nomChamp}`);
    let isValid = true;
    let message = "";

    // Nettoyer l'état précédent
    input?.classList.remove("field-error", "field-success");
    if (errorDiv) errorDiv.style.display = "none";

    switch (nomChamp) {
      case "prenom":
        if (!input.value.trim()) {
          message = "Le prénom est obligatoire";
          isValid = false;
        } else if (input.value.trim().length < 2) {
          message = "Le prénom doit contenir au moins 2 caractères";
          isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\-\s']+$/.test(input.value.trim())) {
          message = "Le prénom ne doit contenir que des lettres";
          isValid = false;
        }
        break;

      case "nom":
        if (!input.value.trim()) {
          message = "Le nom est obligatoire";
          isValid = false;
        } else if (input.value.trim().length < 2) {
          message = "Le nom doit contenir au moins 2 caractères";
          isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\-\s']+$/.test(input.value.trim())) {
          message = "Le nom ne doit contenir que des lettres";
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
        const password = document.getElementById("password").value;
        if (!input.value) {
          message = "Veuillez confirmer votre mot de passe";
          isValid = false;
        } else if (input.value !== password) {
          message = "Les mots de passe ne correspondent pas";
          isValid = false;
        }
        break;
    }

    // Afficher le résultat
    if (isValid) {
      input.classList.add("field-success");
    } else {
      input.classList.add("field-error");
      if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = "block";
      }
    }

    return isValid;
  }

  function verifierForceMotDePasse() {
    const password = document.getElementById("password").value;
    const strengthDiv = document.getElementById("password-strength");

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

    if (strengthDiv) {
      strengthDiv.textContent = message;
      strengthDiv.className = `password-strength ${classe}`;
    }
  }

  function verifierConfirmationMotDePasse() {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("password2").value;

    if (confirm && confirm.length > 0 && password && password.length > 0) {
      clearTimeout(window.passwordTimeout);
      window.passwordTimeout = setTimeout(() => {
        if (password !== confirm) {
          validerChamp("password2");
        } else {
          const input = document.getElementById("password2");
          const errorDiv = document.getElementById("error-password2");
          if (input && errorDiv) {
            input.classList.remove("field-error");
            input.classList.add("field-success");
            errorDiv.style.display = "none";
          }
        }
      }, 500);
    }
  }

  async function validerFormulaire() {
    let isValid = true;

    // Valider tous les champs
    champs.forEach((champ) => {
      if (!validerChamp(champ)) {
        isValid = false;
      }
    });

    // Vérifier les CGU
    const cgu = document.getElementById("cgu");
    const errorCgu = document.getElementById("error-cgu");
    if (!cgu.checked) {
      if (errorCgu) {
        errorCgu.textContent = "Vous devez accepter les conditions générales";
        errorCgu.style.display = "block";
      }
      isValid = false;
    } else {
      if (errorCgu) errorCgu.style.display = "none";
    }

    return isValid;
  }

  async function soumettreFormulaire() {
    btnInscription.disabled = true;
    btnInscription.textContent = "Inscription en cours...";

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      // Simulation de l'envoi avec validation email
      const result = await simulerInscription(data);

      // Succès avec demande de validation email
      afficherMessage(
        "Inscription réussie ! Un email de validation a été envoyé à votre adresse. Veuillez cliquer sur le lien pour activer votre compte.",
        "success"
      );

      form?.reset();
      resetFormulaire();

      // Redirection après 3 secondes pour laisser le temps de lire
      setTimeout(() => {
        window.location.href = "../php/index.php?page=connexion";
      }, 3000);
    } catch (error) {
      // Vérifier si c'est une erreur spécifique à un champ
      if (error.type === "email_exists" && error.field === "email") {
        // Afficher l'erreur directement sous le champ email
        afficherErreurChamp("email", error.message);
      } else {
        // Afficher les autres erreurs comme message global
        afficherMessage(
          error.message || "Erreur lors de l'inscription",
          "error"
        );
      }
    } finally {
      btnInscription.disabled = false;
      btnInscription.textContent = "S'inscrire";
    }
  }

  // Simulation d'API avec validation email
  function simulerInscription(data) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        if (data.email === "test@test.com") {
          reject({
            message: "Cet email est déjà utilisé",
            type: "email_exists",
            field: "email",
          });
        } else if (Math.random() > 0.8) {
          reject({
            message: "Erreur serveur, veuillez réessayer",
            type: "server_error",
          });
        } else {
          // Simulation de l'envoi d'email de validation
          resolve({
            success: true,
            message: "Email de validation envoyé",
            validationToken: generateValidationToken(),
            userData: {
              prenom: data.prenom,
              nom: data.nom,
              email: data.email,
            },
          });
        }
      }, 1500);
    });
  }

  // Fonction pour générer un token de validation (simulation)
  function generateValidationToken() {
    return (
      Math.random().toString(36).substring(2, 15) +
      Math.random().toString(36).substring(2, 15)
    );
  }

  // Fonction pour afficher une erreur spécifique à un champ
  function afficherErreurChamp(nomChamp, message) {
    const input = document.getElementById(nomChamp);
    const errorDiv = document.getElementById(`error-${nomChamp}`);

    if (input && errorDiv) {
      // Marquer le champ comme ayant une erreur
      input.classList.remove("field-success");
      input.classList.add("field-error");

      // Afficher le message d'erreur
      errorDiv.textContent = message;
      errorDiv.style.display = "block";

      // Faire défiler jusqu'au champ en erreur
      input.scrollIntoView({ behavior: "smooth", block: "center" });
      input.focus();
    }
  }

  function afficherMessage(message, type) {
    messageGlobal.textContent = message;
    messageGlobal.className = `message-global ${type}`;
    messageGlobal.style.display = "block";

    // Masquer après 5 secondes si succès
    if (type === "success") {
      setTimeout(() => {
        messageGlobal.style.display = "none";
      }, 8000); // Plus de temps pour lire le message
    }
  }

  function resetFormulaire() {
    // Nettoyer les classes d'erreur/succès
    document
      .querySelectorAll(".field-error, .field-success")
      .forEach((field) => {
        field.classList.remove("field-error", "field-success");
      });

    // Masquer les messages d'erreur
    document.querySelectorAll(".error-text").forEach((error) => {
      error.style.display = "none";
    });

    // Réinitialiser la force du mot de passe
    const strengthDiv = document.getElementById("password-strength");
    if (strengthDiv) {
      strengthDiv.textContent = "";
      strengthDiv.className = "password-strength";
    }

    // Masquer le message global
    messageGlobal.style.display = "none";
  }
});

// Fonction pour basculer la visibilité du mot de passe
function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const icon = field.nextElementSibling;

  if (field.type === "password") {
    field.type = "text";
    if (icon) icon.textContent = "visibility_off";
  } else {
    field.type = "password";
    if (icon) icon.textContent = "visibility";
  }
}

// Fonction pour gérer la validation d'email (à appeler depuis une page de validation)
function validerEmail(token) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (token && token.length > 10) {
        resolve({ success: true, message: "Email validé avec succès" });
      } else {
        reject({ message: "Token de validation invalide" });
      }
    }, 1000);
  });
}
