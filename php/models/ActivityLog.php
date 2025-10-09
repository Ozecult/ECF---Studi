<?php
require_once __DIR__ . '/../config/mongodb.php';

class ActivityLog {
    private $collection;
    
    public function __construct() {
        $mongo = MongoDBConnection::getInstance();
        $this->collection = $mongo->getCollection('activity_logs');
    }
    
    public function log($userId, $action, $details = []) {
        $document = [
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->collection->insertOne($document);
    }
    
    public function getUserLogs($userId, $limit = 50) {
        return $this->collection->find(
            ['user_id' => $userId],
            [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ]
        )->toArray();
    }
    
    public function getRecentLogs($limit = 100) {
        return $this->collection->find(
            [],
            [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ]
        )->toArray();
    }
}
