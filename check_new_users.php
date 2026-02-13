<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>NEW USER REGISTRATIONS - PHOTO CHECK</h2>";

// Get the most recent users
$users = $pdo->query("
    SELECT id, name, email, gov_id_photo, face_photo, gov_id_type, created_at
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Name</th>";
echo "<th>Email</th>";
echo "<th>Gov ID Type</th>";
echo "<th>Gov ID Photo (DB)</th>";
echo "<th>Face Photo (DB)</th>";
echo "<th>Created</th>";
echo "<th>Gov ID File Exists</th>";
echo "<th>Face File Exists</th>";
echo "</tr>";

foreach ($users as $user) {
    $govIdExists = false;
    $faceExists = false;
    
    if ($user['gov_id_photo']) {
        $govIdExists = file_exists(__DIR__ . '/' . $user['gov_id_photo']);
    }
    
    if ($user['face_photo']) {
        $faceExists = file_exists(__DIR__ . '/' . $user['face_photo']);
    }
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['gov_id_type']}</td>";
    echo "<td><code>" . ($user['gov_id_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td><code>" . ($user['face_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td>" . date('M d H:i', strtotime($user['created_at'])) . "</td>";
    echo "<td>" . ($govIdExists ? "<span style='color:green'>✓</span>" : ($user['gov_id_photo'] ? "<span style='color:red'>✗ MISSING</span>" : "<span style='color:orange'>NULL</span>")) . "</td>";
    echo "<td>" . ($faceExists ? "<span style='color:green'>✓</span>" : ($user['face_photo'] ? "<span style='color:red'>✗ MISSING</span>" : "<span style='color:orange'>NULL</span>")) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Files in storage/avatars/</h2>";
$files = array_filter(scandir(__DIR__ . '/storage/avatars'), function($f) {
    return $f !== '.' && $f !== '..' && preg_match('/^user_/', $f);
});

echo "<p>Total: " . count($files) . " files</p>";
echo "<ul>";
foreach (array_slice($files, 0, 30) as $f) {
    $path = __DIR__ . '/storage/avatars/' . $f;
    $size = filesize($path);
    echo "<li>$f (" . number_format($size) . " bytes)</li>";
}
echo "</ul>";

?>
