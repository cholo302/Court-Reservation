<?php
$dbFile = __DIR__ . '/storage/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);

echo "<h2>TESTING IMAGE URLS</h2>";

$user = $pdo->query("
    SELECT id, name, gov_id_photo, face_photo 
    FROM users 
    WHERE gov_id_photo IS NOT NULL AND face_photo IS NOT NULL
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "No user with both photos found!";
    exit;
}

$govPath = $user['gov_id_photo'];
$facePath = $user['face_photo'];

echo "<p><strong>User: {$user['name']} (ID: {$user['id']})</strong></p>";

echo "<h3>Gov ID Photo</h3>";
echo "<p><strong>DB Path:</strong> <code>$govPath</code></p>";

$fullPath = __DIR__ . '/' . $govPath;
$exists = file_exists($fullPath);
echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>";
echo "<p><strong>File Exists:</strong> " . ($exists ? "✓ YES" : "✗ NO") . "</p>";

if ($exists) {
    $size = filesize($fullPath);
    echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>";
    
    echo "<h4>Testing Different URL Formats</h4>";
    
    // Test 1: With leading slash
    $url1 = "/" . $govPath;
    echo "<p><strong>URL 1 (with leading slash):</strong> <code>$url1</code></p>";
    echo "<img src='$url1' style='width: 150px; height: 150px; border: 2px solid red; margin: 10px; object-fit: cover;' alt='Test 1'>";
    
    // Test 2: Without leading slash  
    $url2 = $govPath;
    echo "<p><strong>URL 2 (without leading slash):</strong> <code>$url2</code></p>";
    echo "<img src='$url2' style='width: 150px; height: 150px; border: 2px solid green; margin: 10px; object-fit: cover;' alt='Test 2'>";
    
    // Test 3: With full path from localhost
    $url3 = "/Court-Reservation/" . $govPath;
    echo "<p><strong>URL 3 (full path):</strong> <code>$url3</code></p>";
    echo "<img src='$url3' style='width: 150px; height: 150px; border: 2px solid blue; margin: 10px; object-fit: cover;' alt='Test 3'>";
}

echo "<h3>Face Photo</h3>";
echo "<p><strong>DB Path:</strong> <code>$facePath</code></p>";

$fullPath = __DIR__ . '/' . $facePath;
$exists = file_exists($fullPath);
echo "<p><strong>Full Path:</strong> <code>$fullPath</code></p>";
echo "<p><strong>File Exists:</strong> " . ($exists ? "✓ YES" : "✗ NO") . "</p>";

if ($exists) {
    $size = filesize($fullPath);
    echo "<p><strong>File Size:</strong> " . number_format($size) . " bytes</p>";
    
    echo "<h4>Testing Different URL Formats</h4>";
    
    // Test 1: With leading slash
    $url1 = "/" . $facePath;
    echo "<p><strong>URL 1 (with leading slash):</strong> <code>$url1</code></p>";
    echo "<img src='$url1' style='width: 150px; height: 150px; border: 2px solid red; margin: 10px; border-radius: 50%; object-fit: cover;' alt='Test 1'>";
    
    // Test 2: Without leading slash  
    $url2 = $facePath;
    echo "<p><strong>URL 2 (without leading slash):</strong> <code>$url2</code></p>";
    echo "<img src='$url2' style='width: 150px; height: 150px; border: 2px solid green; margin: 10px; border-radius: 50%; object-fit: cover;' alt='Test 2'>";
    
    // Test 3: With full path from localhost
    $url3 = "/Court-Reservation/" . $facePath;
    echo "<p><strong>URL 3 (full path):</strong> <code>$url3</code></p>";
    echo "<img src='$url3' style='width: 150px; height: 150px; border: 2px solid blue; margin: 10px; border-radius: 50%; object-fit: cover;' alt='Test 3'>";
}

?>
