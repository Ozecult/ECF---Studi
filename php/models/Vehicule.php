<?php
require_once __DIR__ . '/../config/Database.php';

class Vehicule 
{
  private $db;
  
  public function __construct() 
  {
      $this->db = Database::getInstance();
  }
  
  /**
   * Créer un nouveau véhicule
   */
  public function create($data) 
  {
    try {
      $this->validateVehiculeData($data);
      
      // Vérifier l'unicité de l'immatriculation
      if ($this->immatriculationExists($data['immatriculation'])) {
          throw new Exception("Cette immatriculation existe déjà");
      }
      
      $stmt = $this->db->prepare("
          INSERT INTO VEHICULES 
          (utilisateur_id, marque, modele, couleur, energie, immatriculation, 
            premiere_mise_en_circulation, nombre_places_disponibles, est_actif, created_at, updated_at)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
      ");
      
      $result = $stmt->execute([
          $data['utilisateur_id'],
          $data['marque'],
          $data['modele'],
          $data['couleur'],
          $data['energie'],
          $data['immatriculation'],
          $data['premiere_mise_en_circulation'],
          $data['nombre_places_disponibles']
      ]);
      
      if (!$result) {
          throw new Exception("Erreur lors de l'ajout du véhicule");
      }
      
      return $this->db->lastInsertId();
        
    } catch (Exception $e) {
        throw $e;
    }
  }

  /**
   * Récupérer tous les véhicules d'un utilisateur
   */
  public function getVehiculesByUser($userId) 
  {
    $stmt = $this->db->prepare("
        SELECT id, marque, modele, couleur, energie, immatriculation, 
            premiere_mise_en_circulation, nombre_places_disponibles, est_actif, created_at
        FROM VEHICULES 
        WHERE utilisateur_id = ? AND est_actif = 1
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
  }

  /**
   * Récupérer un véhicule par ID
   */
  public function getVehiculeById($vehiculeId, $userId = null) 
  {
    $sql = "
        SELECT v.*, u.pseudo as proprietaire
        FROM VEHICULES v
        JOIN UTILISATEURS u ON v.utilisateur_id = u.id
        WHERE v.id = ? AND v.est_actif = 1
    ";
    
    $params = [$vehiculeId];
    
    if ($userId) {
        $sql .= " AND v.utilisateur_id = ?";
        $params[] = $userId;
    }
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetch();
  }

  /**
   * Mettre à jour un véhicule
   */
  public function update($vehiculeId, $userId, $data) 
  {
    // Vérifier que le véhicule appartient à l'utilisateur
    $vehicule = $this->getVehiculeById($vehiculeId, $userId);
    if (!$vehicule) {
        throw new Exception("Véhicule non trouvé ou accès non autorisé");
    }
    
    $allowedFields = ['marque', 'modele', 'couleur', 'energie', 'nombre_places_disponibles'];
    $updateFields = [];
    $values = [];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field]) && $data[$field] !== '') {
            $updateFields[] = "$field = ?";
            $values[] = $data[$field];
        }
    }
    
    if (empty($updateFields)) {
        throw new Exception("Aucune donnée à mettre à jour");
    }
    
    $updateFields[] = "updated_at = NOW()";
    $values[] = $vehiculeId;
    $values[] = $userId;
    
    $sql = "UPDATE VEHICULES SET " . implode(', ', $updateFields) . " WHERE id = ? AND utilisateur_id = ?";
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute($values);
  }

  /**
   * Désactiver un véhicule (soft delete)
   */
  public function deactivate($vehiculeId, $userId) 
  {
    $stmt = $this->db->prepare("
        UPDATE VEHICULES 
        SET est_actif = 0, updated_at = NOW()
        WHERE id = ? AND utilisateur_id = ?
    ");
    
    return $stmt->execute([$vehiculeId, $userId]);
  }

  /**
   * Vérifier si une immatriculation existe déjà
   */
  private function immatriculationExists($immatriculation) 
  {
    $stmt = $this->db->prepare("
        SELECT COUNT(*) 
        FROM VEHICULES 
        WHERE immatriculation = ? AND est_actif = 1
    ");
    
    $stmt->execute([$immatriculation]);
    return $stmt->fetchColumn() > 0;
  }

  /**
   * Valider les données du véhicule
   */
  private function validateVehiculeData($data) 
  {
    $required = ['utilisateur_id', 'marque', 'modele', 'couleur', 'energie', 
                'immatriculation', 'premiere_mise_en_circulation', 'nombre_places_disponibles'];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Le champ $field est obligatoire");
        }
    }
    
    // Validation de l'immatriculation (format français)
    if (!preg_match('/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/', $data['immatriculation'])) {
        throw new Exception("Format d'immatriculation invalide (ex: AB-123-CD)");
    }
    
    // Validation de l'énergie
    $energiesValides = ['electrique', 'thermique', 'hybride', 'gpl'];
    if (!in_array($data['energie'], $energiesValides)) {
        throw new Exception("Type d'énergie invalide");
    }
    
    // Validation du nombre de places
    if (!is_numeric($data['nombre_places_disponibles']) || $data['nombre_places_disponibles'] < 1 || $data['nombre_places_disponibles'] > 8) {
        throw new Exception("Le nombre de places doit être entre 1 et 8");
    }
    
    // Validation de la date de mise en circulation
    $dateCirculation = DateTime::createFromFormat('Y-m-d', $data['premiere_mise_en_circulation']);
    if (!$dateCirculation || $dateCirculation > new DateTime()) {
        throw new Exception("Date de première mise en circulation incorrecte");
    }
  }
}