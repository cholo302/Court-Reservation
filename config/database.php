<?php
/**
 * SQLite Database Configuration
 * Court Reservation System
 */

// Load .env
if (file_exists(__DIR__ . '/../.env')) {
    $envLines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dbPath = realpath(__DIR__ . '/..') . '/' . getenv('DB_DATABASE');
            $this->connection = new PDO(
                "sqlite:" . $dbPath,
                null,
                null,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            // Enable foreign keys
            $this->connection->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $e) {
            die('Database connection error: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
}

// Create global PDO instance for easy access
$db = Database::getInstance()->getConnection();