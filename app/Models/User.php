<?php

namespace App\Models;

use PDO;
use Exception;

class User
{
    protected static $connection;
    
    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                $dbPath = realpath(__DIR__ . '/../../storage/database.sqlite');
                self::$connection = new PDO(
                    'sqlite:' . $dbPath,
                    null,
                    null,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                // Enable foreign keys for SQLite
                self::$connection->exec('PRAGMA foreign_keys = ON');
            } catch (Exception $e) {
                die('Database Connection Error: ' . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    /**
     * Find user by ID
     */
    public static function find($id)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find user by email
     */
    public static function findByEmail($email)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new user
     */
    public static function create($data)
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO users (name, email, phone, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, datetime("now"), datetime("now"))
        ');
        
        try {
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'] ?? 'user'
            ]);
            
            return self::find($pdo->lastInsertId());
        } catch (Exception $e) {
            throw new Exception('Error creating user: ' . $e->getMessage());
        }
    }
    
    /**
     * Authenticate user with email and password
     */
    public static function authenticate($email, $password)
    {
        $user = self::findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        return $user;
    }
    
    /**
     * Update user
     */
    public static function update($id, $data)
    {
        $pdo = self::getConnection();
        
        $allowedFields = ['name', 'email', 'phone', 'profile_image', 'role'];
        $updates = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $values[] = $id;
        $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $updates) . ', updated_at = datetime("now") WHERE id = ?');
        return $stmt->execute($values);
    }
    
    /**
     * Check if user exists
     */
    public static function exists($email)
    {
        return self::findByEmail($email) !== false;
    }
}

    }
}
