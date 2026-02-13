<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>Photo Path Status Check</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    ORDER BY id DESC 
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo (DB)</th><th>Face Photo (DB)</th><th>Status</th></tr>";

$nullCount = 0;
foreach ($users as $user) {
    $govNull = empty($user['gov_id_photo']);
    $faceNull = empty($user['face_photo']);
    
    $status = "✓ Both OK";
    if ($govNull && $faceNull) {
        $status = "<span style='color:red'>✗ Both NULL</span>";
        $nullCount++;
    } elseif ($govNull) {
        $status = "<span style='color:orange'>⚠ Gov ID NULL</span>";
        $nullCount++;
    } elseif ($faceNull) {
        $status = "<span style='color:orange'>⚠ Face NULL</span>";
        $nullCount++;
    }
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><code>" . ($user['gov_id_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td><code>" . ($user['face_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p>Users with NULL photo paths: <strong style='color:red'>$nullCount</strong></p>";

echo "<h2>Auto-Fixing Photo Paths</h2>";

// Get all files in storage/avatars
$files = scandir(__DIR__ . '/storage/avatars');
$photoFiles = array_filter($files, function($f) {
    return preg_match('/^user_/', $f);
});

echo "<p>Found " . count($photoFiles) . " photo files in storage</p>";

// Group files by user
$userPhotos = [];
foreach ($photoFiles as $file) {
    if (preg_match('/^user_(\d+)_(govid|face)_/', $file, $m)) {
        $userId = $m[1];
        $photoType = $m[2];
        
        if (!isset($userPhotos[$userId])) {
            $userPhotos[$userId] = [];
        }
        
        $userPhotos[$userId][$photoType] = 'storage/avatars/' . $file;
    }
}

// Update database
$updated = 0;
foreach ($userPhotos as $userId => $photos) {
    if (isset($photos['govid'])) {
        $pdo->prepare("UPDATE users SET gov_id_photo = ? WHERE id = ?")->execute([$photos['govid'], $userId]);
        $updated++;
    }
    if (isset($photos['face'])) {
        $pdo->prepare("UPDATE users SET face_photo = ? WHERE id = ?")->execute([$photos['face'], $userId]);
        $updated++;
    }
}

echo "<p style='color: green; font-weight: bold;'>✓ Updated " . $updated . " photo paths</p>";

echo "<h2>Verification After Fix</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo</th><th>Face Photo</th><th>Both Exist</th></tr>";

foreach ($users as $user) {
    $govExists = $user['gov_id_photo'] && file_exists(__DIR__ . '/' . $user['gov_id_photo']);
    $faceExists = $user['face_photo'] && file_exists(__DIR__ . '/' . $user['face_photo']);
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td>" . ($govExists ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "</td>";
    echo "<td>" . ($faceExists ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "</td>";
    echo "<td>" . (($govExists && $faceExists) ? "<span style='color:green'>✓ YES</span>" : "<span style='color:red'>✗ NO</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";

?>
