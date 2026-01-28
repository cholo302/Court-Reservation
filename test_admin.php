<?php
/**
 * Test Admin Access
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    
    // Check if admin user exists
    $admin = $db->fetch("SELECT * FROM users WHERE email = ? AND role = ?", ['admin@courtreserve.ph', 'admin']);
    
    if ($admin) {
        echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
        echo "<h2 style='color: green;'>✓ Admin User Found!</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($admin['name']) . "</p>";
        echo "<p><strong>Role:</strong> " . htmlspecialchars($admin['role']) . "</p>";
        echo "<p><strong>Created:</strong> " . htmlspecialchars($admin['created_at']) . "</p>";
        echo "<hr>";
        echo "<p><strong>Try logging in with:</strong></p>";
        echo "<p>Email: <code>admin@courtreserve.ph</code></p>";
        echo "<p>Password: <code>admin123</code></p>";
        echo "<p><a href='/Court-Reservation/login' style='color: #0038a8; text-decoration: none;'>Go to Login →</a></p>";
        echo "</div>";
    } else {
        echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
        echo "<h2 style='color: red;'>✗ Admin User NOT Found!</h2>";
        echo "<p>The database exists but the admin user hasn't been created.</p>";
        echo "<p><a href='/Court-Reservation/setup.php' style='background: #0038a8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Run Setup</a></p>";
        echo "</div>";
    }
    
    // List all users
    echo "<hr style='margin-top: 40px;'>";
    echo "<h3>All Users in Database:</h3>";
    $users = $db->fetchAll("SELECT id, name, email, role FROM users");
    if ($users) {
        echo "<table style='border-collapse: collapse; font-family: Arial;'>";
        echo "<tr style='background: #f3f4f6;'><th style='border: 1px solid #d1d5db; padding: 8px;'>ID</th><th style='border: 1px solid #d1d5db; padding: 8px;'>Name</th><th style='border: 1px solid #d1d5db; padding: 8px;'>Email</th><th style='border: 1px solid #d1d5db; padding: 8px;'>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td style='border: 1px solid #d1d5db; padding: 8px;'>" . $user['id'] . "</td>";
            echo "<td style='border: 1px solid #d1d5db; padding: 8px;'>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td style='border: 1px solid #d1d5db; padding: 8px;'>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td style='border: 1px solid #d1d5db; padding: 8px;'>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
    echo "<h2 style='color: red;'>✗ Database Error</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='/Court-Reservation/setup.php' style='background: #0038a8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Run Setup</a></p>";
    echo "</div>";
}
?>
