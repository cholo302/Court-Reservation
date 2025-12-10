<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h1>
    
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="w-24 h-24 bg-ph-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <?php if ($user['avatar']): ?>
                    <img src="<?= url('storage/avatars/' . $user['avatar']) ?>" alt="Avatar" class="w-full h-full rounded-full object-cover">
                    <?php else: ?>
                    <i class="fas fa-user text-ph-blue text-3xl"></i>
                    <?php endif; ?>
                </div>
                
                <h2 class="text-xl font-semibold"><?= $user['name'] ?></h2>
                <p class="text-gray-500"><?= $user['email'] ?></p>
                
                <?php if ($user['phone']): ?>
                <p class="text-gray-500 mt-1"><i class="fas fa-phone mr-1"></i><?= $user['phone'] ?></p>
                <?php endif; ?>
                
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">Member since</p>
                    <p class="font-medium"><?= date('F Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h3 class="font-semibold mb-4">Booking Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Bookings</span>
                        <span class="font-semibold"><?= $stats['total_bookings'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Completed</span>
                        <span class="font-semibold text-green-600"><?= $stats['completed'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Spent</span>
                        <span class="font-semibold text-ph-blue"><?= formatPrice($stats['total_spent']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Edit Profile</h3>
                
                <form action="<?= url('profile/update') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" required value="<?= $user['name'] ?>"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="<?= $user['email'] ?>" disabled
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-500">
                            <p class="text-xs text-gray-400 mt-1">Email cannot be changed</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" value="<?= $user['phone'] ?>" placeholder="09XX XXX XXXX"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Change Password</h3>
                
                <form action="<?= url('profile/password') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        </div>
                    </div>
                    
                    <button type="submit" class="mt-4 bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </form>
            </div>
            
            <!-- Notification Preferences -->
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Notification Preferences</h3>
                
                <form action="<?= url('profile/notifications') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                            <div>
                                <p class="font-medium">SMS Notifications</p>
                                <p class="text-sm text-gray-500">Receive booking confirmations via SMS</p>
                            </div>
                            <input type="checkbox" name="sms_notifications" value="1" 
                                   <?= ($user['preferences']['sms'] ?? true) ? 'checked' : '' ?>
                                   class="w-5 h-5 text-ph-blue rounded">
                        </label>
                        
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                            <div>
                                <p class="font-medium">Email Notifications</p>
                                <p class="text-sm text-gray-500">Receive booking updates via email</p>
                            </div>
                            <input type="checkbox" name="email_notifications" value="1"
                                   <?= ($user['preferences']['email'] ?? true) ? 'checked' : '' ?>
                                   class="w-5 h-5 text-ph-blue rounded">
                        </label>
                        
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                            <div>
                                <p class="font-medium">Promotional Emails</p>
                                <p class="text-sm text-gray-500">Receive discounts and special offers</p>
                            </div>
                            <input type="checkbox" name="promotional" value="1"
                                   <?= ($user['preferences']['promotional'] ?? false) ? 'checked' : '' ?>
                                   class="w-5 h-5 text-ph-blue rounded">
                        </label>
                    </div>
                    
                    <button type="submit" class="mt-4 bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Save Preferences
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
