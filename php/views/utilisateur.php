<?php
// views/utilisateur.php - Version intégrée avec la BDD
if (!isset($userData) || empty($userData['user'])) {
    header('Location: /ecoride/php/index.php?page=connexion');
    exit;
}

$user = $userData['user'];
$vehicules = $userData['vehicules'] ?? [];
$preferences = $userData['preferences'] ?? [];
$trajetsAVenir = $userData['trajets_a_venir'] ?? [];
$trajetsPasses = $userData['trajets_passes'] ?? [];
$preferencesTypes = $userData['preferences_types'] ?? [];
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
    <title>EcoRide - Tableau de bord</title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main class="utilisateur">
      <h1>Tableau de bord</h1>

      <!-- Résumé -->
      <section class="resume card">
        <div class="header-profil">
          <div class="profil-credits">
            <h2>Bienvenue <?= htmlspecialchars($user['prenom']) ?></h2>
            <div class="credits-container">
              <p><strong>Nombre de crédits : <?= $user['credits'] ?></strong></p>
              <button type="button" id="btn-recharger-credits" class="btn-recharge">
                <span class="material-symbols-outlined">payments</span>
                Recharger
              </button>
            </div>
          </div>
          
          <div class="profil-photo">
            <?php if (!empty($user['photo_profil'])): ?>
              <img src="<?= htmlspecialchars($user['photo_profil']) ?>" 
                   alt="Photo de profil" 
                   style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--vert-clair);">
            <?php else: ?>
              <span class="material-symbols-outlined" aria-label="Photo profil" role="img">account_circle</span>
            <?php endif; ?>
            <p>Changer sa photo de profil</p>
          </div>
        </div>

        <div class="profil-bio">
          <div class="bio-header">
            <label for="bio">
              <span class="material-symbols-outlined">edit_note</span>
              Présentez-vous
            </label>
            <button type="button" id="edit-bio-btn" class="btn-edit-bio" style="display: none;">
              <span class="material-symbols-outlined">edit</span>
            </button>
          </div>
          
          <div id="bio-display" class="bio-display" style="display: none;">
            <p id="bio-text"></p>
          </div>
          
          <div id="bio-edit" class="bio-edit">
            <textarea id="bio" name="bio" maxlength="500" placeholder="Parlez un peu de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            <button type="button" id="save-bio">Enregistrer</button>
          </div>
        </div>

        <div class="role-choice">
          <h2>Choisissez votre rôle</h2>
          <div class="button">
            <button class="button <?= $user['est_chauffeur'] && !$user['est_passager'] ? 'selected' : '' ?>" 
                    type="submit" aria-label="chauffeur">
              Chauffeur
            </button>
            <button class="button <?= !$user['est_chauffeur'] && $user['est_passager'] ? 'selected' : '' ?>" 
                    type="submit" aria-label="passager">
              Passager
            </button>
            <button class="button <?= $user['est_chauffeur'] && $user['est_passager'] ? 'selected' : '' ?>" 
                    type="submit" aria-label="duo">
              Les deux
            </button>
          </div>
        </div>
      </section>

      <!-- Informations chauffeur -->
      <section class="informations-chauffeur card" style="<?= ($user['est_chauffeur']) ? 'display: grid;' : 'display: none;' ?>">
        <h2>Pour être chauffeur, veuillez renseigner ces informations</h2>

        <div class="vehicule-preferences">
          <!-- Véhicule(s) -->
          <form method="post" class="vehicule card" autocomplete="on">
            <fieldset class="type-vehicule">
              <legend>Véhicule :</legend>

              <div class="vehicule">
                <label for="marque">
                  <span class="material-icons" aria-label="Véhicule thermique" role="img">drive_eta</span>
                  Marque :
                </label>
                <input type="text" name="marque" id="marque" required />
              </div>

              <div class="vehicule">
                <label for="modele">
                  <span class="material-symbols-outlined" aria-label="volant" role="img">search_hands_free</span>
                  Modèle :
                </label>
                <input type="text" name="modele" id="modele" required />
              </div>

              <div class="vehicule">
                <label for="couleur">
                  <span class="material-symbols-outlined" aria-label="couleur" role="img">colors</span>
                  Couleur :
                </label>
                <input type="text" name="couleur" id="couleur" required />
              </div>

              <div class="vehicule">
                <label for="energie">
                  <span class="material-symbols-outlined" aria-label="energie" role="img">drive_eta</span>
                  Énergie :
                </label>
                <select id="energie" name="energie" required>
                  <option value="" disabled selected>-- Sélectionnez une énergie --</option>
                  <option value="electrique">Électrique</option>
                  <option value="thermique">Thermique</option>
                  <option value="hybride">Hybride</option>
                  <option value="gpl">GPL</option>
                </select>
              </div>

              <div class="vehicule">
                <label for="immatriculation">
                  <span class="material-symbols-outlined" aria-label="immatriculation" role="img">app_registration</span>
                  Immatriculation :
                </label>
                <input type="text" name="immatriculation" id="immatriculation" required placeholder="AB-123-CD" />
              </div>

              <div class="vehicule">
                <label for="circulation">
                  <span class="material-symbols-outlined" aria-label="Calendrier" role="img">calendar_month</span>
                  1ère mise en circulation :
                </label>
                <input type="date" name="circulation" id="circulation" required />
              </div>

              <div class="vehicule">
                <label for="places">
                  <span class="material-symbols-outlined" aria-label="Places dispo" role="img">groups</span>
                  Places disponibles :
                </label>
                <input type="number" name="places" id="places" min="1" max="8" required />
              </div>

              <button type="submit">Ajouter le véhicule</button>
              <button type="button" class="btn-ajouter-vehicule" aria-label="Ajouter un véhicule">
                <span class="material-symbols-outlined" aria-hidden="true">add</span>
                Ajouter un autre véhicule
              </button>
            </fieldset>
          </form>

          <!-- Préférences -->
          <div class="preferences-container card">
            <fieldset class="preferences-all">
              <legend>Préférences :</legend>

              <?php 
              // Préférences prédéfinies
              $preferencesPredefines = ['fumeur', 'animaux', 'enfants', 'musique'];
              $userPreferencesMap = [];
              
              // Créer un map des préférences de l'utilisateur
              foreach ($preferences as $pref) {
                $userPreferencesMap[$pref['type_nom']] = $pref['valeur'] === 'oui';
              }
              ?>

              <?php foreach ($preferencesPredefines as $prefName): ?>
              <div class="preferences">
                <input type="checkbox" 
                       id="<?= $prefName ?>" 
                       name="<?= $prefName ?>"
                       <?= isset($userPreferencesMap[$prefName]) && $userPreferencesMap[$prefName] ? 'checked' : '' ?> />
                <label for="<?= $prefName ?>">
                  <span class="material-symbols-outlined" 
                        aria-label="<?= ucfirst($prefName) ?>" 
                        role="img">
                    <?php
                    $icons = [
                      'fumeur' => 'smoking_rooms',
                      'animaux' => 'pets', 
                      'enfants' => 'account_child',
                      'musique' => 'genres'
                    ];
                    echo $icons[$prefName] ?? 'circle';
                    ?>
                  </span>
                  <?= ucfirst($prefName) ?>
                </label>
              </div>
              <?php endforeach; ?>

              <?php 
              // Afficher les préférences personnalisées
              foreach ($preferences as $pref):
                if (!empty($pref['preference_personnalisee']) && !in_array($pref['type_nom'], $preferencesPredefines)):
              ?>
              <div class="preferences">
                <input type="checkbox" 
                       id="<?= htmlspecialchars($pref['type_nom']) ?>" 
                       name="<?= htmlspecialchars($pref['type_nom']) ?>"
                       checked />
                <label for="<?= htmlspecialchars($pref['type_nom']) ?>">
                  <span class="material-symbols-outlined" aria-label="<?= htmlspecialchars($pref['type_nom']) ?>" role="img">add_circle</span>
                  <?= htmlspecialchars($pref['preference_personnalisee']) ?>
                </label>
              </div>
              <?php 
                endif;
              endforeach; 
              ?>

              <button type="button" class="btn-ajouter-preference" aria-label="Ajouter une préférence">
                <span class="material-symbols-outlined" aria-hidden="true">add</span>
                Ajouter une préférence
              </button>
            </fieldset>
          </div>
        </div>
      </section>

      <!-- Ajouter un voyage -->
      <form method="post" class="voyage card" autocomplete="on" style="<?= $user['est_chauffeur'] ? 'display: block;' : 'display: none;' ?>">
        <fieldset class="add-voyage">
          <legend>Ajouter un voyage</legend>

          <div class="voyage-fields">
            <div class="voyage">
              <label for="depart">
                <span class="material-symbols-outlined" aria-label="Départ" role="img">home_pin</span>
                Départ
              </label>
              <input type="text" name="depart" id="depart" required />
            </div>

            <div class="voyage">
              <label for="arrivee">
                <span class="material-symbols-outlined" aria-label="Arrivée" role="img">location_on</span>
                Arrivée
              </label>
              <input type="text" name="arrivee" id="arrivee" required />
            </div>

            <div class="voyage">
              <label for="fixer-prix">
                <span class="material-symbols-outlined" aria-label="Prix" role="img">euro_symbol</span>
                Fixer le prix
              </label>
              <input type="number" name="fixer-prix" id="fixer-prix" min="1" required />
            </div>

            <div class="voyage" data-field="date">
              <label for="date-depart">
                  <span class="material-symbols-outlined" aria-label="Calendrier" role="img">calendar_month</span> 
                  Date et heure
              </label>
              <input type="datetime-local" name="date_depart" id="date-depart" required />
            </div>

            <div class="voyage">
              <label for="choix-vehicule">
                <span class="material-icons" aria-label="Véhicule" role="img">drive_eta</span>
                Véhicule
              </label>
              <select name="choix-vehicule" id="choix-vehicule" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($vehicules as $vehicule): ?>
                <option value="<?= $vehicule['id'] ?>">
                  <?= htmlspecialchars($vehicule['marque']) ?> 
                  <?= htmlspecialchars($vehicule['modele']) ?> 
                  (<?= htmlspecialchars($vehicule['immatriculation']) ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="btn-ajouter-voyage-container">
            <button type="submit" class="btn-ajouter-voyage" aria-label="Ajouter un voyage">
              <span class="material-symbols-outlined" aria-hidden="true">add</span>
            </button>
            <span class="btn-ajouter-voyage-text">Ajouter le voyage</span>
          </div>

          <p>
            Afin de garantir le bon fonctionnement de la plateforme, 2 crédits vous seront débités.
          </p>
        </fieldset>
      </form>

      <!-- Trajets à venir -->
      <section class="trajets card">
        <h3>Trajets à venir</h3>
        <table class="a-venir">
          <thead>
            <tr>
              <th scope="col">
                <span class="material-symbols-outlined">calendar_month</span>
                Date
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">route</span>
                Trajet
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">person</span>
                Conducteur
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">schedule</span>
                Heure
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">radio_button_partial</span>
                Statut
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">euro_symbol</span>
                Crédits
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">group</span>
                Participants
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">touch_app</span>
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($trajetsAVenir as $trajet): 
              $dateDepart = new DateTime($trajet['date_depart']);
              $heureDepart = $dateDepart->format('H:i');
            ?>
            <tr data-trajet-id="<?= $trajet['id'] ?>">
              <td><?= $dateDepart->format('d/m/Y') ?></td>
              <td><?= htmlspecialchars($trajet['adresse_depart']) ?> → <?= htmlspecialchars($trajet['adresse_arrivee']) ?></td>
              <td><?= htmlspecialchars($trajet['conducteur']) ?></td>
              <td><?= $heureDepart ?></td>
              <td><?= htmlspecialchars($trajet['statut']) ?></td>
              <td><?= $trajet['prix_par_passager'] ?></td>
              <td>
                <button class="btn-voir-participants" data-trajet-id="<?= $trajet['id'] ?>">
                  <span class="material-symbols-outlined" style="font-size:1rem;">group</span>
                  <?= $trajet['nb_participants'] ?>/<?= $trajet['places_totales'] ?>
                </button>
              </td>
              <td>
                <?php if ($trajet['statut'] === 'planifie' && $trajet['role_utilisateur'] === 'chauffeur'): ?>
                  <div class="action-menu">
                    <button type="button" class="btn-menu">⋮</button>
                    <div class="menu-options">
                      <button type="button" class="btn-action" data-action="demarrer">▶ Démarrer</button>
                      <button type="button" class="btn-action" data-action="annuler">✕ Annuler</button>
                    </div>
                  </div>
                <?php elseif ($trajet['statut'] === 'en_cours' && $trajet['role_utilisateur'] === 'chauffeur'): ?>
                  <div class="action-menu">
                    <button type="button" class="btn-menu">⋮</button>
                    <div class="menu-options">
                      <button type="button" class="btn-action" data-action="terminer">✓ Terminer</button>
                      <button type="button" class="btn-action" data-action="annuler">✕ Annuler</button>
                    </div>
                  </div>
                <?php elseif ($trajet['statut'] !== 'termine'): ?>
                  <button type="button" class="btn-action" data-action="annuler">✕ Annuler</button>
                <?php else: ?>
                  <span>-</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Trajets passés -->
        <h3>Trajets passés</h3>
        <table class="t-passes">
          <thead>
            <tr>
              <th scope="col">
                <span class="material-symbols-outlined">calendar_month</span>
                Date
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">route</span>
                Trajet
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">person</span>
                Conducteur
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">schedule</span>
                Heure
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">radio_button_partial</span>
                Statut
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">euro_symbol</span>
                Crédits
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">group</span>
                Participants
              </th>
              <th scope="col">
                <span class="material-symbols-outlined">hotel_class</span>
                Avis
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($trajetsPasses as $trajet): 
              $dateDepart = new DateTime($trajet['date_depart']);
              $heureDepart = $dateDepart->format('H:i');
            ?>
            <tr data-trajet-id="<?= $trajet['id'] ?>">
              <td><?= $dateDepart->format('d/m/Y') ?></td>
              <td><?= htmlspecialchars($trajet['adresse_depart']) ?> → <?= htmlspecialchars($trajet['adresse_arrivee']) ?></td>
              <td><?= htmlspecialchars($trajet['conducteur']) ?></td>
              <td><?= $heureDepart ?></td>
              <td><?= htmlspecialchars($trajet['statut']) ?></td>
              <td><?= $trajet['prix_par_passager'] ?></td>
              <td>
                <button class="btn-voir-participants" data-trajet-id="<?= $trajet['id'] ?>">
                  <span class="material-symbols-outlined" style="font-size:1rem;">group</span>
                  <?= $trajet['nb_participants'] ?>/<?= $trajet['places_totales'] ?>
                </button>
              </td>
              <td>
                <?php if ($trajet['statut'] === 'annule'): ?>
                  <span style="color: var(--noir-secondaire); font-style: italic;">-</span>
                <?php elseif ($trajet['role_utilisateur'] === 'chauffeur'): ?>
                  <span style="color: var(--noir-secondaire); font-style: italic;">-</span>
                <?php else: 
                  $avisStatut = $trajet['avis_statut'] ?? null;
                  
                  if ($avisStatut === 'valide'): ?>
                    <button data-status="avis_publie" disabled>
                      ✓ Avis publié
                    </button>
                  
                  <?php elseif ($avisStatut === 'refuse'): ?>
                    <button class="btn-avis-refuse" 
                            data-trajet-id="<?= $trajet['id'] ?>" 
                            data-motif="<?= htmlspecialchars($trajet['avis_motif_refus'] ?? 'Aucun motif fourni') ?>">
                      ✗ Avis refusé
                    </button>
                  
                  <?php elseif ($avisStatut === 'en_attente'): ?>
                    <button data-status="en_validation" disabled>
                      ⏳ En validation
                    </button>
                  
                  <?php else: ?>
                    <button data-status="en_attente_avis" 
                            data-trajet-id="<?= $trajet['id'] ?>" 
                            class="btn-avis">
                      🌟 Laisser un avis
                    </button>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <!-- Modal Participants -->
      <div class="modal-overlay" id="modalParticipants" style="display:none;">
        <div class="modal modal-participants">
          <div class="modal-header">
            <h3>Participants du trajet</h3>
            <button type="button" class="close-modal" id="closeParticipants">&times;</button>
          </div>
          <div class="modal-body">
            <div id="listeParticipants" class="liste-participants">
              <!-- Rempli dynamiquement -->
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Recharge Crédits -->
      <div class="modal-overlay" id="modalRecharge" style="display:none;">
        <div class="modal">
          <div class="modal-header">
            <h3>Recharger mes crédits</h3>
            <button type="button" class="close-modal" id="closeRecharge">&times;</button>
          </div>
          <div class="modal-body">
            <p style="text-align:center;margin-bottom:1.5rem;color:var(--noir-secondaire);">
              Sélectionnez un pack de crédits (1 crédit = 0,50€)
            </p>
            <form id="formRecharge">
              <div class="packs-credits">
                <label class="pack-credit">
                  <input type="radio" name="pack" value="20" required>
                  <div class="pack-content">
                    <span class="pack-amount">20 crédits</span>
                    <span class="pack-price">10,00 €</span>
                  </div>
                </label>
                
                <label class="pack-credit">
                  <input type="radio" name="pack" value="50" required>
                  <div class="pack-content">
                    <span class="pack-amount">50 crédits</span>
                    <span class="pack-price">25,00 €</span>
                    <span class="pack-badge">Populaire</span>
                  </div>
                </label>
                
                <label class="pack-credit">
                  <input type="radio" name="pack" value="100" required>
                  <div class="pack-content">
                    <span class="pack-amount">100 crédits</span>
                    <span class="pack-price">40,00 €</span>
                    <span class="pack-badge">Meilleure offre</span>
                  </div>
                </label>
              </div>
              
              <div class="simulation-paiement">
                <p><strong>Simulation de paiement (projet académique)</strong></p>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1rem;">
                  <span class="material-symbols-outlined">check_circle</span>
                  Confirmer la recharge
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Actions Trajet -->
      <div class="modal-overlay" id="modalActions" style="display:none;">
        <div class="modal modal-actions">
          <div class="modal-header">
            <h3>Actions pour ce trajet</h3>
            <button type="button" class="close-modal" id="closeActions">&times;</button>
          </div>
          <div class="modal-body">
            <div id="actionsContainer">
              <!-- Rempli dynamiquement -->
            </div>
          </div>
        </div>
      </div>

      <!-- Modal pour laisser un avis -->
      <div class="modal-overlay" id="modalAvis">
        <div class="modal">
          <div class="modal-header">
            <h3>Laisser un avis</h3>
            <button type="button" class="close-modal" id="closeModal">
                <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          
          <form id="formAvis" class="modal-body">
            <div class="form-group">
              <label for="note">Note <span class="required">*</span></label>
              <div class="star-rating" id="starRating">
                <span class="star empty material-symbols-outlined" data-value="1">star</span>
                <span class="star empty material-symbols-outlined" data-value="2">star</span>
                <span class="star empty material-symbols-outlined" data-value="3">star</span>
                <span class="star empty material-symbols-outlined" data-value="4">star</span>
                <span class="star empty material-symbols-outlined" data-value="5">star</span>
              </div>
              <input type="hidden" id="note" name="note" required>
            </div>
            
            <div class="form-group">
              <label for="commentaire">Commentaire (optionnel)</label>
              <textarea 
                  id="commentaire" 
                  name="commentaire" 
                  class="form-control" 
                  placeholder="Partagez votre expérience de voyage..."
                  maxlength="500"
              ></textarea>
              <div class="char-counter">
                  <span id="charCount">0</span>/500 caractères
              </div>
            </div>

            <div class="form-group">
              <label>Le trajet s'est-il bien passé ? <span class="required">*</span></label>
              <div class="radio-group">
                <div class="radio-item">
                  <input type="radio" id="trajetOui" name="trajetReussi" value="oui" required>
                  <label for="trajetOui">Oui</label>
                </div>
                <div class="radio-item">
                  <input type="radio" id="trajetNon" name="trajetReussi" value="non">
                  <label for="trajetNon">Non</label>
                </div>
              </div>
            </div>

            <div class="signalement-field" id="signalementField">
              <div class="form-group">
                <label for="signalement">
                  <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.5rem;">warning</span>
                  Signalement <span class="required">*</span>
                </label>
                <textarea 
                    id="signalement" 
                    name="signalement" 
                    class="form-control" 
                    placeholder="Décrivez le problème rencontré..."
                    maxlength="500"
                ></textarea>
                <div class="char-counter">
                  <span id="signalementCharCount">0</span>/500 caractères
                </div>
              </div>
            </div>

            <input type="hidden" id="trajetId" name="trajetId">
          </form>

          <div class="modal-buttons">
            <button type="button" class="btn btn-secondary" id="annulerAvis">Annuler</button>
            <button type="button" class="btn btn-primary" id="soumettreAvis" disabled>Soumettre l'avis</button>
          </div>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
    
    <!-- JavaScript -->
    <script>
      // Passer les données PHP au JavaScript
      window.ecoRideData = {
        apiUrl: '/ecoride/php/api/api-router.php',
        user: <?= json_encode($user) ?>,
        vehicules: <?= json_encode($vehicules) ?>,
        preferences: <?= json_encode($preferences) ?>,
        trajetsAVenir: <?= json_encode($trajetsAVenir) ?>,
        trajetsPasses: <?= json_encode($trajetsPasses) ?>
      };
    </script>
    <script type="module" src="/ecoride/js/script.js"></script>
  </body>
</html>