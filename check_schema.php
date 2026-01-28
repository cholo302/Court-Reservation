<?php
require 'config/database.php';

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);

// Add scanned_at column if it doesn't exist
try {
    $pdo->exec('ALTER TABLE bookings ADD COLUMN scanned_at DATETIME NULL AFTER entry_qr_code');
    echo "scanned_at column added successfully\n";
} catch (Exception $e) {
    echo "Column may already exist or error: " . $e->getMessage() . "\n";
}

$stmt = $pdo->query('DESCRIBE bookings');
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Bookings columns:\n";
print_r($columns);
