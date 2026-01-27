-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    court_id INTEGER NOT NULL,
    booking_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    images TEXT,
    is_approved INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_id) REFERENCES courts(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    UNIQUE (booking_id)
);

CREATE INDEX IF NOT EXISTS idx_reviews_court ON reviews(court_id);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data TEXT,
    channel TEXT DEFAULT 'web' CHECK(channel IN ('web', 'sms', 'email', 'messenger')),
    is_read INTEGER DEFAULT 0,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_read ON notifications(is_read);

-- Activity Log Table (for admin audit)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    model_type VARCHAR(100),
    model_id INTEGER,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_activity_logs_action ON activity_logs(action);
CREATE INDEX IF NOT EXISTS idx_activity_logs_model ON activity_logs(model_type, model_id);

-- Court Schedules (Operating Hours Exceptions)
CREATE TABLE IF NOT EXISTS court_schedules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    court_id INTEGER NOT NULL,
    date DATE NOT NULL,
    is_closed INTEGER DEFAULT 0,
    open_time TIME,
    close_time TIME,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (court_id) REFERENCES courts(id),
    UNIQUE (court_id, date)
);

-- Player Finder (Community Feature)
CREATE TABLE IF NOT EXISTS player_lookups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    court_type_id INTEGER NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    preferred_city VARCHAR(100),
    skill_level TEXT DEFAULT 'intermediate' CHECK(skill_level IN ('beginner', 'intermediate', 'advanced')),
    players_needed INTEGER DEFAULT 1,
    message TEXT,
    is_active INTEGER DEFAULT 1,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (court_type_id) REFERENCES court_types(id)
);

CREATE INDEX IF NOT EXISTS idx_player_lookups_active ON player_lookups(is_active);
CREATE INDEX IF NOT EXISTS idx_player_lookups_type ON player_lookups(court_type_id);

-- Settings Table (for admin configuration)
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    type TEXT DEFAULT 'string' CHECK(type IN ('string', 'number', 'boolean', 'json')),
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Blacklist Table
CREATE TABLE IF NOT EXISTS user_blacklists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    reason TEXT,
    blacklisted_by INTEGER,
    blacklisted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (blacklisted_by) REFERENCES users(id),
    UNIQUE (user_id)
);

CREATE INDEX IF NOT EXISTS idx_user_blacklists_user ON user_blacklists(user_id);

-- Insert default settings
INSERT OR IGNORE INTO settings (key, value, type, description) VALUES
('site_name', 'Court Reservation PH', 'string', 'Website name'),
('reservation_expiry_minutes', '30', 'number', 'Minutes before pay-at-venue reservation expires'),
('downpayment_percent', '50', 'number', 'Default downpayment percentage'),
('peak_hours_start', '18', 'number', 'Peak hour start (24h format)'),
('peak_hours_end', '21', 'number', 'Peak hour end (24h format)'),
('peak_hour_multiplier', '1.25', 'number', 'Price multiplier during peak hours'),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'),
('sms_enabled', 'true', 'boolean', 'Enable SMS notifications'),
('email_enabled', 'true', 'boolean', 'Enable email notifications');
