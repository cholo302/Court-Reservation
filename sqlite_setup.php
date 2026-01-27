<?php
/**
 * SQLite Database Setup Script
 */

echo "<html><head><title>Court Reservation System - SQLite Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
    h2 { color: #333; }
    .success { color: #10b981; }
    .error { color: #ef4444; }
    a { color: #667eea; text-decoration: none; font-weight: bold; }
    a:hover { text-decoration: underline; }
    pre { background: #f3f4f6; padding: 15px; border-radius: 4px; overflow-x: auto; }
</style></head><body>";

try {
    // Get database path
    $dbFile = __DIR__ . '/storage/database.sqlite';
    
    // Create SQLite database connection
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    echo "<h1>SQLite Database Setup</h1>";
    echo "<h2 class='success'>✓ Connected to SQLite database</h2>";
    
    // Run migrations
    echo "<h2>Running Migrations...</h2>";
    
    $migrations = glob(__DIR__ . '/database/migrations/*.sql');
    sort($migrations);
    
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        
        // Skip if already applied
        if ($filename === '001_create_users_table.sql' && 
            $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetch()) {
            echo "<p class='success'>✓ Skipped: $filename (users table already exists)</p>";
            continue;
        }
        
        try {
            $sql = file_get_contents($migration);
            // Split multiple statements and execute each
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            echo "<p class='success'>✓ Executed: $filename</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error in $filename: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check if tables were created
    $tables = $pdo->query("
        SELECT name FROM sqlite_master 
        WHERE type='table' 
        AND name NOT LIKE 'sqlite_%'
        ORDER BY name
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Created Tables:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li class='success'>✓ $table</li>";
    }
    echo "</ul>";
    
    // Create users table if it doesn't exist (fallback)
    if (!in_array('users', $tables)) {
        echo "<h2>Creating Users Table...</h2>";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                phone VARCHAR(20) UNIQUE,
                password VARCHAR(255) NOT NULL,
                role TEXT DEFAULT 'user' CHECK(role IN ('user', 'admin', 'staff')),
                profile_image VARCHAR(255),
                email_verified_at TIMESTAMP NULL,
                phone_verified_at TIMESTAMP NULL,
                is_blacklisted INTEGER DEFAULT 0,
                blacklist_reason TEXT,
                provider VARCHAR(50),
                provider_id VARCHAR(255),
                remember_token VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "<p class='success'>✓ Users table created</p>";
    }
    
    // Create admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, phone, password, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'Admin',
            'admin@courtreservation.ph',
            '09171234567',
            $hashedPassword,
            'admin'
        ]);
        echo "<h2 class='success'>✓ Admin user created</h2>";
        echo "<p style='background: #efe; padding: 15px; border-radius: 4px;'>";
        echo "<strong>Default Admin Account:</strong><br>";
        echo "Email: <code>admin@courtreservation.ph</code><br>";
        echo "Password: <code>password</code>";
        echo "</p>";
    } else {
        echo "<h2 class='success'>✓ Admin user already exists</h2>";
    }
    
    // Create test user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'user@example.com'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, phone, password, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'Test User',
            'user@example.com',
            '09123456789',
            $hashedPassword,
            'user'
        ]);
        echo "<h2 class='success'>✓ Test user created</h2>";
    }
    
    echo "<h2 style='color: green;'>✅ SQLite Database Setup Complete!</h2>";
    echo "<p><a href='/Court-Reservation/'>Go to Home Page →</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    exit(1);
}

echo "</body></html>";
