<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>CHECKING DATABASE NOW</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='15' style='width: 100%; font-size: 14px;'>";
echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Name</th><th>Gov ID Photo Path</th><th>Face Photo Path</th><th>Gov ID File Exists</th><th>Face File Exists</th></tr>";

foreach ($users as $user) {
    $govPath = $user['gov_id_photo'];
    $facePath = $user['face_photo'];
    
    $govExists = false;
    $faceExists = false;
    
    if ($govPath) {
        $fullPath = __DIR__ . '/' . $govPath;
        $govExists = file_exists($fullPath);
    }
    
    if ($facePath) {
        $fullPath = __DIR__ . '/' . $facePath;
        $faceExists = file_exists($fullPath);
    }
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><code style='font-size:11px;'>" . ($govPath ?: '[NULL - EMPTY]') . "</code></td>";
    echo "<td><code style='font-size:11px;'>" . ($facePath ?: '[NULL - EMPTY]') . "</code></td>";
    echo "<td>" . ($govExists ? "<span style='color:green; font-weight:bold;'>✓ YES</span>" : ($govPath ? "<span style='color:red;'>✗ NO</span>" : "<span style='color:orange;'>NULL</span>")) . "</td>";
    echo "<td>" . ($faceExists ? "<span style='color:green; font-weight:bold;'>✓ YES</span>" : ($facePath ? "<span style='color:red;'>✗ NO</span>" : "<span style='color:orange;'>NULL</span>")) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Files in Storage</h2>";
$files = scandir(__DIR__ . '/storage/avatars');
$photoFiles = array_filter($files, function($f) { return preg_match('/^user_/', $f); });

echo "<p>Total files: " . count($photoFiles) . "</p>";
echo "<ul style='columns: 2;'>";
foreach (array_slice($photoFiles, 0, 20) as $f) {
    echo "<li><code style='font-size:11px;'>$f</code></li>";
}
echo "</ul>";

?>
