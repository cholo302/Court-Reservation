<?php
/**
 * Notification Model
 * Handles user notifications
 */
class Notification extends Model {
    protected $table = 'notifications';
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'read_at'
    ];
    
    /**
     * Create a notification
     */
    public function createNotification($userId, $type, $title, $message, $data = null) {
        return $this->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
        ]);
    }
    
    /**
     * Get user's notifications
     */
    public function getByUser($userId, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        $params = [$userId];
        
        if ($unreadOnly) {
            $sql .= " AND read_at IS NULL";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND read_at IS NULL"
        );
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId) {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET read_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$notificationId]);
    }
    
    /**
     * Mark all as read for user
     */
    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL"
        );
        return $stmt->execute([$userId]);
    }
    
    /**
     * Delete old notifications
     */
    public function deleteOld($days = 30) {
        $stmt = $this->db->prepare(
            "DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        return $stmt->execute([$days]);
    }
}
