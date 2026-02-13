<?php
/**
 * Migration: Add Government ID verification columns to users table
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
    $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('gov_id_type', 'gov_id_photo', 'face_photo') AND TABLE_SCHEMA = ?");
    $stmt->execute([DB_NAME]);
    $columns = $stmt->fetchAll();
    
    $existingColumns = array_column($columns, 'COLUMN_NAME');
    
    $changes = [];
    
    // Add gov_id_type column if it doesn't exist
    if (!in_array('gov_id_type', $existingColumns)) {
        $db->exec("ALTER TABLE users ADD COLUMN gov_id_type VARCHAR(50) AFTER remember_token");
        $changes[] = "Added gov_id_type column";
    }
    
    // Add gov_id_photo column if it doesn't exist
    if (!in_array('gov_id_photo', $existingColumns)) {
        $db->exec("ALTER TABLE users ADD COLUMN gov_id_photo VARCHAR(255) AFTER gov_id_type");
        $changes[] = "Added gov_id_photo column";
    }
    
    // Add face_photo column if it doesn't exist
    if (!in_array('face_photo', $existingColumns)) {
        $db->exec("ALTER TABLE users ADD COLUMN face_photo VARCHAR(255) AFTER gov_id_photo");
        $changes[] = "Added face_photo column";
    }
    
    // Try to remove old gov_id_number column if it exists
    try {
        $checkStmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'gov_id_number' AND TABLE_SCHEMA = ?");
        $checkStmt->execute([DB_NAME]);
        if ($checkStmt->fetch()) {
            // Try to drop unique constraint first
            try {
                $db->exec("ALTER TABLE users DROP INDEX unique_gov_id");
            } catch (Exception $e) {
                // Index might not exist
            }
            
            // Drop the column
            $db->exec("ALTER TABLE users DROP COLUMN gov_id_number");
            $changes[] = "Removed gov_id_number column (no longer needed)";
        }
    } catch (Exception $e) {
        // Column doesn't exist or can't be removed, that's fine
    }
    
    if (empty($changes)) {
        $success = true;
        $message = "All required columns already exist in the users table.";
        $details = "No changes needed - your database is up to date!";
    } else {
        $success = true;
        $message = "Migration completed successfully!";
        $details = implode("<br>", $changes);
    }
    
} catch (PDOException $e) {
    $success = false;
    $message = "Database Error: " . htmlspecialchars($e->getMessage());
    $details = $message;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database Migration - Add Government ID Columns</title>
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
        .alert h3 {
            margin-top: 0;
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
        .details {
            background: #f8f9fa;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin-top: 15px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>🗄️ Database Migration</h1>
    
    <div class="alert alert-<?= $success ? 'success' : 'error' ?>">
        <h3><?= $success ? '✓ Success' : '✗ Error' ?></h3>
        <p><?= $message ?></p>
        <?php if ($details): ?>
        <div class="details">
            <?= $details ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($success): ?>
    <div class="info">
        <h4>📋 What was added:</h4>
        <ul>
            <li><strong>gov_id_type</strong> - Type of government ID (LTO, Passport, NBI, etc.)</li>
            <li><strong>gov_id_photo</strong> - Photo of the government-issued ID card</li>
            <li><strong>face_photo</strong> - Clear frontal face photo for identity verification</li>
        </ul>
        <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,64,133,0.2);">
            <strong>You can now:</strong>
        </p>
        <ul>
            <li>Register new users with government ID verification</li>
            <li>Upload both ID card and face photos during registration</li>
            <li>View verification photos in the admin dashboard</li>
        </ul>
    </div>
    <?php endif; ?>
    
    <div style="text-align: center;">
        <a href="/register" class="button">Go to Registration</a>
        <a href="/" class="button" style="background: #6c757d; margin-left: 10px;">Go Home</a>
    </div>
</body>
</html>
