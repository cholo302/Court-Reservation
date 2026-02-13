<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

$user = $pdo->query("
    SELECT gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['gov_id_photo']) {
    echo "No user with gov_id_photo found";
    exit;
}

$photo = $user['gov_id_photo'];
$fullPath = __DIR__ . '/' . $photo;

echo "<h2>Testing Modal Image Display</h2>";
echo "<p>Database path: <code>$photo</code></p>";
echo "<p>Full path: <code>$fullPath</code></p>";
echo "<p>File exists: " . (file_exists($fullPath) ? "✓ YES" : "✗ NO") . "</p>";

echo "<h3>Testing different URL formats:</h3>";

// Test 1: With leading slash
echo "<p><strong>Test 1: With leading slash</strong></p>";
echo "<code>/$photo</code><br>";
echo "<img src='/$photo' style='width: 200px; border: 2px solid red;' alt='Test 1'>";

// Test 2: Without leading slash
echo "<p><strong>Test 2: Without leading slash</strong></p>";
echo "<code>$photo</code><br>";
echo "<img src='$photo' style='width: 200px; border: 2px solid green;' alt='Test 2'>";

// Test 3: Full path from root
echo "<p><strong>Test 3: Full path from localhost</strong></p>";
echo "<code>/Court-Reservation/$photo</code><br>";
echo "<img src='/Court-Reservation/$photo' style='width: 200px; border: 2px solid blue;' alt='Test 3'>";

echo "<h3>HTML for Modal</h3>";
echo "<pre>";
echo "document.getElementById('photoImage').src = '/" . htmlspecialchars($photo) . "';";
echo "</pre>";

?>
