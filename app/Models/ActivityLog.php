<?php
/**
 * ActivityLog Model
 * Tracks user activities in the system
 */
class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id', 'action', 'description', 'model_type', 'model_id', 'ip_address', 'user_agent'
    ];
    
    /**
     * Log an activity
     */
    public function log($action, $description = '', $modelType = null, $modelId = null, $userId = null) {
        $userId = $userId ?? ($_SESSION['user']['id'] ?? null);
        
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
        
        return $this->create($data);
    }
    
    /**
     * Get recent activities
     */
    public function getRecent($limit = 50) {
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
    
    /**
     * Get activities by user
     */
    public function getByUser($userId, $limit = 50) {
        $stmt = $this->db->prepare(
            "SELECT * FROM activity_logs 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get activities by entity
     */
    public function getByEntity($modelType, $modelId) {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.name as user_name 
             FROM activity_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             WHERE al.model_type = ? AND al.model_id = ?
             ORDER BY al.created_at DESC"
        );
        $stmt->execute([$modelType, $modelId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
