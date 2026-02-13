<?php
require_once 'bootstrap/app.php';

$db = new Database();

// Get users 27 and 28
$stmt = $db->query("SELECT id, name, email, gov_id_photo, face_photo FROM users WHERE id IN (27, 28)");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo "User " . $user["id"] . ": " . $user["name"] . "\n";
    echo "  Gov ID Photo DB: " . ($user["gov_id_photo"] ?? "[NULL]") . "\n";
    echo "  Face Photo DB: " . ($user["face_photo"] ?? "[NULL]") . "\n";
    echo "\n";
}

echo "\n\nChecking file system:\n";
$files = array_filter(scandir(__DIR__ . '/storage/avatars'), function($f) {
    return preg_match('/user_(27|28)/', $f);
});

foreach ($files as $file) {
    echo "File: $file\n";
}
?>
