document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-connexion");
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

  // Soumission du formulaire
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    if (await validerFormulaire()) {
      await soumettreFormulaire();
    }
  });

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

  // Soumission du formulaire
  async function soumettreFormulaire() {
    btnConnexion.disabled = true;
    btnConnexion.textContent = "Connexion en cours...";

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      // Simulation de la connexion
      const result = await simulerConnexion(data);

      // Succès de la connexion
      afficherMessage("Connexion réussie ! Redirection en cours...", "success");

      // Sauvegarder la session si "Se souvenir de moi" est coché
      if (data.remember) {
        sauvegarderSession(result.user);
      }

      // Redirection après 2 secondes
      setTimeout(() => {
        window.location.href = result.redirectUrl || "index.html";
      }, 2000);
    } catch (error) {
      // Vérifier si c'est une erreur spécifique à un champ
      if (error.type === "invalid_email" && error.field === "email") {
        afficherErreurChamp("email", error.message);
      } else if (
        error.type === "invalid_password" &&
        error.field === "password"
      ) {
        afficherErreurChamp("password", error.message);
      } else {
        // Afficher les autres erreurs comme message global
        afficherMessage(
          error.message || "Erreur lors de la connexion",
          "error"
        );
      }
    } finally {
      btnConnexion.disabled = false;
      btnConnexion.textContent = "Connexion";
    }
  }

  // Simulation de l'API de connexion
  function simulerConnexion(data) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        // Simulation de différents cas d'erreur
        if (data.email === "bloque@test.com") {
          reject({
            message: "Compte temporairement bloqué. Contactez le support.",
            type: "account_blocked",
          });
        } else if (data.email === "inexistant@test.com") {
          reject({
            message: "Aucun compte associé à cette adresse email",
            type: "invalid_email",
            field: "email",
          });
        } else if (data.password === "mauvaismdp") {
          reject({
            message: "Mot de passe incorrect",
            type: "invalid_password",
            field: "password",
          });
        } else if (Math.random() > 0.9) {
          reject({
            message: "Erreur serveur, veuillez réessayer",
            type: "server_error",
          });
        } else {
          // Connexion réussie
          resolve({
            success: true,
            user: {
              id: 123,
              email: data.email,
              nom: "John Doe",
              credits: 20,
            },
            redirectUrl: "index.html", // ou "index.html" si pas de dashboard
            token: "fake_jwt_token_" + Date.now(),
          });
        }
      }, 1500);
    });
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

  // Fonction pour sauvegarder la session utilisateur
  function sauvegarderSession(user) {
    // Note: Dans un vrai projet, vous utiliseriez localStorage ou des cookies
    // Ici c'est juste pour la démonstration
    const sessionData = {
      user: user,
      loginTime: Date.now(),
      rememberMe: true,
    };

    console.log("Session sauvegardée:", sessionData);
    // localStorage.setItem('ecoride_session', JSON.stringify(sessionData));
  }

  // Fonction pour pré-remplir le formulaire si session sauvegardée
  function chargerSessionSauvegardee() {
    // Note: Dans un vrai projet, vous récupéreriez depuis localStorage
    // const savedSession = localStorage.getItem('ecoride_session');
    // if (savedSession) {
    //   const session = JSON.parse(savedSession);
    //   document.getElementById('email').value = session.user.email;
    //   document.getElementById('remember').checked = true;
    // }
  }

  // Charger la session au chargement de la page
  chargerSessionSauvegardee();

  // Gestion du formulaire "Mot de passe oublié"
  const lienMotDePasseOublie = document.querySelector(
    'a[href="mdp-oublie.html"]'
  );
  if (lienMotDePasseOublie) {
    lienMotDePasseOublie.addEventListener("click", function (e) {
      e.preventDefault();
      const email = document.getElementById("email").value;
      if (email) {
        // Pré-remplir l'email dans l'URL
        window.location.href = `mdp-oublie.html?email=${encodeURIComponent(
          email
        )}`;
      } else {
        window.location.href = "mdp-oublie.html";
      }
    });
  }
});
