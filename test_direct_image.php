<?php
// Test direct file access through the PHP file server

// This should trigger the file server in public/index.php
$testFile = '/storage/avatars/user_27_govid_1770945354.webp';

echo "Testing file server with: " . htmlspecialchars($testFile) . "<br>";
echo "<img src='http://localhost/Court-Reservation" . htmlspecialchars($testFile) . "' style='max-width: 200px; border: 2px solid red;'>";

?>
