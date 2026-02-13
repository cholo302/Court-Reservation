<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>Database vs File System Check</h2>";

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE id IN (27, 28, 29)
    ORDER BY id
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='width: 100%;'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Name</th>";
echo "<th>Gov ID Photo (DB)</th>";
echo "<th>File Exists</th>";
echo "<th>Face Photo (DB)</th>";
echo "<th>File Exists</th>";
echo "</tr>";

foreach ($users as $user) {
    $govPath = $user['gov_id_photo'];
    $facePath = $user['face_photo'];
    
    $govExists = $govPath && file_exists(__DIR__ . '/' . $govPath);
    $faceExists = $facePath && file_exists(__DIR__ . '/' . $facePath);
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><code>" . ($govPath ?: '[NULL]') . "</code></td>";
    echo "<td>" . ($govExists ? "✓" : "✗") . "</td>";
    echo "<td><code>" . ($facePath ?: '[NULL]') . "</code></td>";
    echo "<td>" . ($faceExists ? "✓" : "✗") . "</td>";
    echo "</tr>";
}
echo "</table>";

// Now check actual files
echo "<h2>Files in storage/avatars</h2>";
$files = scandir(__DIR__ . '/storage/avatars');
$photoFiles = array_filter($files, function($f) {
    return preg_match('/^user_\d+_/', $f);
});

echo "<ul>";
foreach ($photoFiles as $f) {
    echo "<li>$f</li>";
}
echo "</ul>";

?>
