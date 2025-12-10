<?php
/**
 * Booking Model
 */
class Booking extends Model {
    protected $table = 'bookings';
    protected $fillable = [
        'booking_code', 'user_id', 'court_id', 'booking_date', 'start_time',
        'end_time', 'duration_hours', 'is_half_court', 'hourly_rate', 
        'total_amount', 'downpayment_amount', 'balance_amount', 'status',
        'payment_status', 'payment_type', 'entry_qr_code', 'player_count',
        'notes', 'admin_notes', 'confirmed_at', 'paid_at', 'cancelled_at',
        'cancellation_reason', 'expires_at'
    ];
    
    public function generateBookingCode() {
        $prefix = 'CR';
        $date = date('ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        return $prefix . $date . $random;
    }
    
    public function createBooking($data) {
        $data['booking_code'] = $this->generateBookingCode();
        $data['entry_qr_code'] = $this->generateQRCode($data['booking_code']);
        
        // Set expiry for pay-at-venue reservations
        if (isset($data['payment_type']) && $data['payment_type'] === 'venue') {
            $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . RESERVATION_EXPIRY_MINUTES . ' minutes'));
        }
        
        return $this->create($data);
    }
    
    public function findByCode($code) {
        $stmt = $this->db->prepare(
            "SELECT b.*, c.name as court_name, c.location, c.city, 
                    ct.name as court_type, u.name as user_name, u.email, u.phone
             FROM bookings b 
             JOIN courts c ON b.court_id = c.id 
             JOIN court_types ct ON c.court_type_id = ct.id
             JOIN users u ON b.user_id = u.id
             WHERE b.booking_code = ?"
        );
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findWithDetails($id) {
        $stmt = $this->db->prepare(
            "SELECT b.*, c.name as court_name, c.location, c.city, c.thumbnail,
                    ct.name as court_type, ct.slug as court_type_slug,
                    u.name as user_name, u.email, u.phone
             FROM bookings b 
             JOIN courts c ON b.court_id = c.id 
             JOIN court_types ct ON c.court_type_id = ct.id
             JOIN users u ON b.user_id = u.id
             WHERE b.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByUser($userId, $page = 1, $perPage = 10) {
        return $this->paginate($page, $perPage, ['user_id' => $userId], 'booking_date', 'DESC');
    }
    
    public function getUpcoming($userId = null) {
        $sql = "SELECT b.*, c.name as court_name, ct.name as court_type 
                FROM bookings b 
                JOIN courts c ON b.court_id = c.id 
                JOIN court_types ct ON c.court_type_id = ct.id
                WHERE b.booking_date >= CURDATE() 
                AND b.status IN ('confirmed', 'paid')";
        $params = [];
        
        if ($userId) {
            $sql .= " AND b.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY b.booking_date, b.start_time";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByDate($date, $courtId = null) {
        $sql = "SELECT b.*, c.name as court_name, u.name as user_name 
                FROM bookings b 
                JOIN courts c ON b.court_id = c.id 
                JOIN users u ON b.user_id = u.id
                WHERE b.booking_date = ? 
                AND b.status NOT IN ('cancelled', 'expired')";
        $params = [$date];
        
        if ($courtId) {
            $sql .= " AND b.court_id = ?";
            $params[] = $courtId;
        }
        
        $sql .= " ORDER BY b.start_time";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPending() {
        $stmt = $this->db->prepare(
            "SELECT b.*, c.name as court_name, u.name as user_name, u.phone
             FROM bookings b 
             JOIN courts c ON b.court_id = c.id 
             JOIN users u ON b.user_id = u.id
             WHERE b.status = 'pending' 
             ORDER BY b.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function confirm($bookingId, $adminNotes = null) {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'confirmed', confirmed_at = NOW(), admin_notes = ? WHERE id = ?"
        );
        return $stmt->execute([$adminNotes, $bookingId]);
    }
    
    public function markPaid($bookingId) {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'paid', payment_status = 'paid', paid_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$bookingId]);
    }
    
    public function cancel($bookingId, $reason = null) {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'cancelled', cancelled_at = NOW(), cancellation_reason = ? WHERE id = ?"
        );
        return $stmt->execute([$reason, $bookingId]);
    }
    
    public function complete($bookingId) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
        return $stmt->execute([$bookingId]);
    }
    
    public function markNoShow($bookingId) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'no_show' WHERE id = ?");
        return $stmt->execute([$bookingId]);
    }
    
    public function expireOldReservations() {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'expired' 
             WHERE status = 'pending' 
             AND payment_type = 'venue' 
             AND expires_at < NOW()"
        );
        return $stmt->execute();
    }
    
    public function verifyEntryQR($qrCode) {
        $stmt = $this->db->prepare(
            "SELECT b.*, c.name as court_name, u.name as user_name 
             FROM bookings b 
             JOIN courts c ON b.court_id = c.id 
             JOIN users u ON b.user_id = u.id
             WHERE b.entry_qr_code = ? 
             AND b.booking_date = CURDATE()
             AND b.status IN ('confirmed', 'paid')"
        );
        $stmt->execute([$qrCode]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $booking;
    }
    
    public function getStats($period = 'month') {
        $dateCondition = match($period) {
            'today' => "DATE(created_at) = CURDATE()",
            'week' => "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            'month' => "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
            'year' => "created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)",
            default => "1=1"
        };
        
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) as no_shows,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as total_revenue
             FROM bookings WHERE {$dateCondition}"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function generateQRCode($data) {
        // Generate a unique QR identifier
        return hash('sha256', $data . time() . rand(1000, 9999));
    }
    
    public function getCalendarData($courtId, $month, $year) {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $stmt = $this->db->prepare(
            "SELECT booking_date, start_time, end_time, status 
             FROM bookings 
             WHERE court_id = ? 
             AND booking_date BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'expired')
             ORDER BY booking_date, start_time"
        );
        $stmt->execute([$courtId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTopCourts($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.name, ct.name as court_type_name, COUNT(b.id) as booking_count, 
                    COALESCE(SUM(b.total_amount), 0) as total_revenue
             FROM courts c
             LEFT JOIN court_types ct ON c.court_type_id = ct.id
             LEFT JOIN bookings b ON c.id = b.court_id AND b.status IN ('completed', 'paid', 'confirmed')
             GROUP BY c.id
             ORDER BY booking_count DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPeakHours() {
        $stmt = $this->db->query(
            "SELECT HOUR(start_time) as hour, COUNT(*) as booking_count
             FROM bookings
             WHERE status IN ('completed', 'paid', 'confirmed')
             AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY HOUR(start_time)
             ORDER BY booking_count DESC
             LIMIT 10"
        );
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get max for percentage calculation
        $maxBookings = 0;
        foreach ($results as $row) {
            if ($row['booking_count'] > $maxBookings) {
                $maxBookings = $row['booking_count'];
            }
        }
        
        // Transform to expected format
        $peakHours = [];
        foreach ($results as $row) {
            $hour = (int)$row['hour'];
            $nextHour = ($hour + 1) % 24;
            $peakHours[] = [
                'time_range' => date('g:i A', mktime($hour, 0)) . ' - ' . date('g:i A', mktime($nextHour, 0)),
                'bookings' => (int)$row['booking_count'],
                'percentage' => $maxBookings > 0 ? round(($row['booking_count'] / $maxBookings) * 100) : 0
            ];
        }
        
        return $peakHours;
    }
    
    public function getRecentScans($limit = 20) {
        $stmt = $this->db->prepare(
            "SELECT b.booking_code, b.scanned_at, c.name as court_name, u.name as user_name
             FROM bookings b
             JOIN courts c ON b.court_id = c.id
             JOIN users u ON b.user_id = u.id
             WHERE b.scanned_at IS NOT NULL
             AND DATE(b.scanned_at) = CURDATE()
             ORDER BY b.scanned_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
