<?php
require 'config/database.php';

$db = Database::getInstance()->getConnection();

// Get all tables
$stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "=== SQLite Database Schema ===\n\n";
echo "Tables in database:\n";
foreach ($tables as $table) {
    echo "  ✓ $table\n";
    
    // Get column info
    $columnStmt = $db->query("PRAGMA table_info($table)");
    $columns = $columnStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "    Columns:\n";
    foreach ($columns as $col) {
        echo "      - " . $col['name'] . " (" . $col['type'] . ")\n";
    }
    echo "\n";
}

echo "✓ SQLite Database schema check complete!\n";
