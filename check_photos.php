<?php
require_once 'bootstrap/app.php';

$db = new Database();
$users = $db->query('SELECT id, name, gov_id_photo, face_photo FROM users WHERE id IN (27, 28) ORDER BY id')->fetchAll();

foreach ($users as $user) {
    echo 'User ' . $user['id'] . ': ' . $user['name'] . "\n";
    echo '  Gov ID Photo: ' . ($user['gov_id_photo'] ?? 'NULL') . "\n";
    echo '  Face Photo: ' . ($user['face_photo'] ?? 'NULL') . "\n";
    echo "\n";
}
?>
