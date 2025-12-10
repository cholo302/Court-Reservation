<?php
require_once __DIR__ . '/config/database.php';

$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
);

// Check admin user
$stmt = $pdo->query("SELECT id, email, password, role FROM users WHERE role = 'admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Admin user in database:\n";
print_r($admin);

echo "\n\nTesting password verification:\n";
$testPassword = 'admin123';
if ($admin) {
    $result = password_verify($testPassword, $admin['password']);
    echo "Password 'admin123' verification: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Stored hash: " . $admin['password'] . "\n";
    
    // Generate correct hash
    echo "\nCorrect hash for 'admin123': " . password_hash('admin123', PASSWORD_DEFAULT) . "\n";
}

// Check all users
echo "\n\nAll users:\n";
$stmt = $pdo->query("SELECT id, name, email, role FROM users");
while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($user);
}
