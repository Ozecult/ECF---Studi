<?php

class ActivityLog {
    private $collection;
    private $mongoAvailable;
   
    public function __construct() {
        try {
            // Vérifier si MongoDB est configuré
            $config = require __DIR__ . '/../config/config.php';
            
            if (empty($config['mongodb']['uri'])) {
                $this->mongoAvailable = false;
                $this->collection = null;
                error_log("ActivityLog: MongoDB non configuré, logs désactivés");
                return;
            }
            
            // Tenter la connexion
            require_once __DIR__ . '/../config/mongodb.php';
            $mongo = MongoDBConnection::getInstance();
            $this->collection = $mongo->getCollection('activity_logs');
            $this->mongoAvailable = true;
            
        } catch (Exception $e) {
            $this->mongoAvailable = false;
            $this->collection = null;
            error_log("ActivityLog: MongoDB non disponible - " . $e->getMessage());
        }
    }
   
    public function log($userId, $action, $details = []) {
        // Si MongoDB non disponible, logger dans error_log et continuer
        if (!$this->mongoAvailable || !$this->collection) {
            error_log("ActivityLog: [$userId] $action - " . json_encode($details));
            return true; // ⬅️ Retourner true pour ne pas bloquer l'exécution
        }
        
        try {
            $document = [
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'timestamp' => new MongoDB\BSON\UTCDateTime()
            ];
           
            return $this->collection->insertOne($document);
            
        } catch (Exception $e) {
            error_log("ActivityLog: Erreur insertion - " . $e->getMessage());
            return false;
        }
    }
   
    public function getUserLogs($userId, $limit = 50) {
        if (!$this->mongoAvailable || !$this->collection) {
            return []; // Retourner tableau vide
        }
        
        try {
            return $this->collection->find(
                ['user_id' => $userId],
                [
                    'sort' => ['timestamp' => -1],
                    'limit' => $limit
                ]
            )->toArray();
        } catch (Exception $e) {
            error_log("ActivityLog: Erreur getUserLogs - " . $e->getMessage());
            return [];
        }
    }
   
    public function getRecentLogs($limit = 100) {
        if (!$this->mongoAvailable || !$this->collection) {
            return []; // Retourner tableau vide
        }
        
        try {
            return $this->collection->find(
                [],
                [
                    'sort' => ['timestamp' => -1],
                    'limit' => $limit
                ]
            )->toArray();
        } catch (Exception $e) {
            error_log("ActivityLog: Erreur getRecentLogs - " . $e->getMessage());
            return [];
        }
    }
    
    // Méthode statique pour logger
    public static function logAction($userId, $action, $details = []) {
        try {
            $logger = new self();
            return $logger->log($userId, $action, $details);
        } catch (Exception $e) {
            error_log("ActivityLog::logAction: " . $e->getMessage());
            return false;
        }
    }
}