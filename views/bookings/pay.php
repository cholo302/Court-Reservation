<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Complete Payment</h1>
        <p class="text-gray-500">Booking #<?= $booking['booking_code'] ?></p>
    </div>
    
    <!-- Booking Summary -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-4">Booking Details</h2>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Court</span>
                <p class="font-medium"><?= $booking['court_name'] ?></p>
            </div>
            <div>
                <span class="text-gray-500">Date</span>
                <p class="font-medium"><?= date('M d, Y (D)', strtotime($booking['booking_date'])) ?></p>
            </div>
            <div>
                <span class="text-gray-500">Time</span>
                <p class="font-medium"><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></p>
            </div>
            <div>
                <span class="text-gray-500">Duration</span>
                <p class="font-medium"><?= $booking['duration_hours'] ?> hour(s)</p>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold">Total Amount</span>
            <span class="text-2xl font-bold text-ph-blue"><?= formatPrice($booking['total_amount']) ?></span>
        </div>
        
        <?php if ($booking['downpayment_amount'] > 0): ?>
        <div class="bg-yellow-50 rounded-lg p-3 mt-4">
            <div class="flex justify-between text-sm">
                <span>Downpayment (<?= round(($booking['downpayment_amount'] / $booking['total_amount']) * 100) ?>%)</span>
                <span class="font-semibold"><?= formatPrice($booking['downpayment_amount']) ?></span>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>Balance (Pay at venue)</span>
                <span><?= formatPrice($booking['balance_amount']) ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Payment Methods -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-4">Select Payment Method</h2>
        
        <div class="space-y-3">
            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-ph-blue transition">
                <input type="radio" name="payment_method" value="gcash" checked class="h-5 w-5 text-ph-blue">
                <div class="ml-4 flex items-center flex-1">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-blue-600 font-bold text-lg">G</span>
                    </div>
                    <div>
                        <p class="font-medium">GCash</p>
                        <p class="text-sm text-gray-500">Scan QR code to pay</p>
                    </div>
                </div>
            </label>
            
           
    
    <!-- Payment Type Selection -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-4">Payment Amount</h2>
        
        <div class="grid grid-cols-2 gap-4">
            <label class="flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer hover:border-ph-blue transition">
                <input type="radio" name="payment_type" value="full" checked class="sr-only peer">
                <div class="text-center peer-checked:text-ph-blue">
                    <p class="font-bold text-lg"><?= formatPrice($booking['total_amount']) ?></p>
                    <p class="text-sm">Full Payment</p>
                </div>
                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-ph-blue rounded-lg pointer-events-none"></div>
            </label>
            
            <?php if ($booking['downpayment_amount'] > 0 && $booking['downpayment_amount'] < $booking['total_amount']): ?>
            <label class="flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer hover:border-ph-blue transition">
                <input type="radio" name="payment_type" value="downpayment" class="sr-only peer">
                <div class="text-center peer-checked:text-ph-blue">
                    <p class="font-bold text-lg"><?= formatPrice($booking['downpayment_amount']) ?></p>
                    <p class="text-sm">Downpayment Only</p>
                </div>
                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-ph-blue rounded-lg pointer-events-none"></div>
            </label>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Generate QR Button -->
    <button type="button" id="generate-qr-btn" class="w-full bg-ph-blue text-white py-4 rounded-xl font-semibold hover:bg-blue-800 transition mb-6">
        <i class="fas fa-qrcode mr-2"></i>Generate Payment QR Code
    </button>
    
    <!-- QR Code Display (Hidden by default) -->
    <div id="qr-section" class="hidden">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 text-center">
            <h3 class="font-semibold text-gray-900 mb-4">Scan to Pay</h3>
            
            <div class="bg-gray-100 rounded-xl p-4 inline-block mb-4">
                <img id="qr-image" src="" alt="Payment QR Code" class="w-64 h-64">
            </div>
            
            <p class="text-lg font-bold text-ph-blue mb-2">
                Amount: <span id="qr-amount"></span>
            </p>
            <p class="text-sm text-gray-500 mb-4">
                Reference: <span id="payment-ref" class="font-mono"></span>
            </p>
            
            <!-- Instructions -->
            <div id="payment-instructions" class="bg-blue-50 rounded-lg p-4 text-left mb-4">
                <h4 class="font-semibold text-blue-800 mb-2">How to Pay:</h4>
                <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                </ol>
            </div>
            
            <p class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                QR Code expires in: <span id="qr-timer" class="font-semibold">60:00</span>
            </p>
        </div>
        
        <!-- Upload Proof -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fas fa-upload mr-2"></i>Upload Payment Proof
            </h3>
            
            <p class="text-sm text-gray-500 mb-4">
                After completing payment, upload a screenshot of your payment confirmation.
            </p>
            
            <form id="proof-form" enctype="multipart/form-data">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-ph-blue transition cursor-pointer" id="upload-area">
                    <input type="file" name="proof" id="proof-input" accept="image/*" class="hidden">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Click or drag image here</p>
                    <p class="text-xs text-gray-400">JPG, PNG up to 5MB</p>
                </div>
                
                <div id="preview-area" class="hidden mt-4">
                    <img id="preview-image" src="" alt="Preview" class="w-full max-h-64 object-contain rounded-lg">
                    <button type="button" id="remove-preview" class="text-red-500 text-sm mt-2">
                        <i class="fas fa-times mr-1"></i>Remove
                    </button>
                </div>
                
                <button type="submit" id="upload-btn" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition mt-4 disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-check mr-2"></i>Submit Payment Proof
                </button>
            </form>
        </div>
    </div>
    
    <!-- Or Pay at Venue -->
    <div class="text-center">
        <a href="<?= url('bookings/' . $booking['id']) ?>" class="text-gray-500 hover:text-ph-blue">
            <i class="fas fa-arrow-left mr-1"></i>Back to Booking Details
        </a>
    </div>
