<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/storage/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== SQLite Database Verification ===\n\n";

// Get tables
$stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
echo "Tables in database:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  ✓ " . $row['name'] . "\n";
}

// Get users
echo "\n";
$stmt = $pdo->query('SELECT id, name, email, role FROM users');
echo "Users in database:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  ID: {$row['id']} | {$row['name']} ({$row['email']}) - {$row['role']}\n";
}

echo "\n✓ SQLite Database is ready!\n";
?>
