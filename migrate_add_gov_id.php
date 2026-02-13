<?php
/**
 * Migration: Add government ID verification columns to users table
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'court_reservation');
define('DB_USER', 'root');
define('DB_PASS', '');

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
    
    // Check if columns already exist
    $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('gov_id_type', 'gov_id_number') AND TABLE_SCHEMA = ?");
    $stmt->execute([DB_NAME]);
    $columns = $stmt->fetchAll();
    
    $missingColumns = [];
    $existingColumns = array_column($columns, 'COLUMN_NAME');
    
    if (!in_array('gov_id_type', $existingColumns)) {
        $missingColumns[] = 'gov_id_type';
    }
    if (!in_array('gov_id_number', $existingColumns)) {
        $missingColumns[] = 'gov_id_number';
    }
    
    if (empty($missingColumns)) {
        $success = true;
        $message = "Government ID columns already exist in the users table.";
    } else {
        // Add missing columns
        if (in_array('gov_id_type', $missingColumns)) {
            $db->exec("ALTER TABLE users ADD COLUMN gov_id_type VARCHAR(50) AFTER remember_token");
        }
        if (in_array('gov_id_number', $missingColumns)) {
            $db->exec("ALTER TABLE users ADD COLUMN gov_id_number VARCHAR(100) AFTER gov_id_type");
        }
        
        // Add unique constraint on gov_id_type and gov_id_number combination
        $db->exec("ALTER TABLE users ADD UNIQUE KEY unique_gov_id (gov_id_type, gov_id_number)");
        
        $success = true;
        $message = "Government ID columns have been added to the users table. Unique constraint applied.";
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
    <title>Migration: Add Government ID Columns</title>
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
        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            color: #004085;
        }
    </style>
</head>
<body>
    <h1>Database Migration</h1>
    
    <div class="alert alert-<?= $success ? 'success' : 'error' ?>">
        <h3><?= $success ? '✓ Success' : '✗ Failed' ?></h3>
        <p><?= $message ?></p>
    </div>
    
    <?php if ($success): ?>
    <div class="info">
        <h4>What was added:</h4>
        <ul>
            <li><strong>gov_id_type</strong> - Stores the type of government ID (LTO, Passport, etc.)</li>
            <li><strong>gov_id_number</strong> - Stores the ID number</li>
            <li><strong>Unique constraint</strong> - Ensures each government ID can only be used once</li>
        </ul>
    </div>
    <?php endif; ?>
    
    <div style="text-align: center;">
        <a href="/admin/users" class="button">Back to Admin</a>
        <a href="/" class="button" style="background: #6c757d; margin-left: 10px;">Go Home</a>
    </div>
</body>
</html>
