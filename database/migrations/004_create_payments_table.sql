-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_reference VARCHAR(50) UNIQUE NOT NULL,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    
    -- Payment Details
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('gcash', 'maya', 'bank_qr', 'cash', 'card') NOT NULL,
    payment_type ENUM('full', 'downpayment', 'balance') DEFAULT 'full',
    
    -- QR Payment Info
    qr_code_url VARCHAR(255), -- Dynamic QR code for payment
    qr_code_data TEXT, -- Raw QR data
    checkout_url VARCHAR(500), -- PayMongo/Maya checkout URL
    
    -- Transaction Details
    transaction_id VARCHAR(255), -- From payment gateway
    gateway_response JSON, -- Full response from payment gateway
    
    -- Proof of Payment
    proof_screenshot VARCHAR(255),
    
    -- Status
    status ENUM('pending', 'processing', 'paid', 'failed', 'refunded', 'expired') DEFAULT 'pending',
    verified_by INT, -- Admin who verified
    verified_at TIMESTAMP NULL,
    
    -- Refund Info
    refund_amount DECIMAL(10, 2),
    refund_reason TEXT,
    refunded_at TIMESTAMP NULL,
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_booking (booking_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_reference (payment_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
