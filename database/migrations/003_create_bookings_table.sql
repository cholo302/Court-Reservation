-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    court_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_hours DECIMAL(3, 1) NOT NULL,
    is_half_court BOOLEAN DEFAULT FALSE,
    
    -- Pricing
    hourly_rate DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    downpayment_amount DECIMAL(10, 2) DEFAULT 0,
    balance_amount DECIMAL(10, 2) DEFAULT 0,
    
    -- Status
    status ENUM('pending', 'confirmed', 'paid', 'completed', 'cancelled', 'no_show', 'expired') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_type ENUM('online', 'venue') DEFAULT 'online',
    
    -- QR Codes
    entry_qr_code VARCHAR(255), -- QR code for entrance verification
    
    -- Additional Info
    player_count INT,
    notes TEXT,
    admin_notes TEXT,
    
    -- Timestamps
    confirmed_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    expires_at TIMESTAMP NULL, -- For pay-at-venue reservations
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_id) REFERENCES courts(id),
    INDEX idx_user (user_id),
    INDEX idx_court (court_id),
    INDEX idx_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_code (booking_code),
    INDEX idx_court_date (court_id, booking_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
