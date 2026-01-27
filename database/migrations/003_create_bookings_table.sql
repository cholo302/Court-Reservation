-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INTEGER NOT NULL,
    court_id INTEGER NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_hours DECIMAL(3, 1) NOT NULL,
    is_half_court INTEGER DEFAULT 0,
    
    -- Pricing
    price_per_hour DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    downpayment_amount DECIMAL(10, 2) DEFAULT 0,
    balance_amount DECIMAL(10, 2) DEFAULT 0,
    
    -- Status
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'confirmed', 'paid', 'completed', 'cancelled', 'no_show', 'expired')),
    payment_status TEXT DEFAULT 'unpaid' CHECK(payment_status IN ('unpaid', 'partial', 'paid', 'refunded')),
    payment_type TEXT DEFAULT 'online' CHECK(payment_type IN ('online', 'venue')),
    
    -- QR Codes
    entry_qr_code VARCHAR(255),
    scanned_at TIMESTAMP NULL,
    
    -- Additional Info
    player_count INTEGER,
    notes TEXT,
    admin_notes TEXT,
    
    -- Timestamps
    confirmed_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_id) REFERENCES courts(id)
);

CREATE INDEX IF NOT EXISTS idx_bookings_user ON bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_court ON bookings(court_id);
CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(booking_date);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_bookings_code ON bookings(booking_code);
CREATE INDEX IF NOT EXISTS idx_bookings_court_date ON bookings(court_id, booking_date);
