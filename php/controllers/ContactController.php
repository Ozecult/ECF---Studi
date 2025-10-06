<?php
require_once __DIR__ . '/../models/MessageContact.php';

class ContactController {
  private $messageModel;
  
  public function __construct() 
  {
    $this->messageModel = new MessageContact();
  }
  
  /**
   * Afficher le formulaire de contact
   */
  public function showContactForm() 
  {
    include __DIR__ . '/../views/contact.php';
  }
  
  /**
   * Traiter l'envoi du formulaire
   */
  public function handleContactSubmit() 
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(false, "Méthode non autorisée", 405);
      return;
    }
    
    try {
      // Validation des données
      $data = [
        'prenom' => $_POST['prenom'] ?? '',
        'nom' => $_POST['nom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['tel'] ?? '',
        'sujet' => $_POST['sujet'] ?? '',
        'message' => $_POST['champtext'] ?? ''
      ];
      
      // Validation
      if (empty($data['prenom']) || empty($data['nom']) || empty($data['email']) || 
        empty($data['sujet']) || empty($data['message'])) {
        throw new Exception("Tous les champs obligatoires doivent être remplis");
      }
      
      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Format d'email invalide");
      }
      
      // Créer le message
      $result = $this->messageModel->create($data);
      
      if (!$result) {
        throw new Exception("Erreur lors de l'envoi du message");
      }
      
      // Simulation d'envoi d'email
      error_log("📧 SIMULATION EMAIL - Message de contact reçu de " . $data['email']);
      
      $this->jsonResponse(true, "Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.", 201);
        
    } catch (Exception $e) {
      error_log("Erreur contact: " . $e->getMessage());
      $this->jsonResponse(false, $e->getMessage());
    }
  }
  
  /**
   * Récupérer tous les messages (admin/employé)
   */
  public function getAllMessages() 
  {
    if (!$this->isEmploye()) {
      $this->jsonResponse(false, "Accès refusé", 403);
      return;
    }
    
    try {
      $statut = $_GET['statut'] ?? null;
      $messages = $this->messageModel->getAll($statut);
      
      $this->jsonResponse(true, "Messages récupérés", 200, [
          'messages' => $messages
      ]);
        
    } catch (Exception $e) {
      $this->jsonResponse(false, $e->getMessage());
    }
  }
  
  /**
   * Traiter un message (admin/employé)
   */
  public function traiterMessage() 
  {
    if (!$this->isEmploye()) {
      $this->jsonResponse(false, "Accès refusé", 403);
      return;
    }
    
    try {
      $messageId = $_POST['message_id'] ?? null;
      $reponse = $_POST['reponse'] ?? '';
      
      // Récupérer l'ID employé depuis la table employes
      $employeId = $this->getEmployeId($_SESSION['user_id']);
      
      if (!$employeId) {
        throw new Exception("Vous n'êtes pas enregistré comme employé");
      }
      
      if (!$messageId || empty($reponse)) {
        throw new Exception("Données manquantes");
      }
      
      $result = $this->messageModel->addReponse($messageId, $reponse, $employeId);
      
      if ($result) {
        $message = $this->messageModel->getById($messageId);
        error_log("📧 SIMULATION EMAIL - Réponse envoyée à " . $message['email']);
        
        $this->jsonResponse(true, "Réponse envoyée avec succès");
      } else {
        throw new Exception("Erreur lors de l'envoi de la réponse");
      }
        
    } catch (Exception $e) {
        $this->jsonResponse(false, $e->getMessage());
    }
  }

    // Ajouter cette méthode privée
    private function getEmployeId($userId) 
    {
      require_once __DIR__ . '/../config/Database.php';
      $stmt = Database::getInstance()->getConnection()->prepare("
        SELECT id FROM employes WHERE utilisateur_id = ? AND statut = 'actif'
      ");
      $stmt->execute([$userId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    }
  
  // Méthodes utilitaires
  
  private function isEmploye() 
  {
    require_once __DIR__ . '/AuthController.php';
    $auth = new AuthController();
    return $auth->hasPermission('employe') || $auth->hasPermission('admin');
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