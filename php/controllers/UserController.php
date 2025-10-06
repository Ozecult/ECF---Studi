<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Vehicule.php';
require_once __DIR__ . '/../models/Trajet.php';
require_once __DIR__ . '/../models/Preference.php';

class UserController {
    private $userModel;
    private $vehiculeModel;
    private $trajetModel;
    private $preferenceModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->vehiculeModel = new Vehicule();
        $this->trajetModel = new Trajet();
        $this->preferenceModel = new Preference();
    }
    
    /**
     * Afficher le tableau de bord utilisateur
     */
    public function showDashboard() {
        // Vérifier que l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            header('Location: /ecoride/php/index.php?page=connexion');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer les données de l'utilisateur
        $userData = $this->getUserData($userId);
        
        // Charger la vue avec les données
        include __DIR__ . '/../views/utilisateur.php';
    }
    
    /**
     * Récupérer toutes les données utilisateur nécessaires
     */
    public function getUserData($userId) {
        try {
            $userData = [
                'user' => $this->userModel->getUserById($userId),
                'vehicules' => $this->vehiculeModel->getVehiculesByUser($userId),
                'preferences' => $this->preferenceModel->getPreferencesByUser($userId),
                'trajets_a_venir' => $this->trajetModel->getTrajetsAVenir($userId),
                'trajets_passes' => $this->trajetModel->getTrajetsPasses($userId),
                'preferences_types' => $this->preferenceModel->getPreferencesTypes()
            ];
            
            // Log de debug
            error_log("UserController::getUserData - Données récupérées pour utilisateur $userId: " . json_encode(array_keys($userData)));
            
            return $userData;
        } catch (Exception $e) {
            error_log("Erreur dans getUserData: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Ajouter un véhicule
     */
    public function addVehicule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, "Méthode non autorisée", 405);
            return;
        }
        
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(false, "Non autorisé", 401);
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            $vehiculeData = [
                'utilisateur_id' => $userId,
                'marque' => $this->sanitizeInput($_POST['marque'] ?? ''),
                'modele' => $this->sanitizeInput($_POST['modele'] ?? ''),
                'couleur' => $this->sanitizeInput($_POST['couleur'] ?? ''),
                'energie' => $_POST['energie'] ?? '',
                'immatriculation' => strtoupper($this->sanitizeInput($_POST['immatriculation'] ?? '')),
                'premiere_mise_en_circulation' => $_POST['circulation'] ?? '',
                'nombre_places_disponibles' => intval($_POST['places'] ?? 0)
            ];
            
            $vehiculeId = $this->vehiculeModel->create($vehiculeData);
            
            $this->jsonResponse(true, "Véhicule ajouté avec succès", 201, [
                'vehicule_id' => $vehiculeId
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ajout véhicule: " . $e->getMessage());
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * Mettre à jour les préférences
     */
    public function updatePreferences() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->jsonResponse(false, "Méthode non autorisée", 405);
        return;
    }
    
    if (!$this->isLoggedIn()) {
        $this->jsonResponse(false, "Non autorisé", 401);
        return;
    }
    
    try {
        $userId = $_SESSION['user_id'];
        
        // DEBUG : Voir ce qui est reçu
        error_log("POST reçu: " . print_r($_POST, true));
        
        // Récupérer les préférences depuis la requête
        $preferences = [];
        if (isset($_POST['preferences'])) {
            if (is_string($_POST['preferences'])) {
                $preferences = json_decode($_POST['preferences'], true);
            } else if (is_array($_POST['preferences'])) {
                $preferences = $_POST['preferences'];
            }
        }
        
        // DEBUG : Voir ce qui est parsé
        error_log("Préférences parsées: " . print_r($preferences, true));
        
        $preferencePersonnalisee = $this->sanitizeInput($_POST['preference_personnalisee'] ?? '');
        
        // Mettre à jour toutes les préférences
        $this->preferenceModel->updateUserPreferences($userId, $preferences, $preferencePersonnalisee);
        
        // DEBUG : Vérifier ce qui est en BDD après
        $prefsEnBdd = $this->preferenceModel->getPreferencesByUser($userId);
        error_log("Préférences en BDD après update: " . print_r($prefsEnBdd, true));
        
        $this->jsonResponse(true, "Préférences mises à jour avec succès");
        
    } catch (Exception $e) {
        error_log("Erreur mise à jour préférences: " . $e->getMessage());
        $this->jsonResponse(false, $e->getMessage());
    }
}
    
    /**
     * Créer un trajet
     */
    public function createTrajet() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, "Méthode non autorisée", 405);
            return;
        }
        
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(false, "Non autorisé", 401);
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            // Vérifier que l'utilisateur a assez de crédits
            $user = $this->userModel->getUserById($userId);
            if ($user['credits'] < 2) {
                $this->jsonResponse(false, "Crédits insuffisants (2 crédits requis)");
                return;
            }
            
            // Préparer la date de départ (par défaut maintenant + 1 heure)
            $dateDepart = isset($_POST['date_depart']) ? $_POST['date_depart'] : date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $trajetData = [
                'chauffeur_id' => $userId,
                'vehicule_id' => intval($_POST['choix_vehicule'] ?? 0),
                'adresse_depart' => $this->sanitizeInput($_POST['depart'] ?? ''),
                'adresse_arrivee' => $this->sanitizeInput($_POST['arrivee'] ?? ''),
                'date_depart' => $dateDepart,
                'prix_par_passager' => intval($_POST['fixer_prix'] ?? 0)
            ];
            
            $trajetId = $this->trajetModel->create($trajetData);
            
            // Débiter 2 crédits via le modèle User (si cette méthode existe)
            if (method_exists($this->userModel, 'updateCredits')) {
                $this->userModel->updateCredits($userId, -2, 'creation_trajet', $trajetId);
            } else {
                // Sinon, mise à jour manuelle des crédits
                $this->updateUserCredits($userId, -2);
            }
            
            $this->jsonResponse(true, "Trajet créé avec succès (2 crédits débités)", 201, [
                'trajet_id' => $trajetId,
                'nouveaux_credits' => $user['credits'] - 2
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur création trajet: " . $e->getMessage());
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * Mettre à jour le rôle utilisateur
     */
    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, "Méthode non autorisée", 405);
            return;
        }
        
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(false, "Non autorisé", 401);
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $role = $_POST['role'] ?? '';
            
            $roleData = [];
            switch ($role) {
                case 'chauffeur':
                    $roleData = ['est_chauffeur' => true, 'est_passager' => false];
                    break;
                case 'passager':
                    $roleData = ['est_chauffeur' => false, 'est_passager' => true];
                    break;
                case 'duo':
                    $roleData = ['est_chauffeur' => true, 'est_passager' => true];
                    break;
                default:
                    throw new Exception("Rôle invalide");
            }
            
            $this->userModel->updateUser($userId, $roleData);
            
            $this->jsonResponse(true, "Rôle mis à jour");
            
        } catch (Exception $e) {
            error_log("Erreur mise à jour rôle: " . $e->getMessage());
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * API pour récupérer les données utilisateur (AJAX)
     */
    public function getUserDataApi() {
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(false, "Non autorisé", 401);
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $userData = $this->getUserData($userId);
            
            $this->jsonResponse(true, "Données récupérées", 200, $userData);
            
        } catch (Exception $e) {
            error_log("Erreur récupération données: " . $e->getMessage());
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    // Méthodes utilitaires
    
    private function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    private function sanitizeInput($input) {
        return trim($input);
    }
    
    private function updateUserCredits($userId, $amount) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }
    
    private function jsonResponse($success, $message, $httpCode = 200, $data = []) {
        header('Content-Type: application/json; charset=utf-8');
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
}