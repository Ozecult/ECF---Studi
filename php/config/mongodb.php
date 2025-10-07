<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class MongoDBConnection {
    private static $instance = null;
    private $client;
    private $database;
    
    private function __construct() {
        $config = require __DIR__ . '/config.php';
        
        // Ajoute dans config.php :
        // 'mongodb' => [
        //     'uri' => 'mongodb+srv://username:password@cluster.mongodb.net/',
        //     'database' => 'ecoride'
        // ]
        
        $this->client = new MongoDB\Client($config['mongodb']['uri']);
        $this->database = $this->client->{$config['mongodb']['database']};
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getCollection($name) {
        return $this->database->{$name};
    }
}