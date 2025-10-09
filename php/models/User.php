<?php
// models/User.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class User {
    private $db;
    private $config;
    
    public function __construct() 
    {
      $this->db = Database::getInstance();
      $this->config = require __DIR__ . '/../config/config.php';
    }

    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password)
    {
      // 1. Vérifier le blocage de compte
      if ($this->isAccountLocked($email)) {
        throw new Exception("Compte temporairement bloqué. Réessayez plus tard.");
      }

      // 2. Récupérer l'utilisateur
      $user = $this->getUserByEmail($email);
      if (!$user) {
        $this->logFailedAttempt($email);
        throw new Exception("Email ou mot de passe incorrect");
      }

      // 3. Vérifier le mot de passe
      if (!password_verify($password, $user['mot_de_passe'])) {
        $this->logFailedAttempt($email, $user['id']);
        throw new Exception("Email ou mot de passe incorrect");
      }

      // 4. Vérifications supplémentaires
      if (!$user['email_verifie']) {
        throw new Exception("Veuillez vérifier votre email avant de vous connecter");
      }

      if ($user['statut'] !== 'actif') {
        throw new Exception("Votre compte n'est pas actif. Contactez le support.");
      }

      // 5. Réinitialiser les tentatives échouées
      $this->resetFailedAttempts($email);

      // 6. Mettre à jour la dernière connexion
      $this->updateLastLogin($user['id']);

      // 7. Retourner les données utilisateur (sans le mot de passe)
      unset($user['mot_de_passe']);
      return $user;
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create($data)
    {
      try {
        $this->db->beginTransaction();

        // Validation des données
        $this->validateUserData($data);

        // Vérifier l'unicité
        if ($this->emailExists($data['email'])) {
          throw new Exception("Cette adresse email est déjà utilisée");
        }

        if ($this->pseudoExists($data['pseudo'])) {
          throw new Exception("Ce pseudo est déjà utilisé");
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash(
          $data['password'], 
          $this->config['security']['password_hash_algo'],
          $this->config['security']['password_options']
        );

        // Générer un token de vérification
        $verificationToken = bin2hex(random_bytes(32));

        // Insérer l'utilisateur
        $stmt = $this->db->prepare("
          INSERT INTO UTILISATEURS 
          (prenom, nom, email, mot_de_passe, pseudo, photo_profil, bio, credits, est_chauffeur, 
          est_passager, statut, token_verification, email_verifie, created_at, updated_at)
          VALUES (?, ?, ?, ?, ?, NULL, NULL, ?, ?, ?, 'actif', ?, true, NOW(), NOW())
        ");

        $executeData = [
          trim($data['prenom']),
          trim($data['nom']),
          filter_var($data['email'], FILTER_SANITIZE_EMAIL),
          $hashedPassword,
          trim($data['pseudo']),
          20, // credits par défaut
          $data['est_chauffeur'] ?? false,
          $data['est_passager'] ?? true,
          $verificationToken
        ];

        $result = $stmt->execute($executeData);

        if (!$result) {
          throw new Exception("Erreur lors de la création du compte");
        }

        $userId = $this->db->lastInsertId();
        
        // Log de création d'utilisateur
        $activityLog = new ActivityLog();
        $activityLog->log($userId, 'user_created', [
            'email' => $data['email'],
            'pseudo' => $data['pseudo']
        ]);

        $this->db->commit();

        // SIMULATION EMAIL INSCRIPTION
        require_once __DIR__ . '/../utils/EmailSimulator.php';
        EmailSimulator::emailInscription(
            $data['email'],
            $data['prenom'],
            $data['pseudo']
        );

        return [
          'id' => $userId,
          'verification_token' => $verificationToken
        ];

      } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
      }
    }
    /**
     * Récupérer un utilisateur par email
     */
    public function getUserByEmail($email)
    {
      $stmt = $this->db->prepare("
        SELECT id, prenom, nom, email, mot_de_passe, pseudo, photo_profil, 
          credits, note_moyenne, nombre_avis, est_chauffeur, est_passager, 
          statut, email_verifie, derniere_connexion, created_at, updated_at
        FROM utilisateurs 
        WHERE email = ? 
        LIMIT 1
      ");
      
      $stmt->execute([$email]);
      return $stmt->fetch();
    }
    /**
     * Récupérer un utilisateur par ID
     */
    public function getUserById($id)
    {
      $stmt = $this->db->prepare("
        SELECT id, prenom, nom, email, pseudo, photo_profil, bio, credits, 
              note_moyenne, nombre_avis, est_chauffeur, est_passager, 
              statut, email_verifie, derniere_connexion
        FROM utilisateurs 
        WHERE id = ? AND statut = 'actif'
        LIMIT 1
      ");
      
      $stmt->execute([$id]);
      return $stmt->fetch();
    }
    /**
     * Mettre à jour les informations utilisateur
     */
    public function updateUser($id, $data)
    {
      $allowedFields = ['prenom', 'nom', 'pseudo', 'photo_profil', 'est_chauffeur', 'est_passager'];
      $updateFields = [];
      $values = [];

      foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
          $updateFields[] = "$field = ?";
            if (is_bool($data[$field])) {
              $values[] = $data[$field] ? 1 : 0;
            } else {
              $values[] = htmlspecialchars($data[$field], ENT_QUOTES, 'UTF-8');
            }
          }
        }

      if (empty($updateFields)) {
        return false;
      }

      $updateFields[] = "updated_at = NOW()";
      $values[] = $id;

      $sql = "UPDATE utilisateurs SET " . implode(', ', $updateFields) . " WHERE id = ?";
      $stmt = $this->db->prepare($sql);
      
      return $stmt->execute($values);
    }
    /**
     * Vérifier si le compte est bloqué
     */
    private function isAccountLocked($email)
    {
      $stmt = $this->db->prepare("
        SELECT COUNT(*) as tentatives
        FROM LOGS_ACTIVITE 
        WHERE JSON_UNQUOTE(JSON_EXTRACT(details, '$.email')) = ?
        AND action = 'failed_login'
        AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
        AND JSON_EXTRACT(details, '$.resolved') IS NULL
      ");
      
      $stmt->execute([$email, $this->config['security']['lockout_time']]);
      $result = $stmt->fetch();
      
      return $result['tentatives'] >= $this->config['security']['max_login_attempts'];
    }
    /**
     * Enregistrer une tentative de connexion échouée
     */
    private function logFailedAttempt($email, $userId = null)
    {
      $stmt = $this->db->prepare("
        INSERT INTO LOGS_ACTIVITE 
        (utilisateur_id, action, table_affectee, details, ip_address, user_agent, created_at)
        VALUES (?, 'failed_login', 'UTILISATEURS', ?, ?, ?, NOW())
      ");
      
      $details = json_encode([
        'email' => $email,
        'timestamp' => date('Y-m-d H:i:s'),
        'reason' => 'invalid_credentials'
      ]);
      
      $stmt->execute([
        $userId,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
      ]);
    }
    /**
     * Réinitialiser les tentatives échouées
     */
    private function resetFailedAttempts($email)
    {
      $stmt = $this->db->prepare("
        UPDATE LOGS_ACTIVITE 
        SET details = JSON_SET(details, '$.resolved', true)
        WHERE JSON_UNQUOTE(JSON_EXTRACT(details, '$.email')) = ?
        AND action = 'failed_login'
        AND JSON_EXTRACT(details, '$.resolved') IS NULL
      ");
      
      $stmt->execute([$email]);
    }
    /**
     * Mettre à jour la dernière connexion
     */
    private function updateLastLogin($userId)
    {
      $stmt = $this->db->prepare("
        UPDATE utilisateurs 
        SET derniere_connexion = NOW(), updated_at = NOW()
        WHERE id = ?
      ");
      
      $stmt->execute([$userId]);

      // Log de connexion réussie
      $this->logActivity($userId, 'successful_login', 'utilisateurs', [
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    /**
     * Vérifier si un email existe
     */
    private function emailExists($email)
    {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
      $stmt->execute([$email]);
      return $stmt->fetchColumn() > 0;
    }
    /**
     * Vérifier si un pseudo existe
     */
    private function pseudoExists($pseudo) 
    {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE pseudo = ?");
      $stmt->execute([$pseudo]);
      return $stmt->fetchColumn() > 0;
    }

    /**
     * Valider les données utilisateur
     */
    private function validateUserData($data)
    {
      $required = ['prenom', 'nom', 'email', 'password', 'pseudo'];
      
      foreach ($required as $field) {
        if (empty($data[$field])) {
          throw new Exception("Le champ $field est obligatoire");
        }
      }

      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Format d'email invalide");
      }

      if (strlen($data['password']) < 8) {
        throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
      }

      if (strlen($data['pseudo']) < 3) {
        throw new Exception("Le pseudo doit contenir au moins 3 caractères");
      }
    }
    /**
     * Enregistrer une activité dans les logs
     */
    private function logActivity($userId, $action, $table, $details = [])
    {
      $stmt = $this->db->prepare("
        INSERT INTO LOGS_ACTIVITE 
        (utilisateur_id, action, table_affectee, details, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
      ");
      
      $stmt->execute([
        $userId,
        $action,
        $table,
        json_encode($details),
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
      ]);
    }
    /**
     * Créer un token "Se souvenir de moi"
     */
    public function createRememberToken($userId)
    {
      $token = bin2hex(random_bytes(32));
      $hashedToken = hash('sha256', $token);
      $expires = time() + $this->config['security']['remember_token_lifetime'];
      
      // Supprimer les anciens tokens
      $stmt = $this->db->prepare("DELETE FROM user_remember_tokens WHERE utilisateur_id = ?");
      $stmt->execute([$userId]);
      
      // Insérer le nouveau token
      $stmt = $this->db->prepare("
          INSERT INTO user_remember_tokens (utilisateur_id, token, expires_at, created_at)
          VALUES (?, ?, FROM_UNIXTIME(?), NOW())
      ");
  
      $stmt->execute([$userId, $hashedToken, $expires]);
      return ['token' => $token, 'expires' => $expires];
    }
    /**
     * Valider un token "Se souvenir de moi"
     */
    public function validateRememberToken($userId, $token)
    {
      $hashedToken = hash('sha256', $token);
      
      $stmt = $this->db->prepare("
          SELECT id FROM user_remember_tokens 
          WHERE utilisateur_id = ? AND token = ? AND expires_at > NOW()
      ");
      
      $stmt->execute([$userId, $hashedToken]);
      
      return $stmt->fetch() !== false;
    }

    /**
     * Supprimer les tokens "Se souvenir de moi"
     */
    public function deleteRememberTokens($userId)
    {
      $stmt = $this->db->prepare("DELETE FROM user_remember_tokens WHERE utilisateur_id = ?");
      return $stmt->execute([$userId]);
    }
}