</div>

<script>
let paymentReference = null;
let timerInterval = null;

document.getElementById('generate-qr-btn').addEventListener('click', function() {
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    const type = document.querySelector('input[name="payment_type"]:checked').value;
    
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    
    fetch(`<?= url('api/payments/create/' . $booking['id']) ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `payment_method=${method}&payment_type=${type}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            paymentReference = data.payment_reference;
            
            document.getElementById('qr-image').src = data.qr_data.qr_image;
            document.getElementById('qr-amount').textContent = data.formatted_amount;
            document.getElementById('payment-ref').textContent = data.payment_reference;
            
            // Show instructions
            const instructionsList = document.querySelector('#payment-instructions ol');
            instructionsList.innerHTML = data.qr_data.instructions.map(i => `<li>${i}</li>`).join('');
            
            document.getElementById('qr-section').classList.remove('hidden');
            document.getElementById('generate-qr-btn').classList.add('hidden');
            
            // Start timer
            startTimer(60 * 60); // 1 hour
        } else {
            alert(data.error || 'Failed to generate QR code');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-qrcode mr-2"></i>Generate Payment QR Code';
        }
    });
});

function startTimer(seconds) {
    const timerEl = document.getElementById('qr-timer');
    
    timerInterval = setInterval(() => {
        seconds--;
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        timerEl.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
        
        if (seconds <= 0) {
            clearInterval(timerInterval);
            timerEl.textContent = 'Expired';
            timerEl.classList.add('text-red-500');
        }
    }, 1000);
}

// File upload handling
const uploadArea = document.getElementById('upload-area');
const proofInput = document.getElementById('proof-input');
const previewArea = document.getElementById('preview-area');
const previewImage = document.getElementById('preview-image');
const uploadBtn = document.getElementById('upload-btn');

uploadArea.addEventListener('click', () => proofInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-ph-blue', 'bg-blue-50');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('border-ph-blue', 'bg-blue-50');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-ph-blue', 'bg-blue-50');
    if (e.dataTransfer.files.length) {
        proofInput.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files[0]);
    }
});

proofInput.addEventListener('change', function() {
    if (this.files.length) {
        showPreview(this.files[0]);
    }
});

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        previewArea.classList.remove('hidden');
        uploadArea.classList.add('hidden');
        uploadBtn.disabled = false;
    };
    reader.readAsDataURL(file);
}

document.getElementById('remove-preview').addEventListener('click', () => {
    previewArea.classList.add('hidden');
    uploadArea.classList.remove('hidden');
    proofInput.value = '';
    uploadBtn.disabled = true;
});

document.getElementById('proof-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!paymentReference) {
        alert('Please generate QR code first');
        return;
    }
    
    const formData = new FormData();
    formData.append('proof', proofInput.files[0]);
    
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    
    fetch(`<?= url('api/payments/') ?>${paymentReference}/upload-proof`, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Payment proof uploaded successfully! Please wait for verification.');
            window.location.href = '<?= url('bookings/' . $booking['id']) ?>';
        } else {
            alert(data.error || 'Failed to upload proof');
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Submit Payment Proof';
        }
    });
});
</script>
