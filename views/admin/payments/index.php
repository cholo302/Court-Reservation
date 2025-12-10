<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Payment Verification</h1>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-yellow-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-yellow-600">Pending Verification</p>
        <p class="text-2xl font-bold text-yellow-700"><?= $stats['pending'] ?? 0 ?></p>
    </div>
    <div class="bg-green-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-green-600">Verified Today</p>
        <p class="text-2xl font-bold text-green-700"><?= $stats['verified_today'] ?? 0 ?></p>
    </div>
    <div class="bg-blue-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-blue-600">Total This Month</p>
        <p class="text-2xl font-bold text-blue-700"><?= formatPrice($stats['month_total'] ?? 0) ?></p>
    </div>
    <div class="bg-red-50 rounded-lg shadow-sm p-4">
        <p class="text-sm text-red-600">Rejected</p>
        <p class="text-2xl font-bold text-red-700"><?= $stats['rejected'] ?? 0 ?></p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" 
                   placeholder="Search reference, user..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="verified" <?= ($_GET['status'] ?? '') === 'verified' ? 'selected' : '' ?>>Verified</option>
                <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>
        <div>
            <select name="method" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Methods</option>
                <option value="gcash" <?= ($_GET['method'] ?? '') === 'gcash' ? 'selected' : '' ?>>GCash</option>
                <option value="maya" <?= ($_GET['method'] ?? '') === 'maya' ? 'selected' : '' ?>>Maya</option>
                <option value="bank" <?= ($_GET['method'] ?? '') === 'bank' ? 'selected' : '' ?>>Bank QR</option>
                <option value="cash" <?= ($_GET['method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
    </form>
</div>

<!-- Payments Grid -->
<div class="grid lg:grid-cols-2 gap-6">
    <?php foreach ($payments['items'] as $payment): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden <?= $payment['status'] === 'pending' ? 'border-2 border-yellow-300' : '' ?>">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-mono font-semibold"><?= $payment['payment_reference'] ?? 'N/A' ?></p>
                    <p class="text-sm text-gray-500"><?= $payment['booking_code'] ?? 'N/A' ?></p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold <?php
                    echo match($payment['status']) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'verified' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                ?>">
                    <?= ucfirst($payment['status']) ?>
                </span>
            </div>
        </div>
        
        <div class="p-4">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="text-xl font-bold text-ph-blue"><?= formatPrice($payment['amount']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Method</p>
                    <p class="font-medium">
                        <?php if ($payment['payment_method'] === 'gcash'): ?>
                        <span class="text-blue-600"><i class="fas fa-wallet mr-1"></i>GCash</span>
                        <?php elseif ($payment['payment_method'] === 'maya'): ?>
                        <span class="text-green-600"><i class="fas fa-wallet mr-1"></i>Maya</span>
                        <?php else: ?>
                        <?= ucfirst($payment['payment_method']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Customer</p>
                    <p class="font-medium"><?= $payment['user_name'] ?? 'Unknown' ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date</p>
                    <p class="font-medium"><?= date('M d, Y g:i A', strtotime($payment['created_at'])) ?></p>
                </div>
            </div>
            
            <?php if (!empty($payment['proof_image'])): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-2">Payment Proof</p>
                <img src="<?= url('storage/proofs/' . $payment['proof_image']) ?>" 
                     alt="Payment Proof" 
                     class="w-full h-48 object-cover rounded-lg border cursor-pointer"
                     onclick="openProofModal('<?= url('storage/proofs/' . $payment['proof_image']) ?>')">
            </div>
            <?php endif; ?>
            
            <?php if ($payment['status'] === 'pending'): ?>
            <div class="flex space-x-2">
                <form action="<?= url('admin/payments/' . $payment['id'] . '/verify') ?>" method="POST" class="flex-1">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Verify
                    </button>
                </form>
                <form action="<?= url('admin/payments/' . $payment['id'] . '/reject') ?>" method="POST" class="flex-1"
                      onsubmit="return confirm('Reject this payment?')">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times mr-2"></i>Reject
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="text-sm text-gray-500">
                <?php if ($payment['verified_at']): ?>
                <p><i class="fas fa-check-circle text-green-500 mr-1"></i>
                   Verified on <?= date('M d, Y g:i A', strtotime($payment['verified_at'])) ?>
                   <?php if (!empty($payment['verified_by_name'])): ?>by <?= $payment['verified_by_name'] ?><?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($payments['items'])): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-check-circle text-gray-300 text-5xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Payments Found</h3>
    <p class="text-gray-500">All payments have been processed.</p>
</div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center mt-8">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-4 py-2 rounded-lg <?= $currentPage === $i ? 'bg-ph-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeProofModal()" class="absolute -top-10 right-0 text-white text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="proofModalImage" src="" alt="Payment Proof" class="max-w-full max-h-[80vh] rounded-lg">
    </div>
</div>

<script>
function openProofModal(src) {
    document.getElementById('proofModalImage').src = src;
    document.getElementById('proofModal').classList.remove('hidden');
}

function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
}

document.getElementById('proofModal').addEventListener('click', function(e) {
    if (e.target === this) closeProofModal();
});
</script>
