<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="<?= url('/') ?>" class="hover:text-ph-blue">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="<?= url('bookings') ?>" class="hover:text-ph-blue">My Bookings</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">#<?= $booking['booking_code'] ?></li>
        </ol>
    </nav>
    
    <!-- Status Banner -->
    <?php
    $statusConfig = match($booking['status']) {
        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock', 'label' => 'Pending Payment'],
        'confirmed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle', 'label' => 'Confirmed'],
        'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-double', 'label' => 'Paid'],
        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-flag-checkered', 'label' => 'Completed'],
        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'label' => 'Cancelled'],
        'no_show' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user-slash', 'label' => 'No Show'],
        'expired' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-hourglass-end', 'label' => 'Expired'],
        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-question-circle', 'label' => ucfirst($booking['status'])]
    };
    ?>
    <div class="<?= $statusConfig['bg'] ?> <?= $statusConfig['text'] ?> rounded-xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas <?= $statusConfig['icon'] ?> text-2xl mr-3"></i>
            <div>
                <p class="font-semibold"><?= $statusConfig['label'] ?></p>
                <p class="text-sm">Booking #<?= $booking['booking_code'] ?></p>
            </div>
        </div>
        
        <?php if ($booking['status'] === 'pending'): ?>
        <a href="<?= url('bookings/' . $booking['id'] . '/pay') ?>" class="bg-ph-blue text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
            <i class="fas fa-credit-card mr-1"></i>Pay Now
        </a>
        <?php endif; ?>
    </div>
    
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Court Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Court Details</h2>
                
                <div class="flex items-start">
                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                        <?php if ($booking['thumbnail']): ?>
                        <img src="<?= url($booking['thumbnail']) ?>" alt="" class="w-full h-full object-cover rounded-lg">
                        <?php else: ?>
                        <i class="fas fa-basketball-ball text-gray-400 text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <span class="text-xs text-ph-blue font-semibold uppercase"><?= $booking['court_type'] ?></span>
                        <h3 class="font-semibold text-lg"><?= $booking['court_name'] ?></h3>
                        <p class="text-gray-500 text-sm">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?= $booking['location'] ?>, <?= $booking['city'] ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Schedule -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Schedule</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-lg"><?= date('F d, Y', strtotime($booking['booking_date'])) ?></p>
                        <p class="text-sm text-gray-500"><?= date('l', strtotime($booking['booking_date'])) ?></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Time</p>
                        <p class="font-semibold text-lg">
                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?>
                        </p>
                        <p class="text-sm text-gray-500"><?= $booking['duration_hours'] ?> hour(s)</p>
                    </div>
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Payment Information</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Hourly Rate</span>
                        <span><?= formatPrice($booking['hourly_rate']) ?>/hr</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Duration</span>
                        <span><?= $booking['duration_hours'] ?> hour(s)</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-ph-blue"><?= formatPrice($booking['total_amount']) ?></span>
                    </div>
                    
                    <?php if ($booking['downpayment_amount'] > 0): ?>
                    <div class="bg-gray-50 rounded-lg p-3 mt-2">
                        <div class="flex justify-between text-sm">
                            <span>Downpayment</span>
                            <span class="font-medium"><?= formatPrice($booking['downpayment_amount']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Balance (at venue)</span>
                            <span><?= formatPrice($booking['balance_amount']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center mt-4">
                        <span class="text-sm text-gray-500 mr-2">Payment Status:</span>
                        <?php
                        $paymentStatusConfig = match($booking['payment_status']) {
                            'unpaid' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                            'partial' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                            'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            'refunded' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                        };
                        ?>
                        <span class="<?= $paymentStatusConfig['bg'] ?> <?= $paymentStatusConfig['text'] ?> px-3 py-1 rounded-full text-sm font-medium">
                            <?= ucfirst($booking['payment_status']) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Payment History -->
                <?php if (!empty($payments)): ?>
                <div class="mt-6 pt-4 border-t">
                    <h3 class="font-medium text-gray-900 mb-3">Payment History</h3>
                    <div class="space-y-2">
                        <?php foreach ($payments as $payment): ?>
                        <div class="flex items-center justify-between text-sm bg-gray-50 rounded-lg p-3">
                            <div>
                                <span class="font-medium"><?= $payment['payment_reference'] ?></span>
                                <span class="text-gray-500 ml-2"><?= ucfirst($payment['payment_method']) ?></span>
                            </div>
                            <div class="text-right">
                                <span class="font-medium"><?= formatPrice($payment['amount']) ?></span>
                                <span class="block text-xs text-gray-500"><?= ucfirst($payment['status']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Actions -->
            <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Actions</h2>
                
                <div class="flex space-x-4">
                    <?php if ($booking['status'] === 'pending'): ?>
                    <a href="<?= url('bookings/' . $booking['id'] . '/pay') ?>" class="flex-1 bg-ph-blue text-white py-3 rounded-lg font-semibold text-center hover:bg-blue-800 transition">
                        <i class="fas fa-credit-card mr-2"></i>Complete Payment
                    </a>
                    <?php endif; ?>
                    
                    <form action="<?= url('bookings/' . $booking['id'] . '/cancel') ?>" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-red-100 text-red-600 py-3 rounded-lg font-semibold hover:bg-red-200 transition">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- QR Code & Entry Pass -->
        <div class="md:col-span-1">
            <?php if (in_array($booking['status'], ['confirmed', 'paid', 'completed'])): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 text-center sticky top-24">
                <h2 class="font-semibold text-gray-900 mb-4">Entry Pass</h2>
                
                <div class="bg-gray-100 rounded-xl p-4 mb-4">
                    <!-- QR Code placeholder - in production, generate actual QR -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($booking['entry_qr_code'] ?? $booking['booking_code']) ?>" 
                        alt="Entry QR Code" class="w-48 h-48 mx-auto">
                </div>
                
                <p class="text-lg font-bold text-ph-blue mb-2"><?= $booking['booking_code'] ?></p>
                <p class="text-sm text-gray-500 mb-4">Show this QR code at the venue entrance</p>
                
                <div class="text-left bg-yellow-50 rounded-lg p-3 text-sm">
                    <p class="font-medium text-yellow-800 mb-1">
                        <i class="fas fa-info-circle mr-1"></i>Important
                    </p>
                    <ul class="text-yellow-700 space-y-1">
                        <li>• Arrive 10 minutes before your slot</li>
                        <li>• Show this QR to staff</li>
                        <li>• Bring valid ID</li>
                    </ul>
                </div>
                
                <button onclick="window.print()" class="mt-4 w-full border border-gray-300 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                    <i class="fas fa-print mr-2"></i>Print Pass
                </button>
            </div>
            <?php else: ?>
            <div class="bg-gray-50 rounded-xl p-6 text-center">
                <i class="fas fa-qrcode text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500">Entry QR code will appear after payment confirmation</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
