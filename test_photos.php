<?php
require_once 'bootstrap/app.php';

$db = new Database();

echo "<h2>Users with Photo Data</h2>";

$users = $db->query('
    SELECT 
        id,
        name,
        email,
        gov_id_type,
        gov_id_photo,
        face_photo,
        created_at
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY created_at DESC
')->fetchAll();

echo "<p>Found " . count($users) . " users with photos</p>";

foreach ($users as $user) {
    echo "<hr>";
    echo "<h3>User #{$user['id']}: {$user['name']}</h3>";
    echo "<p><strong>Email:</strong> {$user['email']}</p>";
    echo "<p><strong>Gov ID Type:</strong> {$user['gov_id_type']}</p>";
    echo "<p><strong>Created:</strong> {$user['created_at']}</p>";
    
    if ($user['gov_id_photo']) {
        $fullPath = __DIR__ . '/' . $user['gov_id_photo'];
        $exists = file_exists($fullPath) ? "✓ EXISTS" : "✗ MISSING";
        echo "<p><strong>Gov ID Photo:</strong> <code>{$user['gov_id_photo']}</code> ($exists)</p>";
        if (file_exists($fullPath)) {
            echo "<p><img src='/{$user['gov_id_photo']}' style='max-width: 200px; border: 1px solid #ccc;'></p>";
        }
    }
    
    if ($user['face_photo']) {
        $fullPath = __DIR__ . '/' . $user['face_photo'];
        $exists = file_exists($fullPath) ? "✓ EXISTS" : "✗ MISSING";
        echo "<p><strong>Face Photo:</strong> <code>{$user['face_photo']}</code> ($exists)</p>";
        if (file_exists($fullPath)) {
            echo "<p><img src='/{$user['face_photo']}' style='max-width: 200px; border: 1px solid #ccc; border-radius: 50%;'></p>";
        }
    }
}
?>
