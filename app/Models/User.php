<?php
/**
 * User Model
 */
class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'profile_image',
        'email_verified_at', 'phone_verified_at', 'is_blacklisted', 
        'blacklist_reason', 'provider', 'provider_id', 'remember_token',
        'gov_id_type', 'gov_id_photo', 'face_photo'
    ];
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByProvider($provider, $providerId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE provider = ? AND provider_id = ?");
        $stmt->execute([$provider, $providerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }
    
    public function updatePassword($userId, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
    }
    
    public function getBookings($userId, $status = null) {
        $sql = "SELECT b.*, c.name as court_name, ct.name as court_type 
                FROM bookings b 
                JOIN courts c ON b.court_id = c.id 
                JOIN court_types ct ON c.court_type_id = ct.id
                WHERE b.user_id = ?";
        $params = [$userId];
        
        if ($status) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.booking_date DESC, b.start_time DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function blacklist($userId, $reason) {
        $stmt = $this->db->prepare("UPDATE users SET is_blacklisted = 1, blacklist_reason = ? WHERE id = ?");
        return $stmt->execute([$reason, $userId]);
    }
    
    public function unblacklist($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_blacklisted = 0, blacklist_reason = NULL WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function isBlacklisted($userId) {
        $user = $this->find($userId);
        return $user && $user['is_blacklisted'];
    }

    public function deactivate($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function activate($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function isActive($userId) {
        $user = $this->find($userId);
        return $user && $user['is_active'];
    }

    public function deleteUser($userId) {
        $user = $this->find($userId);
        if (!$user) return false;

        // Base path to root directory
        $basePath = __DIR__ . '/../../';

        // Delete associated photos
        if (!empty($user['gov_id_photo'])) {
            $photoPath = $basePath . $user['gov_id_photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        if (!empty($user['face_photo'])) {
            $photoPath = $basePath . $user['face_photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        if (!empty($user['profile_image'])) {
            $photoPath = $basePath . $user['profile_image'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        // Delete related records in order of dependencies
        // 1. Delete reviews (depends on bookings, so must be first)
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 2. Delete bookings
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 3. Delete payments (first delete payment proofs)
        $stmt = $this->db->prepare("SELECT id, proof_screenshot FROM payments WHERE user_id = ?");
        $stmt->execute([$userId]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($payments as $payment) {
            if (!empty($payment['proof_screenshot'])) {
                $proofPath = $basePath . $payment['proof_screenshot'];
                if (file_exists($proofPath)) {
                    unlink($proofPath);
                }
            }
        }
        $stmt = $this->db->prepare("DELETE FROM payments WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 4. Delete notifications
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 5. Delete activity logs
        $stmt = $this->db->prepare("DELETE FROM activity_logs WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 6. Delete player lookups
        $stmt = $this->db->prepare("DELETE FROM player_lookups WHERE user_id = ?");
        $stmt->execute([$userId]);

        // 7. Finally delete the user
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function getStats($userId = null) {
        if ($userId !== null) {
            // Get stats for a specific user
            $stmt = $this->db->prepare(
                "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) as no_shows,
                    COALESCE(SUM(total_amount), 0) as total_spent
                 FROM bookings WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Get overall user stats for admin
        $stats = [];
        
        // Active this month
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
        );
        $stmt->execute();
        $stats['active_month'] = $stmt->fetchColumn();
        
        // New this week
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
        );
        $stmt->execute();
        $stats['new_week'] = $stmt->fetchColumn();
        
        // Blacklisted
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users WHERE is_blacklisted = 1"
        );
        $stmt->execute();
        $stats['blacklisted'] = $stmt->fetchColumn();
        
        return $stats;
    }
}
