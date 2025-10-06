<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';

header('Content-Type: application/json; charset=utf-8');

// Debug utile temporaire
function debugLog($message, $data = null) {
    error_log("DEBUG user-data.php: " . $message . ($data ? " - " . json_encode($data) : ""));
}

try {
    debugLog("Début user-data.php");
    
    // Debug session avant AuthController
    debugLog("Session status avant AuthController: " . session_status());
    debugLog("Session ID avant AuthController: " . session_id());
    debugLog("Session data avant AuthController", $_SESSION ?? []);
    
    $auth = new AuthController();
    
    debugLog("Nom de session courant après AuthController: " . session_name());
    debugLog("Session ID actuel: " . session_id());
    debugLog("Session data après AuthController", $_SESSION ?? []);
    debugLog("Cookies reçus:", $_COOKIE);
    
    // Vérifier si la session contient les données attendues
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        debugLog("Session user_id manquant", $_SESSION);
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Session invalide - user_id manquant']);
        exit;
    }
    
    if (!$auth->isLoggedIn()) {
        debugLog("Utilisateur non connecté selon AuthController");
        debugLog("Session complète", $_SESSION);
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        exit;
    }
    
    $currentUser = $auth->getCurrentUser();
    debugLog("Utilisateur récupéré", ['id' => $currentUser['id'] ?? null]);
    
    if (!$currentUser || empty($currentUser['id'])) {
        debugLog("Utilisateur invalide");
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
        exit;
    }
    
    $userController = new UserController();
    $userData = $userController->getUserData($currentUser['id']);
    
    debugLog("Données utilisateur récupérées", array_keys($userData ?? []));
    
    echo json_encode(array_merge([
        'success' => true,
        'message' => 'Données récupérées'
    ], $userData ?: []), JSON_UNESCAPED_UNICODE);
    
} catch (Throwable $e) {
    debugLog("Erreur: " . $e->getMessage());
    debugLog("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
    exit;
}