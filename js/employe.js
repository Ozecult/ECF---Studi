// Attente du chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  // ===========================================
  // INITIALISATION DE LA PAGE
  // ===========================================
  function initialiserPage() {
    validerAvis();
  }

  // ===========================================
  // GESTION DE VALIDATION DES AVIS
  // ===========================================
  function validerAvis() {
    // Sélectionner tous les articles d'avis à valider
    const articlesAvis = document.querySelectorAll(".avis_a_valider");

    articlesAvis.forEach((article) => {
      const boutonValider = article.querySelector(
        'button[data-action="valider"]'
      );
      const boutonRefuser = article.querySelector(
        'button[data-action="refuser"]'
      );

      if (boutonValider) {
        boutonValider.addEventListener("click", (e) => {
          e.preventDefault();
          confirmerAction(article, "valider");
        });
      }

      if (boutonRefuser) {
        boutonRefuser.addEventListener("click", (e) => {
          e.preventDefault();
          confirmerAction(article, "refuser");
        });
      }
    });
  }

  // ===========================================
  // CONFIRMATION D'ACTION
  // ===========================================
  function confirmerAction(article, action) {
    const pseudo = article.querySelector(".pseudo").textContent;
    const commentaire = article.querySelector(
      ".ligne-commentaire p"
    ).textContent;

    const message =
      action === "valider"
        ? `Êtes-vous sûr de vouloir VALIDER l'avis de ${pseudo} ?\n\nCommentaire : ${commentaire}`
        : `Êtes-vous sûr de vouloir REFUSER l'avis de ${pseudo} ?\n\nCommentaire : ${commentaire}`;

    if (confirm(message)) {
      if (action === "valider") {
        validerUnAvis(article);
      } else {
        refuserUnAvis(article);
      }
    }
  }

  // ===========================================
  // VALIDATION D'UN AVIS
  // ===========================================
  function validerUnAvis(article) {
    const avisId = article.dataset.avisId;
    const userId = article.dataset.userId;
    const pseudo = article.querySelector(".pseudo").textContent;
    const commentaire = article.querySelector(
      ".ligne-commentaire p"
    ).textContent;
    const etoiles = article.querySelectorAll(
      ".etoiles .material-symbols-outlined"
    ).length;

    // Créer l'objet avis à sauvegarder
    const avisValide = {
      id: avisId,
      userId: userId,
      pseudo: pseudo,
      commentaire: commentaire,
      note: etoiles,
      dateValidation: new Date().toISOString(),
      statut: "validé",
    };

    // Sauvegarder l'avis validé (simulation en attendant le back-end)
    sauvegarderAvisValide(avisValide);

    // Log pour le développement
    console.log(
      `Avis ${avisId} validé pour l'utilisateur ${userId}:`,
      avisValide
    );

    // Animation de suppression avec effet visuel
    article.style.transition = "all 0.5s ease-out";
    article.style.transform = "translateX(100%)";
    article.style.opacity = "0";

    setTimeout(() => {
      article.remove();
      afficherNotification(
        `✅ Avis de ${pseudo} validé avec succès !`,
        "success"
      );
    }, 500);
  }

  // ===========================================
  // REFUS D'UN AVIS
  // ===========================================
  function refuserUnAvis(article) {
    const avisId = article.dataset.avisId;
    const userId = article.dataset.userId;
    const pseudo = article.querySelector(".pseudo").textContent;

    // Log pour le développement
    console.log(`Avis ${avisId} refusé pour l'utilisateur ${userId}`);

    // Animation de suppression avec effet visuel
    article.style.transition = "all 0.5s ease-out";
    article.style.transform = "translateX(-100%)";
    article.style.opacity = "0";

    setTimeout(() => {
      article.remove();
      afficherNotification(`❌ Avis de ${pseudo} refusé`, "error");
    }, 500);
  }

  // ===========================================
  // SAUVEGARDE DES AVIS VALIDÉS (SIMULATION)
  // ===========================================
  function sauvegarderAvisValide(avis) {
    // Simulation de sauvegarde en localStorage en attendant le back-end
    // Dans votre future implementation, vous remplacerez ceci par un appel API

    try {
      // Récupérer les avis validés existants
      let avisValides = JSON.parse(localStorage.getItem("avisValides") || "[]");

      // Ajouter le nouvel avis
      avisValides.push(avis);

      // Sauvegarder dans localStorage
      localStorage.setItem("avisValides", JSON.stringify(avisValides));

      // Aussi sauvegarder par utilisateur pour faciliter la récupération
      let avisParUtilisateur = JSON.parse(
        localStorage.getItem(`avisUtilisateur_${avis.userId}`) || "[]"
      );
      avisParUtilisateur.push(avis);
      localStorage.setItem(
        `avisUtilisateur_${avis.userId}`,
        JSON.stringify(avisParUtilisateur)
      );

      console.log("Avis sauvegardé avec succès:", avis);
    } catch (error) {
      console.error("Erreur lors de la sauvegarde de l'avis:", error);
      afficherNotification("Erreur lors de la sauvegarde", "error");
    }
  }

  // ===========================================
  // FONCTION DE RÉCUPÉRATION DES AVIS (pour usage futur)
  // ===========================================
  function recupererAvisUtilisateur(userId) {
    try {
      return JSON.parse(
        localStorage.getItem(`avisUtilisateur_${userId}`) || "[]"
      );
    } catch (error) {
      console.error("Erreur lors de la récupération des avis:", error);
      return [];
    }
  }

  // ===========================================
  // FONCTION DE NOTIFICATION AMÉLIORÉE
  // ===========================================
  function afficherNotification(message, type = "info") {
    // Supprimer les notifications existantes
    const notificationsExistantes = document.querySelectorAll(".notification");
    notificationsExistantes.forEach((notif) => notif.remove());

    // Créer la nouvelle notification
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;

    // Styles CSS
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      color: white;
      font-weight: 500;
      z-index: 10000;
      transform: translateX(400px);
      transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      max-width: 350px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      font-size: 0.95rem;
      line-height: 1.4;
    `;

    // Couleurs selon le type
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
      case "warning":
        notification.style.background =
          "linear-gradient(135deg, #f59e0b, #d97706)";
        break;
      case "info":
      default:
        notification.style.background =
          "linear-gradient(135deg, #3b82f6, #2563eb)";
        break;
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    // Animation d'entrée
    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    // Animation de sortie après 4 secondes
    setTimeout(() => {
      notification.style.transform = "translateX(400px)";
      notification.style.opacity = "0";
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification);
        }
      }, 300);
    }, 4000);
  }

  // ===========================================
  // FONCTIONS UTILITAIRES POUR LE FUTUR
  // ===========================================

  // Fonction pour afficher les statistiques des avis
  function afficherStatistiques() {
    try {
      const avisValides = JSON.parse(
        localStorage.getItem("avisValides") || "[]"
      );
      console.log(`Total des avis validés: ${avisValides.length}`);
      return avisValides.length;
    } catch (error) {
      console.error("Erreur statistiques:", error);
      return 0;
    }
  }

  // Fonction pour vider le cache (utile pour les tests)
  function viderCacheAvis() {
    if (
      confirm("Êtes-vous sûr de vouloir supprimer tous les avis sauvegardés ?")
    ) {
      localStorage.removeItem("avisValides");
      // Supprimer aussi tous les avis par utilisateur
      Object.keys(localStorage).forEach((key) => {
        if (key.startsWith("avisUtilisateur_")) {
          localStorage.removeItem(key);
        }
      });
      afficherNotification("Cache des avis vidé", "info");
    }
  }

  // Rendre certaines fonctions globales pour les tests
  window.recupererAvisUtilisateur = recupererAvisUtilisateur;
  window.afficherStatistiques = afficherStatistiques;
  window.viderCacheAvis = viderCacheAvis;
  window.afficherNotification = afficherNotification;

  // ===========================================
  // INITIALISATION
  // ===========================================
  initialiserPage();
});
