<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="<?= url('/') ?>" class="hover:text-ph-blue">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="<?= url('courts') ?>" class="hover:text-ph-blue">Courts</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900"><?= $court['name'] ?></li>
        </ol>
    </nav>
    
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Court Image -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-80 bg-gradient-to-br from-ph-blue to-blue-700 relative">
                    <?php if ($court['thumbnail']): ?>
                    <img src="<?= url($court['thumbnail']) ?>" alt="<?= $court['name'] ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-basketball-ball text-white/30 text-8xl"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-4 left-4 flex space-x-2">
                        <span class="bg-white/90 text-ph-blue text-sm font-semibold px-3 py-1 rounded-full">
                            <?= $court['court_type_name'] ?? 'Court' ?>
                        </span>
                        <?php if (!empty($court['rating']) && $court['rating'] > 0): ?>
                        <span class="bg-ph-yellow text-ph-blue text-sm font-semibold px-3 py-1 rounded-full">
                            <i class="fas fa-star mr-1"></i><?= number_format($court['rating'], 1) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Court Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= $court['name'] ?></h1>
                
                <div class="flex items-center text-gray-500 mb-4">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span><?= $court['location'] ?>, <?= $court['barangay'] ? $court['barangay'] . ', ' : '' ?><?= $court['city'] ?></span>
                </div>
                
                <?php if ($court['description']): ?>
                <p class="text-gray-600 mb-6"><?= nl2br(htmlspecialchars($court['description'])) ?></p>
                <?php endif; ?>
                
                <!-- Amenities -->
                <?php if (!empty($court['amenities_array'])): ?>
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Amenities</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($court['amenities_array'] as $amenity): ?>
                        <span class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg">
                            <?php
                            $icon = match($amenity) {
                                'parking' => 'fa-parking',
                                'shower' => 'fa-shower',
                                'locker' => 'fa-lock',
                                'lights' => 'fa-lightbulb',
                                'aircon' => 'fa-snowflake',
                                default => 'fa-check'
                            };
                            ?>
                            <i class="fas <?= $icon ?> mr-2 text-ph-blue"></i>
                            <?= ucfirst($amenity) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Rules -->
                <?php if ($court['rules']): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Court Rules
                    </h3>
                    <p class="text-gray-600 text-sm"><?= nl2br(htmlspecialchars($court['rules'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Schedule/Availability -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar-alt mr-2 text-ph-blue"></i>Availability
                </h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" id="selected-date" value="<?= $selectedDate ?>" min="<?= date('Y-m-d') ?>" 
                        max="<?= date('Y-m-d', strtotime('+' . BOOKING_ADVANCE_DAYS . ' days')) ?>"
                        class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div id="time-slots" class="grid grid-cols-4 md:grid-cols-6 gap-2">
                    <?php if (isset($slots['closed']) && $slots['closed']): ?>
                    <div class="col-span-full text-center text-gray-500 py-8">
                        <i class="fas fa-ban text-4xl mb-2"></i>
                        <p>Closed on this date</p>
                        <?php if ($slots['reason']): ?>
                        <p class="text-sm">Reason: <?= $slots['reason'] ?></p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <?php foreach ($slots as $slot): ?>
                    <div class="time-slot text-center py-2 px-1 rounded-lg cursor-pointer transition
                        <?= $slot['available'] 
                            ? ($slot['is_peak'] ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-800' : 'bg-green-100 hover:bg-green-200 text-green-800')
                            : 'bg-red-100 text-red-400 cursor-not-allowed' ?>"
                        data-start="<?= $slot['start'] ?>" data-end="<?= $slot['end'] ?>" data-available="<?= $slot['available'] ? '1' : '0' ?>">
                        <div class="text-sm font-medium"><?= date('g:i A', strtotime($slot['start'])) ?></div>
                        <?php if ($slot['is_peak']): ?>
                        <div class="text-xs">Peak</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 flex items-center space-x-4 text-sm">
                    <span class="flex items-center"><span class="w-4 h-4 bg-green-100 rounded mr-2"></span> Available</span>
                    <span class="flex items-center"><span class="w-4 h-4 bg-yellow-100 rounded mr-2"></span> Peak Hour</span>
                    <span class="flex items-center"><span class="w-4 h-4 bg-red-100 rounded mr-2"></span> Booked</span>
                </div>
            </div>
            
            <!-- Reviews -->
            <?php if (!empty($reviews)): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-star mr-2 text-ph-yellow"></i>Reviews (<?= $court['total_reviews'] ?>)
                </h3>
                
                <div class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                    <div class="border-b pb-4">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($review['user_name']) ?></p>
                                <div class="flex items-center text-sm text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $review['rating'] ? '' : 'text-gray-300' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <span class="ml-auto text-sm text-gray-500">
                                <?= date('M d, Y', strtotime($review['created_at'])) ?>
                            </span>
                        </div>
                        <?php if ($review['comment']): ?>
                        <p class="text-gray-600 text-sm"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Booking Card (Sticky) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                <div class="mb-4">
                    <span class="text-3xl font-bold text-ph-blue"><?= formatPrice($court['hourly_rate']) ?></span>
                    <span class="text-gray-500">/hour</span>
                </div>
                
                <?php if ($court['peak_hour_rate']): ?>
                <p class="text-sm text-gray-500 mb-4">
                    <i class="fas fa-clock mr-1"></i>
                    Peak hours (<?= PEAK_HOURS_START ?>:00-<?= PEAK_HOURS_END ?>:00): 
                    <span class="font-semibold text-yellow-600"><?= formatPrice($court['peak_hour_rate']) ?>/hr</span>
                </p>
                <?php endif; ?>
                
                <?php if ($court['half_court_rate']): ?>
                <p class="text-sm text-gray-500 mb-4">
                    <i class="fas fa-arrows-alt-h mr-1"></i>
                    Half court: <span class="font-semibold"><?= formatPrice($court['half_court_rate']) ?>/hr</span>
                </p>
                <?php endif; ?>
                
                <div class="border-t pt-4 mb-4">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        <?= $court['downpayment_percent'] ?>% downpayment required
                    </p>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        Min <?= $court['min_booking_hours'] ?? 1 ?> hour(s), Max <?= $court['max_booking_hours'] ?? 4 ?> hours
                    </p>
                </div>
                
                <?php if (isLoggedIn()): ?>
                <a href="<?= url('bookings/create/' . $court['id']) ?>" 
                    class="block w-full bg-ph-blue text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Book a Court Now
                </a>
                <?php else: ?>
                <a href="<?= url('login') ?>" 
                    class="block w-full bg-ph-blue text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Reserve a Court
                </a>
               
                </p>
                <?php endif; ?>
                
                <!-- Payment Methods -->
                <div class="mt-6 pt-4 border-t">
                    <p class="text-sm text-gray-500 mb-2">Accepted Payments:</p>
                    <div class="flex space-x-2">
                        <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">GCash</span>
                        <span class="bg-green-50 text-green-600 text-xs px-2 py-1 rounded">Maya</span>
                        <span class="bg-gray-50 text-gray-600 text-xs px-2 py-1 rounded">Bank QR</span>
                        <span class="bg-yellow-50 text-yellow-600 text-xs px-2 py-1 rounded">Cash</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('selected-date').addEventListener('change', function() {
    const date = this.value;
    fetch(`<?= url('api/courts/' . $court['id'] . '/slots') ?>?date=${date}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateSlots(data.slots);
            }
        });
});

function updateSlots(slots) {
    const container = document.getElementById('time-slots');
    container.innerHTML = '';
    
    if (slots.closed) {
        container.innerHTML = `
            <div class="col-span-full text-center text-gray-500 py-8">
                <i class="fas fa-ban text-4xl mb-2"></i>
                <p>Closed on this date</p>
            </div>`;
        return;
    }
    
    slots.forEach(slot => {
        const div = document.createElement('div');
        div.className = `time-slot text-center py-2 px-1 rounded-lg cursor-pointer transition ${
            slot.available 
                ? (slot.is_peak ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-800' : 'bg-green-100 hover:bg-green-200 text-green-800')
                : 'bg-red-100 text-red-400 cursor-not-allowed'
        }`;
        div.innerHTML = `
            <div class="text-sm font-medium">${formatTime(slot.start)}</div>
            ${slot.is_peak ? '<div class="text-xs">Peak</div>' : ''}
        `;
        container.appendChild(div);
    });
}

function formatTime(time) {
    const [hours] = time.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour = h % 12 || 12;
    return `${hour}:00 ${ampm}`;
}
</script>
