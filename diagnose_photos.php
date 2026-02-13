<?php
// Test the registration flow manually

$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

// Get the last registered user
$user = $pdo->query("
    SELECT * FROM users ORDER BY created_at DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

echo "<h2>Last Registered User Analysis</h2>";
echo "<p><strong>ID:</strong> {$user['id']}</p>";
echo "<p><strong>Name:</strong> {$user['name']}</p>";
echo "<p><strong>Email:</strong> {$user['email']}</p>";
echo "<p><strong>Created:</strong> {$user['created_at']}</p>";

echo "<h3>Photo Columns</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Column</th><th>Value</th><th>Status</th></tr>";

$govIdPhoto = $user['gov_id_photo'] ?? null;
$facePhoto = $user['face_photo'] ?? null;

echo "<tr>";
echo "<td>gov_id_photo</td>";
echo "<td><code>" . ($govIdPhoto ?: '[NULL]') . "</code></td>";
if ($govIdPhoto) {
    $exists = file_exists(__DIR__ . '/' . $govIdPhoto);
    echo "<td>" . ($exists ? "<span style='color:green'>✓ File exists</span>" : "<span style='color:red'>✗ File missing</span>") . "</td>";
} else {
    echo "<td><span style='color:red'>✗ Database value is NULL</span></td>";
}
echo "</tr>";

echo "<tr>";
echo "<td>face_photo</td>";
echo "<td><code>" . ($facePhoto ?: '[NULL]') . "</code></td>";
if ($facePhoto) {
    $exists = file_exists(__DIR__ . '/' . $facePhoto);
    echo "<td>" . ($exists ? "<span style='color:green'>✓ File exists</span>" : "<span style='color:red'>✗ File missing</span>") . "</td>";
} else {
    echo "<td><span style='color:red'>✗ Database value is NULL</span></td>";
}
echo "</tr>";

echo "</table>";

// Check if files exist in storage for this user
echo "<h3>Files in storage/avatars for this user</h3>";
$files = array_filter(scandir(__DIR__ . '/storage/avatars'), function($f) use ($user) {
    return strpos($f, 'user_' . $user['id'] . '_') === 0;
});

if (count($files) > 0) {
    echo "<p><strong style='color:green'>✓ Found " . count($files) . " files:</strong></p>";
    echo "<ul>";
    foreach ($files as $f) {
        $path = __DIR__ . '/storage/avatars/' . $f;
        $size = filesize($path);
        echo "<li>$f (" . number_format($size) . " bytes)</li>";
    }
    echo "</ul>";
} else {
    echo "<p><strong style='color:orange'>⚠ No files found for user {$user['id']}</strong></p>";
}

echo "<h3>Problem Diagnosis</h3>";
if (!$govIdPhoto && !$facePhoto) {
    echo "<p style='color:red;'><strong>❌ ISSUE: Database photo paths are NULL</strong></p>";
    echo "<p>This means the UPDATE query in the registration didn't work. Possible causes:</p>";
    echo "<ul>";
    echo "<li>PhotoValidator::save() returned wrong path format</li>";
    echo "<li>Database UPDATE query failed silently</li>";
    echo "<li>Photo files weren't saved to disk</li>";
    echo "</ul>";
    
    // Try to fix it
    echo "<h4>Auto-Fix Attempt</h4>";
    if (count($files) > 0) {
        echo "<p>Attempting to update database with existing files...</p>";
        
        $updates = 0;
        foreach ($files as $f) {
            if (preg_match('/^user_\d+_(govid|face)_/', $f, $m)) {
                $photoType = $m[1];
                $path = 'storage/avatars/' . $f;
                
                if ($photoType === 'govid') {
                    $pdo->prepare("UPDATE users SET gov_id_photo = ? WHERE id = ?")->execute([$path, $user['id']]);
                    $updates++;
                    echo "<p>✓ Updated gov_id_photo to: <code>$path</code></p>";
                } elseif ($photoType === 'face') {
                    $pdo->prepare("UPDATE users SET face_photo = ? WHERE id = ?")->execute([$path, $user['id']]);
                    $updates++;
                    echo "<p>✓ Updated face_photo to: <code>$path</code></p>";
                }
            }
        }
        
        echo "<p style='color:green;'><strong>✓ Fixed " . $updates . " photo paths</strong></p>";
    }
} else if ($govIdPhoto && $facePhoto) {
    echo "<p style='color:green;'><strong>✓ OK: Both photo paths are in database</strong></p>";
} else {
    echo "<p style='color:orange;'><strong>⚠ WARNING: Only one photo path is set</strong></p>";
}

?>
