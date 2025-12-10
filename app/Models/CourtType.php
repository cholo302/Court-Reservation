<?php
/**
 * CourtType Model
 */
class CourtType extends Model
{
    protected $table = 'court_types';
    
    /**
     * Get all court types with court count
     */
    public function getAllWithCounts()
    {
        $sql = "SELECT ct.*, COUNT(c.id) as court_count 
                FROM court_types ct 
                LEFT JOIN courts c ON ct.id = c.court_type_id AND c.is_active = 1
                GROUP BY ct.id
                ORDER BY ct.name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Alias for backwards compatibility
     */
    public function getAllWithCourtCount()
    {
        return $this->getAllWithCounts();
    }
    
    /**
     * Get court type by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM court_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get court type by slug
     */
    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM court_types WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get courts by type
     */
    public function getCourts($typeId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, ct.name as type_name 
            FROM courts c 
            JOIN court_types ct ON c.court_type_id = ct.id 
            WHERE c.court_type_id = ? AND c.status = 'active'
            ORDER BY c.name
        ");
        $stmt->execute([$typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
