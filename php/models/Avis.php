<?php
class Avis {
    private $db;
    
    public function __construct() 
    {
        require_once __DIR__ . '/../config/database.php';
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Avis d'un chauffeur sur son trajet
    public function creerAvisTrajet($trajetId, $evaluateurId, $note, $commentaire, $trajetReussi, $signalement = '') 
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO avis (
                    trajet_id, 
                    evaluateur_id, 
                    evalue_id,
                    note, 
                    commentaire, 
                    statut
                ) VALUES (?, ?, ?, ?, ?, 'en_attente')
            ");
            
            // Pour un chauffeur, evalue_id peut être NULL ou le premier passager
            $stmt->execute([$trajetId, $evaluateurId, $evaluateurId, $note, $commentaire]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Avis d'un passager via sa réservation
    public function creerAvisReservation($trajetId, $evaluateurId, $note, $commentaire, $trajetReussi, $signalement = '') 
    {
        $db = Database::getInstance()->getConnection();
        
        // Récupérer infos trajet
        $stmt = $db->prepare("SELECT chauffeur_id FROM trajets WHERE id = ?");
        $stmt->execute([$trajetId]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$trajet) {
            throw new Exception("Trajet introuvable");
        }
        
        $evalueId = $trajet['chauffeur_id'];
        
        // Récupérer reservation_id
        $stmt = $db->prepare("
            SELECT id FROM reservations 
            WHERE trajet_id = ? AND passager_id = ?
            LIMIT 1
        ");
        $stmt->execute([$trajetId, $evaluateurId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            throw new Exception("Réservation introuvable");
        }
        
        // Créer l'avis
        $stmt = $db->prepare("
            INSERT INTO avis (
                reservation_id, trajet_id, evaluateur_id, evalue_id, 
                note, commentaire, statut, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'en_attente', NOW())
        ");
        
        $stmt->execute([
            $reservation['id'],
            $trajetId,
            $evaluateurId,
            $evalueId,
            $note,
            $commentaire
        ]);
        
        // SI signalement → créer séparément
        if ($trajetReussi === 'non' && !empty($signalement)) {
            $stmt = $db->prepare("
                INSERT INTO signalements (
                    trajet_id, signaleur_id, signale_id, type_signalement,
                    description, statut, created_at
                ) VALUES (?, ?, ?, 'comportement', ?, 'nouveau', NOW())
            ");
            $stmt->execute([$trajetId, $evaluateurId, $evalueId, $signalement]);
        }
                
        return true;
    }

    /**
     * Récupérer tous les avis en attente de validation
     */
    public function getAvisEnAttente() {
    $sql = "
        SELECT 
            a.id,
            a.note,
            a.commentaire,
            a.created_at,
            a.statut,
            evaluateur.id as evaluateur_id,
            evaluateur.prenom as evaluateur_prenom,
            evaluateur.photo_profil as evaluateur_photo,
            evalue.prenom as evalue_prenom,
            t.adresse_depart,
            t.adresse_arrivee,
            t.date_depart
        FROM avis a
        JOIN utilisateurs evaluateur ON a.evaluateur_id = evaluateur.id
        JOIN utilisateurs evalue ON a.evalue_id = evalue.id
        JOIN trajets t ON a.trajet_id = t.id
        WHERE a.statut = 'en_attente'
        ORDER BY a.created_at ASC
    ";
    
    error_log("SQL AVIS: " . $sql); // DEBUG
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("RÉSULTATS AVIS: " . count($results) . " trouvés"); // DEBUG
    error_log("DÉTAILS: " . print_r($results, true)); // DEBUG
    
    return $results;
}

    /**
     * Valider un avis
     */
    public function validerAvis($avisId, $validateurUserId) 
    {
        // Récupérer l'ID employé depuis la table employes
        $stmt = $this->db->prepare("SELECT id FROM employes WHERE utilisateur_id = ? AND statut = 'actif'");
        $stmt->execute([$validateurUserId]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$employe) {
            throw new Exception("Employé non trouvé");
        }
        
        // Récupérer l'avis pour savoir qui est évalué
        $stmt = $this->db->prepare("SELECT evalue_id FROM avis WHERE id = ?");
        $stmt->execute([$avisId]);
        $avis = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$avis) {
            throw new Exception("Avis non trouvé");
        }
        
        $stmt = $this->db->prepare("
            UPDATE avis 
            SET statut = 'valide',
                date_validation = NOW(),
                validateur_id = ?
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$employe['id'], $avisId]);
        
        // Recalculer la note moyenne de l'utilisateur évalué
        if ($result) {
            $this->recalculerNoteMoyenne($avis['evalue_id']);
        }
        
        return $result;
    }

    /**
     * Refuser un avis
     */
    public function refuserAvis($avisId, $validateurUserId, $motifRefus = '') 
    {
        // Récupérer l'ID employé depuis la table employes
        $stmt = $this->db->prepare("SELECT id FROM employes WHERE utilisateur_id = ? AND statut = 'actif'");
        $stmt->execute([$validateurUserId]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$employe) {
            throw new Exception("Employé non trouvé");
        }
        
        $stmt = $this->db->prepare("
            UPDATE avis 
            SET statut = 'refuse',
                date_validation = NOW(),
                validateur_id = ?,
                motif_refus = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$employe['id'], $motifRefus, $avisId]);
    }

    /**
     * Récupérer les avis validés
     */
    public function getAvisValides($limit = 50) 
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.id,
                a.note,
                a.commentaire,
                a.date_validation,
                evaluateur.prenom as evaluateur_prenom,
                evaluateur.photo_profil as evaluateur_photo,
                evalue.prenom as evalue_prenom,
                validateur_user.prenom as validateur_prenom
            FROM avis a
            JOIN utilisateurs evaluateur ON a.evaluateur_id = evaluateur.id
            JOIN utilisateurs evalue ON a.evalue_id = evalue.id
            LEFT JOIN employes emp ON a.validateur_id = emp.id
            LEFT JOIN utilisateurs validateur_user ON emp.utilisateur_id = validateur_user.id
            WHERE a.statut = 'valide'
            ORDER BY a.date_validation DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les avis refusés
     */
    public function getAvisRefuses($limit = 50) 
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.id,
                a.note,
                a.commentaire,
                a.motif_refus,
                a.date_validation,
                evaluateur.prenom as evaluateur_prenom,
                evaluateur.photo_profil as evaluateur_photo,
                evalue.prenom as evalue_prenom,
                validateur_user.prenom as validateur_prenom
            FROM avis a
            JOIN utilisateurs evaluateur ON a.evaluateur_id = evaluateur.id
            JOIN utilisateurs evalue ON a.evalue_id = evalue.id
            LEFT JOIN employes emp ON a.validateur_id = emp.id
            LEFT JOIN utilisateurs validateur_user ON emp.utilisateur_id = validateur_user.id
            WHERE a.statut = 'refuse'
            ORDER BY a.date_validation DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    * Recalculer la note moyenne d'un utilisateur
    */
    private function recalculerNoteMoyenne($userId) {
        $stmt = $this->db->prepare("
            SELECT AVG(note) as moyenne, COUNT(*) as total
            FROM avis
            WHERE evalue_id = ? AND statut = 'valide'
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $moyenne = round($result['moyenne'], 1);
        $total = $result['total'];
        
        $stmt = $this->db->prepare("
            UPDATE utilisateurs 
            SET note_moyenne = ?, nombre_avis = ?
            WHERE id = ?
        ");
        $stmt->execute([$moyenne, $total, $userId]);
    }
}
