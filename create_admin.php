<?php
/**
 * Create Admin Account
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    
    // Admin credentials
    $email = 'admin@courtreserve.ph';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $name = 'Admin User';
    $phone = '09999999999'; // Unique phone number
    
    // Check if admin already exists
    $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
    
    if ($existing) {
        echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
        echo "<h2 style='color: orange;'>⚠ Admin Already Exists</h2>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><a href='/Court-Reservation/login' style='background: #0038a8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Go to Login</a></p>";
        echo "</div>";
    } else {
        // Insert admin user
        $db->query(
            "INSERT INTO users (name, email, password, phone, role, email_verified_at, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$name, $email, $password, $phone, 'admin', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
        );
        
        echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
        echo "<h2 style='color: green;'>✓ Admin Account Created Successfully!</h2>";
        echo "<hr>";
        echo "<p><strong>Login Credentials:</strong></p>";
        echo "<p><strong>Email:</strong> <code>$email</code></p>";
        echo "<p><strong>Password:</strong> <code>admin123</code></p>";
        echo "<hr>";
        echo "<p><a href='/Court-Reservation/login' style='background: #0038a8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Go to Login</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
    echo "<h2 style='color: red;'>✗ Error Creating Admin</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
