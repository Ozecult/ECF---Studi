<?php
// 1. Vérifier l'ID - soit trajet soit profil utilisateur
$trajetId = $_GET['trajet'] ?? null;
$userId = $_GET['user'] ?? null;

// Variables par défaut
$trajetDetails       = null;
$prixTrajet          = 0;
$creditsInsuffisants = false;
$creditsUtilisateur  = 0;
$modeProfilSeul      = false;
$noteArrondie        = 0;
$boutonReserver      = '';

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
  require_once __DIR__ . '/../config/Database.php';
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

  // VÉRIFIER SI L'UTILISATEUR EST CONNECTÉ AVANT D'ACCÉDER À SES DONNÉES
  $creditsUtilisateur = 0;
  $creditsInsuffisants = false;
  
  if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
      require_once __DIR__ . '/../models/User.php';
      $userModel = new User();
      $currentUser = $userModel->getUserById($_SESSION['user_id']);
      $creditsUtilisateur = $currentUser['credits'] ?? 0;
      $prixTrajet = $trajetDetails['prix_par_passager'] ?? 0;
      $creditsInsuffisants = $creditsUtilisateur < $prixTrajet;
  }
  
  $noteArrondie = round($trajetDetails['note_moyenne'] ?? 0);
  $modeProfilSeul = false;
}
// GESTION DU BOUTON RÉSERVER SELON CONNEXION
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
  // Non connecté → bouton connexion
  $boutonReserver = '
  <a href="/php/index.php?page=connexion&redirect=' . urlencode($_SERVER['REQUEST_URI']) . '" class="bouton-validation" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;">
    <span class="material-symbols-outlined">login</span>
    <strong>Connectez-vous pour réserver</strong>
  </a>';
} else {
  // Connecté → formulaire de réservation
  if ($trajetDetails) {
    if ($creditsInsuffisants) {
      $boutonReserver = '
      <div class="message-credits-insuffisants">
        <span class="material-symbols-outlined" style="color:#dc3545;font-size:2rem;">error</span>
        <p style="color:#dc3545;font-weight:600;">Crédits insuffisants</p>
        <p style="color:var(--noir-secondaire);">
          Vous avez <strong>' . $creditsUtilisateur . ' crédits</strong>, 
          mais ce trajet coûte <strong>' . $prixTrajet . ' crédits</strong>
        </p>
        <button type="button" class="btn-recharge-details" style="padding:0.8rem 1.5rem;background:var(--vert-clair);color:white;border:none;border-radius:8px;display:inline-block;margin-top:1rem;cursor:pointer;font-weight:600;">
            Recharger mes crédits
        </button>
      </div>';
    } else {
      // Formulaire avec sélecteur de passagers
      $placesDisponibles = $trajetDetails['places_disponibles'] ?? 4;
      
      $boutonReserver = '
      <form method="post" action="/php/api/api-router.php?action=reserver-trajet" id="form-reservation">
        <input type="hidden" name="trajet_id" value="' . $trajetDetails['id'] . '">
        
        <!-- Sélecteur nombre de passagers -->
        <div style="margin:1rem 0;">
          <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;font-weight:600;">
            <span class="material-symbols-outlined">group</span>
            Nombre de passagers
          </label>
          <select name="nombre_places" id="nombre_passagers" style="width:100%;padding:0.8rem;border:2px solid var(--vert-clair);border-radius:8px;font-size:1rem;">
            ';
            
      for ($i = 1; $i <= min($placesDisponibles, 8); $i++) {
        $boutonReserver .= '<option value="' . $i . '">' . $i . ' passager' . ($i > 1 ? 's' : '') . '</option>';
      }
      
      $boutonReserver .= '
          </select>
        </div>
        
        <!-- Prix détails -->
        <div style="background:var(--vert-fond);padding:1rem;border-radius:8px;margin:1rem 0;">
          <p style="margin:0.3rem 0;">Prix par passager : <strong>' . $prixTrajet . ' crédits</strong></p>
          <p style="margin:0.3rem 0;font-size:1.2rem;color:var(--vert-clair);">
            Prix total : <strong id="prix-total-affiche">' . $prixTrajet . '</strong> crédits
          </p>
        </div>
        
        <button class="bouton-validation" type="submit">
          <span class="material-symbols-outlined">task_alt</span>
          <strong>Payer <span id="prix-bouton">' . $prixTrajet . '</span> crédits</strong>
        </button>
      </form>';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoRide - <?= $modeProfilSeul ? 'Profil de ' . htmlspecialchars($userProfile['prenom']) : 'Détails du covoiturage' ?></title>
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/js/script.js"></script>
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
          <p style="margin:0.5rem 0 0 0;">Vous pouvez consulter vos trajets dans votre <a href="/php/index.php?page=utilisateur" style="color:white;text-decoration:underline;">espace personnel</a>.</p>
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

            <?php if (!empty($creditsInsuffisants)): ?>
              <div class="alert alert-warning">Crédits insuffisants</div>
            <?php endif; ?>

            
            <?php
            // Création/validation de l'objet DateTime
            try {
                $dateDepart = new \DateTime($trajetDetails['date_depart'] ?? 'now');
            } catch (\Exception $e) {
                // en cas de chaîne invalide on prend la date actuelle pour éviter erreurs
                $dateDepart = new \DateTime();
            }

            // Heures (conservées comme avant)
            $heureDepart = $dateDepart->format('H\hi');
            $heureArrivee = '??h??'; // garde ta logique ici si tu veux calculer réelle

            // Formatage de la date en français : priorité à Intl, sinon fallback traduit
            $dateFormatee = '';

            // Utilise IntlDateFormatter si disponible
            if (extension_loaded('intl') && class_exists(\IntlDateFormatter::class)) {
                $tzName = $dateDepart->getTimezone() ? $dateDepart->getTimezone()->getName() : 'Europe/Paris';
                $fmt = new \IntlDateFormatter(
                    'fr_FR',
                    \IntlDateFormatter::FULL,   // on utilisera le pattern pour contrôler le rendu
                    \IntlDateFormatter::NONE,
                    $tzName,
                    \IntlDateFormatter::GREGORIAN,
                    "EEEE d MMMM y"             // ex: "vendredi 10 octobre 2025"
                );
                $dateFormatee = $fmt->format($dateDepart);
                if ($dateFormatee === false) {
                    $dateFormatee = '';
                }
            }

            // Fallback si Intl absent ou erreur : formater en anglais puis traduire
            if (empty($dateFormatee)) {
                $raw = $dateDepart->format('l d F Y'); // ex: "Friday 10 October 2025"
                $jours = [
                    'Sunday'=>'dimanche','Monday'=>'lundi','Tuesday'=>'mardi','Wednesday'=>'mercredi',
                    'Thursday'=>'jeudi','Friday'=>'vendredi','Saturday'=>'samedi'
                ];
                $mois = [
                    'January'=>'janvier','February'=>'février','March'=>'mars','April'=>'avril',
                    'May'=>'mai','June'=>'juin','July'=>'juillet','August'=>'août',
                    'September'=>'septembre','October'=>'octobre','November'=>'novembre','December'=>'décembre'
                ];
                $parts = preg_split('/\s+/', $raw); // [0]=dayName [1]=dayNum [2]=monthName [3]=year
                $dayName = $jours[$parts[0]] ?? $parts[0];
                $dayNum = $parts[1] ?? '';
                $monthName = $mois[$parts[2]] ?? $parts[2];
                $year = $parts[3] ?? '';
                $dateFormatee = trim("$dayName $dayNum $monthName $year");
            }
            ?>

            <!-- Date trajet -->
            <div class="date-trajet">
              <span><?= htmlspecialchars($dateFormatee, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <div class="time-bar">
              <span class="heure-depart"><?= htmlspecialchars($heureDepart, ENT_QUOTES, 'UTF-8') ?></span>
              <div class="barre-verte"></div>
              <span class="heure-arrivee"><?= htmlspecialchars($heureArrivee, ENT_QUOTES, 'UTF-8') ?></span>
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

            <?php if (isset($prixTrajet) && is_numeric($prixTrajet)): ?>
              <p class="prix"><?= htmlspecialchars((string)$prixTrajet, ENT_QUOTES, 'UTF-8') ?> crédits</p>
            <?php elseif (!empty($trajetDetails['prix_par_passager']) && is_numeric($trajetDetails['prix_par_passager'])): ?>
              <p class="prix"><?= htmlspecialchars((string)$trajetDetails['prix_par_passager'], ENT_QUOTES, 'UTF-8') ?> crédits</p>
            <?php endif; ?>

            <!-- Bouton réservation -->
            <?= $boutonReserver ?>

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
      <!-- Script modal + calcul heure d'arrivée -->
      <script>
          document.addEventListener('DOMContentLoaded', async function() {
          // ========== MODAL RECHARGE ==========
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
                const response = await fetch('/php/api/api-router.php?action=recharger-credits', {
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

          // ========== CALCUL PRIX PAR PASSAGERS ========== 
          const selectPassagers = document.getElementById('nombre_passagers');
          const prixTotalAffiche = document.getElementById('prix-total-affiche');
          const prixBouton = document.getElementById('prix-bouton');
          
          if (selectPassagers && prixTotalAffiche && prixBouton) {
            const prixUnitaire = parseInt(prixTotalAffiche.textContent);
            
            selectPassagers.addEventListener('change', function() {
              const nbPassagers = parseInt(this.value);
              const prixTotal = nbPassagers * prixUnitaire;
              
              prixTotalAffiche.textContent = prixTotal;
              prixBouton.textContent = prixTotal;
            });
          }
          
          // ========== CALCUL HEURE D'ARRIVÉE ==========
          const adresseDepart = "<?= addslashes($trajetDetails['adresse_depart']) ?>";
          const adresseArrivee = "<?= addslashes($trajetDetails['adresse_arrivee']) ?>";
          const heureDepart = "<?= $dateDepart->format('H:i') ?>";
          const heureArriveeElement = document.querySelector('.heure-arrivee');
          
          // Clé API OpenRouteService
          const API_KEY = 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImNlNzQwOWM0YWU2NjQyMTQ4OTYwYzliODc1OWQxZDRkIiwiaCI6Im11cm11cjY0In0=';
          
          try {
            // 1. Géocoder les adresses
            const [geocodeDepart, geocodeArrivee] = await Promise.all([
              fetch(`https://api.openrouteservice.org/geocode/search?api_key=${API_KEY}&text=${encodeURIComponent(adresseDepart)}`),
              fetch(`https://api.openrouteservice.org/geocode/search?api_key=${API_KEY}&text=${encodeURIComponent(adresseArrivee)}`)
            ]);
            
            const [dataDepart, dataArrivee] = await Promise.all([
              geocodeDepart.json(),
              geocodeArrivee.json()
            ]);
            
            const coordDepart = dataDepart.features[0].geometry.coordinates;
            const coordArrivee = dataArrivee.features[0].geometry.coordinates;
            
            // 2. Calculer l'itinéraire
            const route = await fetch(`https://api.openrouteservice.org/v2/directions/driving-car?api_key=${API_KEY}`, {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({
                coordinates: [coordDepart, coordArrivee]
              })
            });
            
            const routeData = await route.json();
            const dureeSecondes = routeData.routes[0].summary.duration;
            const dureeMinutes = Math.round(dureeSecondes / 60);
            
            // 3. Calculer l'heure d'arrivée
            const [heures, minutes] = heureDepart.split(':').map(Number);
            const departDate = new Date();
            departDate.setHours(heures, minutes, 0, 0);
            
            const arriveeDate = new Date(departDate.getTime() + dureeMinutes * 60000);
            const heureArrivee = String(arriveeDate.getHours()).padStart(2, '0') + 'h' + 
                                String(arriveeDate.getMinutes()).padStart(2, '0');
            
            heureArriveeElement.textContent = heureArrivee;
            
          } catch (error) {
            console.error('Erreur calcul itinéraire:', error);
            // Fallback : estimation basique (100km = ~1h15)
            heureArriveeElement.textContent = '~1h30';
          }
        });
      </script>
    <?php endif; ?>
  </body>
</html>
