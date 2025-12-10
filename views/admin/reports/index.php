<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
    <div class="flex space-x-2">
        <a href="<?= url('admin/reports/export?type=full') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-file-excel mr-2"></i>Export Full Report
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm text-gray-600 mb-1">From Date</label>
            <input type="date" name="from" value="<?= $_GET['from'] ?? date('Y-m-01') ?>"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">To Date</label>
            <input type="date" name="to" value="<?= $_GET['to'] ?? date('Y-m-d') ?>"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Court</label>
            <select name="court" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Courts</option>
                <?php foreach ($courts as $court): ?>
                <option value="<?= $court['id'] ?>" <?= ($_GET['court'] ?? '') == $court['id'] ? 'selected' : '' ?>>
                    <?= $court['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-chart-bar mr-2"></i>Generate Report
        </button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600"><?= formatPrice($summary['total_revenue'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-peso-sign text-green-600"></i>
            </div>
        </div>
        <p class="text-sm mt-2 <?= ($summary['revenue_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
            <i class="fas fa-<?= ($summary['revenue_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?> mr-1"></i>
            <?= abs($summary['revenue_change'] ?? 0) ?>% from last period
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Bookings</p>
                <p class="text-2xl font-bold text-ph-blue"><?= $summary['total_bookings'] ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-ph-blue"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <?= $summary['completed_bookings'] ?? 0 ?> completed
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Avg. Booking Value</p>
                <p class="text-2xl font-bold text-purple-600"><?= formatPrice($summary['avg_booking_value'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Utilization Rate</p>
                <p class="text-2xl font-bold text-yellow-600"><?= $summary['utilization_rate'] ?? 0 ?>%</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-percentage text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Revenue Trend</h3>
        <canvas id="revenueChart" height="250"></canvas>
    </div>
    
    <!-- Bookings by Status -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Bookings by Status</h3>
        <canvas id="statusChart" height="250"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <!-- Top Courts -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Top Performing Courts</h3>
        <div class="space-y-3">
            <?php foreach ($topCourts as $index => $court): ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-6 h-6 bg-ph-blue text-white rounded-full flex items-center justify-center text-sm mr-3">
                        <?= $index + 1 ?>
                    </span>
                    <span class="font-medium"><?= $court['name'] ?></span>
                </div>
                <span class="text-green-600 font-semibold"><?= formatPrice($court['total_revenue'] ?? 0) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Payment Methods</h3>
        <canvas id="paymentChart" height="200"></canvas>
    </div>
    
    <!-- Peak Hours -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Peak Hours</h3>
        <div class="space-y-2">
            <?php if (empty($peakHours)): ?>
            <p class="text-gray-500 text-sm">No booking data available yet.</p>
            <?php else: ?>
            <?php foreach ($peakHours as $hour): ?>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600"><?= $hour['time_range'] ?? 'N/A' ?></span>
                    <span class="font-medium"><?= $hour['bookings'] ?? 0 ?> bookings</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-ph-blue h-2 rounded-full" style="width: <?= $hour['percentage'] ?? 0 ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold">Recent Transactions</h3>
        <a href="<?= url('admin/reports/export?type=transactions') ?>" class="text-ph-blue text-sm hover:underline">
            <i class="fas fa-download mr-1"></i>Export
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Court</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($recentTransactions as $transaction): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y', strtotime($transaction['created_at'])) ?></td>
                    <td class="px-6 py-4 font-mono text-sm"><?= $transaction['booking_code'] ?? 'N/A' ?></td>
                    <td class="px-6 py-4"><?= $transaction['user_name'] ?? 'Unknown' ?></td>
                    <td class="px-6 py-4"><?= $transaction['court_name'] ?? 'N/A' ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs <?php
                            echo match($transaction['payment_method']) {
                                'gcash' => 'bg-blue-100 text-blue-700',
                                'maya' => 'bg-green-100 text-green-700',
                                'bank' => 'bg-purple-100 text-purple-700',
                                'cash' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        ?>">
                            <?= strtoupper($transaction['payment_method']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-green-600"><?= formatPrice($transaction['amount']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartData['labels']) ?>,
        datasets: [{
            label: 'Revenue (₱)',
            data: <?= json_encode($chartData['revenue']) ?>,
            borderColor: '#0038a8',
            backgroundColor: 'rgba(0, 56, 168, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString()
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Confirmed', 'Pending', 'Cancelled'],
        datasets: [{
            data: <?= json_encode($statusData) ?>,
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Payment Methods Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'pie',
    data: {
        labels: ['GCash', 'Maya', 'Bank QR', 'Cash'],
        datasets: [{
            data: <?= json_encode($paymentMethodData) ?>,
            backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
