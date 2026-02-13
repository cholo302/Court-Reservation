<?php
/**
 * Migration: Add is_active column to users table
 * This script adds the is_active column to the existing users table
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'court_reservation');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP has no password

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    // Check if column already exists
    $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'is_active' AND TABLE_SCHEMA = ?");
    $stmt->execute([DB_NAME]);
    $result = $stmt->fetch();
    
    if ($result) {
        $success = true;
        $message = "The 'is_active' column already exists in the users table.";
    } else {
        // Add the column
        $db->exec("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT 1 AFTER is_blacklisted");
        $success = true;
        $message = "The 'is_active' column has been added to the users table. All existing users are now set to active (is_active = 1).";
    }
    
} catch (PDOException $e) {
    $success = false;
    $message = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Migration: Add is_active Column</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .alert {
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button:hover {
            background: #0052a3;
        }
    </style>
</head>
<body>
    <h1>Database Migration</h1>
    
    <div class="alert alert-<?= $success ? 'success' : 'error' ?>">
        <h3><?= $success ? '✓ Success' : '✗ Failed' ?></h3>
        <p><?= $message ?></p>
    </div>
    
    <div style="text-align: center;">
        <a href="/admin/users" class="button">Back to Users</a>
    </div>
</body>
</html>
