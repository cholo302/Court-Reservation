<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="<?= url('/') ?>" class="hover:text-ph-blue">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="<?= url('bookings') ?>" class="hover:text-ph-blue">My Bookings</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="<?= url('bookings/' . $booking['id']) ?>" class="hover:text-ph-blue">#<?= $booking['booking_code'] ?? $booking['id'] ?></a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Review</li>
        </ol>
    </nav>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-ph-blue to-blue-700 px-6 py-8 text-white text-center">
            <i class="fas fa-star text-4xl text-ph-yellow mb-4"></i>
            <h1 class="text-2xl font-bold">Leave a Review</h1>
            <p class="text-blue-100 mt-2">Tell us about your experience at <?= htmlspecialchars($booking['court_name'] ?? 'the court') ?></p>
        </div>
        
        <!-- Booking Summary -->
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($booking['court_name'] ?? 'Unknown Court') ?></p>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        <?= date('l, M d, Y', strtotime($booking['booking_date'])) ?>
                        &bull;
                        <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?>
                    </p>
                </div>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Completed
                </span>
            </div>
        </div>
        
        <!-- Review Form -->
        <form action="<?= url('bookings/' . $booking['id'] . '/review') ?>" method="POST" class="p-6 space-y-6">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <!-- Star Rating -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">How would you rate your experience?</label>
                <div class="flex items-center justify-center space-x-2" id="star-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="button" class="star-btn text-4xl text-gray-300 hover:text-ph-yellow transition focus:outline-none" data-rating="<?= $i ?>">
                        <i class="fas fa-star"></i>
                    </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="rating-input" value="" required>
                <p class="text-center text-sm text-gray-500 mt-2" id="rating-text">Click to rate</p>
            </div>
            
            <!-- Comment -->
            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Share your thoughts</label>
                <textarea name="comment" id="comment" rows="5" required minlength="10" maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-ph-blue focus:border-transparent resize-none"
                    placeholder="What did you like? How was the court condition? Would you recommend it to others?"></textarea>
                <p class="text-sm text-gray-500 mt-1">
                    <span id="char-count">0</span>/500 characters (minimum 10)
                </p>
            </div>
            
            <!-- Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-medium text-blue-800 mb-2"><i class="fas fa-lightbulb mr-2"></i>Tips for a helpful review</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Describe the court condition and facilities</li>
                    <li>• Mention if the location was easy to find</li>
                    <li>• Share if the staff was helpful (if applicable)</li>
                    <li>• Would you book this court again?</li>
                </ul>
            </div>
            
            <!-- Submit -->
            <div class="flex space-x-4">
                <a href="<?= url('bookings/' . $booking['id']) ?>" class="flex-1 text-center bg-gray-100 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    const ratingTexts = {
        1: 'Poor - Very disappointed',
        2: 'Fair - Below expectations',
        3: 'Good - Met expectations',
        4: 'Very Good - Exceeded expectations',
        5: 'Excellent - Highly recommended!'
    };
    
    starButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            // Update star colors
            starButtons.forEach((star, i) => {
                if (i < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-ph-yellow');
                } else {
                    star.classList.remove('text-ph-yellow');
                    star.classList.add('text-gray-300');
                }
            });
            
            // Update rating text
            ratingText.textContent = ratingTexts[rating];
        });
        
        // Hover effects
        btn.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            starButtons.forEach((star, i) => {
                if (i < rating) {
                    star.classList.add('text-ph-yellow');
                    star.classList.remove('text-gray-300');
                }
            });
        });
        
        btn.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value) || 0;
            starButtons.forEach((star, i) => {
                if (i < currentRating) {
                    star.classList.add('text-ph-yellow');
                    star.classList.remove('text-gray-300');
                } else {
                    star.classList.remove('text-ph-yellow');
                    star.classList.add('text-gray-300');
                }
            });
        });
    });
    
    // Character counter
    commentTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
});
</script>
