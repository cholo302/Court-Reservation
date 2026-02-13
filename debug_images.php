<?php
// Check what's being output in the HTML for the photos

$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

$users = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL
    ORDER BY id DESC 
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Database Photo Paths</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>User</th><th>Gov ID Photo Path</th><th>Face Photo Path</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']} - {$user['name']}</td>";
    echo "<td><code>" . htmlspecialchars($user['gov_id_photo'] ?: '[NULL]') . "</code></td>";
    echo "<td><code>" . htmlspecialchars($user['face_photo'] ?: '[NULL]') . "</code></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Testing Image Display</h2>";

foreach ($users as $user) {
    echo "<h3>{$user['name']}</h3>";
    
    if ($user['gov_id_photo']) {
        $path = $user['gov_id_photo'];
        $fullPath = __DIR__ . '/' . $path;
        $exists = file_exists($fullPath);
        
        echo "<p><strong>Gov ID Photo:</strong> $path</p>";
        echo "<p>File exists: " . ($exists ? "✓ YES" : "✗ NO") . "</p>";
        
        if ($exists) {
            echo "<p>Image tag: &lt;img src='/" . htmlspecialchars($path) . "'&gt;</p>";
            echo "<p><strong>Preview:</strong></p>";
            echo "<img src='/" . htmlspecialchars($path) . "' style='height: 80px; border: 2px solid red;' alt='Gov ID'>";
        }
    }
    
    if ($user['face_photo']) {
        $path = $user['face_photo'];
        $fullPath = __DIR__ . '/' . $path;
        $exists = file_exists($fullPath);
        
        echo "<p><strong>Face Photo:</strong> $path</p>";
        echo "<p>File exists: " . ($exists ? "✓ YES" : "✗ NO") . "</p>";
        
        if ($exists) {
            echo "<p>Image tag: &lt;img src='/" . htmlspecialchars($path) . "'&gt;</p>";
            echo "<p><strong>Preview:</strong></p>";
            echo "<img src='/" . htmlspecialchars($path) . "' style='height: 80px; border-radius: 50%; border: 2px solid green;' alt='Face'>";
        }
    }
    
    echo "<hr>";
}

?>
