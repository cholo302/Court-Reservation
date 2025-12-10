<?php
/**
 * Court Model
 */
class Court extends Model {
    protected $table = 'courts';
    protected $fillable = [
        'court_type_id', 'name', 'description', 'location', 'barangay', 'city', 'province',
        'hourly_rate', 'peak_hour_rate', 'half_court_rate',
        'capacity', 'amenities', 'thumbnail', 'is_active'
    ];
    
    public function getAllActive() {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as court_type_name, ct.icon as court_type_icon
             FROM courts c 
             LEFT JOIN court_types ct ON c.court_type_id = ct.id 
             WHERE c.is_active = 1 
             ORDER BY c.name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function allWithType() {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as court_type_name, ct.icon as court_type_icon
             FROM courts c 
             LEFT JOIN court_types ct ON c.court_type_id = ct.id 
             ORDER BY c.name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByType($typeId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, ct.name as court_type_name
             FROM courts c 
             JOIN court_types ct ON c.court_type_id = ct.id 
             WHERE c.court_type_id = ? AND c.is_active = 1 
             ORDER BY c.name"
        );
        $stmt->execute([$typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCities() {
        $stmt = $this->db->query(
            "SELECT DISTINCT city
             FROM courts 
             WHERE is_active = 1 AND city IS NOT NULL
             ORDER BY city"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function findWithType($id) {
        $stmt = $this->db->prepare(
            "SELECT c.*, ct.name as court_type, ct.name as court_type_name, ct.slug as court_type_slug, ct.icon as court_type_icon
             FROM courts c 
             LEFT JOIN court_types ct ON c.court_type_id = ct.id 
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function search($query = '', $filters = []) {
        $sql = "SELECT c.*, ct.name as court_type_name
                FROM courts c 
                LEFT JOIN court_types ct ON c.court_type_id = ct.id 
                WHERE c.is_active = 1";
        $params = [];
        
        if ($query) {
            $sql .= " AND (c.name LIKE ? OR c.location LIKE ? OR c.city LIKE ? OR c.description LIKE ?)";
            $searchTerm = "%{$query}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND c.court_type_id = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND c.city = ?";
            $params[] = $filters['city'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND c.hourly_rate >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND c.hourly_rate <= ?";
            $params[] = $filters['max_price'];
        }
        
        $sql .= " ORDER BY c.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get schedule exception for a specific date
     */
    public function getScheduleException($courtId, $date) {
        $stmt = $this->db->prepare(
            "SELECT * FROM court_schedules WHERE court_id = ? AND date = ?"
        );
        $stmt->execute([$courtId, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get operating hours from court's operating_hours JSON
     */
    public function getOperatingHours($courtId, $dayOfWeek) {
        $court = $this->find($courtId);
        if (!$court) return null;
        
        $operatingHours = json_decode($court['operating_hours'] ?? '{}', true);
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayName = $days[$dayOfWeek] ?? null;
        
        if ($dayName && isset($operatingHours[$dayName])) {
            return $operatingHours[$dayName];
        }
        
        // Default hours if not specified
        return [
            'open' => sprintf('%02d:00', OPERATING_START_HOUR),
            'close' => sprintf('%02d:00', OPERATING_END_HOUR)
        ];
    }
    
    public function getAvailableSlots($courtId, $date) {
        $dayOfWeek = date('w', strtotime($date));
        
        // Get court details for pricing
        $court = $this->find($courtId);
        $regularRate = $court['hourly_rate'] ?? 500;
        $peakRate = $court['peak_rate'] ?? ($regularRate * 1.5);
        $isWeekend = in_array($dayOfWeek, [0, 6]); // Sunday or Saturday
        $weekendRate = $court['weekend_rate'] ?? ($regularRate * 1.25);
        
        // Check for schedule exceptions (closed days, special hours)
        $exception = $this->getScheduleException($courtId, $date);
        if ($exception && $exception['is_closed']) {
            return []; // Court is closed on this date
        }
        
        // Get operating hours
        if ($exception && $exception['open_time'] && $exception['close_time']) {
            $openTime = $exception['open_time'];
            $closeTime = $exception['close_time'];
        } else {
            $hours = $this->getOperatingHours($courtId, $dayOfWeek);
            $openTime = $hours['open'] ?? sprintf('%02d:00', OPERATING_START_HOUR);
            $closeTime = $hours['close'] ?? sprintf('%02d:00', OPERATING_END_HOUR);
        }
        
        // Get existing bookings for this date
        $stmt = $this->db->prepare(
            "SELECT start_time, end_time FROM bookings 
             WHERE court_id = ? AND booking_date = ? AND status NOT IN ('cancelled', 'no_show')"
        );
        $stmt->execute([$courtId, $date]);
        $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate available slots
        $slots = [];
        $current = strtotime($openTime);
        $end = strtotime($closeTime);
        
        while ($current < $end) {
            $slotStart = date('H:i:s', $current);
            $slotEnd = date('H:i:s', $current + 3600); // 1 hour slots
            
            // Check if slot is booked
            $isBooked = false;
            foreach ($bookedSlots as $booked) {
                if ($slotStart >= $booked['start_time'] && $slotStart < $booked['end_time']) {
                    $isBooked = true;
                    break;
                }
            }
            
            // Check if slot is in the past (for today)
            $isPast = false;
            if ($date === date('Y-m-d') && $current < time()) {
                $isPast = true;
            }
            
            // Calculate rate for this slot
            $isPeak = $this->isPeakHour($slotStart);
            if ($isPeak) {
                $rate = $peakRate;
            } elseif ($isWeekend) {
                $rate = $weekendRate;
            } else {
                $rate = $regularRate;
            }
            
            $slots[] = [
                'start' => $slotStart,
                'end' => $slotEnd,
                'label' => date('g:i A', $current) . ' - ' . date('g:i A', $current + 3600),
                'available' => !$isBooked && !$isPast,
                'is_peak' => $isPeak,
                'rate' => $rate
            ];
            
            $current += 3600;
        }
        
        return $slots;
    }
    
    private function isPeakHour($time) {
        $hour = (int)date('G', strtotime($time));
        return $hour >= 17 && $hour < 21;
    }
    
    public function getReviews($courtId) {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.name as user_name
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.court_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$courtId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAverageRating($courtId) {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
             FROM reviews WHERE court_id = ?"
        );
        $stmt->execute([$courtId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function isAvailable($courtId, $date, $startTime, $endTime) {
        // Check if there's any overlapping booking
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM bookings 
             WHERE court_id = ? 
             AND booking_date = ? 
             AND status NOT IN ('cancelled', 'expired')
             AND (
                 (start_time < ? AND end_time > ?) OR
                 (start_time < ? AND end_time > ?) OR
                 (start_time >= ? AND end_time <= ?)
             )"
        );
        $stmt->execute([
            $courtId, $date, 
            $endTime, $startTime,
            $endTime, $startTime,
            $startTime, $endTime
        ]);
        
        return $stmt->fetchColumn() == 0;
    }
    
    public function calculatePrice($courtId, $date, $startTime, $endTime, $isHalfCourt = false) {
        // Get court details
        $court = $this->find($courtId);
        if (!$court) return ['total' => 0, 'hours' => 0, 'downpayment' => 0, 'balance' => 0];
        
        // Calculate duration in hours
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $hours = ($end - $start) / 3600;
        
        $totalPrice = 0;
        $currentTime = $start;
        
        while ($currentTime < $end) {
            $hour = (int)date('G', $currentTime);
            $isPeak = $hour >= (defined('PEAK_HOURS_START') ? PEAK_HOURS_START : 17) 
                   && $hour < (defined('PEAK_HOURS_END') ? PEAK_HOURS_END : 21);
            
            $hourlyRate = $isPeak && !empty($court['peak_hour_rate']) 
                ? $court['peak_hour_rate'] 
                : $court['hourly_rate'];
            
            $totalPrice += $hourlyRate;
            $currentTime += 3600;
        }
        
        // Apply half court discount if applicable
        if ($isHalfCourt && !empty($court['half_court_rate'])) {
            $totalPrice = $hours * $court['half_court_rate'];
        }
        
        // Calculate downpayment (50%) and balance
        $downpayment = $totalPrice * 0.5;
        $balance = $totalPrice - $downpayment;
        
        return [
            'total' => $totalPrice,
            'hours' => $hours,
            'downpayment' => $downpayment,
            'balance' => $balance,
            'hourly_rate' => $court['hourly_rate'],
            'is_half_court' => $isHalfCourt
        ];
    }
}
