<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>FORCE UPDATE ALL PHOTO PATHS</h2>";

// Get all photo files
$files = array_filter(scandir(__DIR__ . '/storage/avatars'), function($f) {
    return preg_match('/^user_\d+_/', $f);
});

echo "<p>Found " . count($files) . " files in storage/avatars</p>";

// Parse and organize files by user
$userPhotos = [];
foreach ($files as $file) {
    if (preg_match('/^user_(\d+)_(govid|face)_\d+\./', $file, $m)) {
        $userId = intval($m[1]);
        $type = $m[2];
        
        if (!isset($userPhotos[$userId])) {
            $userPhotos[$userId] = [];
        }
        
        $userPhotos[$userId][$type] = $file;
    }
}

echo "<p>Organized into " . count($userPhotos) . " users</p>";

// Update database
$totalUpdated = 0;
foreach ($userPhotos as $userId => $photos) {
    echo "<p>User $userId:</p>";
    
    if (isset($photos['govid'])) {
        $path = 'storage/avatars/' . $photos['govid'];
        $stmt = $pdo->prepare("UPDATE users SET gov_id_photo = ? WHERE id = ?");
        $stmt->execute([$path, $userId]);
        echo "  ✓ gov_id_photo = <code>$path</code><br>";
        $totalUpdated++;
    }
    
    if (isset($photos['face'])) {
        $path = 'storage/avatars/' . $photos['face'];
        $stmt = $pdo->prepare("UPDATE users SET face_photo = ? WHERE id = ?");
        $stmt->execute([$path, $userId]);
        echo "  ✓ face_photo = <code>$path</code><br>";
        $totalUpdated++;
    }
}

echo "<p style='color: green; font-weight: bold; font-size: 16px;'>✓ UPDATED " . $totalUpdated . " PHOTO PATHS</p>";

// Verify
echo "<h2>VERIFICATION</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$allGood = true;
foreach ($users as $user) {
    $govOk = false;
    $faceOk = false;
    
    if ($user['gov_id_photo']) {
        $full = __DIR__ . '/' . $user['gov_id_photo'];
        $govOk = file_exists($full);
    }
    
    if ($user['face_photo']) {
        $full = __DIR__ . '/' . $user['face_photo'];
        $faceOk = file_exists($full);
    }
    
    if (!$govOk || !$faceOk) {
        $allGood = false;
    }
    
    echo "User {$user['id']} ({$user['name']}): ";
    echo ($govOk ? "<span style='color:green'>✓ Gov ID</span>" : "<span style='color:red'>✗ Gov ID</span>");
    echo " | ";
    echo ($faceOk ? "<span style='color:green'>✓ Face</span>" : "<span style='color:red'>✗ Face</span>");
    echo "<br>";
}

echo "<p style='font-size: 16px; color: " . ($allGood ? 'green' : 'red') . "; font-weight: bold;'>";
echo $allGood ? "✓ ALL PHOTOS ARE SET AND FILES EXIST!" : "✗ Some photos are missing";
echo "</p>";

echo "<p><a href='/Court-Reservation/admin/users' style='font-size: 16px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>GO TO ADMIN USERS</a></p>";

?>
