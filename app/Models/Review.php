<?php
/**
 * Review Model
 * Handles court reviews and ratings
 */
class Review extends Model {
    protected $table = 'reviews';
    protected $fillable = [
        'court_id', 'user_id', 'booking_id', 'rating', 'comment'
    ];
    
    /**
     * Get reviews for a court
     */
    public function getByCourtId($courtId, $limit = null) {
        $sql = "SELECT r.*, u.name as user_name, u.profile_image as user_avatar 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.court_id = ? 
                ORDER BY r.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Alias for getByCourtId
     */
    public function getByCourt($courtId, $limit = null) {
        return $this->getByCourtId($courtId, $limit);
    }
    
    /**
     * Get average rating for a court
     */
    public function getAverageRating($courtId) {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
             FROM reviews WHERE court_id = ?"
        );
        $stmt->execute([$courtId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user has reviewed a booking
     */
    public function hasReviewed($userId, $bookingId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM reviews WHERE user_id = ? AND booking_id = ?"
        );
        $stmt->execute([$userId, $bookingId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Create a review
     */
    public function createReview($data) {
        $reviewId = $this->create($data);
        
        // Update court rating
        $this->updateCourtRating($data['court_id']);
        
        return $reviewId;
    }
    
    /**
     * Update court's average rating
     */
    private function updateCourtRating($courtId) {
        $stats = $this->getAverageRating($courtId);
        
        $stmt = $this->db->prepare(
            "UPDATE courts SET rating = ?, total_reviews = ? WHERE id = ?"
        );
        $stmt->execute([
            round($stats['avg_rating'], 2),
            $stats['total_reviews'],
            $courtId
        ]);
    }
    
    /**
     * Get reviews by user
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.name as court_name 
             FROM reviews r 
             JOIN courts c ON r.court_id = c.id 
             WHERE r.user_id = ? 
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
