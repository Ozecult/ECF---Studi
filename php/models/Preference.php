<?php
require_once __DIR__ . '/../config/Database.php';

class Preference 
{
  private $db;
  
  public function __construct() 
  {
    $this->db = Database::getInstance();
  }
  
  /**
   * Récupérer tous les types de préférences disponibles
   */
  public function getPreferencesTypes() 
  {
    $stmt = $this->db->prepare("
      SELECT id, nom, description, est_predefinit
      FROM preferences_types 
      ORDER BY est_predefinit DESC, nom ASC
    ");
    
    $stmt->execute();
    return $stmt->fetchAll();
  }
  
  /**
   * Récupérer les préférences d'un utilisateur
   */
  public function getPreferencesByUser($userId) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        pu.id,
        pu.preference_type_id,
        pt.nom as type_nom,
        pu.valeur,
        pu.preference_personnalisee,
        pt.est_predefinit
      FROM preferences_utilisateurs pu
      JOIN preferences_types pt ON pu.preference_type_id = pt.id
      WHERE pu.utilisateur_id = ?
      ORDER BY pt.est_predefinit DESC, pt.nom ASC
    ");
    
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
  }
  
  /**
   * Mettre à jour les préférences d'un utilisateur
   */
  public function updateUserPreferences($userId, $preferences, $preferencePersonnalisee = null) 
  {
    try {
      $this->db->beginTransaction();
      
      // Supprimer TOUTES les anciennes préférences (prédéfinies + personnalisées)
      $stmt = $this->db->prepare("
        DELETE FROM preferences_utilisateurs 
        WHERE utilisateur_id = ?
      ");
      $stmt->execute([$userId]);
      
      // Ajouter uniquement les préférences cochées
      if (!empty($preferences)) {
        foreach ($preferences as $prefName) {
          // Récupérer l'ID du type de préférence
          $stmt = $this->db->prepare("
              SELECT id FROM preferences_types WHERE nom = ?
          ");
          $stmt->execute([$prefName]);
          $typeId = $stmt->fetchColumn();
          
          if ($typeId) {
              $this->addUserPreference($userId, $typeId, 'accepte');
          }
        }
      }
      
      // Ajouter la préférence personnalisée si elle existe
      if (!empty($preferencePersonnalisee)) {
        $this->addCustomPreference($userId, $preferencePersonnalisee);
      }
      
      $this->db->commit();
      return true;
            
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
  }
  
  /**
   * Ajouter une préférence utilisateur
   */
  private function addUserPreference($userId, $preferenceTypeId, $valeur) 
  {
    $stmt = $this->db->prepare("
      INSERT INTO preferences_utilisateurs 
      (utilisateur_id, preference_type_id, valeur, created_at, updated_at)
      VALUES (?, ?, ?, NOW(), NOW())
    ");
    
    return $stmt->execute([$userId, $preferenceTypeId, $valeur]);
  }
  
  /**
   * Ajouter une préférence personnalisée
   */
  private function addCustomPreference($userId, $preferencePersonnalisee) 
  {
    // D'abord créer le type de préférence s'il n'existe pas
    $preferenceTypeId = $this->getOrCreateCustomPreferenceType($preferencePersonnalisee);
    
    // Ensuite l'ajouter à l'utilisateur
    $stmt = $this->db->prepare("
      INSERT INTO preferences_utilisateurs 
      (utilisateur_id, preference_type_id, valeur, preference_personnalisee, created_at, updated_at)
      VALUES (?, ?, 'accepte', ?, NOW(), NOW())
    ");
    
    return $stmt->execute([$userId, $preferenceTypeId, $preferencePersonnalisee]);
  }
  
  /**
   * Créer ou récupérer un type de préférence personnalisé
   */
  private function getOrCreateCustomPreferenceType($nom) 
  {
    // Vérifier si le type existe déjà
    $stmt = $this->db->prepare("
      SELECT id FROM preferences_types 
      WHERE nom = ? AND est_predefinit = 0
    ");
    $stmt->execute([$nom]);
    $existing = $stmt->fetch();
    
    if ($existing) {
      return $existing['id'];
    }
    
    // Créer le nouveau type
    $stmt = $this->db->prepare("
      INSERT INTO preferences_types 
      (nom, description, est_predefinit, created_at)
      VALUES (?, ?, 0, NOW())
    ");
    
    $stmt->execute([
      $nom,
      "Préférence personnalisée créée par un utilisateur"
    ]);
    
    return $this->db->lastInsertId();
  }
  
  /**
   * Initialiser les préférences prédéfinies dans la base
   */
  public function initializeDefaultPreferences() 
  {
    $defaultPrefs = [
      ['nom' => 'fumeur', 'description' => 'Autorise les fumeurs dans le véhicule'],
      ['nom' => 'animaux', 'description' => 'Autorise les animaux de compagnie'],
      ['nom' => 'enfant', 'description' => 'Voyage avec des enfants'],
      ['nom' => 'musique', 'description' => 'Écoute de musique pendant le trajet']
    ];
    
    foreach ($defaultPrefs as $pref) {
      $stmt = $this->db->prepare("
        INSERT IGNORE INTO preferences_types (nom, description, est_predefinit, created_at)
        VALUES (?, ?, 1, NOW())
      ");
      $stmt->execute([$pref['nom'], $pref['description']]);
    }
  }
  
  /**
   * Récupérer les préférences compatibles pour un trajet
   */
  public function getCompatiblePreferences($chauffeurId, $passagerId) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        pt.nom,
        pu_chauffeur.valeur as valeur_chauffeur,
        pu_passager.valeur as valeur_passager,
        CASE 
          WHEN pu_chauffeur.valeur = 'accepte' AND pu_passager.valeur = 'accepte' THEN 'compatible'
          WHEN pu_chauffeur.valeur = 'refuse' AND pu_passager.valeur = 'accepte' THEN 'incompatible'
          WHEN pu_chauffeur.valeur = 'accepte' AND pu_passager.valeur = 'refuse' THEN 'neutre'
          ELSE 'compatible'
        END as compatibilite
      FROM preferences_types pt
      LEFT JOIN preferences_utilisateurs pu_chauffeur ON (pt.id = pu_chauffeur.preference_type_id AND pu_chauffeur.utilisateur_id = ?)
      LEFT JOIN preferences_utilisateurs pu_passager ON (pt.id = pu_passager.preference_type_id AND pu_passager.utilisateur_id = ?)
      WHERE pt.est_predefinit = 1
      ORDER BY pt.nom
    ");
    
    $stmt->execute([$chauffeurId, $passagerId]);
    return $stmt->fetchAll();
  }
  
  /**
   * Calculer le score de compatibilité entre deux utilisateurs
   */
  public function calculateCompatibilityScore($chauffeurId, $passagerId) 
  {
    $preferences = $this->getCompatiblePreferences($chauffeurId, $passagerId);
    
    $totalPreferences = count($preferences);
    if ($totalPreferences === 0) {
      return 100; // Score neutre si pas de préférences
    }
    
    $compatibleCount = 0;
    $incompatibleCount = 0;
    
    foreach ($preferences as $pref) {
      switch ($pref['compatibilite']) {
        case 'compatible':
          $compatibleCount++;
          break;
        case 'incompatible':
          $incompatibleCount++;
          break;
        // 'neutre' ne compte ni pour ni contre
      }
    }
    
    // Score = (préférences compatibles - incompatibles) / total * 100
    $score = (($compatibleCount - $incompatibleCount) / $totalPreferences) * 100;
    
    // Ramener le score entre 0 et 100
    return max(0, min(100, $score));
  }
  
  /**
   * Supprimer une préférence utilisateur
   */
  public function removeUserPreference($userId, $preferenceId) 
  {
    $stmt = $this->db->prepare("
        DELETE FROM preferences_utilisateurs 
        WHERE id = ? AND utilisateur_id = ?
    ");
    
    return $stmt->execute([$preferenceId, $userId]);
  }
  
  /**
   * Récupérer les préférences les plus populaires
   */
  public function getPopularPreferences($limit = 10) 
  {
    $stmt = $this->db->prepare("
      SELECT 
          pt.nom,
          pt.description,
          COUNT(pu.id) as nombre_utilisateurs,
          ROUND((COUNT(pu.id) * 100.0 / (SELECT COUNT(DISTINCT utilisateur_id) FROM preferences_utilisateurs)), 2) as pourcentage
      FROM preferences_types pt
      LEFT JOIN preferences_utilisateurs pu ON pt.id = pu.preference_type_id
      WHERE pt.est_predefinit = 1
      GROUP BY pt.id, pt.nom, pt.description
      ORDER BY nombre_utilisateurs DESC
      LIMIT ?
    ");
    
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
  }
}
