<?php
require_once 'bootstrap/app.php';

$db = new Database();

echo "<h2>Photo Path Debugging</h2>";

// Get all users with photos
$users = $db->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC
    LIMIT 10
")->fetchAll();

echo "<p>Found " . count($users) . " users with photos</p>\n\n";

foreach ($users as $user) {
    echo "<hr>\n";
    echo "<h3>User #{$user['id']}: {$user['name']}</h3>\n";
    
    if ($user['gov_id_photo']) {
        $path = $user['gov_id_photo'];
        $fullPath = __DIR__ . '/' . $path;
        $fileExists = file_exists($fullPath);
        
        echo "<p><strong>Gov ID Photo Path (DB):</strong> <code>$path</code></p>\n";
        echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>\n";
        echo "<p><strong>File Exists:</strong> " . ($fileExists ? "<span style='color:green'>✓ YES</span>" : "<span style='color:red'>✗ NO</span>") . "</p>\n";
        
        if ($fileExists) {
            $size = filesize($fullPath);
            echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>\n";
            echo "<p><strong>Preview:</strong></p>\n";
            echo "<img src='/$path' alt='Gov ID Photo' style='max-width: 200px; border: 1px solid #ccc;'>\n";
        }
    }
    
    if ($user['face_photo']) {
        $path = $user['face_photo'];
        $fullPath = __DIR__ . '/' . $path;
        $fileExists = file_exists($fullPath);
        
        echo "<p><strong>Face Photo Path (DB):</strong> <code>$path</code></p>\n";
        echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>\n";
        echo "<p><strong>File Exists:</strong> " . ($fileExists ? "<span style='color:green'>✓ YES</span>" : "<span style='color:red'>✗ NO</span>") . "</p>\n";
        
        if ($fileExists) {
            $size = filesize($fullPath);
            echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>\n";
            echo "<p><strong>Preview:</strong></p>\n";
            echo "<img src='/$path' alt='Face Photo' style='max-width: 200px; border: 1px solid #ccc; border-radius: 50%;'>\n";
        }
    }
}
?>
