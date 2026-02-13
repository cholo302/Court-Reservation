<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>FIXING PHOTO PATHS IN DATABASE</h2>";

// Get all files
$files = scandir(__DIR__ . '/storage/avatars');
$photoFiles = array_filter($files, function($f) {
    return preg_match('/^user_/', $f);
});

echo "<p>Found " . count($photoFiles) . " photo files</p>";

// Group by user and type
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

echo "<h3>Updating Database</h3>";

$updated = 0;
foreach ($userPhotos as $userId => $photos) {
    // Check if user exists
    $userCheck = $pdo->query("SELECT id FROM users WHERE id = " . $userId)->fetch();
    if (!$userCheck) continue;
    
    echo "<p>User $userId:</p>";
    
    if (isset($photos['govid'])) {
        $pdo->prepare("UPDATE users SET gov_id_photo = ? WHERE id = ?")->execute([$photos['govid'], $userId]);
        echo "  ✓ gov_id_photo: " . $photos['govid'] . "<br>";
        $updated++;
    }
    
    if (isset($photos['face'])) {
        $pdo->prepare("UPDATE users SET face_photo = ? WHERE id = ?")->execute([$photos['face'], $userId]);
        echo "  ✓ face_photo: " . $photos['face'] . "<br>";
        $updated++;
    }
}

echo "<p style='color: green; font-weight: bold;'>✓ Updated " . $updated . " photo paths</p>";

echo "<h3>Verification</h3>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo</th><th>File Exists</th><th>Face Photo</th><th>File Exists</th></tr>";

foreach ($users as $user) {
    $govExists = $user['gov_id_photo'] && file_exists(__DIR__ . '/' . $user['gov_id_photo']);
    $faceExists = $user['face_photo'] && file_exists(__DIR__ . '/' . $user['face_photo']);
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td>" . ($user['gov_id_photo'] ? '<code>' . htmlspecialchars($user['gov_id_photo']) . '</code>' : '[NULL]') . "</td>";
    echo "<td>" . ($govExists ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "</td>";
    echo "<td>" . ($user['face_photo'] ? '<code>' . htmlspecialchars($user['face_photo']) . '</code>' : '[NULL]') . "</td>";
    echo "<td>" . ($faceExists ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='/Court-Reservation/admin/users'>Go to Admin Users</a></p>";

?>
