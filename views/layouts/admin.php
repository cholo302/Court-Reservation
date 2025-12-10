<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - ' . APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ph-blue': '#0038A8',
                        'ph-red': '#CE1126',
                        'ph-yellow': '#FCD116',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-4 border-b border-gray-800">
                <a href="<?= url('admin') ?>" class="flex items-center">
                    <i class="fas fa-basketball-ball text-ph-yellow text-2xl mr-2"></i>
                    <span class="text-lg font-bold">Admin Panel</span>
                </a>
            </div>
            
            <nav class="flex-1 p-4 space-y-1">
                <a href="<?= url('admin') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
                </a>
                
                <div class="pt-4 pb-2 px-4 text-xs text-gray-500 uppercase">Management</div>
                
                <a href="<?= url('admin/bookings') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/bookings') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-calendar-check w-5 mr-3"></i> Bookings
                </a>
                <a href="<?= url('admin/payments') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/payments') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-money-bill-wave w-5 mr-3"></i> Payments
                </a>
                <a href="<?= url('admin/courts') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/courts') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-map-marker-alt w-5 mr-3"></i> Courts
                </a>
                <a href="<?= url('admin/users') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-users w-5 mr-3"></i> Users
                </a>
                
                <div class="pt-4 pb-2 px-4 text-xs text-gray-500 uppercase">Tools</div>
                
                <a href="<?= url('admin/scanner') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/scanner') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-qrcode w-5 mr-3"></i> QR Scanner
                </a>
                <a href="<?= url('admin/reports') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/reports') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-chart-bar w-5 mr-3"></i> Reports
                </a>
                <a href="<?= url('admin/logs') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/logs') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-history w-5 mr-3"></i> Activity Logs
                </a>
                <a href="<?= url('admin/settings') ?>" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition <?= strpos($_SERVER['REQUEST_URI'], '/settings') !== false ? 'bg-gray-800' : '' ?>">
                    <i class="fas fa-cog w-5 mr-3"></i> Settings
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-800">
                <a href="<?= url('/') ?>" class="flex items-center text-gray-400 hover:text-white transition">
                    <i class="fas fa-arrow-left w-5 mr-3"></i> Back to Site
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <h1 class="text-xl font-semibold text-gray-800"><?= $title ?? 'Dashboard' ?></h1>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user-shield mr-2"></i>
                        <?= $_SESSION['user_name'] ?? 'Admin' ?>
                    </span>
                    <a href="<?= url('logout') ?>" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                <?php if ($message = flash('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                    <p><i class="fas fa-check-circle mr-2"></i><?= $message ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($message = flash('error')): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    <p><i class="fas fa-exclamation-circle mr-2"></i><?= $message ?></p>
                </div>
                <?php endif; ?>
                
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
