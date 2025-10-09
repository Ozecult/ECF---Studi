<?php
class Database 
{
    private static $instance = null;
    private $connection;
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;

    private function __construct()
    {
        $config = require __DIR__ . '/config.php';

        $this->host = $config['database']['host'];
        $this->db_name = $config['database']['dbname'];
        $this->username = $config['database']['username'];
        $this->password = $config['database']['password'];
        $this->charset = $config['database']['charset'] ?? 'utf8mb4';

        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $config = require __DIR__ . '/config.php';
            $dbConf = $config['database'] ?? [];

            $host = $dbConf['host'] ?? 'localhost';
            $port = $dbConf['port'] ?? 3307;
            $dbname = $dbConf['dbname'] ?? '';
            $charset = $dbConf['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            $options = $dbConf['options'] ?? [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion BDD ecoRide: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }

    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    public function prepare($query)
    {
        return $this->getConnection()->prepare($query);
    }

    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit()
    {
        return $this->getConnection()->commit();
    }

    public function rollBack()
    {
        return $this->getConnection()->rollBack();
    }

    public function __clone()
    {
        throw new Exception("Le clonage de cette classe n'est pas autorisé.");
    }

    public function __wakeup()
    {
        throw new Exception("La désérialisation de cette classe n'est pas autorisée.");
    }
}
