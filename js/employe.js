// Attente du chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  // ===========================================
  // INITIALISATION DE LA PAGE
  // ===========================================
  function initialiserPage() {
    validerAvis();
    gererSignalements();
    rechercheSignalementsResolus();
    rechercheAvis();
    gererMessagesContact();
  }

  // ===========================================
  // GESTION DE VALIDATION DES AVIS
  // ===========================================
  function validerAvis() {
    // Gestion validation des avis
    document.querySelectorAll(".btn-valider-avis").forEach((btn) => {
      btn.addEventListener("click", async function (e) {
        e.preventDefault();
        const article = this.closest(".avis_a_valider");
        const avisId = article.dataset.avisId;
        const pseudo = article.querySelector(".pseudo").textContent;

        if (!confirm(`Valider l'avis de ${pseudo} ?`)) return;

        try {
          const response = await fetch(
            "/ecoride/php/api/api-router.php?action=valider-avis",
            {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `avis_id=${avisId}`,
              credentials: "same-origin",
            }
          );

          const data = await response.json();

          if (data.success) {
            article.style.transition = "all 0.5s ease-out";
            article.style.transform = "translateX(100%)";
            article.style.opacity = "0";

            setTimeout(() => {
              article.remove();
              afficherNotification(`✅ Avis de ${pseudo} validé !`, "success");
            }, 500);
          } else {
            afficherNotification("Erreur: " + data.message, "error");
          }
        } catch (error) {
          console.error("Erreur:", error);
          afficherNotification("Erreur lors de la validation", "error");
        }
      });
    });

    // Gestion refus des avis
    document.querySelectorAll(".btn-refuser-avis").forEach((btn) => {
      btn.addEventListener("click", async function (e) {
        e.preventDefault();
        const article = this.closest(".avis_a_valider");
        const avisId = article.dataset.avisId;
        const pseudo = article.querySelector(".pseudo").textContent;

        const motif = prompt("Motif du refus (optionnel):");
        if (motif === null) return;

        try {
          const response = await fetch(
            "/ecoride/php/api/api-router.php?action=refuser-avis",
            {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `avis_id=${avisId}&motif=${encodeURIComponent(motif)}`,
              credentials: "same-origin",
            }
          );

          const data = await response.json();

          if (data.success) {
            article.style.transition = "all 0.5s ease-out";
            article.style.transform = "translateX(-100%)";
            article.style.opacity = "0";

            setTimeout(() => {
              article.remove();
              afficherNotification(`❌ Avis de ${pseudo} refusé`, "error");
            }, 500);
          } else {
            afficherNotification("Erreur: " + data.message, "error");
          }
        } catch (error) {
          console.error("Erreur:", error);
          afficherNotification("Erreur lors du refus", "error");
        }
      });
    });
  }

  // ===========================================
  // GESTION DES SIGNALEMENTS
  // ===========================================
  function gererSignalements() {
    const modal = document.getElementById("modalSignalement");
    const closeBtn = document.getElementById("closeSignalement");
    const modalBody = document.getElementById("signalementDetails");

    if (!modal) return;

    // Ouvrir modal pour examiner (desktop + mobile)
    document.addEventListener("click", function (e) {
      if (
        e.target.matches(".btn-examiner") ||
        e.target.matches(".btn-examiner-mobile")
      ) {
        const signalementId = e.target.dataset.signalementId;
        const card = e.target.closest(".signalement-card, .trajet-carte");

        if (!card) {
          console.error("Card non trouvée");
          return;
        }

        ouvrirModalSignalement(card, signalementId, modalBody, modal);
      }
    });

    // Ouvrir modal pour voir détails signalements résolus
    document.addEventListener("click", function (e) {
      const item = e.target.closest(".signalement-item-small");
      if (item) {
        ouvrirModalSignalementResolu(item, modalBody, modal);
      }
    });

    // Ouvrir modal pour résoudre
    document.addEventListener("click", function (e) {
      if (
        e.target.matches(".btn-resoudre") ||
        e.target.closest(".btn-resoudre")
      ) {
        const button = e.target.closest(".btn-resoudre");
        const signalementId = button.dataset.signalementId;
        const card = button.closest(".signalement-card"); // ← Remonter à la card

        ouvrirModalResolution(card, signalementId, modalBody, modal);
      }
    });

    // Fermer modal
    closeBtn?.addEventListener("click", () => (modal.style.display = "none"));
    modal?.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });
  }

  function ouvrirModalSignalement(card, signalementId, modalBody, modal) {
    // Version DESKTOP d'abord
    let typeElement = card.querySelector(".signalement-type");
    let type = "";
    if (typeElement) {
      const clone = typeElement.cloneNode(true);
      clone
        .querySelectorAll("span.material-symbols-outlined")
        .forEach((el) => el.remove());
      type = clone.innerText.trim();
    }

    let description = card
      .querySelector(".signalement-description p")
      ?.innerText.trim();
    let trajetText = card
      .querySelector(".signalement-trajet")
      ?.innerText.trim();
    let personnesElements = card.querySelectorAll(
      ".signalement-personnes > div"
    );

    // Si pas de type trouvé = version MOBILE
    if (!type || type === "") {
      const allValues = card.querySelectorAll(".trajet-info .value");

      // Les indices correspondent à l'ordre dans le HTML mobile
      const dateValue = allValues[0]?.textContent.trim();
      trajetText = allValues[1]?.textContent.trim();
      const signaleurText = allValues[2]?.textContent.trim();
      const signaleText = allValues[3]?.textContent.trim();
      type = allValues[4]?.textContent.trim();
      description = allValues[5]?.textContent.trim();

      personnesElements = [
        { textContent: signaleurText || "" },
        { textContent: signaleText || "" },
      ];
    }

    // Extraction des noms de personnes
    let signaleur = "N/A";
    let signale = "N/A";

    if (personnesElements && personnesElements.length >= 2) {
      const text0 = personnesElements[0].textContent || "";
      const text1 = personnesElements[1].textContent || "";

      signaleur = text0.replace(/Signalé par\s*:\s*/i, "").trim() || "N/A";
      signale =
        text1
          .replace(/Utilisateur concerné\s*:\s*/i, "")
          .replace(/Concerné\s*:\s*/i, "")
          .trim() || "N/A";
    }

    // Nettoyer le trajet (enlever "Trajet n°X :")
    let trajet = trajetText || "N/A";
    if (trajet !== "N/A") {
      trajet = trajet
        .replace(/route/gi, "")
        .replace(/Trajet\s*n°\d+\s*:\s*/i, "")
        .trim();
    }

    modalBody.innerHTML = `
      <div class="detail-section">
        <h4>
          <span class="material-symbols-outlined">info</span>
          Informations du signalement
        </h4>
        <div class="detail-content">
          <strong>Type :</strong> ${type || "N/A"}<br>
          <strong>Trajet :</strong> ${trajet}
        </div>
      </div>

      <div class="detail-section">
        <h4>
          <span class="material-symbols-outlined">description</span>
          Description
        </h4>
        <div class="detail-content">
          ${description || "Aucune description"}
        </div>
      </div>

      <div class="detail-section">
        <h4>
          <span class="material-symbols-outlined">people</span>
          Personnes impliquées
        </h4>
        <div class="detail-grid detail-grid-vertical">
          <div class="detail-item">
            <strong>Signaleur</strong>
            ${signaleur}
          </div>
          <div class="detail-item">
            <strong>Signalé</strong>
            ${signale}
          </div>
        </div>
      </div>

      <div class="commentaire-field">
        <label for="commentaire-employe">Commentaire interne (optionnel)</label>
        <textarea id="commentaire-employe" placeholder="Notes sur ce signalement..."></textarea>
      </div>

      <div class="modal-actions-btns">
        <button class="btn-prendre-charge" data-signalement-id="${signalementId}">
          <span class="material-symbols-outlined">assignment_ind</span>
          Prendre en charge
        </button>
        <button class="btn-classer" data-signalement-id="${signalementId}">
          <span class="material-symbols-outlined">close</span>
          Classer sans suite
        </button>
      </div>
    `;

    modalBody
      .querySelector(".btn-prendre-charge")
      ?.addEventListener("click", function () {
        prendreEnChargeSignalement(signalementId, modal);
      });

    modalBody
      .querySelector(".btn-classer")
      ?.addEventListener("click", function () {
        classerSansSuite(signalementId, modal);
      });

    modal.style.display = "flex";
  }

  function ouvrirModalResolution(card, signalementId, modalBody, modal) {
    // Récupérer directement depuis data-description
    const description = card.dataset.description || "Aucune description";

    // Chercher le commentaire interne s'il existe
    const commentaireElement = card.querySelector(".commentaire-interne p");
    const commentaireExistant = commentaireElement
      ? commentaireElement.textContent.trim()
      : "";

    modalBody.innerHTML = `
    <div class="detail-section">
      <h4>
        <span class="material-symbols-outlined">description</span>
        Description du signalement
      </h4>
      <div class="detail-content">
        ${description}
      </div>
    </div>

    ${
      commentaireExistant
        ? `
    <div class="detail-section">
      <h4>
        <span class="material-symbols-outlined">notes</span>
        Mon commentaire précédent
      </h4>
      <div class="detail-content">
        ${commentaireExistant}
      </div>
    </div>
    `
        : ""
    }

    <div class="commentaire-field">
      <label for="commentaire-resolution">Commentaire de résolution <span style="color:#dc3545;">*</span></label>
      <textarea id="commentaire-resolution" placeholder="Détails de la résolution..." required></textarea>
    </div>

    <div class="suspension-option">
      <input type="checkbox" id="demande-suspension">
      <label for="demande-suspension">
        <span class="material-symbols-outlined" style="vertical-align:middle;margin-right:0.3rem;">warning</span>
        Demander une suspension de compte à l'admin
      </label>
    </div>

    <div class="modal-actions-btns">
      <button class="btn-resoudre-modal" data-signalement-id="${signalementId}">
        <span class="material-symbols-outlined">check_circle</span>
        Résoudre le signalement
      </button>
    </div>
  `;

    modalBody
      .querySelector(".btn-resoudre-modal")
      ?.addEventListener("click", function () {
        resoudreSignalement(signalementId, modal);
      });

    modal.style.display = "flex";
  }

  function ouvrirModalSignalementResolu(item, modalBody, modal) {
    // Extraire depuis les data-attributes
    const type = item.dataset.type || "N/A";
    const date = item.dataset.date || "N/A";
    const trajet = item.dataset.trajet || "N/A";
    const signaleur = item.dataset.signaleur || "N/A";
    const signale = item.dataset.signale || "N/A";

    modalBody.innerHTML = `
    <div class="detail-section">
      <h4>
        <span class="material-symbols-outlined">info</span>
        Informations du signalement
      </h4>
      <div class="detail-content">
        <strong>Type :</strong> ${type}<br>
        <strong>Date de résolution :</strong> ${date}<br>
        <strong>Trajet :</strong> ${trajet}
      </div>
    </div>

    <div class="detail-section">
      <h4>
        <span class="material-symbols-outlined">people</span>
        Personnes impliquées
      </h4>
      <div class="detail-grid detail-grid-vertical">
        <div class="detail-item">
          <strong>Signaleur</strong>
          ${signaleur}
        </div>
        <div class="detail-item">
          <strong>Signalé</strong>
          ${signale}
        </div>
      </div>
    </div>

    <div style="text-align:center; padding:1rem; color:var(--noir-secondaire); font-style:italic;">
      <span class="material-symbols-outlined" style="vertical-align:middle; color:var(--vert-clair);">check_circle</span>
      Ce signalement a été résolu
    </div>
  `;

    modal.style.display = "flex";
  }

  async function prendreEnChargeSignalement(signalementId, modal) {
    const commentaire =
      document.getElementById("commentaire-employe")?.value || "";

    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=prendre-en-charge-signalement",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `signalement_id=${signalementId}&commentaire=${encodeURIComponent(
            commentaire
          )}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        modal.style.display = "none";
        afficherNotification("Signalement pris en charge !", "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        afficherNotification("Erreur: " + data.message, "error");
      }
    } catch (error) {
      console.error("Erreur:", error);
      afficherNotification("Erreur lors de la prise en charge", "error");
    }
  }

  async function classerSansSuite(signalementId, modal) {
    const commentaire = document.getElementById("commentaire-employe")?.value;

    if (!commentaire || commentaire.trim() === "") {
      afficherNotification(
        "Un motif est requis pour classer sans suite",
        "warning"
      );
      return;
    }

    if (!confirm("Confirmer le classement sans suite ?")) return;

    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=classer-sans-suite",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `signalement_id=${signalementId}&commentaire=${encodeURIComponent(
            commentaire
          )}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        modal.style.display = "none";
        afficherNotification("Signalement classé sans suite", "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        afficherNotification("Erreur: " + data.message, "error");
      }
    } catch (error) {
      console.error("Erreur:", error);
      afficherNotification("Erreur", "error");
    }
  }

  async function resoudreSignalement(signalementId, modal) {
    const commentaire = document.getElementById(
      "commentaire-resolution"
    )?.value;
    const demandeSuspension = document.getElementById("demande-suspension")
      ?.checked
      ? "1"
      : "0";

    if (!commentaire || commentaire.trim() === "") {
      afficherNotification(
        "Un commentaire de résolution est requis",
        "warning"
      );
      return;
    }

    if (!confirm("Confirmer la résolution de ce signalement ?")) return;

    try {
      const response = await fetch(
        "/ecoride/php/api/api-router.php?action=resoudre-signalement",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `signalement_id=${signalementId}&commentaire=${encodeURIComponent(
            commentaire
          )}&demande_suspension=${demandeSuspension}`,
          credentials: "same-origin",
        }
      );

      const data = await response.json();

      if (data.success) {
        modal.style.display = "none";
        afficherNotification(data.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        afficherNotification("Erreur: " + data.message, "error");
      }
    } catch (error) {
      console.error("Erreur:", error);
      afficherNotification("Erreur", "error");
    }
  }

  // ===========================================
  // RECHERCHE SIGNALEMENTS RÉSOLUS
  // ===========================================
  function rechercheSignalementsResolus() {
    const searchInput = document.getElementById("search-signalements-resolus");
    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      const items = document.querySelectorAll(
        "#liste-signalements-resolus .signalement-item-small"
      );

      items.forEach((item) => {
        const text = item.textContent.toLowerCase();
        if (text.includes(query)) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      });
    });
  }

  // ===========================================
  // FONCTION DE NOTIFICATION
  // ===========================================
  function afficherNotification(message, type = "info") {
    const notificationsExistantes = document.querySelectorAll(".notification");
    notificationsExistantes.forEach((notif) => notif.remove());

    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
      position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem;
      border-radius: 12px; color: white; font-weight: 500; z-index: 10001;
      transform: translateX(400px); transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      max-width: 350px; box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      font-size: 0.95rem; line-height: 1.4;
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
      case "warning":
        notification.style.background =
          "linear-gradient(135deg, #f59e0b, #d97706)";
        break;
      default:
        notification.style.background =
          "linear-gradient(135deg, #3b82f6, #2563eb)";
        break;
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => (notification.style.transform = "translateX(0)"), 100);
    setTimeout(() => {
      notification.style.transform = "translateX(400px)";
      notification.style.opacity = "0";
      setTimeout(() => notification.remove(), 300);
    }, 4000);
  }

  window.afficherNotification = afficherNotification;

  // ===========================================
  // RECHERCHE AVIS VALIDÉS/REFUSÉS
  // ===========================================
  function rechercheAvis() {
    const searchValides = document.getElementById("search-avis-valides");
    const searchRefuses = document.getElementById("search-avis-refuses");

    if (searchValides) {
      searchValides.addEventListener("input", function () {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll(
          "#liste-avis-valides .avis-traite"
        );

        console.log("Recherche valides:", query); // DEBUG

        items.forEach((item) => {
          const text = item.textContent.toLowerCase();
          if (query === "" || text.includes(query)) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      });
    }

    if (searchRefuses) {
      searchRefuses.addEventListener("input", function () {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll(
          "#liste-avis-refuses .avis-traite"
        );

        console.log("Recherche refusés:", query); // DEBUG

        items.forEach((item) => {
          const text = item.textContent.toLowerCase();
          if (query === "" || text.includes(query)) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      });
    }
  }

  // ===========================================
  // GESTION DES MESSAGES DE CONTACT
  // ===========================================
  function gererMessagesContact() {
    chargerMessagesContact();

    // Charger les messages
    async function chargerMessagesContact() {
      try {
        const response = await fetch(
          "/ecoride/php/api/api-router.php?action=get-messages-contact"
        );
        const data = await response.json();

        if (data.success) {
          const nouveaux = data.messages.filter(
            (m) => m.statut === "nouveau" || m.statut === "lu"
          );
          const traites = data.messages.filter(
            (m) => m.statut === "traite" || m.statut === "ferme"
          );

          afficherMessagesNouveaux(nouveaux);
          afficherMessagesTraites(traites);

          const badgeNouveaux = document.getElementById("badge-nouveaux");
          const badgeTraites = document.getElementById("badge-traites");
          if (badgeNouveaux) badgeNouveaux.textContent = nouveaux.length;
          if (badgeTraites) badgeTraites.textContent = traites.length;
        }
      } catch (error) {
        console.error("Erreur chargement messages:", error);
      }
    }

    function afficherMessagesNouveaux(messages) {
      const container = document.getElementById("messages-nouveaux");
      if (!container) return;

      if (messages.length === 0) {
        container.innerHTML =
          '<div class="empty-state"><p>Aucun message en attente</p></div>';
        return;
      }

      container.innerHTML = messages
        .map(
          (msg) => `
      <article class="message-card card" data-message-id="${msg.id}">
        <div class="message-header">
          <span class="message-sujet">${msg.sujet}</span>
          <span class="message-date">${new Date(
            msg.created_at
          ).toLocaleDateString("fr-FR")}</span>
        </div>
        <div class="message-body">
          <div class="message-contact">
            <strong>${msg.prenom} ${msg.nom}</strong><br>
            📧 ${msg.email}<br>
            ${msg.telephone ? "📱 " + msg.telephone : ""}
          </div>
          <div class="message-texte">
            <p>${msg.message}</p>
          </div>
        </div>
        <div class="message-footer">
          <button class="btn-traiter-message" data-message-id="${msg.id}">
            <span class="material-symbols-outlined">reply</span>
            Répondre
          </button>
        </div>
      </article>
    `
        )
        .join("");

      document.querySelectorAll(".btn-traiter-message").forEach((btn) => {
        btn.addEventListener("click", () =>
          ouvrirModalReponse(btn.dataset.messageId, messages)
        );
      });
    }

    function afficherMessagesTraites(messages) {
      const container = document.getElementById("messages-traites");
      if (!container) return;

      if (messages.length === 0) {
        container.innerHTML =
          '<div class="empty-state"><p>Aucun message traité</p></div>';
        return;
      }

      container.innerHTML = messages
        .map(
          (msg) => `
      <div class="message-item-small">
        <div class="message-item-header">
          <span>${msg.prenom} ${msg.nom}</span>
          <span class="message-date-small">${new Date(
            msg.created_at
          ).toLocaleDateString("fr-FR")}</span>
        </div>
        <div class="message-item-body">
          <strong>${msg.sujet}</strong> - ${msg.message.substring(0, 100)}...
        </div>
        ${msg.reponse ? '<div class="message-repondu">✓ Répondu</div>' : ""}
      </div>
    `
        )
        .join("");
    }

    function ouvrirModalReponse(messageId, messages) {
      const message = messages.find((m) => m.id == messageId);
      const modal = document.getElementById("modalTraiterMessage");
      if (!modal) return;

      document.getElementById("messageDetails").innerHTML = `
      <div class="message-detail-card">
        <h4>De : ${message.prenom} ${message.nom}</h4>
        <p><strong>Email :</strong> ${message.email}</p>
        ${
          message.telephone
            ? `<p><strong>Tél :</strong> ${message.telephone}</p>`
            : ""
        }
        <p><strong>Sujet :</strong> ${message.sujet}</p>
        <p><strong>Date :</strong> ${new Date(
          message.created_at
        ).toLocaleString("fr-FR")}</p>
        <div class="message-original">
          <strong>Message :</strong>
          <p>${message.message}</p>
        </div>
      </div>
    `;

      document.getElementById("messageId").value = messageId;
      document.getElementById("reponseMessage").value = "";
      document.getElementById("reponseCharCount").textContent = "0";

      modal.style.display = "flex";
    }

    // Gestion modal
    const modal = document.getElementById("modalTraiterMessage");
    const closeBtn = document.getElementById("closeTraiterMessage");
    const annulerBtn = document.getElementById("annulerReponse");
    const envoyerBtn = document.getElementById("envoyerReponse");
    const reponseTextarea = document.getElementById("reponseMessage");

    closeBtn?.addEventListener("click", () => (modal.style.display = "none"));
    annulerBtn?.addEventListener("click", () => (modal.style.display = "none"));

    reponseTextarea?.addEventListener("input", function () {
      document.getElementById("reponseCharCount").textContent =
        this.value.length;
    });

    envoyerBtn?.addEventListener("click", async function () {
      const messageId = document.getElementById("messageId").value;
      const reponse = document.getElementById("reponseMessage").value;

      if (!reponse.trim()) {
        afficherNotification("Veuillez rédiger une réponse", "warning");
        return;
      }

      try {
        const formData = new FormData();
        formData.append("message_id", messageId);
        formData.append("reponse", reponse);

        const response = await fetch(
          "/ecoride/php/api/api-router.php?action=traiter-message-contact",
          {
            method: "POST",
            body: formData,
          }
        );

        const data = await response.json();

        if (data.success) {
          afficherNotification("✅ Réponse envoyée avec succès", "success");
          modal.style.display = "none";
          chargerMessagesContact();
        } else {
          afficherNotification("❌ " + data.message, "error");
        }
      } catch (error) {
        console.error("Erreur:", error);
        afficherNotification("❌ Erreur lors de l'envoi", "error");
      }
    });
  }
  // ===========================================
  // INITIALISATION
  // ===========================================
  initialiserPage();
});
