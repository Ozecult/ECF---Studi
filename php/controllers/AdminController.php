<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {
  private $db;
  
  public function __construct() 
  {
    $this->db = Database::getInstance()->getConnection();
  }
  
  public function createEmployee() {
    try {
      // Vérifier le token CSRF
      require_once __DIR__ . '/AuthController.php';
      $auth = new AuthController();
      
      $token = $_POST['csrf_token'] ?? '';
      if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        throw new Exception("Token CSRF invalide");
      }
      
      // Validation
      $nom = trim($_POST['nom'] ?? '');
      $prenom = trim($_POST['prenom'] ?? '');
      $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
      $password = $_POST['password'] ?? '';
      $password2 = $_POST['password2'] ?? '';
      
      if (!$email) throw new Exception("Email invalide");
      if (empty($nom) || empty($prenom) || empty($password)) throw new Exception("Tous les champs sont requis");
      if ($password !== $password2) throw new Exception("Les mots de passe ne correspondent pas");
      if (strlen($password) < 8) throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
      
      // Créer l'utilisateur
      $userModel = new User();
      $userData = [
        'nom' => $nom,
        'prenom' => $prenom,
        'pseudo' => strtolower($prenom . substr($nom, 0, 1)),
        'email' => $email,
        'password' => $password
      ];
      
      $result = $userModel->create($userData);
      $userId = $result['id']; // ✅ 'id' et non 'user_id'
      error_log("userId récupéré: $userId (type: " . gettype($userId) . ")");
      
      // Créer l'entrée employé
      $stmt = $this->db->prepare("
        INSERT INTO employes (utilisateur_id, role_id, date_embauche, statut)
        VALUES (?, 2, CURDATE(), 'actif')
      ");
      $success = $stmt->execute([$userId]);

      error_log("Insert employes - success: " . ($success ? 'OUI' : 'NON'));

      if (!$success) {
          $errorInfo = $stmt->errorInfo();
          error_log("Erreur SQL: " . json_encode($errorInfo));
      }
      
      header('Location: /ecoride/php/index.php?page=admin&success=employee_created');
      exit;
      
    } catch (Exception $e) {
        error_log("Erreur createEmployee: " . $e->getMessage());
        header('Location: /ecoride/php/index.php?page=admin&error=' . urlencode($e->getMessage()));
        exit;
    }
  }
  
  public function getAllUsers() 
  {
    $stmt = $this->db->prepare("
      SELECT 
        u.id,
        u.prenom,
        u.nom,
        u.email,
        u.statut,
        CASE 
          WHEN e.id IS NOT NULL THEN r.nom
          WHEN u.est_chauffeur = 1 AND u.est_passager = 1 THEN 'Polyvalent'
          WHEN u.est_chauffeur = 1 THEN 'Conducteur'
          WHEN u.est_passager = 1 THEN 'Passager'
          ELSE 'Utilisateur'
        END as role
      FROM utilisateurs u
      LEFT JOIN employes e ON e.utilisateur_id = u.id AND e.statut = 'actif'
      LEFT JOIN roles r ON r.id = e.role_id
      ORDER BY u.id DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function suspendUser($userId) 
  {
    $stmt = $this->db->prepare("UPDATE utilisateurs SET statut = 'suspendu' WHERE id = ?");
    $stmt->execute([$userId]);
  }

  public function reactivateUser($userId) 
  {
    $stmt = $this->db->prepare("UPDATE utilisateurs SET statut = 'actif' WHERE id = ?");
    $stmt->execute([$userId]);
  }
  
  public function getStatistics() 
  {
    // Nombre de trajets aujourd'hui
    $stmt = $this->db->query("SELECT COUNT(*) as count FROM trajets WHERE DATE(created_at) = CURDATE()");
    $trajetsAujourdhui = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // TOTAL DE TOUS LES TRAJETS
    $stmt = $this->db->query("SELECT COUNT(*) as count FROM trajets");
    $totalTrajets = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // TOTAL CRÉDITS GAGNÉS PAR LA PLATEFORME
    // 2 crédits par trajet publié + recharges achetées
    $totalCreditsPublications = $totalTrajets * 2;
    $stmt = $this->db->query("SELECT COALESCE(SUM(montant), 0) as total FROM transactions WHERE type_transaction = 'recharge'");
    $totalRecharges = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalCredits = $totalCreditsPublications + $totalRecharges;
    
    // CRÉDITS PAR JOUR (7 derniers jours)
    // Trajets publiés (2 crédits chacun) + recharges
    $stmt = $this->db->query("
      SELECT 
        DATE(t.created_at) as date,
        (COUNT(t.id) * 2) + COALESCE(SUM(tr.montant), 0) as total
      FROM trajets t
      LEFT JOIN transactions tr ON DATE(tr.created_at) = DATE(t.created_at) 
        AND tr.type_transaction = 'recharge'
      WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        AND t.created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
      GROUP BY DATE(t.created_at)
      ORDER BY date ASC
    ");
    $creditsParJour = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // TRAJETS PAR JOUR (7 derniers jours)
    $stmt = $this->db->query("
      SELECT DATE(created_at) as date, COUNT(*) as total
      FROM trajets 
      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
          AND created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
      GROUP BY DATE(created_at) 
      ORDER BY date ASC
    ");
    $trajetsParJour = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
      'trajets_aujourdhui' => $trajetsAujourdhui,
      'total_trajets' => $totalTrajets,
      'total_credits' => $totalCredits,
      'credits_par_jour' => $creditsParJour,
      'trajets_par_jour' => $trajetsParJour
    ];
  }

  //  Récupérer les signalements avec demande de suspension
  public function getUsersWithSignalements() 
  {
    $stmt = $this->db->query("
      SELECT 
        u.id,
        u.prenom,
        u.nom,
        u.email,
        u.statut,
        CASE 
          WHEN e.id IS NOT NULL THEN r.nom
          WHEN u.est_chauffeur = 1 AND u.est_passager = 1 THEN 'Polyvalent'
          WHEN u.est_chauffeur = 1 THEN 'Conducteur'
          WHEN u.est_passager = 1 THEN 'Passager'
          ELSE 'Utilisateur'
        END as role,
        COUNT(CASE WHEN s.statut IN ('en_attente', 'en_cours') THEN 1 END) as signalements_actifs,
        COUNT(CASE WHEN s.statut = 'resolu' THEN 1 END) as signalements_resolus,
        COUNT(CASE WHEN s.demande_suspension = 1 AND s.statut = 'resolu' THEN 1 END) as demandes_suspension
      FROM utilisateurs u
      LEFT JOIN employes e ON e.utilisateur_id = u.id AND e.statut = 'actif'
      LEFT JOIN roles r ON r.id = e.role_id
      LEFT JOIN signalements s ON u.id = s.signale_id
      GROUP BY u.id
      ORDER BY demandes_suspension DESC, signalements_resolus DESC, signalements_actifs DESC, u.prenom ASC
    ");
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ignorerDemandeSuspension($userId) {
    // Marquer toutes les demandes de suspension comme traitées pour cet utilisateur
    $stmt = $this->db->prepare("
      UPDATE signalements 
      SET demande_suspension = 0 
      WHERE signale_id = ? AND demande_suspension = 1
    ");
    
    return $stmt->execute([$userId]);
  }
}