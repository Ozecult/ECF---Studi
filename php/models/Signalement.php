<?php
class Signalement {
    private $db;
    
    public function __construct() 
    {
      require_once __DIR__ . '/../config/database.php';
      $this->db = Database::getInstance()->getConnection();
    }
    
    public function getSignalementsEnAttente() 
    {
      $sql = "
        SELECT 
          s.id,
          s.type_signalement,
          s.description,
          s.created_at,
          s.statut,
          t.id as trajet_id,
          t.adresse_depart,
          t.adresse_arrivee,
          DATE(t.date_depart) as date_trajet,
          signaleur.prenom as signaleur_prenom,
          signaleur.email as signaleur_email,
          signale.prenom as signale_prenom,
          signale.email as signale_email
        FROM signalements s
        JOIN trajets t ON s.trajet_id = t.id
        JOIN utilisateurs signaleur ON s.signaleur_id = signaleur.id
        JOIN utilisateurs signale ON s.signale_id = signale.id
        WHERE s.statut IN ('nouveau', 'en_cours')  -- ← CHANGÉ ICI
        ORDER BY s.created_at ASC
      ";
      
      error_log("SQL SIGNALEMENTS: " . $sql);
      
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      error_log("RÉSULTATS SIGNALEMENTS: " . count($results) . " trouvés");
    
      return $results;
  }

  public function getSignalementsResolus($limit = 50) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        s.id,
        s.type_signalement,
        s.description,
        s.date_resolution,
        t.id as trajet_id,
        t.adresse_depart,
        t.adresse_arrivee,
        DATE(t.date_depart) as date_trajet,
        signaleur.prenom as signaleur_prenom,
        signale.prenom as signale_prenom
      FROM signalements s
      JOIN trajets t ON s.trajet_id = t.id
      JOIN utilisateurs signaleur ON s.signaleur_id = signaleur.id
      JOIN utilisateurs signale ON s.signale_id = signale.id
      WHERE s.statut = 'resolu'
      ORDER BY s.date_resolution DESC
      LIMIT ?
    ");
    
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
 * Prendre en charge un signalement
 */
public function prendreEnCharge($signalementId, $employeId, $commentaire = '') {
    // Récupérer l'ID employé depuis la table employes
    $stmt = $this->db->prepare("SELECT id FROM employes WHERE utilisateur_id = ? AND statut = 'actif'");
    $stmt->execute([$employeId]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$employe) {
        throw new Exception("Employé non trouvé");
    }
    
    $stmt = $this->db->prepare("
        UPDATE signalements 
        SET statut = 'en_cours',
            employe_assigne_id = ?,
            commentaire_employe = ?
        WHERE id = ?
    ");
    
    return $stmt->execute([$employe['id'], $commentaire, $signalementId]);
}

/**
 * Résoudre un signalement
 */
public function resoudreSignalement($signalementId, $commentaire = '', $demandeSuspension = false) {
    $stmt = $this->db->prepare("
        UPDATE signalements 
        SET statut = 'resolu',
            commentaire_employe = ?,
            demande_suspension = ?,
            date_resolution = NOW()
        WHERE id = ?
    ");
    
    return $stmt->execute([$commentaire, $demandeSuspension ? 1 : 0, $signalementId]);
}

/**
 * Classer sans suite
 */
public function classerSansSuite($signalementId, $commentaire) {
    $stmt = $this->db->prepare("
        UPDATE signalements 
        SET statut = 'ferme',
            commentaire_employe = ?,
            date_resolution = NOW()
        WHERE id = ?
    ");
    
    return $stmt->execute([$commentaire, $signalementId]);
  }

  /**
   * Récupérer mes signalements en cours
   */
  public function getMesSignalements($employeUserId) 
  {
    // Récupérer l'ID employé
    $stmt = $this->db->prepare("SELECT id FROM employes WHERE utilisateur_id = ?");
    $stmt->execute([$employeUserId]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$employe) return [];
    
    $stmt = $this->db->prepare("
      SELECT 
        s.id,
        s.type_signalement,
        s.description,
        s.commentaire_employe,
        s.created_at,
        t.id as trajet_id,
        t.adresse_depart,
        t.adresse_arrivee,
        DATE(t.date_depart) as date_trajet,
        signaleur.id as signaleur_id,
        signaleur.prenom as signaleur_prenom,
        signaleur.email as signaleur_email,
        signale.id as signale_id,
        signale.prenom as signale_prenom,
        signale.email as signale_email
      FROM signalements s
      JOIN trajets t ON s.trajet_id = t.id
      JOIN utilisateurs signaleur ON s.signaleur_id = signaleur.id
      JOIN utilisateurs signale ON s.signale_id = signale.id
      WHERE s.employe_assigne_id = ? AND s.statut = 'en_cours'
      ORDER BY s.created_at ASC
    ");
    
    $stmt->execute([$employe['id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
