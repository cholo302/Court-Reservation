-- Court Types Table
CREATE TABLE IF NOT EXISTS court_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default court types (Philippine sports facilities)
INSERT OR IGNORE INTO court_types (name, slug, icon, description) VALUES 
('Basketball Court', 'basketball', 'basketball', 'Full court and half court basketball facilities'),
('Badminton Court', 'badminton', 'badminton', 'Indoor badminton courts with proper flooring'),
('Tennis Court', 'tennis', 'tennis', 'Outdoor and indoor tennis courts'),
('Volleyball Court', 'volleyball', 'volleyball', 'Indoor and beach volleyball courts'),
('Futsal Court', 'futsal', 'futsal', 'Indoor futsal/football courts'),
('Covered Court', 'covered', 'building', 'Multipurpose covered courts'),
('Barangay Gym', 'gym', 'gym', 'Local barangay gymnasium facilities'),
('Swimming Pool', 'swimming', 'swimming', 'Swimming pool facilities');

-- Courts Table
CREATE TABLE IF NOT EXISTS courts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    court_type_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    barangay VARCHAR(100),
    city VARCHAR(100),
    province VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    hourly_rate DECIMAL(10, 2) NOT NULL,
    peak_hour_rate DECIMAL(10, 2),
    half_court_rate DECIMAL(10, 2),
    downpayment_percent INTEGER DEFAULT 50,
    min_booking_hours INTEGER DEFAULT 1,
    max_booking_hours INTEGER DEFAULT 4,
    capacity INTEGER,
    amenities TEXT,
    rules TEXT,
    images TEXT,
    thumbnail VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0,
    total_reviews INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    requires_approval INTEGER DEFAULT 0,
    operating_hours TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (court_type_id) REFERENCES court_types(id)
);

CREATE INDEX IF NOT EXISTS idx_courts_type ON courts(court_type_id);
CREATE INDEX IF NOT EXISTS idx_courts_city ON courts(city);
CREATE INDEX IF NOT EXISTS idx_courts_active ON courts(is_active);

-- Insert sample courts
INSERT OR IGNORE INTO courts (court_type_id, name, description, location, barangay, city, province, hourly_rate, peak_hour_rate, capacity, amenities, rules, thumbnail) VALUES 
(2, 'QC Badminton Center - Court A', 'Professional badminton court with wooden flooring and proper lighting', 'Timog Avenue', 'Sacred Heart', 'Quezon City', 'Metro Manila', 300.00, 400.00, 4, '[\"parking\", \"shower\", \"aircon\", \"lights\"]', 'Proper badminton shoes required. No food inside court.', '/images/courts/badminton-1.jpg'),
(2, 'QC Badminton Center - Court B', 'Professional badminton court with wooden flooring and proper lighting', 'Timog Avenue', 'Sacred Heart', 'Quezon City', 'Metro Manila', 300.00, 400.00, 4, '[\"parking\", \"shower\", \"aircon\", \"lights\"]', 'Proper badminton shoes required. No food inside court.', '/images/courts/badminton-2.jpg'),
(1, 'Brgy. San Antonio Basketball Court', 'Full court outdoor basketball with lights', 'San Antonio Street', 'San Antonio', 'Makati City', 'Metro Manila', 500.00, 700.00, 20, '[\"lights\", \"parking\"]', 'Respect barangay curfew (10PM). Proper attire required.', '/images/courts/basketball-1.jpg'),
(1, 'Makati Hoops Arena - Full Court', 'Indoor air-conditioned full court basketball', 'Jupiter Street', 'Bel-Air', 'Makati City', 'Metro Manila', 1500.00, 2000.00, 20, '[\"parking\", \"shower\", \"locker\", \"lights\", \"aircon\"]', 'Rubber shoes only. No food or drinks on court.', '/images/courts/basketball-2.jpg'),
(5, 'Manila Futsal Club', 'Premium futsal court with artificial turf', 'Taft Avenue', 'Malate', 'Manila', 'Metro Manila', 800.00, 1000.00, 14, '[\"parking\", \"shower\", \"locker\", \"lights\"]', 'Futsal shoes only. Shin guards recommended.', '/images/courts/futsal-1.jpg'),
(6, 'Barangay Covered Court - Pasig', 'Multipurpose covered court for various sports', 'Pasig Boulevard', 'Kapitolyo', 'Pasig City', 'Metro Manila', 400.00, 500.00, 30, '[\"lights\", \"parking\"]', 'First come first served for equipment. Follow barangay rules.', '/images/courts/covered-1.jpg'),
(3, 'Rizal Tennis Club', 'Clay tennis court with professional maintenance', 'Vito Cruz', 'Malate', 'Manila', 'Metro Manila', 600.00, 800.00, 4, '[\"parking\", \"shower\", \"locker\", \"lights\"]', 'Tennis shoes only. No metal spikes.', '/images/courts/tennis-1.jpg');
