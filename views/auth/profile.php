<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-ph-blue to-blue-700 px-6 py-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-ph-blue text-4xl font-bold shadow-lg">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div class="md:ml-6 mt-4 md:mt-0 text-center md:text-left">
                    <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($user['name']) ?></h1>
                    <p class="text-blue-100"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="text-blue-100">
                        <i class="fas fa-phone mr-1"></i>
                        <?= htmlspecialchars($user['phone'] ?? 'No phone added') ?>
                    </p>
                </div>
                <div class="md:ml-auto mt-4 md:mt-0">
                    <span class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-full text-sm">
                        <i class="fas fa-user mr-1"></i>
                        Member since <?= isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A' ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-200">
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-ph-blue"><?= $stats['total_bookings'] ?? 0 ?></p>
                <p class="text-sm text-gray-500">Total Bookings</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-green-600"><?= $stats['completed_bookings'] ?? 0 ?></p>
                <p class="text-sm text-gray-500">Completed</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-yellow-600"><?= $stats['pending_bookings'] ?? 0 ?></p>
                <p class="text-sm text-gray-500">Pending</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-gray-900"><?= formatPrice($stats['total_spent'] ?? 0) ?></p>
                <p class="text-sm text-gray-500">Total Spent</p>
            </div>
        </div>
    </div>
    
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left Column: Edit Profile -->
        <div class="md:col-span-2 space-y-8">
            <!-- Update Profile Form -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-user-edit text-ph-blue mr-2"></i>
                    Edit Profile
                </h2>
                
                <form action="<?= url('profile/update') ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="+63 9XX XXX XXXX">
                    </div>
                    
                    <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-lock text-ph-blue mr-2"></i>
                    Change Password
                </h2>
                
                <form action="<?= url('profile/password') ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="password" name="password" required minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                    
                    <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                        <i class="fas fa-key mr-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
        
       
