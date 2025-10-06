<?php
require_once __DIR__ . '/../config/Database.php';

class MessageContact {
  private $db;
  
  public function __construct() 
  {
    $this->db = Database::getInstance();
  }
  
  /**
   * Créer un nouveau message de contact
   */
  public function create($data) 
  {
    $stmt = $this->db->prepare("
      INSERT INTO messages_contact 
      (prenom, nom, email, telephone, sujet, message, statut, created_at)
      VALUES (?, ?, ?, ?, ?, ?, 'nouveau', NOW())
    ");
    
    return $stmt->execute([
      trim($data['prenom']),
      trim($data['nom']),
      filter_var($data['email'], FILTER_SANITIZE_EMAIL),
      trim($data['telephone']),
      trim($data['sujet']),
      trim($data['message'])
    ]);
  }
  
  /**
   * Récupérer tous les messages (pour admin/employé)
   */
  public function getAll($statut = null) 
  {
    $sql = "SELECT * FROM messages_contact";
    
    if ($statut) {
      $sql .= " WHERE statut = ?";
      $stmt = $this->db->prepare($sql . " ORDER BY created_at DESC");
      $stmt->execute([$statut]);
    } else {
      $stmt = $this->db->prepare($sql . " ORDER BY created_at DESC");
      $stmt->execute();
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  /**
   * Récupérer un message par ID
   */
  public function getById($id) 
  {
    $stmt = $this->db->prepare("SELECT * FROM messages_contact WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  
  /**
   * Mettre à jour le statut
   */
  public function updateStatut($id, $statut) 
  {
    $stmt = $this->db->prepare("
      UPDATE messages_contact 
      SET statut = ?, updated_at = NOW()
      WHERE id = ?
    ");
    
    return $stmt->execute([$statut, $id]);
  }
  
  /**
   * Assigner un employé
   */
  public function assignerEmploye($id, $employeId) 
  {
    $stmt = $this->db->prepare("
      UPDATE messages_contact 
      SET employe_assigne_id = ?, statut = 'lu', updated_at = NOW()
      WHERE id = ?
    ");
      
    return $stmt->execute([$employeId, $id]);
  }
  
  /**
   * Ajouter une réponse
   */
  public function addReponse($id, $reponse, $employeId) {
    $stmt = $this->db->prepare("
      UPDATE messages_contact 
      SET reponse = ?, 
        employe_assigne_id = ?,
        date_reponse = NOW(),
        statut = 'traite',
        updated_at = NOW()
      WHERE id = ?
    ");
    
    return $stmt->execute([trim($reponse), $employeId, $id]);
  }
}