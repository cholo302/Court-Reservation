<?php
/**
 * Debug Script - Check Government ID Photos
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
    
    // Get all users with their photo paths
    $stmt = $db->prepare("SELECT id, name, email, gov_id_type, gov_id_photo, face_photo FROM users WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL ORDER BY id DESC LIMIT 10");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    $baseDir = __DIR__;
    
} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug - Government ID Photos</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
        }
        .user-section {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #0066cc;
        }
        .photo-info {
            margin: 10px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 3px;
        }
        .exists {
            color: #155724;
            font-weight: bold;
        }
        .missing {
            color: #721c24;
            font-weight: bold;
        }
        .info {
            color: #004085;
        }
        img {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>🔍 Debug: Government ID Photos</h1>
    
    <?php if (empty($users)): ?>
        <div class="user-section">
            <p class="info">No users with photos found in database.</p>
        </div>
    <?php else: ?>
        <p>Found <?= count($users) ?> user(s) with photo data:</p>
        
        <?php foreach ($users as $user): ?>
        <div class="user-section">
            <h2><?= htmlspecialchars($user['name']) ?> (ID: <?= $user['id'] ?>)</h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>ID Type:</strong> <?= htmlspecialchars($user['gov_id_type'] ?? 'Not set') ?></p>
            
            <!-- Government ID Photo -->
            <div class="photo-info">
                <h3>Government ID Photo</h3>
                <p><strong>Database Path:</strong> <?= htmlspecialchars($user['gov_id_photo'] ?? 'NULL') ?></p>
                
                <?php if (!empty($user['gov_id_photo'])): ?>
                    <?php 
                    $fullPath = $baseDir . '/' . $user['gov_id_photo'];
                    $exists = file_exists($fullPath);
                    ?>
                    <p class="<?= $exists ? 'exists' : 'missing' ?>">
                        File Exists: <?= $exists ? 'YES ✓' : 'NO ✗' ?>
                    </p>
                    
                    <?php if ($exists): ?>
                        <p>File Size: <?= filesize($fullPath) ?> bytes</p>
                        <img src="/<?= $user['gov_id_photo'] ?>" alt="ID Card">
                    <?php else: ?>
                        <p style="color: red;">Expected path: <?= htmlspecialchars($fullPath) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="missing">No photo path in database</p>
                <?php endif; ?>
            </div>
            
            <!-- Face Photo -->
            <div class="photo-info">
                <h3>Face Photo</h3>
                <p><strong>Database Path:</strong> <?= htmlspecialchars($user['face_photo'] ?? 'NULL') ?></p>
                
                <?php if (!empty($user['face_photo'])): ?>
                    <?php 
                    $fullPath = $baseDir . '/' . $user['face_photo'];
                    $exists = file_exists($fullPath);
                    ?>
                    <p class="<?= $exists ? 'exists' : 'missing' ?>">
                        File Exists: <?= $exists ? 'YES ✓' : 'NO ✗' ?>
                    </p>
                    
                    <?php if ($exists): ?>
                        <p>File Size: <?= filesize($fullPath) ?> bytes</p>
                        <img src="/<?= $user['face_photo'] ?>" alt="Face Photo">
                    <?php else: ?>
                        <p style="color: red;">Expected path: <?= htmlspecialchars($fullPath) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="missing">No photo path in database</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
        <h3>✓ Next Steps:</h3>
        <ul>
            <li>Check if files exist and display correctly above</li>
            <li>If files don't exist, the registration photo upload failed</li>
            <li>If paths are wrong, check the storage/avatars/ directory</li>
            <li><a href="/">Go back home</a></li>
        </ul>
    </div>
</body>
</html>
