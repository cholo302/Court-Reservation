<?php
// Debug: Check what photos are in database and if files exist

session_start();
require_once 'bootstrap/app.php';

echo "<h1>Photo Debug Report</h1>";

// Check storage directory
$storageDir = __DIR__ . '/storage/avatars';
echo "<h2>Storage Directory: " . $storageDir . "</h2>";

if (is_dir($storageDir)) {
    echo "<p style='color: green;'>✓ Directory exists</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($storageDir)), -4) . "</p>";
    
    $files = array_filter(scandir($storageDir), function($f) {
        return $f !== '.' && $f !== '..';
    });
    
    echo "<p>Files in directory: " . count($files) . "</p>";
    if (count($files) > 0) {
        echo "<ul>";
        foreach ($files as $file) {
            $fullPath = $storageDir . '/' . $file;
            $size = filesize($fullPath);
            echo "<li>$file (" . number_format($size) . " bytes)</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>✗ Directory does NOT exist</p>";
}

// Check database records
echo "<h2>Database Records</h2>";

$db = new Database();
$users = $db->query("SELECT id, name, email, gov_id_type, gov_id_photo, face_photo FROM users WHERE gov_id_photo IS NOT NULL OR face_photo IS NOT NULL ORDER BY created_at DESC LIMIT 10")->fetchAll();

echo "<p>Users with photos: " . count($users) . "</p>";

if (count($users) > 0) {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%;'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Name</th>";
    echo "<th>Email</th>";
    echo "<th>ID Type</th>";
    echo "<th>Gov ID Photo Path</th>";
    echo "<th>Gov ID Photo Exists</th>";
    echo "<th>Face Photo Path</th>";
    echo "<th>Face Photo Exists</th>";
    echo "</tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['gov_id_type']) . "</td>";
        
        // Gov ID Photo
        echo "<td>";
        if ($user['gov_id_photo']) {
            echo "<code>" . htmlspecialchars($user['gov_id_photo']) . "</code>";
        } else {
            echo "<em>NULL/Empty</em>";
        }
        echo "</td>";
        
        echo "<td>";
        if ($user['gov_id_photo']) {
            $fullPath = __DIR__ . '/' . $user['gov_id_photo'];
            if (file_exists($fullPath)) {
                echo "<span style='color: green;'>✓ Yes</span>";
            } else {
                echo "<span style='color: red;'>✗ No</span> (checked: $fullPath)";
            }
        } else {
            echo "-";
        }
        echo "</td>";
        
        // Face Photo
        echo "<td>";
        if ($user['face_photo']) {
            echo "<code>" . htmlspecialchars($user['face_photo']) . "</code>";
        } else {
            echo "<em>NULL/Empty</em>";
        }
        echo "</td>";
        
        echo "<td>";
        if ($user['face_photo']) {
            $fullPath = __DIR__ . '/' . $user['face_photo'];
            if (file_exists($fullPath)) {
                echo "<span style='color: green;'>✓ Yes</span>";
            } else {
                echo "<span style='color: red;'>✗ No</span> (checked: $fullPath)";
            }
        } else {
            echo "-";
        }
        echo "</td>";
        
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users with photo records found.</p>";
}

echo "<h2>Recent Registrations</h2>";
$recentUsers = $db->query("SELECT id, name, email, gov_id_type, gov_id_photo, face_photo, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%;'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Name</th>";
echo "<th>Created</th>";
echo "<th>Gov ID Photo</th>";
echo "<th>Face Photo</th>";
echo "</tr>";

foreach ($recentUsers as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
    echo "<td>" . $user['created_at'] . "</td>";
    echo "<td>" . ($user['gov_id_photo'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($user['face_photo'] ? '✓' : '✗') . "</td>";
    echo "</tr>";
}

echo "</table>";

?>
