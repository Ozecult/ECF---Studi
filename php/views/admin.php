<?php
if (!isset($authController)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $authController = new AuthController();
}

// Vérifier que l'utilisateur est connecté
if (!$authController->isLoggedIn()) {
    header('Location: /ecoride/php/index.php?page=connexion');
    exit;
}

// Vérifier que c'est un admin
$currentUser = $authController->getCurrentUser();
if (!$currentUser || ($currentUser['pseudo'] !== 'admin' && $currentUser['email'] !== 'admin@ecoride.fr')) {
    http_response_code(403);
    echo "<h1>403 — Accès refusé</h1><p>Vous n'avez pas les droits d'administrateur.</p>";
    exit;
}

// Charger les données pour la page
require_once __DIR__ . '/../controllers/AdminController.php';
$adminController = new AdminController();

$users = $adminController->getUsersWithSignalements();
$stats = $adminController->getStatistics();
$csrfToken = $authController->generateCSRFToken();

?>

<!DOCTYPE html>
<html lang="fr">
  <html lang="fr">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta
        name="description"
        content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
      />
      <title>EcoRide - Espace administrateur</title>
      <link rel="stylesheet" href="/ecoride/css/style.css" />
      <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
      />
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
      <script type="module" src="/ecoride/js/script.js"></script>
      <script>
        window.statsData = <?= json_encode([
          'trajets_aujourdhui' => $stats['trajets_aujourdhui'],
          'total_credits' => $stats['total_credits'],
          'credits_par_jour' => $stats['credits_par_jour'],
          'trajets_par_jour' => $stats['trajets_par_jour'] ?? []
        ]) ?>;
      </script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    </head>
    <body>
      <!-- HEADER -->
        <?php require 'header.php'; ?>
      <!-- MAIN -->
      <main class="administrateur">
        <h1>Espace administrateur</h1>

        <!-- Créer un compte employé -->
        <section class="creation-employe">
          <h2>Créer un compte employé</h2>

          <!-- formulaire -->
          <form method="post" action="/ecoride/php/api/api-router.php?action=create-employee" class="compte card" autocomplete="on">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES) ?>">
            <fieldset>
              <legend>Créer un compte employé</legend>
              <!-- nom -->
              <div class="compte">
                <label for="nom"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Nom"
                    role="img"
                    >badge</span
                  >Nom :</label
                >
                <input type="text" name="nom" id="nom" required />
              </div>
              <!-- prénom -->
              <div class="compte">
                <label for="prenom"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Prénom"
                    role="img"
                    >person</span
                  >Prénom :</label
                >
                <input type="text" name="prenom" id="prenom" required />
              </div>
              <!-- email -->
              <div class="compte">
                <label for="email"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Email"
                    role="img"
                    >email</span
                  >Email :</label
                >
                <input type="email" name="email" id="email" required />
              </div>
              <!-- password -->
              <div class="compte">
                <label for="password"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Password"
                    role="img"
                    >lock</span
                  >Mot de passe :</label
                >
                <input type="password" name="password" id="password" required />
              </div>
              <!-- password2 -->
              <div class="compte">
                <label for="password2"
                  ><span
                    class="material-symbols-outlined"
                    aria-label="Password2"
                    role="img"
                    >lock_reset</span
                  >Confirmez le mot de passe :</label
                >
                <input
                  type="password"
                  name="password2"
                  id="password2"
                  required
                />
              </div>

              <button class="button" type="submit" aria-label="creer">
                Créer le compte
              </button>
            </fieldset>
          </form>
        </section>

        <!-- Statistiques -->
        <section class="statistiques card">
          <h2>Statistiques</h2>

          <div class="graphiques-container">
            <!-- Graphique Covoiturages -->
            <div class="graphique-wrapper">
              <h3>Covoiturages (7 derniers jours)</h3>
              <canvas id="graph-covoiturages"></canvas>
            </div>

            <!-- Graphique Crédits -->
            <div class="graphique-wrapper">
              <h3>Crédits générés (7 derniers jours)</h3>
              <canvas id="graph-credits"></canvas>
            </div>
          </div>

          <!-- Totaux globaux -->
          <div class="totaux-globaux">
            <p id="trajets-info"><strong>Total trajets : <?= number_format($stats['total_trajets']) ?></strong></p>
            <p id="credits-info"><strong>Total crédits : <?= number_format($stats['total_credits']) ?> crédits</strong></p>
          </div>
        </section>

        <!-- Liste des utilisateurs -->
        <section class="liste-utilisateur card">
          <h2>Liste des utilisateurs</h2>

          <?php
          // Calculer les totaux signalements
          $totalSignalementsActifs = array_sum(array_column($users, 'signalements_actifs'));
          $totalDemandesSuspension = array_sum(array_column($users, 'demandes_suspension'));

          if ($totalSignalementsActifs > 0 || $totalDemandesSuspension > 0): ?>
            <div class="compteurs-signalements">
              <?php if ($totalSignalementsActifs > 0): ?>
                <div class="compteur-item warning">
                  <span class="material-symbols-outlined">warning</span>
                  <span><?= $totalSignalementsActifs ?> signalement<?= $totalSignalementsActifs > 1 ? 's' : '' ?> actif<?= $totalSignalementsActifs > 1 ? 's' : '' ?></span>
                </div>
              <?php endif; ?>
              
              <?php if ($totalDemandesSuspension > 0): ?>
                <div class="compteur-item danger">
                  <span class="material-symbols-outlined">report</span>
                  <span><?= $totalDemandesSuspension ?> demande<?= $totalDemandesSuspension > 1 ? 's' : '' ?> de suspension</span>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <table class="user">
            <thead>
              <tr>
                <th scope="col">
                  <span class="material-symbols-outlined" aria-label="Utilisateur" role="img">person</span>Utilisateur
                </th>
                <th scope="col">
                  <span class="material-symbols-outlined" aria-label="Email" role="img">email</span>Email
                </th>
                <th scope="col">
                  <span class="material-symbols-outlined" aria-label="Rôle" role="img">assignment_ind</span>Rôle
                </th>
                <th scope="col">
                  <span class="material-symbols-outlined" aria-label="Signalements" role="img">warning</span>Signalements
                </th>
                <th scope="col">
                  <span class="material-symbols-outlined" aria-label="Action" role="img">touch_app</span>Action
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): 
                $hasSignalements = $user['signalements_actifs'] > 0;
                $hasDemandeSuspension = $user['demandes_suspension'] > 0;
                
                $rowClass = '';
                if ($hasDemandeSuspension) {
                  $rowClass = 'user-demande-suspension';
                } elseif ($hasSignalements) {
                  $rowClass = 'user-signalement-actif';
                }
              ?>
                <tr class="<?= $rowClass ?>">
                  <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                  <td><?= htmlspecialchars($user['email']) ?></td>
                  <td><?= htmlspecialchars($user['role']) ?></td>
                  <td>
                    <?php 
                    $totalSignalements = $user['signalements_actifs'] + ($user['signalements_resolus'] ?? 0);
                    if ($totalSignalements > 0 || $hasDemandeSuspension): 
                    ?>
                      <div style="display:flex; flex-direction:column; gap:0.3rem; align-items:center;">
                        <?php if ($user['signalements_actifs'] > 0): ?>
                          <span class="badge-signalement">
                            <?= $user['signalements_actifs'] ?> actif<?= $user['signalements_actifs'] > 1 ? 's' : '' ?>
                          </span>
                        <?php endif; ?>
                        <?php if (isset($user['signalements_resolus']) && $user['signalements_resolus'] > 0): ?>
                          <span class="badge-resolu">
                            <?= $user['signalements_resolus'] ?> résolu<?= $user['signalements_resolus'] > 1 ? 's' : '' ?>
                          </span>
                        <?php endif; ?>
                        <?php if ($hasDemandeSuspension): ?>
                          <span class="badge-suspension">
                            <?= $user['demandes_suspension'] ?> urgent<?= $user['demandes_suspension'] > 1 ? 's' : '' ?>
                          </span>
                        <?php endif; ?>
                      </div>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="actions-admin">
                      <?php if ($user['statut'] === 'actif'): ?>
                        <form method="post" action="/ecoride/php/api/api-router.php?action=suspend-user" style="display:inline;">
                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                          <button type="submit" class="btn-suspend">Suspendre</button>
                        </form>
                      <?php else: ?>
                        <form method="post" action="/ecoride/php/api/api-router.php?action=reactivate-user" style="display:inline;">
                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                          <button type="submit" class="btn-reactivate">Réactiver</button>
                        </form>
                      <?php endif; ?>
                      
                      <?php if ($hasDemandeSuspension): ?>
                        <form method="post" action="/ecoride/php/api/api-router.php?action=ignorer-demande-suspension" style="display:inline;">
                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                          <button type="submit" class="btn-ignorer">Ignorer</button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Version mobile -->
          <div class="user-mobile">
            <?php foreach ($users as $user): 
              $hasSignalements = $user['signalements_actifs'] > 0;
              $hasDemandeSuspension = $user['demandes_suspension'] > 0;
              
              $cardClass = '';
              if ($hasDemandeSuspension) {
                $cardClass = 'user-demande-suspension';
              } elseif ($hasSignalements) {
                $cardClass = 'user-signalement-actif';
              }
            ?>
              <div class="user-card <?= $cardClass ?>">
                <div class="user-info">
                  <div class="user-info-header">
                    <span class="material-symbols-outlined">person</span>
                    <span class="label">Utilisateur</span>
                  </div>
                  <div class="value"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
                </div>
                
                <div class="user-info">
                  <div class="user-info-header">
                    <span class="material-symbols-outlined">email</span>
                    <span class="label">Email</span>
                  </div>
                  <div class="value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <div class="user-info">
                  <div class="user-info-header">
                    <span class="material-symbols-outlined">assignment_ind</span>
                    <span class="label">Rôle</span>
                  </div>
                  <div class="value"><?= htmlspecialchars($user['role']) ?></div>
                </div>
                
                  <?php 
                  $totalSignalements = $user['signalements_actifs'] + ($user['signalements_resolus'] ?? 0);
                  if ($totalSignalements > 0 || $hasDemandeSuspension): 
                  ?>
                  <div class="user-info">
                    <div class="user-info-header">
                      <span class="material-symbols-outlined">warning</span>
                      <span class="label">Signalements</span>
                    </div>
                    <div class="value" style="display:flex; flex-direction:column; gap:0.3rem;">
                      <?php if ($user['signalements_actifs'] > 0): ?>
                        <span class="badge-signalement">
                          <?= $user['signalements_actifs'] ?> actif<?= $user['signalements_actifs'] > 1 ? 's' : '' ?>
                        </span>
                      <?php endif; ?>
                      <?php if (isset($user['signalements_resolus']) && $user['signalements_resolus'] > 0): ?>
                        <span class="badge-resolu">
                          <?= $user['signalements_resolus'] ?> résolu<?= $user['signalements_resolus'] > 1 ? 's' : '' ?>
                        </span>
                      <?php endif; ?>
                      <?php if ($hasDemandeSuspension): ?>
                        <span class="badge-suspension">
                          <?= $user['demandes_suspension'] ?> urgent<?= $user['demandes_suspension'] > 1 ? 's' : '' ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php endif; ?>
                
                <div class="user-info">
                  <div class="user-info-header">
                    <span class="material-symbols-outlined">touch_app</span>
                    <span class="label">Actions</span>
                  </div>
                  <div class="value actions-admin-mobile">
                    <?php if ($user['statut'] === 'actif'): ?>
                      <form method="post" action="/ecoride/php/api/api-router.php?action=suspend-user">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn-suspend">Suspendre</button>
                      </form>
                    <?php else: ?>
                      <form method="post" action="/ecoride/php/api/api-router.php?action=reactivate-user">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn-reactivate">Réactiver</button>
                      </form>
                    <?php endif; ?>
                    
                    <?php if ($hasDemandeSuspension): ?>
                      <form method="post" action="/ecoride/php/api/api-router.php?action=ignorer-demande-suspension">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn-ignorer">Ignorer</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      </main>
      <!-- FOOTER -->
      <?php require 'footer.php'; ?>
    </body>
  </html>
</html>
