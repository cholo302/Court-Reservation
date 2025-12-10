-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    court_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    images JSON,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_id) REFERENCES courts(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    UNIQUE KEY unique_booking_review (booking_id),
    INDEX idx_court (court_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL, -- booking_confirmed, payment_received, reminder, etc.
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON, -- Additional data like booking_id, etc.
    channel ENUM('web', 'sms', 'email', 'messenger') DEFAULT 'web',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log Table (for admin audit)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    model_type VARCHAR(100), -- 'booking', 'payment', 'user', etc.
    model_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_model (model_type, model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Court Schedules (Operating Hours Exceptions)
CREATE TABLE IF NOT EXISTS court_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    court_id INT NOT NULL,
    date DATE NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE,
    open_time TIME,
    close_time TIME,
    reason VARCHAR(255), -- "Maintenance", "Holiday", etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (court_id) REFERENCES courts(id),
    UNIQUE KEY unique_court_date (court_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Player Finder (Community Feature)
CREATE TABLE IF NOT EXISTS player_lookups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    court_type_id INT NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    preferred_city VARCHAR(100),
    skill_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'intermediate',
    players_needed INT DEFAULT 1,
    message TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_type_id) REFERENCES court_types(id),
    INDEX idx_active (is_active),
    INDEX idx_type (court_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table (for admin configuration)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (`key`, value, type, description) VALUES
('site_name', 'Court Reservation PH', 'string', 'Website name'),
('reservation_expiry_minutes', '30', 'number', 'Minutes before pay-at-venue reservation expires'),
('downpayment_percent', '50', 'number', 'Default downpayment percentage'),
('peak_hours_start', '18', 'number', 'Peak hour start (24h format)'),
('peak_hours_end', '21', 'number', 'Peak hour end (24h format)'),
('peak_hour_multiplier', '1.25', 'number', 'Price multiplier during peak hours'),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'),
('sms_enabled', 'true', 'boolean', 'Enable SMS notifications'),
('email_enabled', 'true', 'boolean', 'Enable email notifications')
ON DUPLICATE KEY UPDATE id=id;
