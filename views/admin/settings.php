<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
    </div>
    
    <form action="<?= url('admin/settings') ?>" method="POST" class="space-y-6">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        
        <!-- General Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-cog mr-2 text-gray-400"></i>General Settings
            </h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? APP_NAME) ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="+63 XXX XXX XXXX">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Booking Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Booking Settings
            </h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Booking Hours</label>
                    <input type="number" name="min_booking_hours" min="1" value="<?= $settings['min_booking_hours'] ?? 1 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Booking Hours</label>
                    <input type="number" name="max_booking_hours" min="1" value="<?= $settings['max_booking_hours'] ?? 8 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Advance Booking Days</label>
                    <input type="number" name="advance_booking_days" min="1" value="<?= $settings['advance_booking_days'] ?? 30 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Expiry (minutes)</label>
                    <input type="number" name="reservation_expiry" min="5" value="<?= $settings['reservation_expiry'] ?? 30 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operating Start Hour</label>
                    <select name="operating_start" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <?php for ($i = 0; $i < 24; $i++): ?>
                        <option value="<?= $i ?>" <?= ($settings['operating_start'] ?? 6) == $i ? 'selected' : '' ?>>
                            <?= date('g:i A', strtotime("$i:00")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operating End Hour</label>
                    <select name="operating_end" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <?php for ($i = 0; $i < 24; $i++): ?>
                        <option value="<?= $i ?>" <?= ($settings['operating_end'] ?? 22) == $i ? 'selected' : '' ?>>
                            <?= date('g:i A', strtotime("$i:00")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Pricing Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-peso-sign mr-2 text-gray-400"></i>Pricing Settings
            </h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Hourly Rate (₱)</label>
                    <input type="number" name="default_hourly_rate" min="0" step="0.01" value="<?= $settings['default_hourly_rate'] ?? 500 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peak Hour Rate (₱)</label>
                    <input type="number" name="peak_hour_rate" min="0" step="0.01" value="<?= $settings['peak_hour_rate'] ?? 700 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weekend Rate (₱)</label>
                    <input type="number" name="weekend_rate" min="0" step="0.01" value="<?= $settings['weekend_rate'] ?? 600 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Downpayment Percentage (%)</label>
                    <input type="number" name="downpayment_percent" min="0" max="100" value="<?= $settings['downpayment_percent'] ?? 50 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
            </div>
            
            <div class="mt-4 grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peak Hours Start</label>
                    <select name="peak_hours_start" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <?php for ($i = 0; $i < 24; $i++): ?>
                        <option value="<?= $i ?>" <?= ($settings['peak_hours_start'] ?? 17) == $i ? 'selected' : '' ?>>
                            <?= date('g:i A', strtotime("$i:00")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peak Hours End</label>
                    <select name="peak_hours_end" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <?php for ($i = 0; $i < 24; $i++): ?>
                        <option value="<?= $i ?>" <?= ($settings['peak_hours_end'] ?? 21) == $i ? 'selected' : '' ?>>
                            <?= date('g:i A', strtotime("$i:00")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Payment Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-credit-card mr-2 text-gray-400"></i>Payment Settings
            </h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GCash Number</label>
                    <input type="text" name="gcash_number" value="<?= htmlspecialchars($settings['gcash_number'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="09XX XXX XXXX">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GCash Name</label>
                    <input type="text" name="gcash_name" value="<?= htmlspecialchars($settings['gcash_name'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maya Number</label>
                    <input type="text" name="maya_number" value="<?= htmlspecialchars($settings['maya_number'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="09XX XXX XXXX">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maya Name</label>
                    <input type="text" name="maya_name" value="<?= htmlspecialchars($settings['maya_name'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" name="auto_verify_payments" value="1" 
                           <?= ($settings['auto_verify_payments'] ?? false) ? 'checked' : '' ?>
                           class="w-4 h-4 text-ph-blue rounded">
                    <span class="ml-2 text-sm text-gray-700">Auto-verify payments (requires PayMongo integration)</span>
                </label>
            </div>
        </div>
        
        <!-- Notification Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-bell mr-2 text-gray-400"></i>Notification Settings
            </h2>
            
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="sms_notifications" value="1" 
                           <?= ($settings['sms_notifications'] ?? false) ? 'checked' : '' ?>
                           class="w-4 h-4 text-ph-blue rounded">
                    <span class="ml-2 text-sm text-gray-700">Enable SMS notifications</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="email_notifications" value="1" 
                           <?= ($settings['email_notifications'] ?? true) ? 'checked' : '' ?>
                           class="w-4 h-4 text-ph-blue rounded">
                    <span class="ml-2 text-sm text-gray-700">Enable email notifications</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="reminder_notifications" value="1" 
                           <?= ($settings['reminder_notifications'] ?? true) ? 'checked' : '' ?>
                           class="w-4 h-4 text-ph-blue rounded">
                    <span class="ml-2 text-sm text-gray-700">Send booking reminders (1 hour before)</span>
                </label>
            </div>
        </div>
        
        <!-- No-Show & Blacklist Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-user-slash mr-2 text-gray-400"></i>No-Show & Blacklist
            </h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No-Show Threshold</label>
                    <input type="number" name="no_show_threshold" min="1" value="<?= $settings['no_show_threshold'] ?? 3 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Auto-blacklist after this many no-shows</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ban Duration (days)</label>
                    <input type="number" name="ban_duration" min="1" value="<?= $settings['ban_duration'] ?? 30 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Duration of automatic bans</p>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
