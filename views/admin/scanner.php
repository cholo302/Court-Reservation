<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">QR Code Scanner</h1>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Scanner Area -->
        <div class="mb-6">
            <div id="reader" class="w-full aspect-square bg-gray-100 rounded-lg overflow-hidden"></div>
        </div>
        
        <!-- Manual Entry -->
        <div class="mb-6">
            <p class="text-center text-gray-500 mb-3">Or enter booking code manually</p>
            <form id="manualForm" class="flex space-x-2">
                <input type="text" id="manualCode" placeholder="Enter booking code" 
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                       pattern="[A-Z0-9-]+" maxlength="20">
                <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Verify
                </button>
            </form>
        </div>
        
        <!-- Result Area -->
        <div id="resultArea" class="hidden">
            <div id="resultContent"></div>
        </div>
        
        <!-- Instructions -->
        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
            <h4 class="font-semibold mb-2">Instructions:</h4>
            <ol class="list-decimal list-inside space-y-1">
                <li>Point the camera at the customer's QR code</li>
                <li>The system will automatically scan and verify</li>
                <li>Valid entries will show booking details</li>
                <li>Click "Confirm Entry" to mark attendance</li>
            </ol>
        </div>
    </div>
    
    <!-- Recent Scans -->
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Entries Today</h3>
        
        <div id="recentScans" class="space-y-3">
            <?php if (empty($recentScans)): ?>
            <p class="text-center text-gray-500 py-4">No entries recorded today</p>
            <?php else: ?>
            <?php foreach ($recentScans as $scan): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium"><?= $scan['booking_code'] ?></p>
                        <p class="text-sm text-gray-500"><?= $scan['user_name'] ?> - <?= $scan['court_name'] ?></p>
                    </div>
                </div>
                <span class="text-sm text-gray-500"><?= date('g:i A', strtotime($scan['scanned_at'])) ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const html5QrCode = new Html5Qrcode("reader");

const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    html5QrCode.pause();
    verifyBooking(decodedText);
};

const config = { fps: 10, qrbox: { width: 250, height: 250 } };

Html5Qrcode.getCameras().then(devices => {
    if (devices && devices.length) {
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            qrCodeSuccessCallback
        ).catch(err => {
            console.error("Camera start failed:", err);
            document.getElementById('reader').innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-camera-slash text-4xl mb-2"></i>
                        <p>Camera not available</p>
                        <p class="text-sm">Use manual entry below</p>
                    </div>
                </div>
            `;
        });
    }
}).catch(err => console.error(err));

document.getElementById('manualForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('manualCode').value.trim();
    if (code) verifyBooking(code);
});

function verifyBooking(code) {
    const resultArea = document.getElementById('resultArea');
    const resultContent = document.getElementById('resultContent');
    
    resultArea.classList.remove('hidden');
    resultContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-ph-blue mb-2"></i>
            <p>Verifying...</p>
        </div>
    `;
    
    fetch('<?= url('api/verify-entry') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: code, _token: '<?= csrf_token() ?>' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultContent.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-check text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-green-700">Valid Entry</h3>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Booking Code:</span>
                            <span class="font-medium">${data.booking.code}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Customer:</span>
                            <span class="font-medium">${data.booking.user_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Court:</span>
                            <span class="font-medium">${data.booking.court_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">${data.booking.time_slot}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Players:</span>
                            <span class="font-medium">${data.booking.num_players}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex space-x-2">
                        <button onclick="confirmEntry('${data.booking.id}')" 
                                class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Confirm Entry
                        </button>
                        <button onclick="resetScanner()" 
                                class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition">
                            Scan Again
                        </button>
                    </div>
                </div>
            `;
        } else {
            resultContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-times text-red-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-red-700 mb-2">Invalid Entry</h3>
                    <p class="text-red-600">${data.message}</p>
                    <button onclick="resetScanner()" 
                            class="mt-4 bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                        Try Again
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        resultContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                <p class="text-red-600">Error verifying code. Please try again.</p>
                <button onclick="resetScanner()" 
                        class="mt-4 bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                    Try Again
                </button>
            </div>
        `;
    });
}

function confirmEntry(bookingId) {
    fetch('<?= url('api/confirm-entry') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId, _token: '<?= csrf_token() ?>' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('resultContent').innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check-double text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-green-700">Entry Confirmed!</h3>
                    <p class="text-gray-600 mt-2">Customer may now proceed to the court.</p>
                    <button onclick="resetScanner()" 
                            class="mt-4 bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Scan Next
                    </button>
                </div>
            `;
            // Reload recent scans
            location.reload();
        }
    });
}

function resetScanner() {
    document.getElementById('resultArea').classList.add('hidden');
    document.getElementById('manualCode').value = '';
    try {
        html5QrCode.resume();
    } catch(e) {}
}
</script>
