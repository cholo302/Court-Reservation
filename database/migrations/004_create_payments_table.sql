-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    payment_reference VARCHAR(50) UNIQUE NOT NULL,
    booking_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    
    -- Payment Details
    amount DECIMAL(10, 2) NOT NULL,
    payment_method TEXT NOT NULL CHECK(payment_method IN ('gcash', 'maya', 'bank_qr', 'cash', 'card')),
    payment_type TEXT DEFAULT 'full' CHECK(payment_type IN ('full', 'downpayment', 'balance')),
    
    -- QR Payment Info
    qr_code_url VARCHAR(255),
    qr_code_data TEXT,
    checkout_url VARCHAR(500),
    
    -- Transaction Details
    transaction_id VARCHAR(255),
    gateway_response TEXT,
    
    -- Proof of Payment
    proof_screenshot VARCHAR(255),
    
    -- Status
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'processing', 'paid', 'failed', 'refunded', 'expired')),
    verified_by INTEGER,
    verified_at TIMESTAMP NULL,
    
    -- Refund Info
    refund_amount DECIMAL(10, 2),
    refund_reason TEXT,
    refunded_at TIMESTAMP NULL,
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id)
);

CREATE INDEX IF NOT EXISTS idx_payments_booking ON payments(booking_id);
CREATE INDEX IF NOT EXISTS idx_payments_user ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payments_reference ON payments(payment_reference);
