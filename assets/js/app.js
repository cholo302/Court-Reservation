/**
 * Court Reservation System - Main JavaScript
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeFlashMessages();
    initializeDatePicker();
    initializeSlotSelection();
    initializePriceCalculator();
    initializeFormValidation();
});

/**
 * Mobile Menu Toggle
 */
function initializeMobileMenu() {
    const menuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

/**
 * Auto-dismiss flash messages
 */
function initializeFlashMessages() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

/**
 * Date Picker Enhancement
 */
function initializeDatePicker() {
    const datePickers = document.querySelectorAll('input[type="date"]');
    
    datePickers.forEach(picker => {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        picker.setAttribute('min', today);
        
        // Set maximum date to 30 days from now
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        picker.setAttribute('max', maxDate.toISOString().split('T')[0]);
    });
}

/**
 * Time Slot Selection
 */
function initializeSlotSelection() {
    const slotContainer = document.getElementById('timeSlots');
    
    if (!slotContainer) return;
    
    slotContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot') && !e.target.classList.contains('unavailable')) {
            // Deselect all
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected', 'bg-ph-blue', 'text-white');
            });
            
            // Select clicked
            e.target.classList.add('selected', 'bg-ph-blue', 'text-white');
            
            // Update hidden input
            const input = document.getElementById('selectedSlot');
            if (input) {
                input.value = e.target.dataset.slot;
            }
            
            // Trigger price calculation
            calculatePrice();
        }
    });
}

/**
 * Load available time slots via AJAX
 */
function loadTimeSlots(courtId, date) {
    const container = document.getElementById('timeSlots');
    if (!container) return;
    
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-ph-blue"></i><p class="mt-2 text-gray-500">Loading available slots...</p></div>';
    
    fetch(`/Court-Reservation/api/courts/${courtId}/slots?date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTimeSlots(data.slots);
            } else {
                container.innerHTML = `<div class="text-center py-8 text-red-500">${data.message}</div>`;
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="text-center py-8 text-red-500">Failed to load time slots</div>';
        });
}

/**
 * Render time slots
 */
function renderTimeSlots(slots) {
    const container = document.getElementById('timeSlots');
    if (!container) return;
    
    if (slots.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No available slots for this date</div>';
        return;
    }
    
    let html = '<div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">';
    
    slots.forEach(slot => {
        const classes = slot.available 
            ? 'time-slot cursor-pointer hover:bg-ph-blue hover:text-white' 
            : 'time-slot unavailable bg-gray-200 text-gray-400 cursor-not-allowed';
        
        html += `
            <button type="button" 
                    class="${classes} border rounded-lg py-2 px-3 text-sm transition"
                    data-slot="${slot.start}"
                    data-price="${slot.price}"
                    ${!slot.available ? 'disabled' : ''}>
                ${slot.label}
                ${slot.is_peak ? '<span class="text-xs text-yellow-500 block">Peak</span>' : ''}
            </button>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Price Calculator
 */
function initializePriceCalculator() {
    const durationSelect = document.getElementById('duration');
    const dateInput = document.getElementById('bookingDate');
    
    if (durationSelect) {
        durationSelect.addEventListener('change', calculatePrice);
    }
    
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const courtId = document.getElementById('courtId')?.value;
            if (courtId) {
                loadTimeSlots(courtId, this.value);
            }
            calculatePrice();
        });
    }
}

/**
 * Calculate booking price
 */
function calculatePrice() {
    const courtId = document.getElementById('courtId')?.value;
    const date = document.getElementById('bookingDate')?.value;
    const duration = document.getElementById('duration')?.value || 1;
    const selectedSlot = document.querySelector('.time-slot.selected');
    
    if (!courtId || !date || !selectedSlot) return;
    
    const priceDisplay = document.getElementById('totalPrice');
    if (!priceDisplay) return;
    
    fetch('/Court-Reservation/api/calculate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            court_id: courtId,
            date: date,
            start_time: selectedSlot.dataset.slot,
            duration: duration
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            priceDisplay.textContent = '₱' + data.total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            
            // Update breakdown if exists
            const breakdown = document.getElementById('priceBreakdown');
            if (breakdown && data.breakdown) {
                let html = '';
                data.breakdown.forEach(item => {
                    html += `<div class="flex justify-between text-sm">
                        <span class="text-gray-600">${item.label}</span>
                        <span>₱${item.price.toLocaleString()}</span>
                    </div>`;
                });
                breakdown.innerHTML = html;
            }
        }
    });
}

