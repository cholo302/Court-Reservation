<?php
require_once 'bootstrap/app.php';

$db = new Database();

$users = $db->query("SELECT id, name, gov_id_photo, face_photo FROM users WHERE id IN (27, 28, 29)")->fetchAll();

echo "<pre>";
foreach ($users as $u) {
    echo "User {$u['id']}: {$u['name']}\n";
    echo "  gov_id_photo: " . ($u['gov_id_photo'] ?? '[NULL]') . "\n";
    echo "  face_photo: " . ($u['face_photo'] ?? '[NULL]') . "\n";
    echo "\n";
}
echo "</pre>";

// Also test the file server directly
echo "<h2>Testing File Server</h2>";
echo "<p>Trying to load: /storage/avatars/user_27_govid_1770945354.webp</p>";
echo "<img src='/Court-Reservation/storage/avatars/user_27_govid_1770945354.webp' alt='test' style='max-width: 200px; border: 1px solid red;' onerror='console.log(\"Image failed to load\")'>";

?>
