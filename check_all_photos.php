<?php
require_once 'bootstrap/app.php';

$db = new Database();

echo "<h2>Checking Photo Paths in Database</h2>\n\n";

// Get all users with photos from recent registrations
$users = $db->query("
    SELECT id, name, email, gov_id_photo, face_photo, created_at
    FROM users 
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll();

foreach ($users as $user) {
    echo "User #{$user['id']}: {$user['name']} ({$user['email']})\n";
    
    // Check gov_id_photo
    if ($user['gov_id_photo']) {
        $path = $user['gov_id_photo'];
        $fullPath = __DIR__ . '/../' . $path;
        $exists = file_exists($fullPath) ? 'YES ✓' : 'NO ✗';
        echo "  gov_id_photo: $path ($exists)\n";
    } else {
        echo "  gov_id_photo: [NULL or EMPTY]\n";
    }
    
    // Check face_photo
    if ($user['face_photo']) {
        $path = $user['face_photo'];
        $fullPath = __DIR__ . '/../' . $path;
        $exists = file_exists($fullPath) ? 'YES ✓' : 'NO ✗';
        echo "  face_photo: $path ($exists)\n";
    } else {
        echo "  face_photo: [NULL or EMPTY]\n";
    }
    
    echo "\n";
}

// List all files in storage/avatars
echo "\n<h2>Files in storage/avatars/</h2>\n";
$avatarsDir = __DIR__ . '/../storage/avatars';
if (is_dir($avatarsDir)) {
    $files = array_filter(scandir($avatarsDir), function($f) {
        return $f !== '.' && $f !== '..';
    });
    
    echo "Total files: " . count($files) . "\n\n";
    
    foreach (array_slice($files, 0, 20) as $file) {
        $fullPath = $avatarsDir . '/' . $file;
        $size = filesize($fullPath);
        echo "$file (" . number_format($size) . " bytes)\n";
    }
} else {
    echo "Directory does not exist!\n";
}

?>
