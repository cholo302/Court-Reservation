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
        
        <!-- Right Column: Recent Bookings -->
        <div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-history text-ph-blue mr-2"></i>
                        Recent Bookings
                    </h2>
                    <a href="<?= url('bookings') ?>" class="text-ph-blue hover:text-blue-800 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if (empty($bookings)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No bookings yet</p>
                        <a href="<?= url('courts') ?>" class="inline-block mt-4 text-ph-blue hover:text-blue-800">
                            <i class="fas fa-search mr-1"></i> Browse Courts
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-ph-blue transition">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($booking['court_name'] ?? 'Unknown Court') ?></h3>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        <?php
                                        $status = $booking['status'] ?? 'pending';
                                        if ($status === 'confirmed') echo 'bg-green-100 text-green-800';
                                        elseif ($status === 'pending') echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($status === 'completed') echo 'bg-blue-100 text-blue-800';
                                        elseif ($status === 'cancelled') echo 'bg-red-100 text-red-800';
                                        else echo 'bg-gray-100 text-gray-800';
                                        ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?= date('M d, Y', strtotime($booking['booking_date'])) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                    <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                </p>
                                <p class="text-sm font-medium text-ph-blue mt-2">
                                    <?= formatPrice($booking['total_amount'] ?? 0) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-ph-yellow mr-2"></i>
                    Quick Actions
                </h2>
                
                <div class="space-y-3">
                    <a href="<?= url('courts') ?>" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-ph-blue hover:bg-blue-50 transition">
                        <i class="fas fa-search text-ph-blue mr-3"></i>
                        <span>Browse Courts</span>
                    </a>
                    <a href="<?= url('bookings') ?>" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-ph-blue hover:bg-blue-50 transition">
                        <i class="fas fa-calendar-alt text-ph-blue mr-3"></i>
                        <span>My Bookings</span>
                    </a>
                    <a href="<?= url('notifications') ?>" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-ph-blue hover:bg-blue-50 transition">
                        <i class="fas fa-bell text-ph-blue mr-3"></i>
                        <span>Notifications</span>
                    </a>
                    <form action="<?= url('logout') ?>" method="POST" class="m-0">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full flex items-center p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition text-left">
                            <i class="fas fa-sign-out-alt text-red-500 mr-3"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
