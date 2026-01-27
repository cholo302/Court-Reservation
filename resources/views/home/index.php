<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Home'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }
        
        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #667eea;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .btn-logout {
            background: #f87171;
            color: white;
        }
        
        .btn-logout:hover {
            background: #ef4444;
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .content {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .feature {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .feature h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .user-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .user-section h2 {
            margin-bottom: 20px;
        }
        
        .user-section p {
            margin-bottom: 20px;
            color: #666;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="navbar">
            <div class="logo">🏀 Court Reservation</div>
            <nav class="nav-links">
                <a href="/">Home</a>
                <a href="#courts">Courts</a>
                <a href="#bookings">Bookings</a>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/login" class="btn btn-primary">Login</a>
                    <a href="/register" class="btn btn-secondary">Register</a>
                <?php else: ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin/dashboard" class="btn btn-primary">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="/logout" class="btn btn-logout">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Book Your Perfect Court</h1>
            <p>Find and reserve sports courts in the Philippines with ease</p>
            <div class="hero-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/register" class="btn btn-primary">Get Started</a>
                    <a href="#courts" class="btn btn-secondary">Browse Courts</a>
                <?php else: ?>
                    <a href="#courts" class="btn btn-primary">Browse Courts</a>
                    <a href="/bookings" class="btn btn-secondary">My Bookings</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Features -->
        <div class="features">
            <div class="feature">
                <div class="feature-icon">📅</div>
                <h3>Easy Booking</h3>
                <p>Browse available courts and book in just a few clicks</p>
            </div>
            <div class="feature">
                <div class="feature-icon">💳</div>
                <h3>Secure Payment</h3>
                <p>Pay via GCash, Maya, or other secure payment methods</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📍</div>
                <h3>Find Location</h3>
                <p>Discover courts near you with detailed information</p>
            </div>
            <div class="feature">
                <div class="feature-icon">⭐</div>
                <h3>Reviews</h3>
                <p>Read and write reviews from other court users</p>
            </div>
        </div>
        
        <!-- User Section -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-section">
                <h2>Your Profile</h2>
                <p>Email: <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <p>Role: <?php echo ucfirst($_SESSION['user_role']); ?></p>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <p><strong>You are an Administrator</strong></p>
                    <a href="/admin/dashboard" class="btn btn-primary">Go to Admin Dashboard</a>
                <?php else: ?>
                    <a href="/bookings" class="btn btn-primary">View My Bookings</a>
                    <a href="/courts" class="btn btn-secondary">Browse Courts</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Court Reservation System. All rights reserved.</p>
    </footer>
</body>
</html>
