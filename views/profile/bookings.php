<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Bookings</h1>
        <a href="<?= url('courts') ?>" class="bg-ph-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>New Booking
        </a>
    </div>
    
    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="flex border-b">
            <a href="?status=upcoming" class="px-6 py-3 font-medium <?= ($_GET['status'] ?? 'upcoming') === 'upcoming' ? 'text-ph-blue border-b-2 border-ph-blue' : 'text-gray-500 hover:text-gray-700' ?>">
                Upcoming
            </a>
            <a href="?status=completed" class="px-6 py-3 font-medium <?= ($_GET['status'] ?? '') === 'completed' ? 'text-ph-blue border-b-2 border-ph-blue' : 'text-gray-500 hover:text-gray-700' ?>">
                Completed
            </a>
            <a href="?status=cancelled" class="px-6 py-3 font-medium <?= ($_GET['status'] ?? '') === 'cancelled' ? 'text-ph-blue border-b-2 border-ph-blue' : 'text-gray-500 hover:text-gray-700' ?>">
                Cancelled
            </a>
            <a href="?status=all" class="px-6 py-3 font-medium <?= ($_GET['status'] ?? '') === 'all' ? 'text-ph-blue border-b-2 border-ph-blue' : 'text-gray-500 hover:text-gray-700' ?>">
                All
            </a>
        </div>
    </div>
    
    <!-- Bookings List -->
    <div class="space-y-4">
        <?php if (empty($bookings)): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Bookings Found</h3>
            <p class="text-gray-500 mb-4">You haven't made any bookings yet.</p>
            <a href="<?= url('courts') ?>" class="inline-block bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-basketball-ball mr-2"></i>Browse Courts
            </a>
        </div>
        <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Court Image -->
                <div class="md:w-48 h-32 md:h-auto bg-gray-200 flex-shrink-0">
                    <?php if ($booking['court_image']): ?>
                    <img src="<?= url('storage/courts/' . $booking['court_image']) ?>" alt="<?= $booking['court_name'] ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-basketball-ball text-gray-400 text-3xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Booking Details -->
                <div class="flex-1 p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-semibold text-lg"><?= $booking['court_name'] ?></h3>
                            <p class="text-sm text-gray-500"><?= $booking['booking_code'] ?></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php
                            echo match($booking['status']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-gray-100 text-gray-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-3">
                        <div>
                            <p class="text-gray-500">Date</p>
                            <p class="font-medium"><?= date('M d, Y', strtotime($booking['booking_date'])) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Time</p>
                            <p class="font-medium"><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Players</p>
                            <p class="font-medium"><?= $booking['num_players'] ?> players</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total</p>
                            <p class="font-semibold text-ph-blue"><?= formatPrice($booking['total_amount']) ?></p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <a href="<?= url('bookings/' . $booking['id']) ?>" 
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        
                        <?php if ($booking['status'] === 'pending'): ?>
                        <a href="<?= url('bookings/' . $booking['id'] . '/pay') ?>" 
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                            <i class="fas fa-credit-card mr-1"></i>Pay Now
                        </a>
                        <?php endif; ?>
                        
                        <?php if (in_array($booking['status'], ['paid', 'confirmed'])): ?>
                        <button onclick="showQR('<?= $booking['entry_qr_code'] ?>')" 
                                class="bg-ph-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                            <i class="fas fa-qrcode mr-1"></i>Show QR
                        </button>
                        <?php endif; ?>
                        
                        <?php if (in_array($booking['status'], ['pending', 'confirmed']) && strtotime($booking['booking_date']) > strtotime('+24 hours')): ?>
                        <form action="<?= url('bookings/' . $booking['id'] . '/cancel') ?>" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition text-sm">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] === 'completed' && !$booking['has_review']): ?>
                        <button onclick="openReviewModal(<?= $booking['id'] ?>)" 
                                class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg hover:bg-yellow-200 transition text-sm">
                            <i class="fas fa-star mr-1"></i>Leave Review
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="flex justify-center mt-8">
        <nav class="flex space-x-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&status=<?= $_GET['status'] ?? 'upcoming' ?>" 
               class="px-4 py-2 rounded-lg <?= $currentPage === $i ? 'bg-ph-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center">
        <h3 class="text-lg font-semibold mb-4">Entry QR Code</h3>
        <img id="qrImage" src="" alt="QR Code" class="w-48 h-48 mx-auto mb-4">
        <p class="text-sm text-gray-500 mb-4">Show this QR code at the entrance</p>
        <button onclick="closeQRModal()" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
            Close
        </button>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold mb-4">Leave a Review</h3>
        
        <form id="reviewForm" action="" method="POST">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex space-x-2" id="starRating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="button" onclick="setRating(<?= $i ?>)" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn" data-rating="<?= $i ?>">
                        <i class="fas fa-star"></i>
                    </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="5">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                <textarea name="comment" rows="4" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                          placeholder="Share your experience..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeReviewModal()" 
                        class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-ph-blue text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showQR(src) {
    document.getElementById('qrImage').src = src;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function openReviewModal(bookingId) {
    document.getElementById('reviewForm').action = `<?= url('bookings') ?>/${bookingId}/review`;
    document.getElementById('reviewModal').classList.remove('hidden');
    setRating(5);
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function setRating(rating) {
    document.getElementById('ratingInput').value = rating;
    document.querySelectorAll('.star-btn').forEach((btn, index) => {
        if (index < rating) {
            btn.classList.remove('text-gray-300');
            btn.classList.add('text-yellow-400');
        } else {
            btn.classList.add('text-gray-300');
            btn.classList.remove('text-yellow-400');
        }
    });
}
</script>
