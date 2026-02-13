<?php
/**
 * Migration: Fix photo paths in database based on files in storage/avatars
 */
require_once 'bootstrap/app.php';

echo "Starting photo path migration...\n\n";

$db = new Database();
$avatarDir = __DIR__ . '/storage/avatars';

// Get all files in avatars directory
$files = array_filter(scandir($avatarDir), function($f) {
    return $f !== '.' && $f !== '..';
});

echo "Found " . count($files) . " files in storage/avatars\n\n";

// Group files by user ID
$userPhotos = [];
foreach ($files as $file) {
    // Parse filename: user_ID_TYPE_TIMESTAMP.EXT
    if (preg_match('/^user_(\d+)_(govid|face|profile)_(\d+)\.(.+)$/', $file, $matches)) {
        $userId = $matches[1];
        $photoType = $matches[2];
        
        if (!isset($userPhotos[$userId])) {
            $userPhotos[$userId] = [];
        }
        
        $userPhotos[$userId][$photoType] = 'storage/avatars/' . $file;
    }
}

echo "Parsed " . count($userPhotos) . " users with photos:\n\n";

// Update database records
$updated = 0;
foreach ($userPhotos as $userId => $photos) {
    $user = $db->query("SELECT id, name, gov_id_photo, face_photo FROM users WHERE id = ?", [$userId])->fetch();
    
    if (!$user) {
        echo "⚠ User $userId not found in database\n";
        continue;
    }
    
    echo "User $userId (" . $user['name'] . "):\n";
    
    // Check gov_id_photo
    if (isset($photos['govid'])) {
        if ($user['gov_id_photo'] === $photos['govid']) {
            echo "  ✓ gov_id_photo already correct: " . $photos['govid'] . "\n";
        } else {
            $db->query("UPDATE users SET gov_id_photo = ? WHERE id = ?", [$photos['govid'], $userId]);
            echo "  ✓ Updated gov_id_photo: " . $photos['govid'] . "\n";
            $updated++;
        }
    } else {
        echo "  ⚠ No govid photo found in filesystem\n";
    }
    
    // Check face_photo
    if (isset($photos['face'])) {
        if ($user['face_photo'] === $photos['face']) {
            echo "  ✓ face_photo already correct: " . $photos['face'] . "\n";
        } else {
            $db->query("UPDATE users SET face_photo = ? WHERE id = ?", [$photos['face'], $userId]);
            echo "  ✓ Updated face_photo: " . $photos['face'] . "\n";
            $updated++;
        }
    } else {
        echo "  ⚠ No face photo found in filesystem\n";
    }
    
    echo "\n";
}

echo "\nMigration complete!\n";
echo "Total updates: $updated\n";

// Verify
echo "\n\nVerifying updates...\n\n";

$verifyUsers = $db->query("SELECT id, name, gov_id_photo, face_photo FROM users WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL ORDER BY id DESC LIMIT 10")->fetchAll();

echo "Users with photo data (last 10):\n";
foreach ($verifyUsers as $user) {
    echo "User #{$user['id']}: {$user['name']}\n";
    echo "  Gov ID Photo: " . ($user['gov_id_photo'] ?? "[NULL]") . "\n";
    echo "  Face Photo: " . ($user['face_photo'] ?? "[NULL]") . "\n";
}

?>
