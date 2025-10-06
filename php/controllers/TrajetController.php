<?php
class TrajetController {
  private $db;
  
  public function __construct() 
  {
    require_once __DIR__ . '/../config/database.php';
    $this->db = Database::getInstance()->getConnection();
  }
  
  public function rechercherTrajets($depart, $destination, $date, $passagers) 
  {
    $villeDepartClean = preg_replace('/\s*\([^)]*\)/', '', $depart);
    $villeDestinationClean = preg_replace('/\s*\([^)]*\)/', '', $destination);
      
    $conditions = [
      "t.statut = 'planifie'",
      "DATE(t.date_depart) = ?",
      "t.places_disponibles >= ?",
      "(t.adresse_depart LIKE ? OR t.ville_depart_nom LIKE ?)",
      "(t.adresse_arrivee LIKE ? OR t.ville_arrivee_nom LIKE ?)"
    ];
    
    $params = [
      $date,
      $passagers,
      "%$villeDepartClean%", "%$villeDepartClean%",
      "%$villeDestinationClean%", "%$villeDestinationClean%"
    ];
    
    // Filtres motorisation
    if (isset($_GET['electrique']) && !isset($_GET['thermique'])) {
      $conditions[] = "v.energie = 'electrique'";
    } elseif (isset($_GET['thermique']) && !isset($_GET['electrique'])) {
      $conditions[] = "v.energie != 'electrique'";
    }
    
    // Filtre prix
    if (!empty($_GET['prix_max']) && $_GET['prix_max'] > 0) {
      $conditions[] = "t.prix_par_passager <= ?";
      $params[] = $_GET['prix_max'];
    }
    
    // Filtre durée
    if (!empty($_GET['duree_max']) && $_GET['duree_max'] > 0) {
      $conditions[] = "(t.duree_estimee_minutes <= ? OR t.duree_estimee_minutes IS NULL)";
      $params[] = $_GET['duree_max'];
    }
    
    // Filtres horaires
    $horaires = [];
    if (isset($_GET['horaire_nuit'])) $horaires[] = "HOUR(t.date_depart) BETWEEN 0 AND 5";
    if (isset($_GET['horaire_matin'])) $horaires[] = "HOUR(t.date_depart) BETWEEN 6 AND 11";
    if (isset($_GET['horaire_am'])) $horaires[] = "HOUR(t.date_depart) BETWEEN 12 AND 17";
    if (isset($_GET['horaire_soir'])) $horaires[] = "HOUR(t.date_depart) BETWEEN 18 AND 23";
    
    if (!empty($horaires)) {
      $conditions[] = "(" . implode(" OR ", $horaires) . ")";
    }
    
    $whereClause = implode(" AND ", $conditions);
    
    $stmt = $this->db->prepare("
      SELECT 
        t.*,
        u.prenom as conducteur_prenom,
        u.nom as conducteur_nom,
        u.photo_profil,
        v.energie,
        COALESCE(AVG(a.note), 0) as note_moyenne,
        COUNT(DISTINCT a.id) as nb_avis
      FROM trajets t
      JOIN utilisateurs u ON t.chauffeur_id = u.id
      JOIN vehicules v ON t.vehicule_id = v.id
      LEFT JOIN avis a ON a.evalue_id = t.chauffeur_id AND a.statut = 'valide'
      WHERE $whereClause
      GROUP BY t.id
      ORDER BY t.date_depart ASC
    ");
    
    $stmt->execute($params);
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer durée estimée si manquante
    foreach ($trajets as &$trajet) {
      if (empty($trajet['duree_estimee_minutes'])) {
        $duree = $this->calculerDureeEstimee($trajet['distance_km']);
        $trajet['duree_estimee_minutes'] = $duree;
        
        $dateDepart = new DateTime($trajet['date_depart']);
        $dateDepart->modify("+{$duree} minutes");
        $trajet['date_arrivee_estimee'] = $dateDepart->format('Y-m-d H:i:s');
      }
    }
    
    return $trajets;
  }
  
  private function calculerDureeEstimee($distanceKm) 
  {
    if (empty($distanceKm) || $distanceKm <= 0) return 120;
    $vitesseMoyenne = 80;
    return round(($distanceKm / $vitesseMoyenne) * 1.1 * 60);
  }

  //Page Détails
  public function getTrajetDetails($trajetId) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        t.*,
        u.id as conducteur_id,
        u.prenom as conducteur_prenom,
        u.nom as conducteur_nom,
        u.photo_profil,
        u.bio,
        v.marque,
        v.modele,
        v.energie,
        COALESCE(AVG(a.note), 0) as note_moyenne
      FROM trajets t
      JOIN utilisateurs u ON t.chauffeur_id = u.id
      JOIN vehicules v ON t.vehicule_id = v.id
      LEFT JOIN avis a ON a.evalue_id = u.id AND a.statut = 'valide'
      WHERE t.id = ?
      GROUP BY t.id
    ");
    
    $stmt->execute([$trajetId]);
    $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$trajet) return null;
    
    // ✅ Calculer durée si manquante (comme dans rechercherTrajets)
    if (empty($trajet['duree_estimee_minutes']) || empty($trajet['date_arrivee_estimee'])) {
      $duree = $this->calculerDureeEstimee($trajet['distance_km']);
      $trajet['duree_estimee_minutes'] = $duree;
      
      $dateDepart = new DateTime($trajet['date_depart']);
      $dateDepart->modify("+{$duree} minutes");
      $trajet['date_arrivee_estimee'] = $dateDepart->format('Y-m-d H:i:s');
    }
    
    // Récupérer les préférences du conducteur
    $stmt = $this->db->prepare("
      SELECT pt.nom, pu.valeur, pu.preference_personnalisee
      FROM preferences_utilisateurs pu
      JOIN preferences_types pt ON pu.preference_type_id = pt.id
      WHERE pu.utilisateur_id = ? AND pu.valeur = 'accepte'
    ");
    $stmt->execute([$trajet['conducteur_id']]);
    $trajet['preferences'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les avis validés
    $stmt = $this->db->prepare("
      SELECT a.*, u.prenom as auteur_prenom, u.photo_profil as auteur_photo
      FROM avis a
      JOIN utilisateurs u ON a.evaluateur_id = u.id
      WHERE a.evalue_id = ? AND a.statut = 'valide'
      ORDER BY a.created_at DESC
      LIMIT 10
    ");
    $stmt->execute([$trajet['conducteur_id']]);
    $trajet['avis'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $trajet;
  }
}