<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
  private $userModel;
  private $config;

  public function __construct()
  {
    $this->userModel = new User();
    $this->config = require __DIR__ . '/../config/config.php';
    $this->configureSession();
  }
  /**
   * Configuration sécurisée des sessions
   */
  private function configureSession()
  {
    if (session_status() === PHP_SESSION_NONE) {
      $sessionConfig = $this->config['session'];

      // IMPORTANT : Configurer le nom de session AVANT session_start()
      session_name($this->config['security']['session_name']);
      
      // Configuration des paramètres de session
      ini_set('session.cookie_httponly', $sessionConfig['cookie_httponly'] ? '1' : '0');
      ini_set('session.cookie_secure', $sessionConfig['cookie_secure'] ? '1' : '0');
      ini_set('session.use_strict_mode', $sessionConfig['use_strict_mode'] ? '1' : '0');
      ini_set('session.cookie_samesite', $sessionConfig['cookie_samesite']);
      ini_set('session.gc_maxlifetime', $sessionConfig['gc_maxlifetime']);
      ini_set('session.cookie_lifetime', $sessionConfig['cookie_lifetime']);
      
      // Configuration supplémentaire pour le chemin et le domaine
      if (isset($sessionConfig['cookie_path'])) {
        ini_set('session.cookie_path', $sessionConfig['cookie_path']);
      }
      if (isset($sessionConfig['cookie_domain'])) {
        ini_set('session.cookie_domain', $sessionConfig['cookie_domain']);
      }
      
      // Démarrer la session
      session_start();
      
      // Debug temporaire
      error_log("Session configurée - Name: " . session_name() . ", ID: " . session_id());
      error_log("Session config: " . json_encode([
        'cookie_httponly' => ini_get('session.cookie_httponly'),
        'cookie_secure' => ini_get('session.cookie_secure'),
        'cookie_samesite' => ini_get('session.cookie_samesite'),
        'cookie_path' => ini_get('session.cookie_path')
      ]));
    } else {
      // Si une session existe déjà, vérifier qu'elle utilise le bon nom
      $expectedName = $this->config['security']['session_name'];
      if (session_name() !== $expectedName) {
        error_log("ATTENTION: Session avec nom incorrect. Attendu: $expectedName, Actuel: " . session_name());
        // Optionnel : forcer la régénération avec le bon nom
        // session_write_close();
        // session_name($expectedName);
        // session_start();
      }
    }
  }
  
  /**
   * Afficher la page de connexion
   */
  public function showLogin()
  {
    // Si déjà connecté, rediriger
    if ($this->isLoggedIn()) {
      $this->redirect($this->getRedirectUrl());
      return;
    }
    // Générer token CSFR
    $csrfToken = $this->generateCSRFToken();
    // Charger la vue
    include __DIR__ . '/../views/connexion.php';
  }
  /**
   * Traiter la connexion
   */
  public function handleLogin()
  {
    error_log("=== DÉBUT handleLogin() ===");
    error_log("POST data: " . json_encode($_POST));
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    // Vérifier la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(false, "Méthode non autorisée", 405);
      return;
    }
    //Vérifier le token CSRF
    if (!$this->validateCSRFToken()) {
      $this->jsonResponse(false, "Token CSRF invalide", 403);
      return;
    }

    // Limitation du taux de requêtes
    if (!$this->checkRateLimit()) {
      $this->jsonResponse(false, "Trop de tentatives. Réessayez plus tard.", 429);
      return;
    }

    try {
      // Validation et nettoyage des données
      $email = $this->sanitizeInput($_POST['email'] ?? '');
      $password = $_POST['password'] ?? '';
      $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
      // Validation des champs
      if (empty($email) || empty($password)) {
          $this->jsonResponse(false, "Email et mot de passe requis");
          return;
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $this->jsonResponse(false, "Format d'email invalide");
          return;
      }

      // Authentification
      $user = $this->userModel->authenticate($email, $password);

      // Création de la session sécurisée
      $this->createSecureSession($user, $remember);
      error_log("LOGIN OK -> session_id: " . session_id());
      error_log("LOGIN OK -> _SESSION: " . json_encode($_SESSION));

      // Réponse de succès
      error_log("=== AVANT jsonResponse de succès ===");
      $this->jsonResponse(true, "Connexion réussie", 200, [
          'redirect_url' => $this->getRedirectUrl($user),
          'user' => $this->formatUserData($user)
      ]);

    } catch (Exception $e) {
        error_log("Erreur de connexion EcoRide: " . $e->getMessage());
        $this->jsonResponse(false, $e->getMessage());
    }
  }
  /**
   * Afficher la page d'inscription
   */
  public function showRegister()
  {
    if ($this->isLoggedIn()) {
        $this->redirect($this->getRedirectUrl());
        return;
    }
    $csrfToken = $this->generateCSRFToken();
    include __DIR__ . '/../views/inscription.php';
  }
  /**
   * Traiter l'inscription
   */
  public function handleRegister() 
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(false, "Méthode non autorisée", 405);
      return;
    }
    if (!$this->validateCSRFToken()) {
      $this->jsonResponse(false, "Token CSRF invalide", 403);
      return;
    }
    try {
      // Récupérer et valider les données
      $userData = [
          'prenom' => $this->sanitizeInput($_POST['prenom'] ?? ''),
          'nom' => $this->sanitizeInput($_POST['nom'] ?? ''),
          'pseudo' => $this->sanitizeInput($_POST['pseudo'] ?? ''),
          'email' => $this->sanitizeInput($_POST['email'] ?? ''),
          'password' => $_POST['password'] ?? '',
      ];
      // Créer l'utilisateur
      $result = $this->userModel->create($userData);
      // Envoyer l'email de vérification (à implémenter)
      // $this->sendVerificationEmail($userData['email'], $result['verification_token']);

      $this->jsonResponse(true, "Inscription réussie. Vérifiez votre email.", 201, [
          'redirect_url' => '/php/index.php?page=connexion'
      ]);
    } catch (Exception $e) {
        error_log("Erreur d'inscription EcoRide: " . $e->getMessage());
        $this->jsonResponse(false, $e->getMessage());
    }        
  }
  /**
   * Déconnexion
   */
  public function logout() {
    error_log("=== DÉBUT LOGOUT ===");
    error_log("Session actuelle: " . json_encode($_SESSION ?? []));
    error_log("Cookies actuels: " . json_encode($_COOKIE ?? []));
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        error_log("Déconnexion utilisateur ID: " . $userId);
        
        // Supprimer les tokens remember de la base
        try {
            $this->userModel->deleteRememberTokens($userId);
            error_log("Tokens remember supprimés de la BDD pour user " . $userId);
        } catch (Exception $e) {
            error_log("Erreur suppression tokens: " . $e->getMessage());
        }
    }
    
    // Supprimer TOUS les cookies remember possibles
    $cookieNames = ['ecoride_remember', 'ECORIDE_REMEMBER'];
    foreach ($cookieNames as $cookieName) {
        if (isset($_COOKIE[$cookieName])) {
            error_log("Suppression cookie: " . $cookieName);
            setcookie($cookieName, '', time() - 3600, '/', '', false, true);
            setcookie($cookieName, '', time() - 3600, '/');
            unset($_COOKIE[$cookieName]);
        }
    }
    
    // Détruire la session
    session_unset();
    session_destroy();
    
    // Supprimer cookies de session
    setcookie('ECORIDE_SESSION', '', time()-3600, '/');
    setcookie('PHPSESSID', '', time()-3600, '/');
    
    error_log("=== LOGOUT TERMINÉ ===");
    
    header('Location: /php/index.php?page=home');
    exit;
}
  /**
   * Vérifier si l'utilisateur est connecté
   */
  public function isLoggedIn() 
  {
    error_log("=== isLoggedIn() appelé ===");
    error_log("SESSION: " . json_encode($_SESSION ?? []));
    error_log("user_id existe: " . (isset($_SESSION['user_id']) ? 'OUI' : 'NON'));
    error_log("expires_at existe: " . (isset($_SESSION['expires_at']) ? 'OUI' : 'NON'));
    // Vérifier la session active
    if (isset($_SESSION['user_id']) && isset($_SESSION['expires_at'])) {
      if (time() < $_SESSION['expires_at']) {
        // Prolonger la session
        $_SESSION['expires_at'] = time() + $this->config['security']['session_lifetime'];
        return true;
      }
    }
    // Vérifier le cookie "se souvenir de moi"
    if (isset($_COOKIE['ecoride_remember'])) {
      return $this->handleRememberMe();
    }
    return false;
  }
  /**
  * Récupérer l'utilisateur actuel
  */

  public function getCurrentUser() 
  {
    if (!$this->isLoggedIn() || !isset($_SESSION['user_id'])) {
     return null;
    }
    return $this->userModel->getUserById($_SESSION['user_id']);
  }
  /**
   * Créer une session sécurisée
   */
