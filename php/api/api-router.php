<?php
// api/api-router.php

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// CORS si nécessaire
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Fonction pour vérifier l'authentification
function checkAuth() 
{
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();
    
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
}

// Fonction pour répondre en JSON
function jsonResponse($success, $message, $data = [], $httpCode = 200) 
{
    http_response_code($httpCode);
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Router basé sur l'action
$action = $_GET['action'] ?? '';

try {
    require_once __DIR__ . '/../config/database.php';
    $db = Database::getInstance()->getConnection();
    
    switch ($action) {
        
        // ========================================
        // AUTHENTIFICATION & DONNÉES UTILISATEUR
        // ========================================
        
        case 'get-user-data':
            // Récupère toutes les données de l'utilisateur connecté
            checkAuth();
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->getUserDataApi();
            break;
        
        case 'update-role':
            // Met à jour le rôle de l'utilisateur (chauffeur/passager/duo)
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->updateRole();
            break;
        
        case 'update-bio':
            // Met à jour la bio du profil utilisateur
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $bio = $_POST['bio'] ?? '';
            
            if (strlen($bio) > 500) {
                jsonResponse(false, "La bio ne peut pas dépasser 500 caractères");
            }
            
            $stmt = $db->prepare("UPDATE utilisateurs SET bio = ? WHERE id = ?");
            $stmt->execute([$bio, $_SESSION['user_id']]);
            
            jsonResponse(true, "Bio mise à jour avec succès");
            break;
        
        case 'upload-photo':
            // Upload de la photo de profil
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            if (!isset($_FILES['photo'])) {
                jsonResponse(false, "Aucune photo fournie");
            }
            
            $file = $_FILES['photo'];
            
            // Validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                jsonResponse(false, "Type de fichier non autorisé (JPG, PNG, WebP uniquement)");
            }
            
            if ($file['size'] > 2 * 1024 * 1024) {
                jsonResponse(false, "Fichier trop volumineux (max 2MB)");
            }
            
            $uploadDir = __DIR__ . '/../../uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Récupérer l'ancienne photo
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $userData = $userModel->getUserById($_SESSION['user_id']);
            $anciennePhoto = $userData['photo_profil'] ?? null;
            
            // Générer un nom unique
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $photoUrl = '/ecoride/uploads/photos/' . $filename;
                
                $updateSuccess = $userModel->updateUser($_SESSION['user_id'], ['photo_profil' => $photoUrl]);
                
                if ($updateSuccess) {
                    // Supprimer l'ancienne photo
                    if ($anciennePhoto && $anciennePhoto !== $photoUrl) {
                        $ancienFichier = basename($anciennePhoto);
                        $ancienChemin = $uploadDir . $ancienFichier;
                        if (file_exists($ancienChemin)) {
                            unlink($ancienChemin);
                        }
                    }
                    jsonResponse(true, "Photo mise à jour", ['photo_url' => $photoUrl]);
                } else {
                    unlink($uploadPath);
                    jsonResponse(false, "Erreur lors de la mise à jour en base");
                }
            } else {
                jsonResponse(false, "Erreur lors de l'upload du fichier");
            }
            break;
        
        case 'recharger-credits':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $montant = intval($_POST['montant'] ?? 0);
            
            if (!in_array($montant, [20, 50, 100])) {
                jsonResponse(false, "Montant invalide");
            }
            
            $db->beginTransaction();
            try {
                // Récupérer le solde actuel
                $stmt = $db->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $soldeAvant = $stmt->fetchColumn();
                
                // Ajouter les crédits
                $stmt = $db->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?");
                $stmt->execute([$montant, $_SESSION['user_id']]);
                
                $soldeApres = $soldeAvant + $montant;
                
                // Enregistrer la transaction
                $stmt = $db->prepare("
                    INSERT INTO transactions (
                        utilisateur_id, 
                        type_transaction, 
                        montant, 
                        solde_avant, 
                        solde_apres, 
                        description,
                        created_at
                    ) VALUES (?, 'recharge', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $montant,
                    $soldeAvant,
                    $soldeApres,
                    "Achat de $montant crédits"
                ]);
                
                $db->commit();
                jsonResponse(true, "Crédits rechargés", ['nouveaux_credits' => $soldeApres]);
                
            } catch (Exception $e) {
                $db->rollBack();
                jsonResponse(false, "Erreur: " . $e->getMessage());
            }
            break;
        
        // ========================================
        // GESTION DES VÉHICULES
        // ========================================
        
        case 'add-vehicule':
            // Ajoute un véhicule à l'utilisateur
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->addVehicule();
            break;
        
        case 'get-vehicules':
            // Récupère tous les véhicules de l'utilisateur
            checkAuth();
            require_once __DIR__ . '/../models/Vehicule.php';
            $vehiculeModel = new Vehicule();
            $vehicules = $vehiculeModel->getVehiculesByUser($_SESSION['user_id']);
            jsonResponse(true, "Véhicules récupérés", ['vehicules' => $vehicules]);
            break;
        
        // ========================================
        // GESTION DES PRÉFÉRENCES
        // ========================================
        
        case 'update-preferences':
            // Met à jour les préférences de l'utilisateur
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->updatePreferences();
            break;
        
        case 'add-custom-preference':
            // Ajoute une préférence personnalisée
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }

            $preference = $_POST['preference'] ?? '';
            $existingPreferences = isset($_POST['existing_preferences']) ? json_decode($_POST['existing_preferences'], true) : [];

            if (empty($preference)) {
                jsonResponse(false, "Préférence requise");
            }

            require_once __DIR__ . '/../models/Preference.php';
            $preferenceModel = new Preference();
            $preferenceModel->updateUserPreferences($_SESSION['user_id'], $existingPreferences, $preference);

            jsonResponse(true, "Préférence personnalisée ajoutée");
            break;
        
        // ========================================
        // GESTION DES TRAJETS
        // ========================================
        
        case 'create-trajet':
            // Créer un nouveau trajet
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->createTrajet();
            break;
        
        case 'search-trajets':
            // Rechercher des trajets disponibles (avec filtres)
            $depart = $_GET['depart'] ?? '';
            $destination = $_GET['destination'] ?? '';
            $date = $_GET['date'] ?? '';
            $passagers = $_GET['passagers'] ?? 1;
            
            if (empty($depart) || empty($destination) || empty($date)) {
                jsonResponse(false, "Paramètres manquants");
            }
            
            require_once __DIR__ . '/../controllers/TrajetController.php';
            $trajetController = new TrajetController();
            $trajets = $trajetController->rechercherTrajets($depart, $destination, $date, $passagers);
            
            jsonResponse(true, "Trajets trouvés", ['trajets' => $trajets]);
            break;
        
        case 'demarrer-trajet':
            // Démarrer un trajet planifié
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $trajetId = $_POST['trajet_id'] ?? '';
            if (empty($trajetId)) {
                jsonResponse(false, "ID trajet requis");
            }
            
            require_once __DIR__ . '/../models/Trajet.php';
            $trajetModel = new Trajet();
            $trajetModel->demarrerTrajet($trajetId, $_SESSION['user_id']);
            
            jsonResponse(true, "Trajet démarré avec succès");
            break;

        case 'terminer-trajet':
            // Terminer un trajet en cours
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $trajetId = $_POST['trajet_id'] ?? '';
            if (empty($trajetId)) {
                jsonResponse(false, "ID trajet requis");
            }
            
            require_once __DIR__ . '/../models/Trajet.php';
            $trajetModel = new Trajet();
            $trajetModel->terminerTrajet($trajetId, $_SESSION['user_id']);
            
            jsonResponse(true, "Trajet terminé avec succès");
            break;
        
        case 'annuler-trajet':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $trajetId = $_POST['trajet_id'] ?? '';
            if (empty($trajetId)) {
                jsonResponse(false, "ID trajet requis");
            }
            
            // Vérifier le rôle de l'utilisateur sur ce trajet
            $stmt = $db->prepare("
                SELECT chauffeur_id 
                FROM trajets 
                WHERE id = ?
            ");
            $stmt->execute([$trajetId]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$trajet) {
                jsonResponse(false, "Trajet introuvable");
            }
            
            // SI l'utilisateur est le CHAUFFEUR → annuler tout le trajet
            if ($trajet['chauffeur_id'] == $_SESSION['user_id']) {
                require_once __DIR__ . '/../models/Trajet.php';
                $trajetModel = new Trajet();
                $trajetModel->annulerTrajet($trajetId, $_SESSION['user_id'], 'Annulé par le conducteur');
                
                jsonResponse(true, "Trajet annulé avec succès");
            }
            // SINON c'est un PASSAGER → annuler seulement sa réservation
            else {
                $db->beginTransaction();
                try {
                    // Vérifier qu'il y a bien une réservation active
                    $stmt = $db->prepare("
                        SELECT id, prix_total, nombre_places
                        FROM reservations 
                        WHERE trajet_id = ? 
                        AND passager_id = ? 
                        AND statut = 'confirmee'
                    ");
                    $stmt->execute([$trajetId, $_SESSION['user_id']]);
                    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$reservation) {
                        throw new Exception("Aucune réservation active trouvée");
                    }
                    
                    // SUPPRIMER la réservation (au lieu de juste la marquer annulée)
                    $stmt = $db->prepare("
                        DELETE FROM reservations 
                        WHERE id = ?
                    ");
                    $stmt->execute([$reservation['id']]);
                    
                    // Rembourser les crédits
                    $stmt = $db->prepare("
                        UPDATE utilisateurs 
                        SET credits = credits + ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$reservation['prix_total'], $_SESSION['user_id']]);
                    
                    // Augmenter les places disponibles
                    $stmt = $db->prepare("
                        UPDATE trajets 
                        SET places_disponibles = places_disponibles + ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$reservation['nombre_places'], $trajetId]);
                    
                    $db->commit();
                    jsonResponse(true, "Votre réservation a été annulée et vos crédits remboursés");
                    
                } catch (Exception $e) {
                    $db->rollBack();
                    error_log("Erreur annulation réservation: " . $e->getMessage());
                    jsonResponse(false, "Erreur lors de l'annulation: " . $e->getMessage());
                }
            }
            break;

        case 'get-participants':
            // Récupérer la liste des participants
            checkAuth();
            $trajetId = $_GET['trajet_id'] ?? '';
            
            if (empty($trajetId)) {
                jsonResponse(false, "ID trajet requis");
            }
            
            // Vérifier que l'utilisateur est le chauffeur ou un participant
            $stmt = $db->prepare("
                SELECT chauffeur_id FROM trajets WHERE id = ?
            ");
            $stmt->execute([$trajetId]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $db->prepare("
                SELECT u.id, u.prenom, u.nom, u.photo_profil as photo, u.note_moyenne, u.nombre_avis
                FROM reservations r
                JOIN utilisateurs u ON r.passager_id = u.id
                WHERE r.trajet_id = ? AND r.statut = 'confirmee'
                ORDER BY r.date_reservation ASC
            ");
            $stmt->execute([$trajetId]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse(true, "Participants récupérés", ['participants' => $participants]);
            break;

        case 'reserver-trajet':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $trajetId = intval($_POST['trajet_id'] ?? 0);
            $nombrePlaces = intval($_POST['nombre_places'] ?? 1);
            
            if (!$trajetId || $nombrePlaces < 1) {
                jsonResponse(false, "Données invalides");
            }
            
            $db->beginTransaction();
            try {
                // Récupérer infos trajet
                $stmt = $db->prepare("
                    SELECT chauffeur_id, prix_par_passager, places_disponibles 
                    FROM trajets 
                    WHERE id = ? AND statut = 'planifie'
                ");
                $stmt->execute([$trajetId]);
                $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$trajet) {
                    throw new Exception("Trajet non disponible");
                }
                
                if ($trajet['places_disponibles'] < $nombrePlaces) {
                    throw new Exception("Pas assez de places disponibles");
                }
                
                $prixTotal = $trajet['prix_par_passager'] * $nombrePlaces;
                
                // Vérifier solde passager
                $stmt = $db->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $soldePassager = $stmt->fetchColumn();
                
                if ($soldePassager < $prixTotal) {
                    throw new Exception("Crédits insuffisants");
                }
                
                // Créer réservation
                $stmt = $db->prepare("
                    INSERT INTO reservations (trajet_id, passager_id, nombre_places, prix_total, statut, created_at)
                    VALUES (?, ?, ?, ?, 'confirmee', NOW())
                ");
                $stmt->execute([$trajetId, $_SESSION['user_id'], $nombrePlaces, $prixTotal]);
                
                // Débiter passager
                $stmt = $db->prepare("UPDATE utilisateurs SET credits = credits - ? WHERE id = ?");
                $stmt->execute([$prixTotal, $_SESSION['user_id']]);
                
                // Transaction passager
                $stmt = $db->prepare("
                    INSERT INTO transactions (utilisateur_id, type_transaction, montant, solde_avant, solde_apres, description, created_at)
                    VALUES (?, 'debit', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $prixTotal,
                    $soldePassager,
                    $soldePassager - $prixTotal,
                    "Réservation trajet #$trajetId"
                ]);
                
                // CRÉDITER LE CONDUCTEUR
                $stmt = $db->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
                $stmt->execute([$trajet['chauffeur_id']]);
                $soldeConducteur = $stmt->fetchColumn();
                
                $stmt = $db->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?");
                $stmt->execute([$prixTotal, $trajet['chauffeur_id']]);
                
                // Transaction conducteur
                $stmt = $db->prepare("
                    INSERT INTO transactions (utilisateur_id, type_transaction, montant, solde_avant, solde_apres, description, created_at)
                    VALUES (?, 'credit', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $trajet['chauffeur_id'],
                    $prixTotal,
                    $soldeConducteur,
                    $soldeConducteur + $prixTotal,
                    "Réservation reçue trajet #$trajetId"
                ]);
                
                // Mettre à jour places
                $stmt = $db->prepare("UPDATE trajets SET places_disponibles = places_disponibles - ? WHERE id = ?");
                $stmt->execute([$nombrePlaces, $trajetId]);
                
                $db->commit();
                
                // Redirection classique
                header('Location: /ecoride/php/index.php?page=details&trajet=' . $trajetId . '&success=reservation');
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                error_log("Erreur réservation: " . $e->getMessage());
                header('Location: /ecoride/php/index.php?page=details&trajet=' . $trajetId . '&error=' . urlencode($e->getMessage()));
                exit;
            }
            break;
        
        // ========================================
        // GESTION DES AVIS
        // ========================================
        
        case 'soumettre-avis':
            // Soumettre un avis sur un trajet
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $trajetId = $_POST['trajet_id'] ?? '';
            $note = $_POST['note'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';
            $trajetReussi = $_POST['trajet_reussi'] ?? '';
            $signalement = $_POST['signalement'] ?? '';

            // Vérifier que l'utilisateur N'EST PAS le conducteur
            $stmt = $db->prepare("SELECT chauffeur_id FROM trajets WHERE id = ?");
            $stmt->execute([$trajetId]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($trajet['chauffeur_id'] == $_SESSION['user_id']) {
                jsonResponse(false, "Le conducteur ne peut pas laisser d'avis sur son propre trajet");
            }
            
            if (empty($trajetId) || empty($note) || empty($trajetReussi)) {
                jsonResponse(false, "Données incomplètes");
            }
            
            require_once __DIR__ . '/../models/Avis.php';
            $avisModel = new Avis();
            
            // Créer l'avis via réservation (passager)
            $avisModel->creerAvisReservation(
                $trajetId,
                $_SESSION['user_id'],
                $note,
                $commentaire,
                $trajetReussi,
                $signalement
            );
            
            jsonResponse(true, "Avis soumis avec succès");
            break;

        case 'valider-avis':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $avisId = $_POST['avis_id'] ?? '';
            if (empty($avisId)) {
                jsonResponse(false, "ID avis requis");
            }
            
            require_once __DIR__ . '/../models/Avis.php';
            $avisModel = new Avis();
            
            $result = $avisModel->validerAvis($avisId, $_SESSION['user_id']);
            
            if ($result) {
                jsonResponse(true, "Avis validé avec succès");
            } else {
                jsonResponse(false, "Erreur lors de la validation");
            }
            break;

        case 'refuser-avis':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            $avisId = $_POST['avis_id'] ?? '';
            $motif = $_POST['motif'] ?? '';
            
            if (empty($avisId)) {
                jsonResponse(false, "ID avis requis");
            }
            
            require_once __DIR__ . '/../models/Avis.php';
            $avisModel = new Avis();
            
            $result = $avisModel->refuserAvis($avisId, $_SESSION['user_id'], $motif);
            
            if ($result) {
                jsonResponse(true, "Avis refusé");
            } else {
                jsonResponse(false, "Erreur lors du refus");
            }
            break;

        // ========================================
        // GESTION DES SIGNALEMENTS (EMPLOYÉS)
        // ========================================

        case 'prendre-en-charge-signalement':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            if (!$auth->hasPermission('employe')) {
                jsonResponse(false, "Accès refusé", [], 403);
            }
            
            $signalementId = $_POST['signalement_id'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';
            
            if (empty($signalementId)) {
                jsonResponse(false, "ID signalement requis");
            }
            
            require_once __DIR__ . '/../models/Signalement.php';
            $signalementModel = new Signalement();
            
            try {
                $result = $signalementModel->prendreEnCharge($signalementId, $_SESSION['user_id'], $commentaire);
                if ($result) {
                    jsonResponse(true, "Signalement pris en charge");
                } else {
                    jsonResponse(false, "Erreur lors de la prise en charge");
                }
            } catch (Exception $e) {
                jsonResponse(false, $e->getMessage());
            }
            break;

        case 'resoudre-signalement':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            if (!$auth->hasPermission('employe')) {
                jsonResponse(false, "Accès refusé", [], 403);
            }
            
            $signalementId = $_POST['signalement_id'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';
            $demandeSuspension = isset($_POST['demande_suspension']) && $_POST['demande_suspension'] === '1';
            
            if (empty($signalementId)) {
                jsonResponse(false, "ID signalement requis");
            }
            
            require_once __DIR__ . '/../models/Signalement.php';
            $signalementModel = new Signalement();
            
            try {
                $result = $signalementModel->resoudreSignalement($signalementId, $commentaire, $demandeSuspension);
                if ($result) {
                    $message = $demandeSuspension ? "Signalement résolu. Demande de suspension envoyée à l'admin." : "Signalement résolu";
                    jsonResponse(true, $message);
                } else {
                    jsonResponse(false, "Erreur lors de la résolution");
                }
            } catch (Exception $e) {
                jsonResponse(false, $e->getMessage());
            }
            break;

        case 'classer-sans-suite':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            if (!$auth->hasPermission('employe')) {
                jsonResponse(false, "Accès refusé", [], 403);
            }
            
            $signalementId = $_POST['signalement_id'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';
            
            if (empty($signalementId) || empty($commentaire)) {
                jsonResponse(false, "ID signalement et motif requis");
            }
            
            require_once __DIR__ . '/../models/Signalement.php';
            $signalementModel = new Signalement();
            
            try {
                $result = $signalementModel->classerSansSuite($signalementId, $commentaire);
                if ($result) {
                    jsonResponse(true, "Signalement classé sans suite");
                } else {
                    jsonResponse(false, "Erreur");
                }
            } catch (Exception $e) {
                jsonResponse(false, $e->getMessage());
            }
            break;
        
        // ========================================
        // ADMINISTRATION
        // ========================================
        
        case 'create-employee':
            // Créer un compte employé
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            
            if (!$auth->hasPermission('admin')) {
                jsonResponse(false, "Accès refusé - Droits administrateur requis", [], 403);
            }
            
            require_once __DIR__ . '/../controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->createEmployee();
            break;

        case 'suspend-user':
            // Suspendre un utilisateur
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            
            if (!$auth->hasPermission('admin')) {
                jsonResponse(false, "Accès refusé - Droits administrateur requis", [], 403);
            }
            
            $userId = $_POST['user_id'] ?? '';
            if (empty($userId)) {
                jsonResponse(false, "ID utilisateur requis");
            }
            
            require_once __DIR__ . '/../controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->suspendUser($userId);
            
            header('Location: /ecoride/php/index.php?page=admin&success=user_suspended');
            exit;
            break;
        
        case 'reactivate-user':
            // Réactiver un utilisateur suspendu
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            
            if (!$auth->hasPermission('admin')) {
                jsonResponse(false, "Accès refusé - Droits administrateur requis", [], 403);
            }
            
            $userId = $_POST['user_id'] ?? '';
            if (empty($userId)) {
                jsonResponse(false, "ID utilisateur requis");
            }
            
            require_once __DIR__ . '/../controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->reactivateUser($userId);
            
            header('Location: /ecoride/php/index.php?page=admin&success=user_reactivated');
            exit;
            break;

        case 'ignorer-demande-suspension':
            checkAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(false, "Méthode non autorisée", [], 405);
            }
            
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            if (!$auth->hasPermission('admin')) {
                jsonResponse(false, "Accès refusé", [], 403);
            }
            
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                header('Location: /ecoride/php/index.php?page=admin&error=' . urlencode('ID utilisateur manquant'));
                exit;
            }
            
            require_once __DIR__ . '/../controllers/AdminController.php';
            $adminController = new AdminController();
            
            try {
                $result = $adminController->ignorerDemandeSuspension($userId);
                if ($result) {
                    header('Location: /ecoride/php/index.php?page=admin&success=' . urlencode('Demande ignorée'));
                } else {
                    header('Location: /ecoride/php/index.php?page=admin&error=' . urlencode('Erreur lors de l\'opération'));
                }
            } catch (Exception $e) {
                header('Location: /ecoride/php/index.php?page=admin&error=' . urlencode($e->getMessage()));
            }
            exit;
            break;

        // ========================================
        // FORMULAIRE DE CONTACT
        // ========================================
        // Ajouter après les autres routes
        case 'send-contact':
            require_once __DIR__ . '/../controllers/ContactController.php';
            $controller = new ContactController();
            $controller->handleContactSubmit();
            break;

        case 'get-messages-contact':
            require_once __DIR__ . '/../controllers/ContactController.php';
            $controller = new ContactController();
            $controller->getAllMessages();
            break;

        case 'traiter-message-contact':
            require_once __DIR__ . '/../controllers/ContactController.php';
            $controller = new ContactController();
            $controller->traiterMessage();
            break;
        
        default:
            jsonResponse(false, "Action non reconnue", [], 404);
            break;
    }   
    
} catch (Exception $e) {
    error_log("Erreur API: " . $e->getMessage());
    jsonResponse(false, "Erreur serveur: " . $e->getMessage(), [], 500);
}