<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Bookings</h1>
        <p class="text-gray-600 mt-2">View and manage your court reservations</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-gray-700 font-medium">Filter by status:</span>
            <div class="flex flex-wrap gap-2">
                
                <a href="<?= url('bookings?status=pending') ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $currentStatus === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Pending
                </a>
    
                <a href="<?= url('bookings?status=paid') ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $currentStatus === 'paid' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Paid
                </a>
                <a href="<?= url('bookings?status=completed') ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $currentStatus === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Completed
                </a>
                <a href="<?= url('bookings?status=cancelled') ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $currentStatus === 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Cancelled
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bookings List -->
    <?php if (empty($bookings['items'])): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No bookings found</h3>
            <p class="text-gray-500 mb-6">
                <?php if ($currentStatus): ?>
                    You don't have any <?= $currentStatus ?> bookings.
                <?php else: ?>
                    You haven't made any reservations yet.
                <?php endif; ?>
            </p>
            <a href="<?= url('courts') ?>" class="inline-flex items-center bg-ph-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                <i class="fas fa-search mr-2"></i>
                Browse Courts
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookings['items'] as $booking): ?>
                <?php
                $statusConfig = match($booking['status'] ?? 'pending') {
                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
                    'confirmed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle'],
                    'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-double'],
                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-flag-checkered'],
                    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                    'no_show' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user-slash'],
                    'expired' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-hourglass-end'],
                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-question-circle']
                };
                ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row">
                        <!-- Court Image -->
                        <div class="md:w-48 h-32 md:h-auto bg-gray-200 flex-shrink-0">
                            <?php if (!empty($booking['thumbnail'])): ?>
                                <img src="<?= url($booking['thumbnail']) ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-basketball-ball text-gray-400 text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Booking Details -->
                        <div class="flex-1 p-4 md:p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <div class="flex items-center mb-2">
                                        <span class="<?= $statusConfig['bg'] ?> <?= $statusConfig['text'] ?> px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas <?= $statusConfig['icon'] ?> mr-1"></i>
                                            <?= ucfirst($booking['status'] ?? 'pending') ?>
                                        </span>
                                        <span class="text-gray-400 text-sm ml-3">#<?= $booking['booking_code'] ?? 'N/A' ?></span>
                                    </div>
                                    
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?= htmlspecialchars($booking['court_name'] ?? 'Unknown Court') ?>
                                    </h3>
                                    
                                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-calendar text-ph-blue mr-1"></i>
                                            <?= date('l, M d, Y', strtotime($booking['booking_date'])) ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-clock text-ph-blue mr-1"></i>
                                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-users text-ph-blue mr-1"></i>
                                            <?= $booking['player_count'] ?? 1 ?> player(s)
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-ph-blue"><?= formatPrice($booking['total_amount'] ?? 0) ?></p>
                                    <p class="text-xs text-gray-500">Total Amount</p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                                
                                
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <a href="<?= url('bookings/' . $booking['id'] . '/pay') ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-ph-blue text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                                        <i class="fas fa-credit-card mr-2"></i>Pay Now
                                    </a>
                                    <form action="<?= url('bookings/' . $booking['id'] . '/cancel') ?>" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition">
                                            <i class="fas fa-times mr-2"></i>Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (in_array($booking['status'], ['paid', 'confirmed'])): ?>
                                    <a href="<?= url('bookings/' . $booking['id'] . '/qr') ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition">
                                        <i class="fas fa-qrcode mr-2"></i>View QR Code
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] === 'completed' && empty($booking['has_review'])): ?>
                                    <a href="<?= url('bookings/' . $booking['id'] . '/review') ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-200 transition">
                                        <i class="fas fa-star mr-2"></i>Leave Review
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (($bookings['total_pages'] ?? 1) > 1): ?>
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if (($bookings['page'] ?? 1) > 1): ?>
                        <a href="<?= url('bookings?page=' . (($bookings['page'] ?? 1) - 1) . ($currentStatus ? '&status=' . $currentStatus : '')) ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= ($bookings['total_pages'] ?? 1); $i++): ?>
                        <a href="<?= url('bookings?page=' . $i . ($currentStatus ? '&status=' . $currentStatus : '')) ?>" 
                           class="px-4 py-2 rounded-lg <?= $i === ($bookings['page'] ?? 1) ? 'bg-ph-blue text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if (($bookings['page'] ?? 1) < ($bookings['total_pages'] ?? 1)): ?>
                        <a href="<?= url('bookings?page=' . (($bookings['page'] ?? 1) + 1) . ($currentStatus ? '&status=' . $currentStatus : '')) ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Quick Stats -->
    <div class="mt-8 bg-gradient-to-r from-ph-blue to-blue-700 rounded-xl p-6 text-white">
        <h3 class="text-lg font-semibold mb-4">Quick Tips</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-start">
                <i class="fas fa-info-circle mr-2 mt-1"></i>
                <p>Pay within 30 minutes to avoid automatic cancellation.</p>
            </div>
            <div class="flex items-start">
                <i class="fas fa-qrcode mr-2 mt-1"></i>
                <p>Show your QR code at the facility entrance for entry.</p>
            </div>
            <div class="flex items-start">
                <i class="fas fa-star mr-2 mt-1"></i>
                <p>Leave a review after your game to help other players!</p>
            </div>
        </div>
    </div>
</div>
