<?php
/**
 * Payment Model
 */
class Payment extends Model {
    protected $table = 'payments';
    protected $fillable = [
        'payment_reference', 'booking_id', 'user_id', 'amount', 'payment_method',
        'payment_type', 'qr_code_url', 'qr_code_data', 'checkout_url', 
        'transaction_id', 'gateway_response', 'proof_screenshot', 'status',
        'verified_by', 'verified_at', 'refund_amount', 'refund_reason',
        'refunded_at', 'paid_at', 'expires_at'
    ];
    
    public function generateReference() {
        return 'PAY' . date('ymd') . strtoupper(substr(uniqid(), -6));
    }
    
    public function createPayment($data) {
        $data['payment_reference'] = $this->generateReference();
        $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
        return $this->create($data);
    }
    
    public function findByReference($reference) {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.booking_code, b.booking_date, c.name as court_name 
             FROM payments p 
             JOIN bookings b ON p.booking_id = b.id 
             JOIN courts c ON b.court_id = c.id
             WHERE p.payment_reference = ?"
        );
        $stmt->execute([$reference]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByBooking($bookingId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPending() {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.booking_code, b.booking_date, c.name as court_name, u.name as user_name
             FROM payments p 
             JOIN bookings b ON p.booking_id = b.id 
             JOIN courts c ON b.court_id = c.id
             JOIN users u ON p.user_id = u.id
             WHERE p.status IN ('pending', 'processing')
             ORDER BY p.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markPaid($paymentId, $transactionId = null) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'paid', transaction_id = ?, paid_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$transactionId, $paymentId]);
    }
    
    public function verify($paymentId, $adminId) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'paid', verified_by = ?, verified_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$adminId, $paymentId]);
        
        // Also update the booking
        $payment = $this->find($paymentId);
        if ($payment) {
            $booking = new Booking();
            $booking->markPaid($payment['booking_id']);
        }
        
        return true;
    }
    
    public function reject($paymentId, $adminId, $reason = null) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'rejected', verified_by = ?, verified_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$adminId, $paymentId]);
        
        // Cancel the associated booking
        $payment = $this->find($paymentId);
        if ($payment) {
            $booking = new Booking();
            $booking->cancel($payment['booking_id'], $reason ?? 'Payment rejected');
        }
        
        return true;
    }
    
    public function uploadProof($paymentId, $filename) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET proof_screenshot = ?, status = 'processing' WHERE id = ?"
        );
        return $stmt->execute([$filename, $paymentId]);
    }
    
    public function refund($paymentId, $amount, $reason, $adminId) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'refunded', refund_amount = ?, refund_reason = ?, 
             refunded_at = NOW(), verified_by = ? WHERE id = ?"
        );
        $stmt->execute([$amount, $reason, $adminId, $paymentId]);
        
        // Update booking status
        $payment = $this->find($paymentId);
        if ($payment) {
            $booking = new Booking();
            $booking->cancel($payment['booking_id'], 'Refunded: ' . $reason);
        }
        
        return true;
    }
    
    public function getStats($period = 'month') {
        // Get pending count
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE status = 'pending'");
        $stmt->execute();
        $pending = $stmt->fetchColumn();
        
        // Get verified today count
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE status IN ('verified', 'paid') AND DATE(verified_at) = CURDATE()");
        $stmt->execute();
        $verifiedToday = $stmt->fetchColumn();
        
        // Get month total
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status IN ('verified', 'paid') AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $monthTotal = $stmt->fetchColumn();
        
        // Get rejected count
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE status = 'rejected'");
        $stmt->execute();
        $rejected = $stmt->fetchColumn();
        
        return [
            'pending' => $pending,
            'verified_today' => $verifiedToday,
            'month_total' => $monthTotal,
            'rejected' => $rejected
        ];
    }
    
    public function getRevenueByDate($startDate, $endDate) {
        $stmt = $this->db->prepare(
            "SELECT DATE(paid_at) as date, SUM(amount) as revenue, COUNT(*) as transactions
             FROM payments 
             WHERE status IN ('paid', 'verified') AND paid_at BETWEEN ? AND ?
             GROUP BY DATE(paid_at)
             ORDER BY date"
        );
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecent($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.booking_code, c.name as court_name, u.name as user_name, u.email as user_email
             FROM payments p
             LEFT JOIN bookings b ON p.booking_id = b.id
             LEFT JOIN courts c ON b.court_id = c.id
             LEFT JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function paginateWithDetails($page = 1, $perPage = 20, $conditions = [], $orderBy = 'created_at', $direction = 'DESC') {
        $offset = ($page - 1) * $perPage;
        
        // Build where clause
        $where = "1=1";
        $params = [];
        
        if (!empty($conditions['status'])) {
            $where .= " AND p.status = ?";
            $params[] = $conditions['status'];
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) FROM payments p WHERE {$where}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();
        
        // Get items with details
        $sql = "SELECT p.*, 
                       p.proof_screenshot as proof_image,
                       b.booking_code, 
                       u.name as user_name, 
                       u.email as user_email,
                       v.name as verified_by_name
                FROM payments p
                LEFT JOIN bookings b ON p.booking_id = b.id
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN users v ON p.verified_by = v.id
                WHERE {$where}
                ORDER BY p.{$orderBy} {$direction}
                LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => (int)$page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage)
        ];
    }
}
