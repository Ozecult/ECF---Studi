<?php
// Si $authController n'est pas fourni par index.php
if (!isset($authController)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $authController = new AuthController();
}
// Forcer la connexion
$authController->requireAuth();
// Vérifier que c'est un employé (admin OU employe)
if (!$authController->hasPermission('admin') && !$authController->hasPermission('employe')) {
    http_response_code(403);
    echo "<h1>403 — Accès refusé</h1><p>Cette page est réservée aux employés.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Espace employé</title>
    <link rel="stylesheet" href="/css/style.css" />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main class="employe">
      <!-- Avis à valider -->
      <section class="choix-avis card">
        <h1>Avis à valider</h1>
        
        <?php if (empty($employeData['avis'])): ?>
          <p style="text-align:center; padding:2rem; color:var(--noir-secondaire);">Aucun avis en attente de validation</p>
        <?php else: ?>
          <?php foreach ($employeData['avis'] as $avis): ?>
            <article class="avis_a_valider card" data-avis-id="<?= $avis['id'] ?>">
              <div class="ligne-profil">
                <?php if ($avis['evaluateur_photo']): ?>
                  <img src="<?= htmlspecialchars($avis['evaluateur_photo']) ?>" 
                      alt="Photo de profil" 
                      class="photo-profil" 
                      width="60" height="60" />
                <?php else: ?>
                  <span class="material-symbols-outlined" aria-label="Icône profil" role="img">account_circle</span>
                <?php endif; ?>
                <p class="pseudo"><?= htmlspecialchars($avis['evaluateur_prenom']) ?></p>
              </div>

              <div class="ligne-commentaire">
                <p>"<?= htmlspecialchars($avis['commentaire']) ?>"</p>
                <div class="etoiles">
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <span class="material-symbols-outlined <?= $i < $avis['note'] ? 'star-filled' : 'star-empty' ?>">star</span>
                  <?php endfor; ?>
                </div>
                <p style="font-size:0.9rem; color:var(--noir-secondaire); margin-top:0.5rem;">
                  Trajet: <?= htmlspecialchars($avis['adresse_depart']) ?> → <?= htmlspecialchars($avis['adresse_arrivee']) ?>
                </p>
              </div>

              <div class="boutons-avis">
                <button class="button btn-valider-avis" type="button" data-action="valider">
                  Valider
                </button>
                <button class="button btn-refuser-avis" type="button" data-action="refuser">
                  Refuser
                </button>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

      <!-- Avis validés et refusés -->
      <section class="avis-traites">
        <!-- Avis validés -->
        <div class="avis-section card">
          <h3>
            Avis validés 
            <span class="badge badge-success"><?= count($employeData['avis_valides']) ?></span>
          </h3>
          
          <div class="search-box">
            <span class="material-symbols-outlined">search</span>
            <input type="text" id="search-avis-valides" placeholder="Rechercher un avis validé...">
          </div>

          <div class="avis-list" id="liste-avis-valides">
            <?php foreach ($employeData['avis_valides'] as $avis): ?>
              <div class="avis-traite">
                <div class="avis-traite-header">
                  <div class="avis-traite-user">
                    <?php if ($avis['evaluateur_photo']): ?>
                      <img src="<?= htmlspecialchars($avis['evaluateur_photo']) ?>" alt="Photo">
                    <?php else: ?>
                      <span class="material-symbols-outlined">account_circle</span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($avis['evaluateur_prenom']) ?></span>
                  </div>
                  <span class="avis-traite-date"><?= date('d/m/Y', strtotime($avis['date_validation'])) ?></span>
                </div>
                <div class="avis-traite-content">
                  <p class="avis-traite-commentaire"><?= htmlspecialchars($avis['commentaire']) ?></p>
                  <div class="avis-traite-note">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <span class="material-symbols-outlined <?= $i < $avis['note'] ? 'star-filled' : 'star-empty' ?>">star</span>
                    <?php endfor; ?>
                  </div>
                  <div class="avis-traite-footer">
                    <span>Pour: <?= htmlspecialchars($avis['evalue_prenom']) ?></span>
                    <span class="avis-traite-validateur">Validé par: <?= htmlspecialchars($avis['validateur_prenom'] ?? 'N/A') ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Avis refusés -->
        <div class="avis-section card">
          <h3>
            Avis refusés 
            <span class="badge" style="background:#ef4444;"><?= count($employeData['avis_refuses']) ?></span>
          </h3>
          
          <div class="search-box">
            <span class="material-symbols-outlined">search</span>
            <input type="text" id="search-avis-refuses" placeholder="Rechercher un avis refusé...">
          </div>

          <div class="avis-list" id="liste-avis-refuses">
            <?php foreach ($employeData['avis_refuses'] as $avis): ?>
              <div class="avis-traite refuse">
                <div class="avis-traite-header">
                  <div class="avis-traite-user">
                    <?php if ($avis['evaluateur_photo']): ?>
                      <img src="<?= htmlspecialchars($avis['evaluateur_photo']) ?>" alt="Photo">
                    <?php else: ?>
                      <span class="material-symbols-outlined">account_circle</span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($avis['evaluateur_prenom']) ?></span>
                  </div>
                  <span class="avis-traite-date"><?= date('d/m/Y', strtotime($avis['date_validation'])) ?></span>
                </div>
                <div class="avis-traite-content">
                  <p class="avis-traite-commentaire"><?= htmlspecialchars($avis['commentaire']) ?></p>
                  <div class="avis-traite-note">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <span class="material-symbols-outlined <?= $i < $avis['note'] ? 'star-filled' : 'star-empty' ?>">star</span>
                    <?php endfor; ?>
                  </div>
                  <?php if (!empty($avis['motif_refus'])): ?>
                  <div class="motif-refus">
                    <strong>Motif du refus:</strong> <?= htmlspecialchars($avis['motif_refus']) ?>
                  </div>
                  <?php endif; ?>
                  <div class="avis-traite-footer">
                    <span>Pour: <?= htmlspecialchars($avis['evalue_prenom']) ?></span>
                    <span class="avis-traite-validateur">Refusé par: <?= htmlspecialchars($avis['validateur_prenom'] ?? 'N/A') ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- Trajets signalés -->
      <section class="signalements-section card">
        <h2>Gestion des signalements</h2>

        <!-- Signalements en attente -->
        <div class="signalements-container">
          <h3>
            Signalements en attente 
            <span class="badge"><?= count($employeData['signalements']) ?></span>
          </h3>
          
          <?php if (empty($employeData['signalements'])): ?>
            <div class="empty-state">
              <span class="material-symbols-outlined">check_circle</span>
              <p>Aucun signalement en attente</p>
            </div>
          <?php else: ?>
            <!-- VERSION DESKTOP - Cards Grid -->
            <div class="signalements-grid">
              <?php foreach ($employeData['signalements'] as $signalement): 
                $dateTrajet = new DateTime($signalement['date_trajet']);
              ?>
                <article class="signalement-card card" data-signalement-id="<?= $signalement['id'] ?>">
                  <div class="signalement-header">
                    <span class="signalement-type type-<?= strtolower($signalement['type_signalement']) ?>">
                      <span class="material-symbols-outlined">
                        <?php
                        $icons = [
                          'comportement' => 'sentiment_dissatisfied',
                          'securite' => 'warning',
                          'vehicule' => 'directions_car',
                          'itineraire' => 'wrong_location',
                          'autre' => 'error'
                        ];
                        echo $icons[$signalement['type_signalement']] ?? 'error';
                        ?>
                      </span>
                      <?= ucfirst($signalement['type_signalement']) ?>
                    </span>
                    <span class="signalement-date">
                      <?= $dateTrajet->format('d/m/Y') ?>
                    </span>
                  </div>

                  <div class="signalement-body">
                    <div class="signalement-trajet">
                      <span class="material-symbols-outlined">route</span>
                      Trajet n°<?= $signalement['trajet_id'] ?> : 
                      <?= htmlspecialchars($signalement['adresse_depart']) ?> → 
                      <?= htmlspecialchars($signalement['adresse_arrivee']) ?>
                    </div>

                    <div class="signalement-description">
                      <strong>Description :</strong>
                      <p><?= htmlspecialchars($signalement['description']) ?></p>
                    </div>

                    <div class="signalement-personnes">
                      <div>
                        <strong>Signalé par :</strong>
                        <?= htmlspecialchars($signalement['signaleur_prenom']) ?>
                        (<?= htmlspecialchars($signalement['signaleur_email']) ?>)
                      </div>
                      <div>
                        <strong>Utilisateur concerné :</strong>
                        <?= htmlspecialchars($signalement['signale_prenom']) ?>
                        (<?= htmlspecialchars($signalement['signale_email']) ?>)
                      </div>
                    </div>
                  </div>

                  <div class="signalement-footer">
                    <button class="btn-examiner" data-signalement-id="<?= $signalement['id'] ?>">
                      <span class="material-symbols-outlined">search</span>
                      Examiner
                    </button>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>

            <!-- VERSION MOBILE - Cards compactes -->
            <div class="signalements-mobile">
              <?php foreach ($employeData['signalements'] as $signalement): 
                $dateTrajet = new DateTime($signalement['date_trajet']);
              ?>
                <div class="trajet-carte" data-signalement-id="<?= $signalement['id'] ?>">
                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">calendar_month</span>
                      <span class="label">Date</span>
                    </div>
                    <div class="value"><?= $dateTrajet->format('d/m/Y') ?></div>
                  </div>

                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">route</span>
                      <span class="label">Trajet</span>
                    </div>
                    <div class="value">n°<?= $signalement['trajet_id'] ?> : <?= htmlspecialchars($signalement['adresse_depart']) ?> → <?= htmlspecialchars($signalement['adresse_arrivee']) ?></div>
                  </div>

                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">person</span>
                      <span class="label">Signalé par</span>
                    </div>
                    <div class="value"><?= htmlspecialchars($signalement['signaleur_prenom']) ?> : <?= htmlspecialchars($signalement['signaleur_email']) ?></div>
                  </div>

                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">person</span>
                      <span class="label">Concerné</span>
                    </div>
                    <div class="value"><?= htmlspecialchars($signalement['signale_prenom']) ?> : <?= htmlspecialchars($signalement['signale_email']) ?></div>
                  </div>

                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">chat</span>
                      <span class="label">Type</span>
                    </div>
                    <div class="value"><?= ucfirst($signalement['type_signalement']) ?></div>
                  </div>

                  <div class="trajet-info">
                    <div class="trajet-info-header">
                      <span class="material-symbols-outlined">description</span>
                      <span class="label">Description</span>
                    </div>
                    <div class="value"><?= htmlspecialchars($signalement['description']) ?></div>
                  </div>

                  <div class="trajet-action">
                    <button class="btn-examiner-mobile" data-signalement-id="<?= $signalement['id'] ?>">
                      <span class="material-symbols-outlined">search</span>
                      Examiner
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Mes signalements en cours -->
        <?php if (!empty($employeData['mes_signalements'])): ?>
        <div class="signalements-container mes-signalements">
          <h3>
            Mes signalements en cours 
            <span class="badge badge-warning"><?= count($employeData['mes_signalements']) ?></span>
          </h3>
          
          <div class="signalements-grid">
            <?php foreach ($employeData['mes_signalements'] as $signalement): 
              $dateTrajet = new DateTime($signalement['date_trajet']);
            ?>
              <article class="signalement-card card en-cours" 
                      data-signalement-id="<?= $signalement['id'] ?>"
                      data-description="<?= htmlspecialchars($signalement['description']) ?>">
                <div class="signalement-header">
                  <span class="signalement-type type-<?= strtolower($signalement['type_signalement']) ?>">
                    <span class="material-symbols-outlined">pending</span>
                    En cours
                  </span>
                  <span class="signalement-date">
                    <?= $dateTrajet->format('d/m/Y') ?>
                  </span>
                </div>

                <div class="signalement-body">
                  <div class="signalement-trajet">
                    <span class="material-symbols-outlined">route</span>
                    Trajet n°<?= $signalement['trajet_id'] ?>
                  </div>

                  <div class="signalement-description">
                    <strong>Description :</strong>
                    <p><?= htmlspecialchars($signalement['description']) ?></p>
                  </div>

                  <?php if (!empty($signalement['commentaire_employe'])): ?>
                  <div class="commentaire-interne">
                    <strong>Mon commentaire :</strong>
                    <p><?= htmlspecialchars($signalement['commentaire_employe']) ?></p>
                  </div>
                  <?php endif; ?>
                </div>

                <div class="signalement-footer">
                  <button class="btn-resoudre" data-signalement-id="<?= $signalement['id'] ?>">
                    <span class="material-symbols-outlined">check_circle</span>
                    Résoudre
                  </button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Signalements résolus -->
        <?php if (!empty($employeData['signalements_resolus'])): ?>
        <div class="signalements-container signalements-resolus">
          <h3>
            Signalements résolus 
            <span class="badge badge-success"><?= count($employeData['signalements_resolus']) ?></span>
          </h3>
          
          <div class="search-box">
            <span class="material-symbols-outlined">search</span>
            <input type="text" id="search-signalements-resolus" placeholder="Rechercher...">
          </div>

          <div class="signalements-list" id="liste-signalements-resolus">
            <?php foreach ($employeData['signalements_resolus'] as $signalement): 
              $dateResolution = new DateTime($signalement['date_resolution']);
            ?>
              <div class="signalement-item-small" 
                  data-type="<?= $signalement['type_signalement'] ?>"
                  data-date="<?= date('d/m/Y', strtotime($signalement['date_resolution'])) ?>"
                  data-trajet="<?= htmlspecialchars($signalement['adresse_depart']) ?> → <?= htmlspecialchars($signalement['adresse_arrivee']) ?>"
                  data-signaleur="<?= htmlspecialchars($signalement['signaleur_prenom']) ?>"
                  data-signale="<?= htmlspecialchars($signalement['signale_prenom']) ?>">
                
                <div class="signalement-item-header">
                  <span class="signalement-type-small"><?= ucfirst($signalement['type_signalement']) ?></span>
                  <span class="signalement-date-small"><?= date('d/m/Y', strtotime($signalement['date_resolution'])) ?></span>
                </div>
                <div class="signalement-item-body">
                  <strong>Trajet n°<?= $signalement['trajet_id'] ?> :</strong>
                  <?= htmlspecialchars($signalement['adresse_depart']) ?> → <?= htmlspecialchars($signalement['adresse_arrivee']) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- Messages de contact -->
      <section class="messages-contact-section card">
        <h2>Messages de contact</h2>

        <!-- Messages non traités -->
        <div class="messages-container">
          <h3>
            Messages en attente 
            <span class="badge" id="badge-nouveaux">0</span>
          </h3>
          
          <div class="messages-list" id="messages-nouveaux">
            <!-- Rempli dynamiquement -->
          </div>
        </div>

        <!-- Messages traités -->
        <div class="messages-container">
          <h3>
            Messages traités 
            <span class="badge badge-success" id="badge-traites">0</span>
          </h3>
          
          <div class="search-box">
            <span class="material-symbols-outlined">search</span>
            <input type="text" id="search-messages-traites" placeholder="Rechercher...">
          </div>

          <div class="messages-list" id="messages-traites">
            <!-- Rempli dynamiquement -->
          </div>
        </div>
      </section>

      <!-- Modal Traiter Message -->
      <div class="modal-overlay" id="modalTraiterMessage" style="display:none;">
        <div class="modal">
          <div class="modal-header">
            <h3>Répondre au message</h3>
            <button type="button" class="close-modal" id="closeTraiterMessage">&times;</button>
          </div>
          <div class="modal-body">
            <div id="messageDetails" class="message-details">
              <!-- Détails du message -->
            </div>
            
            <div class="commentaire-field">
              <label for="reponseMessage">Votre réponse <span class="required">*</span></label>
              <textarea 
                id="reponseMessage" 
                name="reponse" 
                class="form-control" 
                placeholder="Rédigez votre réponse..."
                maxlength="1000"
                required
              ></textarea>
              <div class="char-counter">
                <span id="reponseCharCount">0</span>/1000 caractères
              </div>
            </div>
            <input type="hidden" id="messageId" name="message_id">
          </div>
          
          <div class="modal-actions-btns">
            <button type="button" class="btn-classer" id="annulerReponse">Annuler</button>
            <button type="button" class="btn-prendre-charge" id="envoyerReponse">Envoyer la réponse</button>
          </div>
        </div>
      </div>

      <!-- Modal Examen Signalement -->
      <div class="modal-overlay" id="modalSignalement" style="display:none;">
        <div class="modal modal-signalement">
          <div class="modal-header">
            <h3>Examen du signalement</h3>
            <button type="button" class="close-modal" id="closeSignalement">&times;</button>
          </div>
          <div class="modal-body" id="signalementDetails">
            <!-- Rempli dynamiquement -->
          </div>
        </div>
      </div>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