private function createSecureSession($user, $remember = false) 
  {
    // Détruire complètement l'ancienne session avant d'en créer une nouvelle
    if (session_status() !== PHP_SESSION_NONE) {
      session_unset();
      session_destroy();
    }
    
    // Configurer et redémarrer la session
    $this->configureSession();
    
    // Régénérer l'ID de session pour sécurité
    session_regenerate_id(true);
    
    // Calculer le nouveau temps d'expiration
    $currentTime = time();
    $expiresAt = $currentTime + $this->config['security']['session_lifetime'];
    
    // Debug temporaire
    error_log("=== CRÉATION NOUVELLE SESSION ===");
    error_log("Current time: $currentTime");
    error_log("Session lifetime: " . $this->config['security']['session_lifetime']);
    error_log("Expires at: $expiresAt");
    error_log("Session ID: " . session_id());
    
    // Données de session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_pseudo'] = $user['pseudo'];
    $_SESSION['login_time'] = $currentTime;
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $_SESSION['session_token'] = $this->generateSecureToken();
    $_SESSION['expires_at'] = $expiresAt;
    
    error_log("Session data set: " . json_encode($_SESSION));
    
    // Cookie "se souvenir de moi" si demandé
    if ($remember) {
      $this->createRememberMeCookie($user['id']);
    }
  }
  /**
   * Créer un cookie "se souvenir de moi"
   */
  private function createRememberMeCookie($userId) 
  {
    $tokenData = $this->userModel->createRememberToken($userId);    
    $cookieValue = base64_encode($userId . ':' . $tokenData['token']);

    $secure = $this->config['session']['cookie_secure'] ?? false;
    $httponly = $this->config['session']['cookie_httponly'] ?? true;
    $expires = $tokenData['expires'];

    setcookie(
        'ecoride_remember',
        $cookieValue,
        $expires,
        '/',
        $_SERVER['HTTP_HOST'] ?? 'localhost',
        $secure,
        $httponly
    );
}
  /**
   * Gérer la reconnexion automatique via cookie
   */
  private function handleRememberMe() 
  {
    $cookieData = base64_decode($_COOKIE['ecoride_remember']);
    if (!$cookieData || strpos($cookieData, ':') === false) {
      return false;
    }
    list($userId, $token) = explode(':', $cookieData, 2);    
    // Valider le token
    if (!$this->userModel->validateRememberToken($userId, $token)) {
      // Token invalide, supprimer le cookie
      setcookie(
        'ecoride_remember',
        '',
        time() - 3600,
        '/',
        $_SERVER['HTTP_HOST'] ?? 'localhost',
        true,
        true
      );
      return false;
    }
    // Récupérer les données utilisateur
    $user = $this->userModel->getUserById($userId);
    if (!$user || $user['statut'] !== 'actif') {
      return false;
    }
    // Créer une nouvelle session
    $this->createSecureSession($user, true);
    return true;
  }
  /**
   * Valider le token CSRF
   */
  private function validateCSRFToken() 
  {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
  }
  /**
   * Générer un token CSRF
   */
  public function generateCSRFToken() 
  {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = $this->generateSecureToken();
    }
    return $_SESSION['csrf_token'];
  }
  /**
   * Vérification du rate limiting
   */
  private function checkRateLimit() 
  {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'login_attempts_' . md5($ip);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }

    $attempts = &$_SESSION[$key];    
    // Réinitialiser si la fenêtre de temps est écoulée
    if (time() - $attempts['first_attempt'] > $this->config['security']['rate_limit_window']) {
        $attempts = ['count' => 0, 'first_attempt' => time()];
    }
    // Vérifier la limite
    if ($attempts['count'] >= $this->config['security']['rate_limit_attempts']) {
        return false;
    }

    $attempts['count']++;
    return true;
  }
  /**
   * Formater les données utilisateur pour la réponse
   */
  private function formatUserData($user) 
  {
    return [
      'id' => $user['id'],
      'prenom' => $user['prenom'],
      'nom' => $user['nom'],
      'email' => $user['email'],
      'pseudo' => $user['pseudo'],
      'photo_profil' => $user['photo_profil'],
      'credits' => $user['credits'],
      'note_moyenne' => $user['note_moyenne'],
      'nombre_avis' => $user['nombre_avis'],
      'est_chauffeur' => $user['est_chauffeur'],
      'est_passager' => $user['est_passager']
    ];
  }
  /**
   * Nettoyer les données d'entrée
   */
  private function sanitizeInput($input) 
  {
    return trim($input);
  }
  /**
   * Générer un token sécurisé
   */
  private function generateSecureToken($length = 32) 
  {
    return bin2hex(random_bytes($length));
  }
  /**
   * Redirection sécurisée
   */
  private function redirect($url) 
  {
    header('Location: ' . $url);
    exit;
  }
  /**
  * Déterminer l'URL de redirection après connexion
  */
  private function getRedirectUrl($user = null) 
  {
    if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
      $redirect = urldecode($_GET['redirect']);
      if (strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
          return $redirect;
      }
    }
    
    if ($user) {
      // Vérifier si l'utilisateur a un rôle dans la table employes
      $role = $this->getEmployeeRole($user['id']);
      
      if ($role) {
        // Redirection selon le rôle exact
        if ($role === 'administrateur') {
          return '/php/index.php?page=admin';
        } elseif ($role === 'employe') {
          return '/php/index.php?page=employe';
        }
      }
      
      // Tous les utilisateurs standards → page utilisateur
      return '/php/index.php?page=utilisateur';
    }
      
    return '/php/index.php?page=home';
  }
  /**
 * Récupérer le rôle exact d'un employé depuis la table roles
 */
  private function getEmployeeRole($userId) 
  {
    require_once __DIR__ . '/../config/Database.php';
    $stmt = Database::getInstance()->getConnection()->prepare("
      SELECT r.nom 
      FROM employes e 
      JOIN roles r ON e.role_id = r.id 
      WHERE e.utilisateur_id = ? AND e.statut = 'actif'
      LIMIT 1
    ");
    
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['nom'] : null;
  }
  /**
   * Réponse JSON sécurisée
   */
  private function jsonResponse($success, $message, $httpCode = 200, $data = []) {
    // Headers de sécurité
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
      
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

  /**
   * Middleware pour vérifier l'authentification
   */
  public function requireAuth() 
  {
    if (!$this->isLoggedIn()) {
      if (isset($_POST['ajax']) || isset($_GET['ajax'])) {
        $this->jsonResponse(false, "Authentification requise", 401);
      } else {
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $this->redirect('/php/index.php?page=connexion&redirect=' . urlencode($currentUrl));
      }
    }
  }
  /**
   * Middleware pour les invités seulement
   */
  public function requireGuest() 
  {
    if ($this->isLoggedIn()) {
      $this->redirect($this->getRedirectUrl());
    }
  }
  /**
   * Vérifier les permissions utilisateur
   */
  public function hasPermission($permission)
  {
    $user = $this->getCurrentUser();
    if (!$user) {
        return false;
    }
    
    switch ($permission) {
      case 'create_trajet':
        return $user['est_chauffeur'];
      case 'book_trajet':
        return $user['est_passager'];
      case 'admin':
        return $this->isAdmin($user['id']);
      case 'employe':
        // Vérifier si c'est un employé (role_id = 2)
        $stmt = Database::getInstance()->getConnection()->prepare("
            SELECT r.nom 
            FROM employes e 
            JOIN roles r ON e.role_id = r.id 
            WHERE e.utilisateur_id = ? AND e.statut = 'actif'
        ");
        $stmt->execute([$user['id']]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role && ($role['nom'] === 'employe' || $role['nom'] === 'administrateur');
      default:
        return false;
    }
  }
  /**
   * Vérifier si l'utilisateur est administrateur
   */
  private function isAdmin($userId) 
  {
    $stmt = Database::getInstance()->prepare("
      SELECT r.nom 
      FROM EMPLOYES e 
      JOIN ROLES r ON e.role_id = r.id 
      WHERE e.utilisateur_id = ? AND e.statut = 'actif'
    ");
    
    $stmt->execute([$userId]);
    $role = $stmt->fetch();
    
    return $role && in_array($role['nom'], ['administrateur']);
  }
  /**
   * Mot de passe oublié - afficher le formulaire
   */
  public function showForgotPassword() 
  {
    $this->requireGuest();
    $csrfToken = $this->generateCSRFToken();
    include __DIR__ . '/../views/mdp-oublie.php';
  }
  /**
   * Mot de passe oublié - traitement
   */
  public function handleForgotPassword() 
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(false, "Méthode non autorisée", 405);
      return;
    }

    if (!$this->validateCSRFToken()) {
      $this->jsonResponse(false, "Token CSRF invalide", 403);
      return;
    }

    try {
      $email = $this->sanitizeInput($_POST['email'] ?? '');
      
      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->jsonResponse(false, "Email invalide");
        return;
      }

      $user = $this->userModel->getUserByEmail($email);
      if ($user) {
        // Générer un token de réinitialisation
        $resetToken = $this->generateSecureToken();
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 heure
        
        // Sauvegarder le token
        $stmt = Database::getInstance()->prepare("
            UPDATE UTILISATEURS 
            SET token_reset_password = ?, token_reset_expires_at = ?
            WHERE id = ?
        ");
        $stmt->execute([$resetToken, $expiresAt, $user['id']]);
        
        // Envoyer l'email (à implémenter)
        // $this->sendPasswordResetEmail($email, $resetToken);
      }
      // Toujours renvoyer la même réponse pour éviter l'énumération d'emails
      $this->jsonResponse(true, "Si cette adresse existe, un email de réinitialisation a été envoyé.");
    } catch (Exception $e) {
        error_log("Erreur mot de passe oublié: " . $e->getMessage());
        $this->jsonResponse(false, "Erreur lors du traitement de la demande");
    }
  }
}
