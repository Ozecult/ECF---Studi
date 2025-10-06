document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-connexion");
  if (!form) return;

  const btnConnexion = document.getElementById("btn-connexion");
  const togglePassword = document.getElementById("toggle-password");

  // Créer un élément pour les messages globaux
  let messageGlobal = document.createElement("div");
  messageGlobal.className = "message-global";
  messageGlobal.style.display = "none";
  document
    .querySelector("main")
    .insertBefore(
      messageGlobal,
      document.querySelector(".formulaire-connexion")
    );

  // Validation en temps réel
  const champs = ["email", "password"];

  champs.forEach((champ) => {
    const input = document.getElementById(champ);
    if (input) {
      input.addEventListener("blur", () => validerChamp(champ));
      input.addEventListener("input", () => {
        // Effacer les erreurs en temps réel si l'utilisateur corrige
        const errorDiv = document.getElementById(`error-${champ}`);
        if (errorDiv && errorDiv.style.display === "block") {
          setTimeout(() => validerChamp(champ), 300);
        }
      });
    }
  });

  // Gestion du bouton afficher/masquer mot de passe
  if (togglePassword) {
    togglePassword.addEventListener("click", function () {
      const passwordInput = document.getElementById("password");
      const icon = togglePassword.querySelector("span");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.textContent = "visibility_off";
      } else {
        passwordInput.type = "password";
        icon.textContent = "visibility";
      }
    });
  }

  // Validation des champs
  function validerChamp(nomChamp) {
    const input = document.getElementById(nomChamp);
    const errorDiv = document.getElementById(`error-${nomChamp}`);
    let isValid = true;
    let message = "";

    // Nettoyer l'état précédent
    input?.classList.remove("field-error", "field-success");
    if (errorDiv) errorDiv.style.display = "none";

    switch (nomChamp) {
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
        } else if (input.value.length < 3) {
          message = "Le mot de passe est trop court";
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

  // Validation complète du formulaire
  async function validerFormulaire() {
    let isValid = true;

    // Valider tous les champs
    champs.forEach((champ) => {
      if (!validerChamp(champ)) {
        isValid = false;
      }
    });

    return isValid;
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

  // Fonction pour afficher les messages globaux
  function afficherMessage(message, type) {
    messageGlobal.textContent = message;
    messageGlobal.className = `message-global ${type}`;
    messageGlobal.style.display = "block";

    // Masquer après 5 secondes si succès
    if (type === "success") {
      setTimeout(() => {
        messageGlobal.style.display = "none";
      }, 5000);
    }
  }
  // Soumission du formulaire vers PHP
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    if (!(await validerFormulaire())) return;

    btnConnexion.disabled = true;
    btnConnexion.textContent = "Connexion en cours...";

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch("/ecoride/php/index.php?page=connexion", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data),
      });

      const result = await response.json();

      if (result.success) {
        afficherMessage(result.message || "Connexion réussie !", "success");

        // Petite pause pour que l'utilisateur voie le message
        setTimeout(() => {
          if (result.redirect_url) {
            window.location.href = result.redirect_url;
          } else {
            window.location.href = "/ecoride/php/index.php?page=utilisateur";
          }
        }, 500);
      } else {
        if (result.field) {
          afficherErreurChamp(result.field, result.message);
        } else {
          afficherMessage(
            result.message || "Erreur lors de la connexion",
            "error"
          );
        }
      }
    } catch (err) {
      console.error("Erreur connexion:", err);
      afficherMessage("Erreur réseau. Réessayez.", "error");
    } finally {
      btnConnexion.disabled = false;
      btnConnexion.textContent = "Connexion";
    }
  });

  // Gestion du formulaire "Mot de passe oublié"
  const lienMotDePasseOublie = document.querySelector(
    'a[href="../php/index.php?page=mdp-oublie"]'
  );
  if (lienMotDePasseOublie) {
    lienMotDePasseOublie.addEventListener("click", function (e) {
      e.preventDefault();
      const email = document.getElementById("email").value;
      if (email) {
        // Pré-remplir l'email dans l'URL
        window.location.href = `../php/index.php?page=mdp-oublie&email=${encodeURIComponent(
          email
        )}`;
      } else {
        window.location.href = "../php/index.php?page=mdp-oublie";
      }
    });
  }
});