/**
 * Form Validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
            });
            
            // Validate required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showFieldError(field, 'This field is required');
                }
            });
            
            // Validate email
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value && !isValidEmail(emailField.value)) {
                isValid = false;
                showFieldError(emailField, 'Please enter a valid email address');
            }
            
            // Validate phone
            const phoneField = form.querySelector('input[name="phone"]');
            if (phoneField && phoneField.value && !isValidPhone(phoneField.value)) {
                isValid = false;
                showFieldError(phoneField, 'Please enter a valid Philippine phone number');
            }
            
            // Validate password match
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="password_confirmation"]');
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                showFieldError(confirmPassword, 'Passwords do not match');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.add('border-red-500');
    
    const error = document.createElement('p');
    error.className = 'error-message text-red-500 text-sm mt-1';
    error.textContent = message;
    
    field.parentNode.appendChild(error);
}

/**
 * Validate email
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Validate Philippine phone number
 */
function isValidPhone(phone) {
    // Remove spaces and dashes
    phone = phone.replace(/[\s-]/g, '');
    return /^(\+63|0)?9\d{9}$/.test(phone);
}

/**
 * Payment method selection
 */
function selectPaymentMethod(method) {
    // Update hidden input
    document.getElementById('paymentMethod').value = method;
    
    // Update UI
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('border-ph-blue', 'bg-blue-50');
        option.classList.add('border-gray-200');
    });
    
    const selected = document.querySelector(`.payment-option[data-method="${method}"]`);
    if (selected) {
        selected.classList.remove('border-gray-200');
        selected.classList.add('border-ph-blue', 'bg-blue-50');
    }
    
    // Show/hide QR code based on method
    const qrSection = document.getElementById('qrCodeSection');
    const uploadSection = document.getElementById('uploadSection');
    
    if (qrSection) {
        if (method === 'cash') {
            qrSection.classList.add('hidden');
            if (uploadSection) uploadSection.classList.add('hidden');
        } else {
            qrSection.classList.remove('hidden');
            if (uploadSection) uploadSection.classList.remove('hidden');
            loadPaymentQR(method);
        }
    }
}

/**
 * Load payment QR code
 */
function loadPaymentQR(method) {
    const qrImage = document.getElementById('paymentQR');
    const amount = document.getElementById('paymentAmount')?.value;
    const bookingCode = document.getElementById('bookingCode')?.value;
    
    if (!qrImage || !amount) return;
    
    // In production, this would load actual payment provider QR
    // For demo, we'll use a placeholder
    let qrData = `${method.toUpperCase()}:${bookingCode}:${amount}`;
    qrImage.src = `https://quickchart.io/qr?text=${encodeURIComponent(qrData)}&size=250`;
}

/**
 * Preview uploaded image
 */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

/**
 * Check payment status (polling)
 */
function checkPaymentStatus(bookingId) {
    const statusEl = document.getElementById('paymentStatus');
    
    const interval = setInterval(() => {
        fetch(`/Court-Reservation/api/bookings/${bookingId}/payment-status`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'paid') {
                    clearInterval(interval);
                    if (statusEl) {
                        statusEl.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Payment Verified!</span>';
                    }
                    // Redirect to confirmation
                    setTimeout(() => {
                        window.location.href = `/Court-Reservation/bookings/${bookingId}`;
                    }, 2000);
                }
            });
    }, 5000); // Check every 5 seconds
    
    // Stop after 10 minutes
    setTimeout(() => clearInterval(interval), 600000);
}

/**
 * Confirm booking cancellation
 */
function confirmCancel(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        document.getElementById(`cancelForm-${bookingId}`).submit();
    }
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!');
    }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('Copied to clipboard!');
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}
