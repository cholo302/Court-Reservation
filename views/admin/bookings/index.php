<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Booking Management</h1>
    <div class="flex space-x-2">
        <a href="<?= url('admin/reports/export?type=bookings') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-file-excel mr-2"></i>Export Excel
        </a>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Total</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?? 0 ?></p>
    </div>
    <div class="bg-yellow-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-yellow-600">Pending</p>
        <p class="text-2xl font-bold text-yellow-700"><?= $stats['pending'] ?? 0 ?></p>
    </div>
    <div class="bg-green-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-green-600">Confirmed</p>
        <p class="text-2xl font-bold text-green-700"><?= $stats['confirmed'] ?? 0 ?></p>
    </div>
    <div class="bg-red-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-red-600">Cancelled</p>
        <p class="text-2xl font-bold text-red-700"><?= $stats['cancelled'] ?? 0 ?></p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" 
                   placeholder="Search booking code, user..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="paid" <?= ($_GET['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div>
            <select name="court" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Courts</option>
                <?php foreach ($courts as $court): ?>
                <option value="<?= $court['id'] ?>" <?= ($_GET['court'] ?? '') == $court['id'] ? 'selected' : '' ?>>
                    <?= $court['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
        <?php if (!empty($_GET)): ?>
        <a href="<?= url('admin/bookings') ?>" class="text-gray-500 px-4 py-2 hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Clear
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Court</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Schedule</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($bookings['items'])): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p>No bookings found</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($bookings['items'] as $booking): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900"><?= $booking['booking_code'] ?></p>
                        <p class="text-xs text-gray-500"><?= date('M d, Y g:i A', strtotime($booking['created_at'])) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-900"><?= $booking['user_name'] ?></p>
                        <p class="text-xs text-gray-500"><?= $booking['user_phone'] ?? 'No phone' ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-900"><?= $booking['court_name'] ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-900"><?= date('M d, Y', strtotime($booking['booking_date'])) ?></p>
                        <p class="text-xs text-gray-500">
                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                            <?= date('g:i A', strtotime($booking['end_time'])) ?>
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-ph-blue"><?= formatPrice($booking['total_amount']) ?></p>
                        <p class="text-xs text-gray-500"><?= ucfirst($booking['payment_method'] ?? 'Not set') ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php
                            echo match($booking['status']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-gray-100 text-gray-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'no_show' => 'bg-purple-100 text-purple-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        ?>">
                            <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="<?= url('admin/bookings/' . $booking['id']) ?>" 
                               class="text-gray-500 hover:text-ph-blue" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($booking['status'] === 'pending'): ?>
                            <form action="<?= url('admin/bookings/' . $booking['id'] . '/confirm') ?>" method="POST" class="inline">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="text-green-600 hover:text-green-700" title="Confirm">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                            <form action="<?= url('admin/bookings/' . $booking['id'] . '/cancel') ?>" method="POST" class="inline"
                                  onsubmit="return confirm('Cancel this booking?')">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="text-red-600 hover:text-red-700" title="Cancel">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] === 'paid'): ?>
                            <form action="<?= url('admin/bookings/' . $booking['id'] . '/complete') ?>" method="POST" class="inline">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="text-blue-600 hover:text-blue-700" title="Mark as Complete">
                                    <i class="fas fa-flag-checkered"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-between items-center mt-6">
    <p class="text-sm text-gray-600">
        Showing <?= (($currentPage - 1) * 20) + 1 ?> to <?= min($currentPage * 20, $totalBookings) ?> of <?= $totalBookings ?> bookings
    </p>
    <nav class="flex space-x-2">
        <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-3 py-2 rounded-lg bg-white text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chevron-left"></i>
        </a>
        <?php endif; ?>
        
        <?php 
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        for ($i = $start; $i <= $end; $i++): 
        ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-4 py-2 rounded-lg <?= $currentPage === $i ? 'bg-ph-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?= $currentPage + 1 ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-3 py-2 rounded-lg bg-white text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </nav>
</div>
<?php endif; ?>
