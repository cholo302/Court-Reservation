<?php
/**
 * Migration: Replace gov_id_number with gov_id_photo and face_photo columns
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
    $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('gov_id_photo', 'face_photo', 'gov_id_number') AND TABLE_SCHEMA = ?");
    $stmt->execute([DB_NAME]);
    $columns = $stmt->fetchAll();
    
    $existingColumns = array_column($columns, 'COLUMN_NAME');
    
    // Check what needs to be done
    $hasGovIdPhoto = in_array('gov_id_photo', $existingColumns);
    $hasFacePhoto = in_array('face_photo', $existingColumns);
    $hasGovIdNumber = in_array('gov_id_number', $existingColumns);
    
    if ($hasGovIdPhoto && $hasFacePhoto) {
        $success = true;
        $message = "Government ID photo and face photo columns already exist.";
        $details = "Columns are already set up correctly.";
    } else {
        // Add gov_id_photo column if it doesn't exist
        if (!$hasGovIdPhoto) {
            $db->exec("ALTER TABLE users ADD COLUMN gov_id_photo VARCHAR(255) AFTER gov_id_type");
        }
        
        // Add face_photo column if it doesn't exist
        if (!$hasFacePhoto) {
            $db->exec("ALTER TABLE users ADD COLUMN face_photo VARCHAR(255) AFTER gov_id_photo");
        }
        
        // Drop gov_id_number column if it exists (no longer needed)
        if ($hasGovIdNumber) {
            $db->exec("ALTER TABLE users DROP COLUMN gov_id_number");
        }
        
        // Drop the unique constraint on gov_id_number if it exists
        try {
            $db->exec("ALTER TABLE users DROP INDEX unique_gov_id");
        } catch (Exception $e) {
            // Index might not exist, that's okay
        }
        
        $success = true;
        $message = "Migration completed successfully!";
        $details = "Added gov_id_photo and face_photo columns. Removed gov_id_number column.";
    }
    
} catch (PDOException $e) {
    $success = false;
    $message = "Error: " . htmlspecialchars($e->getMessage());
    $details = $message;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Migration: Add Government ID and Face Photos</title>
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
    
    <?php if ($success && isset($details)): ?>
    <div class="info">
        <h4>Details:</h4>
        <p><?= $details ?></p>
        <ul>
            <li><strong>gov_id_photo</strong> - Photo of the government-issued ID card</li>
            <li><strong>face_photo</strong> - Clear frontal face photo for identity verification</li>
            <li><strong>gov_id_number</strong> - Removed (no longer needed)</li>
        </ul>
    </div>
    <?php endif; ?>
    
    <div style="text-align: center;">
        <a href="/" class="button">Go Home</a>
    </div>
</body>
</html>
