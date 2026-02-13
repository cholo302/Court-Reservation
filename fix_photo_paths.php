<?php
// Direct database fix without using bootstrap

$dbFile = __DIR__ . '/storage/database.sqlite';

if (!file_exists($dbFile)) {
    echo "Database file not found at: " . $dbFile;
    exit;
}

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h2>Step 1: Checking Database Paths</h2>";

$users = $pdo->query("
    SELECT id, name, email, gov_id_photo, face_photo 
    FROM users 
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo (DB)</th><th>Face Photo (DB)</th></tr>";
foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><code>" . ($user['gov_id_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td><code>" . ($user['face_photo'] ?: '[NULL]') . "</code></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Step 2: Checking Files in Storage</h2>";

$avatarDir = __DIR__ . '/storage/avatars';
$files = scandir($avatarDir);
$photoFiles = array_filter($files, function($f) {
    return preg_match('/^user_\d+_/', $f);
});

echo "<p>Found " . count($photoFiles) . " photo files</p>";
echo "<ul>";
foreach (array_slice($photoFiles, 0, 10) as $file) {
    echo "<li>$file</li>";
}
echo "</ul>";

echo "<h2>Step 3: Fixing Database Paths</h2>";

// Parse files and update database
$userPhotos = [];
foreach ($photoFiles as $file) {
    if (preg_match('/^user_(\d+)_(govid|face)_\d+\.(.+)$/', $file, $m)) {
        $userId = $m[1];
        $photoType = $m[2];
        
        if (!isset($userPhotos[$userId])) {
            $userPhotos[$userId] = [];
        }
        
        $userPhotos[$userId][$photoType] = 'storage/avatars/' . $file;
    }
}

$updated = 0;
foreach ($userPhotos as $userId => $photos) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
        if (isset($photos['govid'])) {
            $stmt = $pdo->prepare("UPDATE users SET gov_id_photo = ? WHERE id = ?");
            $stmt->execute([$photos['govid'], $userId]);
            $updated++;
        }
        if (isset($photos['face'])) {
            $stmt = $pdo->prepare("UPDATE users SET face_photo = ? WHERE id = ?");
            $stmt->execute([$photos['face'], $userId]);
            $updated++;
        }
    }
}

echo "<p>Updated $updated photo paths in database</p>";

echo "<h2>Step 4: Verification</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo</th><th>Preview ID</th><th>Face Photo</th><th>Preview Face</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><small><code>" . ($user['gov_id_photo'] ?: '[NULL]') . "</code></small></td>";
    
    if ($user['gov_id_photo'] && file_exists(__DIR__ . '/' . $user['gov_id_photo'])) {
        echo "<td><img src='/{$user['gov_id_photo']}' style='height: 50px; border: 1px solid #ccc;'></td>";
    } else {
        echo "<td>-</td>";
    }
    
    echo "<td><small><code>" . ($user['face_photo'] ?: '[NULL]') . "</code></small></td>";
    
    if ($user['face_photo'] && file_exists(__DIR__ . '/' . $user['face_photo'])) {
        echo "<td><img src='/{$user['face_photo']}' style='height: 50px; border-radius: 50%; border: 1px solid #ccc;'></td>";
    } else {
        echo "<td>-</td>";
    }
    
    echo "</tr>";
}
echo "</table>";

echo "<h2>Done!</h2>";
echo "<p>Go to <a href='/Court-Reservation/admin/users'>/admin/users</a> to see the photos in the verification column</p>";

?>
