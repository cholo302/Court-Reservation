<?php
require_once 'bootstrap/app.php';

$db = new Database();

echo "<h2>Final Verification</h2>";

$users = $db->query("
    SELECT id, name, email, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Gov ID Photo DB</th><th>Gov ID Exists</th><th>Face Photo DB</th><th>Face Exists</th><th>Admin Display</th></tr>";

foreach ($users as $user) {
    $govIdPath = $user['gov_id_photo'] ?? '';
    $facePath = $user['face_photo'] ?? '';
    
    $govIdExists = $govIdPath && file_exists(__DIR__ . '/' . $govIdPath) ? '✓' : '✗';
    $faceExists = $facePath && file_exists(__DIR__ . '/' . $facePath) ? '✓' : '✗';
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td><code>" . ($govIdPath ?: '[NULL]') . "</code></td>";
    echo "<td>$govIdExists</td>";
    echo "<td><code>" . ($facePath ?: '[NULL]') . "</code></td>";
    echo "<td>$faceExists</td>";
    echo "<td>";
    
    if ($govIdPath && file_exists(__DIR__ . '/' . $govIdPath)) {
        echo "<img src='/" . htmlspecialchars($govIdPath) . "' style='height: 40px; width: auto; margin-right: 5px;' alt='ID'>";
    }
    if ($facePath && file_exists(__DIR__ . '/' . $facePath)) {
        echo "<img src='/" . htmlspecialchars($facePath) . "' style='height: 40px; width: auto; border-radius: 50%;' alt='Face'>";
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

?>
