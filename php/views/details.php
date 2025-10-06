<?php
// 1. Vérifier l'ID - soit trajet soit profil utilisateur
$trajetId = $_GET['trajet'] ?? null;
$userId = $_GET['user'] ?? null;

// Mode "profil utilisateur seul"
if ($userId && !$trajetId) {
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../models/Preference.php';
    
    $userModel = new User();
    $preferenceModel = new Preference();
    
    $userProfile = $userModel->getUserById($userId);
    if (!$userProfile) {
        echo "Utilisateur introuvable";
        exit;
    }
    
    $preferences = $preferenceModel->getPreferencesByUser($userId);
    
    // Récupérer les avis
    require_once __DIR__ . '/../config/database.php';
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT a.*, u.prenom as auteur_prenom, u.photo_profil as auteur_photo
        FROM avis a
        JOIN utilisateurs u ON a.evaluateur_id = u.id
        WHERE a.evalue_id = ? AND a.statut = 'valide'
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $avisUtilisateur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $noteArrondie = round($userProfile['note_moyenne'] ?? 0);
    $modeProfilSeul = true;
}
// Mode "détails trajet"
else if ($trajetId) {
    require_once __DIR__ . '/../controllers/TrajetController.php';
    $trajetController = new TrajetController();
    $trajetDetails = $trajetController->getTrajetDetails($trajetId);

    if (!$trajetDetails) {
        echo "Trajet introuvable";
        exit;
    }

    require_once __DIR__ . '/../models/User.php';
    $userModel = new User();
    $currentUser = $userModel->getUserById($_SESSION['user_id']);
    $creditsUtilisateur = $currentUser['credits'] ?? 0;
    $prixTrajet = $trajetDetails['prix_par_passager'];
    $creditsInsuffisants = $creditsUtilisateur < $prixTrajet;
    
    $noteArrondie = round($trajetDetails['note_moyenne']);
    $modeProfilSeul = false;
} else {
    header('Location: /ecoride/php/index.php?page=covoiturages');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoRide - <?= $modeProfilSeul ? 'Profil de ' . htmlspecialchars($userProfile['prenom']) : 'Détails du covoiturage' ?></title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/ecoride/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main id="contenu" class="page-details">
      <?php if (!$modeProfilSeul && isset($_GET['success']) && $_GET['success'] === 'reservation'): ?>
        <div style="background:linear-gradient(135deg,var(--vert-clair),var(--vert-fond-menu-footer));color:white;padding:1.5rem;border-radius:12px;margin-bottom:2rem;text-align:center;box-shadow:0 4px 12px rgba(121,156,121,0.3);">
          <span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem;">check_circle</span>
          <strong style="font-size:1.2rem;">Réservation confirmée !</strong>
          <p style="margin:0.5rem 0 0 0;">Vous pouvez consulter vos trajets dans votre <a href="/ecoride/php/index.php?page=utilisateur" style="color:white;text-decoration:underline;">espace personnel</a>.</p>
        </div>
      <?php endif; ?>

      <?php if (!$modeProfilSeul): ?>
        <!-- BARRE DE RECHERCHE seulement en mode trajet -->
        <?php require __DIR__ . '/barrerecherche.php'; ?>
      <?php endif; ?>

      <!-- RESUME DU COVOITURAGE -->
      <div class="disposition">
        <!-- Card profil -->
        <div class="info-user">
          <section class="card-profil card">
            <h2>Profil <?= $modeProfilSeul ? 'de l\'utilisateur' : 'du conducteur' ?></h2>

            <div class="profil">
              <div class="header-card">
                <div class="user-img">
                  <?php 
                  // Choisir les bonnes variables selon le mode
                  if ($modeProfilSeul) {
                      $photo = $userProfile['photo_profil'] ?? null;
                      $prenom = $userProfile['prenom'];
                  } else {
                      $photo = $trajetDetails['photo_profil'] ?? null;
                      $prenom = $trajetDetails['conducteur_prenom'];
                  }
                  ?>
                  
                  <?php if ($photo): ?>
                    <img src="<?= htmlspecialchars($photo) ?>" alt="Photo de profil" class="photo-profil" width="100" height="100" />
                  <?php else: ?>
                    <span class="material-symbols-outlined" style="font-size:100px;">account_circle</span>
                  <?php endif; ?>
                  <p class="pseudo"><?= htmlspecialchars($prenom) ?></p>
                </div>

                <div class="etoiles">
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <span class="material-symbols-outlined <?= $i < $noteArrondie ? 'star-filled' : 'star-empty' ?>">star</span>
                  <?php endfor; ?>
                </div>
              </div>

              <?php 
              $bio = $modeProfilSeul ? ($userProfile['bio'] ?? '') : ($trajetDetails['bio'] ?? '');
              ?>
              <p><?= $bio ? nl2br(htmlspecialchars($bio)) : 'Cet utilisateur n\'a pas encore ajouté de description.' ?></p>

              <ul class="infos-trajets">
                <?php 
                $preferencesAffichage = $modeProfilSeul ? $preferences : $trajetDetails['preferences'];
                foreach ($preferencesAffichage as $pref): 
                  // Vérifier la valeur selon la source
                  $valeurPref = $modeProfilSeul ? $pref['valeur'] : ($pref['valeur'] ?? 'accepte');
                  if ($valeurPref === 'accepte'):
                ?>
                  <li>
                    <span class="material-symbols-outlined">
                      <?php
                      $icons = [
                        'fumeur' => 'smoking_rooms',
                        'animaux' => 'pets',
                        'enfants' => 'account_child',
                        'musique' => 'music_note'
                      ];
                      $nomPref = $modeProfilSeul ? $pref['type_nom'] : $pref['nom'];
                      echo $icons[$nomPref] ?? 'check_circle';
                      ?>
                    </span>
                    <?= ucfirst($nomPref) ?> autorisé(s)
                  </li>
                <?php 
                  endif;
                endforeach; 
                ?>
                
                <?php if (!$modeProfilSeul): ?>
                  <li>
                    <span class="material-icons">
                      <?= $trajetDetails['energie'] === 'electrique' ? 'electric_car' : 'directions_car' ?>
                    </span>
                    <?= htmlspecialchars($trajetDetails['marque']) ?> <?= htmlspecialchars($trajetDetails['modele']) ?>
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          </section>

          <section class="avis card">
            <h2>Avis <?= $modeProfilSeul ? 'de l\'utilisateur' : 'du conducteur' ?></h2>
            
            <?php 
            $avisAffichage = $modeProfilSeul ? $avisUtilisateur : $trajetDetails['avis'];
            if (empty($avisAffichage)): 
            ?>
              <p style="text-align:center; padding:2rem; color:var(--noir-secondaire);">Aucun avis pour le moment</p>
            <?php else: ?>
              <?php foreach ($avisAffichage as $avis): ?>
                <article class="avis-item card">
                  <h3>Avis de <?= htmlspecialchars($avis['auteur_prenom']) ?></h3>
                  <div class="user-img">
                    <?php if ($avis['auteur_photo']): ?>
                      <img src="<?= htmlspecialchars($avis['auteur_photo']) ?>" alt="Photo" class="photo-profil" width="60" height="60" />
                    <?php else: ?>
                      <span class="material-symbols-outlined">account_circle</span>
                    <?php endif; ?>
                    <div class="etoiles">
                      <?php for ($i = 0; $i < 5; $i++): ?>
                        <span class="material-symbols-outlined <?= $i < $noteArrondie ? 'star-filled' : 'star-empty' ?>">star</span>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </section>
        </div>

        <?php if (!$modeProfilSeul): ?>
          <!-- Section RESERVATION seulement en mode trajet -->
          <section class="reservation card">
            <h2>Réservation</h2>
            
            <?php
            $dateDepart = new DateTime($trajetDetails['date_depart']);
            $heureDepart = $dateDepart->format('H\hi');
            $heureArrivee = $trajetDetails['date_arrivee_estimee'] ? 
              (new DateTime($trajetDetails['date_arrivee_estimee']))->format('H\hi') : 
              '??h??';
            ?>
            
            <!-- Date trajet -->
            <div class="date-trajet">
              <span><?= strftime('%A %d %B %Y', $dateDepart->getTimestamp()) ?></span>
            </div>

            <div class="time-bar">
              <span class="heure-depart"><?= $heureDepart ?></span>
              <div class="barre-verte"></div>
              <span class="heure-arrivee"><?= $heureArrivee ?></span>
            </div>

            <!-- Trajet -->
            <div class="trajet-infos">
              <div><strong>Départ :</strong> <?= htmlspecialchars($trajetDetails['adresse_depart']) ?></div>
              <div><strong>Arrivée :</strong> <?= htmlspecialchars($trajetDetails['adresse_arrivee']) ?></div>
            </div>

            <div class="user-img">
              <?php if ($trajetDetails['photo_profil']): ?>
                <img src="<?= htmlspecialchars($trajetDetails['photo_profil']) ?>" alt="Photo de profil" class="photo-profil" width="100" height="100" />
              <?php else: ?>
                <span class="material-symbols-outlined" style="font-size:100px;">account_circle</span>
              <?php endif; ?>
              <p><?= htmlspecialchars($trajetDetails['conducteur_prenom']) ?></p>
            </div>

            <ul class="infos-trajets">
              <?php if ($trajetDetails['energie'] === 'electrique'): ?>
                <li>
                  <span class="material-icons">electric_car</span>
                  Véhicule électrique !
                </li>
                <li>
                  <span class="material-icons">power_off</span>
                  Pas besoin de recharge !
                </li>
              <?php else: ?>
                <li>
                  <span class="material-icons">local_gas_station</span>
                  Véhicule thermique
                </li>
              <?php endif; ?>
            </ul>

            <!-- Bouton réservation -->
            <?php if ($creditsInsuffisants): ?>
              <div class="message-credits-insuffisants">
                <span class="material-symbols-outlined" style="color:#dc3545;font-size:2rem;">error</span>
                <p style="color:#dc3545;font-weight:600;">Crédits insuffisants</p>
                <p style="color:var(--noir-secondaire);">
                  Vous avez <strong><?= $creditsUtilisateur ?> crédits</strong>, 
                  mais ce trajet coûte <strong><?= $prixTrajet ?> crédits</strong>
                </p>
                <button type="button" class="btn-recharge-details" style="padding:0.8rem 1.5rem;background:var(--vert-clair);color:white;border:none;border-radius:8px;display:inline-block;margin-top:1rem;cursor:pointer;font-weight:600;">
                  Recharger mes crédits
                </button>
              </div>
            <?php else: ?>
              <form method="post" action="/ecoride/php/api/api-router.php?action=reserver-trajet">
                <input type="hidden" name="trajet_id" value="<?= $trajetDetails['id'] ?>">
                <button class="bouton-validation" type="submit">
                  <span class="material-symbols-outlined">task_alt</span>
                  <strong>Payer <?= $prixTrajet ?> crédits</strong>
                </button>
              </form>
            <?php endif; ?>
          </section>
        <?php endif; ?>
      </div>

      <!-- Modal Recharge -->
      <?php if (!$modeProfilSeul && $creditsInsuffisants): ?>
      <div class="modal-overlay" id="modalRechargeDetails" style="display:none;">
        <div class="modal">
          <div class="modal-header">
            <h3>Recharger mes crédits</h3>
            <button type="button" class="close-modal" id="closeRechargeDetails">&times;</button>
          </div>
          <div class="modal-body">
            <p style="text-align:center;margin-bottom:1.5rem;color:var(--noir-secondaire);">
              Sélectionnez un pack de crédits (1 crédit = 0,50€)
            </p>
            <form id="formRechargeDetails">
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
      <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>

    <?php if (!$modeProfilSeul): ?>
    <!-- Script modal seulement en mode trajet -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const btnRecharge = document.querySelector('.btn-recharge-details');
        const modal = document.getElementById('modalRechargeDetails');
        const closeBtn = document.getElementById('closeRechargeDetails');
        const form = document.getElementById('formRechargeDetails');
        
        if (btnRecharge && modal) {
          btnRecharge.addEventListener('click', () => modal.style.display = 'flex');
          closeBtn?.addEventListener('click', () => modal.style.display = 'none');
          modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
          });
          
          form?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const pack = document.querySelector('input[name="pack"]:checked')?.value;
            
            try {
              const response = await fetch('/ecoride/php/api/api-router.php?action=recharger-credits', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `montant=${pack}`,
                credentials: 'same-origin'
              });
              
              const data = await response.json();
              if (data.success) {
                modal.style.display = 'none';
                window.location.reload();
              }
            } catch (error) {
              alert('Erreur lors de la recharge');
            }
          });
        }
      });
    </script>
    <?php endif; ?>
  </body>
</html>