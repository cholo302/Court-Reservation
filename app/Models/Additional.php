<?php
/**
 * CourtType Model
 */
class CourtType extends Model {
    protected $table = 'court_types';
    protected $fillable = ['name', 'slug', 'icon', 'description'];
    
    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM court_types WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllWithCounts() {
        $stmt = $this->db->query(
            "SELECT ct.*, COUNT(c.id) as court_count 
             FROM court_types ct 
             LEFT JOIN courts c ON ct.id = c.court_type_id AND c.is_active = 1
             GROUP BY ct.id 
             ORDER BY ct.name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Notification Model
 */
class Notification extends Model {
    protected $table = 'notifications';
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'channel', 
        'is_read', 'read_at', 'sent_at'
    ];
    
    public function getUnread($userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll($userId, $limit = 50) {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markRead($notificationId) {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$notificationId]);
    }
    
    public function markAllRead($userId) {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0"
        );
        return $stmt->execute([$userId]);
    }
    
    public function createNotification($userId, $type, $title, $message, $data = [], $channel = 'web') {
        return $this->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => json_encode($data),
            'channel' => $channel,
        ]);
    }
}

/**
 * Review Model
 */
class Review extends Model {
    protected $table = 'reviews';
    protected $fillable = ['user_id', 'court_id', 'booking_id', 'rating', 'comment', 'images', 'is_approved'];
    
    public function getByCourt($courtId, $limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.name as user_name, u.profile_image 
             FROM reviews r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.court_id = ? AND r.is_approved = 1 
             ORDER BY r.created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$courtId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function canReview($userId, $bookingId) {
        // Check if booking exists, belongs to user, and is completed
        $stmt = $this->db->prepare(
            "SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'completed'"
        );
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) return false;
        
        // Check if already reviewed
        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE booking_id = ?");
        $stmt->execute([$bookingId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return !$existing;
    }
}

/**
 * ActivityLog Model
 */
class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id', 'action', 'description', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];
    
    public function log($action, $description, $modelType = null, $modelId = null, $oldValues = null, $newValues = null) {
        $userId = null;
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'] ?? null;
        }
        
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
    
    public function getRecent($limit = 100) {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.name as user_name 
             FROM activity_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Setting Model
 */
class Setting extends Model {
    protected $table = 'settings';
    protected $fillable = ['key', 'value', 'type', 'description'];
    
    public function get($key, $default = null) {
        $stmt = $this->db->prepare("SELECT * FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$setting) return $default;
        
        return match($setting['type']) {
            'number' => (float)$setting['value'],
            'boolean' => $setting['value'] === 'true',
            'json' => json_decode($setting['value'], true),
            default => $setting['value']
        };
    }
    
    public function set($key, $value, $type = 'string') {
        if ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        } elseif ($type === 'json') {
            $value = json_encode($value);
        }
        
        $stmt = $this->db->prepare(
            "INSERT INTO settings (`key`, value, type) VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE value = ?, type = ?"
        );
        return $stmt->execute([$key, $value, $type, $value, $type]);
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = match($setting['type']) {
                'number' => (float)$setting['value'],
                'boolean' => $setting['value'] === 'true',
                'json' => json_decode($setting['value'], true),
                default => $setting['value']
            };
        }
        return $result;
    }
}
