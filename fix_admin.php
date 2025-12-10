<?php
$pdo = new PDO('mysql:host=localhost;dbname=court_reservation', 'root', '');
$hash = password_hash('admin123', PASSWORD_DEFAULT);

// Update existing admin
$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
$result = $stmt->execute([$hash, 'admin@courtreservation.ph']);

if ($result) {
    echo "Admin password updated successfully!\n";
    echo "Email: admin@courtreservation.ph\n";
    echo "Password: admin123\n";
    echo "Hash: $hash\n";
} else {
    echo "Failed to update.\n";
}

// Verify
$stmt = $pdo->prepare('SELECT id, email, password, role FROM users WHERE email = ?');
$stmt->execute(['admin@courtreservation.ph']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($user);

// Test password verification
if ($user && password_verify('admin123', $user['password'])) {
    echo "\nPassword verification: SUCCESS\n";
} else {
    echo "\nPassword verification: FAILED\n";
}
