<?php
/**
 * Debug Session and User Data
 */

session_start();

echo "<div style='font-family: Arial; padding: 20px; max-width: 800px; margin: 50px auto;'>";
echo "<h2>Session Debug Information</h2>";

echo "<h3>Session User Data:</h3>";
if (isset($_SESSION['user'])) {
    echo "<pre>" . htmlspecialchars(json_encode($_SESSION['user'], JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ No user in session. You need to login first.</p>";
    echo "<p><a href='/Court-Reservation/login'>Go to Login</a></p>";
}

echo "<h3>All Session Data:</h3>";
echo "<pre>" . htmlspecialchars(json_encode($_SESSION, JSON_PRETTY_PRINT)) . "</pre>";

// Check database
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    
    echo "<h3>Admin User in Database:</h3>";
    $admin = $db->fetch("SELECT id, name, email, role FROM users WHERE email = ?", ['admin@courtreserve.ph']);
    
    if ($admin) {
        echo "<pre>" . htmlspecialchars(json_encode($admin, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Admin user not found in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}

echo "</div>";
?>
