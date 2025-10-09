<?php
require_once __DIR__ . '/../config/Database.php';

class Trajet 
{
  private $db;
  
  public function __construct() 
  {
    $this->db = Database::getInstance();
  }
  
  /**
   * Créer un nouveau trajet
   */
  public function create($data) 
  {
    try {
      $this->db->beginTransaction();
      
      $this->validateTrajetData($data);

      // Récupérer le nombre de places du véhicule
      $vehicule = $this->getVehiculeInfo($data['vehicule_id'], $data['chauffeur_id']);
      if (!$vehicule) {
          throw new Exception("Véhicule non trouvé ou non autorisé");
      }
      
      $stmt = $this->db->prepare("
        INSERT INTO trajets 
        (chauffeur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, prix_par_passager, 
         commission_plateforme, places_disponibles, places_totales, 
         statut, description, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'planifie', ?, NOW(), NOW())
      ");
      
      $placesDisponibles = $vehicule['nombre_places_disponibles'];
      
      $result = $stmt->execute([
        $data['chauffeur_id'],
        $data['vehicule_id'],
        $data['adresse_depart'],
        $data['adresse_arrivee'],
        $data['date_depart'],
        $data['prix_par_passager'],
        2.00, // commission par défaut
        $placesDisponibles,
        $placesDisponibles,
        $data['description'] ?? ''
      ]);
      
      if (!$result) {
          throw new Exception("Erreur lors de la création du trajet");
      }
      
      $trajetId = $this->db->lastInsertId();
      $this->db->commit();
      
      return $trajetId;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
  }
  
  /**
 * Récupérer les trajets à venir d'un utilisateur
 */
  public function getTrajetsAVenir($userId) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        t.id,
        t.adresse_depart,
        t.adresse_arrivee,
        t.date_depart,
        t.prix_par_passager,
        t.places_disponibles,
        t.places_totales,
        t.statut,
        v.marque,
        v.modele,
        v.immatriculation,
        CASE 
          WHEN t.chauffeur_id = ? THEN 'chauffeur'
          ELSE 'passager'
        END as role_utilisateur,
        CASE 
          WHEN t.chauffeur_id = ? THEN 'Vous-même'
          ELSE COALESCE(u_chauffeur.pseudo, u_chauffeur.prenom)
        END as conducteur,
        r.id as reservation_id,
        r.statut as statut_reservation,
        (SELECT COUNT(*) FROM reservations WHERE trajet_id = t.id AND statut = 'confirmee') as nb_participants
      FROM trajets t
      LEFT JOIN vehicules v ON t.vehicule_id = v.id
      LEFT JOIN utilisateurs u_chauffeur ON t.chauffeur_id = u_chauffeur.id
      LEFT JOIN reservations r ON (t.id = r.trajet_id AND r.passager_id = ?)
      WHERE (t.chauffeur_id = ? OR r.passager_id = ?) 
        AND t.date_depart > NOW()
        AND t.statut NOT IN ('annule', 'termine')
      GROUP BY t.id
      ORDER BY t.date_depart ASC
    ");
    
    $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
    return $stmt->fetchAll();
  }

  /**
   * Récupérer les trajets passés d'un utilisateur
   */
  public function getTrajetsPasses($userId) 
  {
    $stmt = $this->db->prepare("
      SELECT 
        t.*,
        t.places_totales,
        u.prenom as conducteur,
        CASE 
          WHEN t.chauffeur_id = ? THEN 'chauffeur'
          ELSE 'passager'
        END as role_utilisateur,
        COALESCE(a_trajet.id, a_reservation.id) as avis_id,
        COALESCE(a_trajet.statut, a_reservation.statut) as statut_avis,
        COALESCE(a_trajet.motif_refus, a_reservation.motif_refus) as avis_motif_refus,
        COALESCE(a_trajet.note, a_reservation.note) as note,
        COALESCE(a_trajet.commentaire, a_reservation.commentaire) as commentaire,
        (SELECT COUNT(*) FROM reservations WHERE trajet_id = t.id AND statut = 'confirmee') as nb_participants
      FROM trajets t
      LEFT JOIN utilisateurs u ON t.chauffeur_id = u.id
      LEFT JOIN reservations r ON r.trajet_id = t.id AND r.passager_id = ?
      LEFT JOIN avis a_trajet ON a_trajet.trajet_id = t.id AND a_trajet.evaluateur_id = ? AND t.chauffeur_id = ?
      LEFT JOIN avis a_reservation ON a_reservation.reservation_id = r.id AND a_reservation.evaluateur_id = ?
      WHERE (t.chauffeur_id = ? OR t.id IN (
        SELECT trajet_id FROM reservations WHERE passager_id = ?
      ))
      AND t.statut IN ('termine', 'annule')
      GROUP BY t.id
      ORDER BY t.date_depart DESC
    ");
    $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  /**
   * Annuler un trajet
  */
  public function annulerTrajet($trajetId, $userId) 
  {
    try {
        $this->db->beginTransaction();
        
        // Récupérer les infos du trajet
        $stmt = $this->db->prepare("
            SELECT chauffeur_id, statut 
            FROM trajets 
            WHERE id = ? AND (chauffeur_id = ? OR id IN (
                SELECT trajet_id FROM reservations WHERE passager_id = ?
            ))
        ");
        $stmt->execute([$trajetId, $userId, $userId]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$trajet) {
            throw new Exception("Trajet non trouvé ou accès non autorisé");
        }
        
        if ($trajet['statut'] === 'annule' || $trajet['statut'] === 'termine') {
            throw new Exception("Ce trajet ne peut plus être annulé");
        }
        
        // Mettre à jour le statut
        $stmt = $this->db->prepare("
            UPDATE trajets 
            SET statut = 'annule' 
            WHERE id = ?
        ");
        $stmt->execute([$trajetId]);
        
        // REMBOURSER 2 CRÉDITS AU CONDUCTEUR
        if ($trajet['chauffeur_id'] == $userId) {
            $stmt = $this->db->prepare("
                UPDATE utilisateurs 
                SET credits = credits + 2 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        }
        
        $this->db->commit();
        
        // ENVOYER EMAIL AUX PARTICIPANTS
        require_once __DIR__ . '/../helpers/EmailSimulator.php';

        $stmt = $this->db->prepare("
          SELECT u.email, u.prenom
          FROM reservations r
          JOIN utilisateurs u ON r.passager_id = u.id
          WHERE r.trajet_id = ?
        ");
        $stmt->execute([$trajetId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($participants as $participant) {
          EmailSimulator::emailAnnulationTrajet(
            $participant['email'],
            $participant['prenom'],
            $trajet['adresse_depart'],
            $trajet['adresse_arrivee'],
            $trajet['date_depart']
          );
        }
        return true;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
  }
  
  /**
   * Démarrer un trajet
   */
  public function demarrerTrajet($trajetId, $chauffeurId) 
  {
    $stmt = $this->db->prepare("
      UPDATE trajets 
      SET statut = 'en_cours', date_demarrage = NOW(), updated_at = NOW()
      WHERE id = ? AND chauffeur_id = ? AND statut IN ('planifie')
    ");
    
    $result = $stmt->execute([$trajetId, $chauffeurId]);
    
    if ($stmt->rowCount() === 0) {
      throw new Exception("Impossible de démarrer ce trajet");
    }
    
    return true;
  }
  
  /**
   * Terminer un trajet
   */
  public function terminerTrajet($trajetId, $chauffeurId) 
  {
    $this->db->beginTransaction();
    try {
      // Récupérer infos trajet
      $stmt = $this->db->prepare("
        SELECT t.*, u.email as chauffeur_email, u.prenom as chauffeur_prenom
        FROM trajets t
        JOIN utilisateurs u ON t.chauffeur_id = u.id
        WHERE t.id = ? AND t.chauffeur_id = ? AND t.statut = 'en_cours'
      ");
      $stmt->execute([$trajetId, $chauffeurId]);
      $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$trajet) {
        throw new Exception("Impossible de terminer ce trajet");
      }
      
      // 1. Marquer le trajet comme terminé
      $stmt = $this->db->prepare("
        UPDATE trajets 
        SET statut = 'termine', date_arrivee_reelle = NOW(), updated_at = NOW()
        WHERE id = ?
      ");
      $stmt->execute([$trajetId]);
      
      // 2. VALIDER TOUTES LES RÉSERVATIONS (débiter passagers, créditer chauffeur)
      $stmt = $this->db->prepare("
        UPDATE reservations 
        SET a_valide_trajet = TRUE, statut = 'terminee'
        WHERE trajet_id = ? AND statut = 'confirmee'
      ");
      $stmt->execute([$trajetId]);
      
      // 3. ENVOYER EMAIL AUX PARTICIPANTS
      require_once __DIR__ . '/../helpers/EmailSimulator.php';
      
      $stmt = $this->db->prepare("
        SELECT u.email, u.prenom
        FROM reservations r
        JOIN utilisateurs u ON r.passager_id = u.id
        WHERE r.trajet_id = ?
      ");
      $stmt->execute([$trajetId]);
      $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      foreach ($participants as $participant) {
        EmailSimulator::emailFinTrajet(
          $participant['email'],
          $participant['prenom'],
          $trajet['adresse_depart'],
          $trajet['adresse_arrivee'],
          $trajet['date_depart']
        );
      }
      
      $this->db->commit();
      return true;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
  }
  
  // Méthodes utilitaires privées
  
  public function getTrajetById($trajetId) 
  {
    $stmt = $this->db->prepare("
      SELECT * FROM trajets WHERE id = ?
    ");
    $stmt->execute([$trajetId]);
    return $stmt->fetch();
  }
  
  private function getVehiculeInfo($vehiculeId, $userId) 
  {
    $stmt = $this->db->prepare("
      SELECT nombre_places_disponibles 
      FROM vehicules 
      WHERE id = ? AND utilisateur_id = ? AND est_actif = 1
    ");
    $stmt->execute([$vehiculeId, $userId]);
    return $stmt->fetch();
  }
  
  private function hasReservation($trajetId, $userId) 
  {
    $stmt = $this->db->prepare("
      SELECT COUNT(*) 
      FROM reservations 
      WHERE trajet_id = ? AND passager_id = ? AND statut != 'annulee'
    ");
    $stmt->execute([$trajetId, $userId]);
    return $stmt->fetchColumn() > 0;
  }
  
  private function rembourserPassagers($trajetId) 
  {
    // Récupérer toutes les réservations du trajet
    $stmt = $this->db->prepare("
      SELECT passager_id, prix_total 
      FROM reservations 
      WHERE trajet_id = ? AND statut != 'annulee'
    ");
    $stmt->execute([$trajetId]);
    $reservations = $stmt->fetchAll();
    
    // Rembourser chaque passager
    foreach ($reservations as $reservation) {
      $this->ajouterCredits($reservation['passager_id'], $reservation['prix_total'], 'remboursement_annulation', $trajetId);
    }
  }
  
  private function updatePlacesDisponibles($trajetId) 
  {
    $stmt = $this->db->prepare("
      UPDATE trajets t
      SET places_disponibles = places_totales - (
        SELECT COUNT(*) 
        FROM reservations r 
        WHERE r.trajet_id = t.id AND r.statut != 'annulee'
      )
      WHERE t.id = ?
    ");
    $stmt->execute([$trajetId]);
  }
  
  private function ajouterCredits($userId, $montant, $motif, $referenceId) 
  {
    // Récupérer le solde actuel
    $stmt = $this->db->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $soldeAvant = $stmt->fetchColumn();
    
    // Mettre à jour les crédits utilisateur
    $stmt = $this->db->prepare("
      UPDATE utilisateurs 
      SET credits = credits + ?, updated_at = NOW()
      WHERE id = ?
    ");
    $stmt->execute([$montant, $userId]);
    
    // Enregistrer la transaction
    $stmt = $this->db->prepare("
      INSERT INTO transactions 
      (utilisateur_id, type_transaction, montant, solde_avant, solde_apres, motif, reference_id, created_at)
      VALUES (?, 'credit', ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $montant, $soldeAvant, $soldeAvant + $montant, $motif, $referenceId]);
  }
  
  private function validateTrajetData($data) 
  {
    $required = ['chauffeur_id', 'vehicule_id', 'adresse_depart', 'adresse_arrivee', 'date_depart', 'prix_par_passager'];
    
    foreach ($required as $field) {
      if (empty($data[$field])) {
        throw new Exception("Le champ $field est obligatoire");
      }
    }
    
    // Validation de la date de départ
    $dateDepart = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_depart']);
    if (!$dateDepart) {
      // Essayer d'autres formats
      $dateDepart = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_depart']);
      if (!$dateDepart) {
        throw new Exception("Format de date invalide");
      }
    }
    
    if ($dateDepart <= new DateTime()) {
      throw new Exception("La date de départ doit être dans le futur");
    }
    
    // Validation du prix
    if (!is_numeric($data['prix_par_passager']) || $data['prix_par_passager'] <= 0) {
      throw new Exception("Le prix doit être un nombre positif");
    }
  }
}
