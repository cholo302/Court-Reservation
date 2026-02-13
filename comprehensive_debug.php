<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>COMPREHENSIVE PHOTO DEBUG</h2>";

// Get the first user with photos
$user = $pdo->query("
    SELECT id, name, email, gov_id_photo, face_photo, gov_id_type
    FROM users 
    WHERE (gov_id_photo IS NOT NULL AND gov_id_photo != '') 
       OR (face_photo IS NOT NULL AND face_photo != '')
    ORDER BY id DESC 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color: red;'>No users with photos found!</p>";
    exit;
}

echo "<h3>User: {$user['name']} (ID: {$user['id']})</h3>";
echo "<p>Email: {$user['email']}</p>";
echo "<p>Gov ID Type: {$user['gov_id_type']}</p>";

// Check Gov ID Photo
echo "<h4>Government ID Photo</h4>";
$govPath = $user['gov_id_photo'];
echo "<p><strong>Database Value:</strong> <code>" . htmlspecialchars($govPath) . "</code></p>";

if ($govPath) {
    $fullPath = __DIR__ . '/' . $govPath;
    $exists = file_exists($fullPath);
    $size = $exists ? filesize($fullPath) : 0;
    
    echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>";
    echo "<p><strong>File Exists:</strong> " . ($exists ? "<span style='color: green;'>✓ YES</span>" : "<span style='color: red;'>✗ NO</span>") . "</p>";
    echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>";
    
    echo "<p><strong>HTML Tag to Use:</strong></p>";
    echo "<code>&lt;img src='/" . htmlspecialchars($govPath) . "' &gt;</code>";
    
    echo "<p><strong>Display Tests:</strong></p>";
    echo "<p>Small (w-10 h-10 = 40x40px):</p>";
    echo "<img src='/" . htmlspecialchars($govPath) . "' style='width: 40px; height: 40px; border: 2px solid blue;' alt='Gov ID Small'>";
    
    echo "<p>Medium (200x200px):</p>";
    echo "<img src='/" . htmlspecialchars($govPath) . "' style='width: 200px; height: 200px; border: 2px solid blue;' alt='Gov ID Medium'>";
} else {
    echo "<p style='color: orange;'>⚠ NULL or Empty in database</p>";
}

// Check Face Photo
echo "<h4>Face Photo</h4>";
$facePath = $user['face_photo'];
echo "<p><strong>Database Value:</strong> <code>" . htmlspecialchars($facePath) . "</code></p>";

if ($facePath) {
    $fullPath = __DIR__ . '/' . $facePath;
    $exists = file_exists($fullPath);
    $size = $exists ? filesize($fullPath) : 0;
    
    echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>";
    echo "<p><strong>File Exists:</strong> " . ($exists ? "<span style='color: green;'>✓ YES</span>" : "<span style='color: red;'>✗ NO</span>") . "</p>";
    echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>";
    
    echo "<p><strong>HTML Tag to Use:</strong></p>";
    echo "<code>&lt;img src='/" . htmlspecialchars($facePath) . "' &gt;</code>";
    
    echo "<p><strong>Display Tests:</strong></p>";
    echo "<p>Small (w-10 h-10 = 40x40px):</p>";
    echo "<img src='/" . htmlspecialchars($facePath) . "' style='width: 40px; height: 40px; border-radius: 50%; border: 2px solid green;' alt='Face Small'>";
    
    echo "<p>Medium (200x200px):</p>";
    echo "<img src='/" . htmlspecialchars($facePath) . "' style='width: 200px; height: 200px; border-radius: 50%; border: 2px solid green;' alt='Face Medium'>";
} else {
    echo "<p style='color: orange;'>⚠ NULL or Empty in database</p>";
}

// List all files
echo "<h3>All Photo Files</h3>";
$files = array_filter(scandir(__DIR__ . '/storage/avatars'), function($f) {
    return preg_match('/^user_/', $f);
});

echo "<p>Found " . count($files) . " files:</p>";
echo "<ul>";
foreach (array_slice($files, 0, 20) as $f) {
    $path = __DIR__ . '/storage/avatars/' . $f;
    $size = filesize($path);
    echo "<li>$f (" . number_format($size) . " bytes)</li>";
}
echo "</ul>";

?>
