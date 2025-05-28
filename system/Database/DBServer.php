<?php

namespace System\Database;

use PDO;
use System\Core\Logger;

class DBServer
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        
        if(!isset($_ENV)){
            Logger::Error("Database connection failed: Environment variables not set.");
            return false;
        }
        $host = $_ENV['DB_SERVER'];
        $dbname = $_ENV['DB_NAME'] ?? 'vinulms';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? 'root';
        $port = $_ENV['DB_PORT'] ?? '3306';

        // Check available PDO drivers
        $availableDrivers = PDO::getAvailableDrivers();
        $driver = $_ENV['DB_DRIVER'] ?? 'mysql'; // Default to 'mysql'

        // Validate the selected driver and use the first available driver if not valid
        if (!in_array($driver, $availableDrivers)) {
            Logger::Warning("Selected driver '{$driver}' is not available. Using the first available driver.");
            $driver = $availableDrivers[0];
        }

        $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
       
        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            Logger::Info("Database connection established successfully");
        } catch (\PDOException $e) {
            Logger::Error("Database connection failed: " . $e->getMessage());
            throw $e;
        }
    }

    public static function connect()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //function to get array of the description of the tablename provided
    public function describeTable($tableName){
        $stmt = $this->pdo->prepare("DESCRIBE {$tableName}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCollection($tableName)
    {
        return new DBCollection($this->pdo, $tableName);
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function getDb()
    {
        return $this->pdo;
    }
    
}
?>