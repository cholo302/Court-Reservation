<?php
/**
 * SQLite Database Setup Script
 */

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $envLines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

echo "<html><head><title>Court Reservation System - SQLite Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
    h2 { color: #333; }
    .success { color: #10b981; }
    .error { color: #ef4444; }
    a { color: #667eea; text-decoration: none; font-weight: bold; }
    a:hover { text-decoration: underline; }
    pre { background: #f3f4f6; padding: 15px; border-radius: 4px; }
</style></head><body>";

try {
    // Create storage directory if it doesn't exist
    $storageDir = __DIR__ . '/storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
        echo "<h2 class='success'>✓ Created storage directory</h2>";
    }
    
    // Get database path from .env
    $dbFile = $storageDir . '/database.sqlite';
    $dbExists = file_exists($dbFile);
    
    // Create or open SQLite database
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    if ($dbExists) {
        echo "<h2 class='success'>✓ SQLite database exists: " . basename($dbFile) . "</h2>";
    } else {
        echo "<h2 class='success'>✓ SQLite database created: " . basename($dbFile) . "</h2>";
    }
    
    // Create users table
    $sql = <<<SQL
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
    );
    SQL;
    
    $pdo->exec($sql);
    echo "<h2 class='success'>✓ Users table created</h2>";
    
    // Create index for email
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_role ON users(role)');
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        // Create default admin user
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
    
    // Create sample user for testing
    $testEmail = 'user@example.com';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, phone, password, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'Test User',
            $testEmail,
            '09123456789',
            $hashedPassword,
            'user'
        ]);
        echo "<h2 class='success'>✓ Test user created</h2>";
        echo "<p style='background: #efe; padding: 15px; border-radius: 4px;'>";
        echo "<strong>Test Account:</strong><br>";
        echo "Email: <code>user@example.com</code><br>";
        echo "Password: <code>password</code>";
        echo "</p>";
    }
    
    echo "<h2 style='color: green;'>✓ SQLite Database Setup Complete!</h2>";
    echo "<p><a href='/Court-Reservation/'>Go to Home Page →</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    exit(1);
}


foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✓ Created: " . str_replace(__DIR__, '', $dir) . "</p>";
        } else {
            echo "<p class='error'>✗ Failed to create: " . str_replace(__DIR__, '', $dir) . "</p>";
        }
    } else {
        echo "<p class='info'>○ Already exists: " . str_replace(__DIR__, '', $dir) . "</p>";
    }
}

// Step 4: Database setup
echo "<h2>4. Database Setup</h2>";
echo "<p class='info'>ℹ️ Make sure MySQL is running in XAMPP</p>";

try {
    $host = 'localhost';
    $dbname = 'court_reservation';
    $user = 'root';
    $pass = '';
    
    // Connect without database first to create it
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✓ Database '$dbname' ready</p>";
    
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Run migrations
    echo "<h3>Running Migrations...</h3>";
    
    $migrations = glob(__DIR__ . '/database/migrations/*.sql');
    sort($migrations);
    
    foreach ($migrations as $migration) {
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
        echo "<p class='success'>✓ Executed: " . basename($migration) . "</p>";
    }
    
    // Check if data exists
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    if ($userCount == 0) {
        echo "<h3>Seeding Database...</h3>";
        
        // Seed court types
        $types = [
            ['Basketball Court', 'Standard basketball court', 'fa-basketball-ball'],
            ['Badminton Court', 'Professional badminton court', 'fa-shuttlecock'],
            ['Volleyball Court', 'Indoor volleyball court', 'fa-volleyball-ball'],
            ['Tennis Court', 'Hard court tennis facility', 'fa-baseball-ball'],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO court_types (name, description, icon) VALUES (?, ?, ?)");
        foreach ($types as $type) {
            $stmt->execute($type);
        }
        echo "<p class='success'>✓ Court types seeded</p>";
        
        // Seed sample courts
        $courts = [
            [1, 'Court A - Makati Sports Center', 'Premium indoor basketball court', '123 Ayala Ave, Makati City', 500, 700, 600, 10],
            [1, 'Court B - BGC Arena', 'Outdoor basketball court with lights', '456 Bonifacio High Street, Taguig', 400, 550, 500, 10],
            [2, 'Badminton Court 1', 'Air-conditioned badminton facility', '321 Taft Avenue, Manila', 300, 400, 350, 4],
            [3, 'Volleyball Arena', 'Standard indoor volleyball court', '890 Shaw Boulevard, Mandaluyong', 450, 600, 550, 12],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO courts (court_type_id, name, description, location, price_per_hour, peak_price_per_hour, weekend_price_per_hour, max_players, amenities, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '[]', 'active')");
        foreach ($courts as $court) {
            $stmt->execute($court);
        }
        echo "<p class='success'>✓ Sample courts seeded</p>";
        
        // Seed court schedules
        $courtIds = $pdo->query("SELECT id FROM courts")->fetchAll(PDO::FETCH_COLUMN);
        $stmt = $pdo->prepare("INSERT INTO court_schedules (court_id, day_of_week, open_time, close_time, is_available) VALUES (?, ?, ?, ?, 1)");
        foreach ($courtIds as $courtId) {
            for ($day = 0; $day <= 6; $day++) {
                $stmt->execute([$courtId, $day, '06:00:00', '22:00:00']);
            }
        }
        echo "<p class='success'>✓ Court schedules seeded</p>";
        
        // Create admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, phone, role, email_verified_at) VALUES ('Admin User', 'admin@courtreserve.ph', '$adminPassword', '09171234567', 'admin', NOW())");
        echo "<p class='success'>✓ Admin user created</p>";
        
        // Create test user
        $userPassword = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, phone, role) VALUES ('Test User', 'test@example.com', '$userPassword', '09181234567', 'user')");
        echo "<p class='success'>✓ Test user created</p>";
    } else {
        echo "<p class='info'>○ Database already has data, skipping seed</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Step 5: Show credentials
echo "<h2>5. Login Credentials</h2>";
echo "<pre>";
echo "Admin Login:\n";
echo "  Email: admin@courtreserve.ph\n";
echo "  Password: admin123\n\n";
echo "Test User Login:\n";
echo "  Email: test@example.com\n";
echo "  Password: password123\n";
echo "</pre>";

echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Your Court Reservation System is ready to use.</p>";
echo "<a href='index.php' class='btn'>Go to Homepage</a>";
echo " <a href='index.php?url=admin' class='btn' style='background: #fbbf24; color: #1f2937;'>Admin Dashboard</a>";

echo "</body></html>";
