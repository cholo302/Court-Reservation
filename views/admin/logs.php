<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
    <div class="flex space-x-2">
        <button onclick="location.reload()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   placeholder="Search logs..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <select name="action" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Actions</option>
                <option value="booking_created" <?= ($_GET['action'] ?? '') === 'booking_created' ? 'selected' : '' ?>>Booking Created</option>
                <option value="booking_confirmed" <?= ($_GET['action'] ?? '') === 'booking_confirmed' ? 'selected' : '' ?>>Booking Confirmed</option>
                <option value="booking_cancelled" <?= ($_GET['action'] ?? '') === 'booking_cancelled' ? 'selected' : '' ?>>Booking Cancelled</option>
                <option value="payment_verified" <?= ($_GET['action'] ?? '') === 'payment_verified' ? 'selected' : '' ?>>Payment Verified</option>
                <option value="user_blacklisted" <?= ($_GET['action'] ?? '') === 'user_blacklisted' ? 'selected' : '' ?>>User Blacklisted</option>
                <option value="court_created" <?= ($_GET['action'] ?? '') === 'court_created' ? 'selected' : '' ?>>Court Created</option>
                <option value="settings_updated" <?= ($_GET['action'] ?? '') === 'settings_updated' ? 'selected' : '' ?>>Settings Updated</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-clipboard-list text-4xl mb-2"></i>
                        <p>No activity logs found</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <div class="text-gray-900"><?= date('M d, Y', strtotime($log['created_at'])) ?></div>
                        <div class="text-gray-500 text-xs"><?= date('g:i:s A', strtotime($log['created_at'])) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($log['user_name']): ?>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <span class="text-blue-600 text-xs font-semibold"><?= strtoupper(substr($log['user_name'], 0, 1)) ?></span>
                            </div>
                            <span class="text-sm font-medium"><?= htmlspecialchars($log['user_name']) ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-400 text-sm">System</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $actionColors = [
                            'booking_created' => 'bg-blue-100 text-blue-700',
                            'booking_confirmed' => 'bg-green-100 text-green-700',
                            'booking_cancelled' => 'bg-red-100 text-red-700',
                            'payment_verified' => 'bg-green-100 text-green-700',
                            'payment_rejected' => 'bg-red-100 text-red-700',
                            'user_blacklisted' => 'bg-red-100 text-red-700',
                            'user_unblacklisted' => 'bg-yellow-100 text-yellow-700',
                            'court_created' => 'bg-purple-100 text-purple-700',
                            'court_updated' => 'bg-purple-100 text-purple-700',
                            'settings_updated' => 'bg-gray-100 text-gray-700',
                        ];
                        $colorClass = $actionColors[$log['action']] ?? 'bg-gray-100 text-gray-700';
                        ?>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                            <?= ucwords(str_replace('_', ' ', $log['action'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        <?= htmlspecialchars($log['description'] ?? '') ?>
                        <?php if ($log['model_type'] && $log['model_id']): ?>
                        <span class="text-gray-400 text-xs ml-1">(<?= $log['model_type'] ?> #<?= $log['model_id'] ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                        <?= $log['ip_address'] ?? '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Info -->
<div class="mt-6 text-sm text-gray-500 text-center">
    <i class="fas fa-info-circle mr-1"></i>
    Showing last 100 activity logs
</div>
