<?php
/**
 * Base Model Class
 * Uses PDO for database operations
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct() {
        // Get PDO connection
        if (isset($GLOBALS['db']) && $GLOBALS['db'] instanceof PDO) {
            $this->db = $GLOBALS['db'];
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all records
     */
    public function all($orderBy = 'id', $order = 'DESC') {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find records by column value
     */
    public function where($column, $value, $operator = '=') {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find single record by conditions
     */
    public function findWhere($conditions) {
        $where = [];
        $params = [];
        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = ?";
            $params[] = $value;
        }
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        if (empty($filtered)) {
            $filtered = $data; // If fillable is empty, use all data
        }
        
        $columns = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($filtered));
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        if (empty($filtered)) {
            $filtered = $data;
        }
        
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($filtered)));
        $params = array_values($filtered);
        $params[] = $id;
        
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute($params);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }
    
    /**
     * Count records
     */
    public function count($conditions = []) {
        if (empty($conditions)) {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $where = [];
            $params = [];
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $where)
            );
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $result['count'] ?? 0;
    }
    
    /**
     * Paginate records
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = 'id', $order = 'DESC') {
        $offset = ($page - 1) * $perPage;
        
        if (empty($conditions)) {
            $total = $this->count();
            $stmt = $this->db->query(
                "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order} LIMIT {$perPage} OFFSET {$offset}"
            );
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $where = [];
            $params = [];
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $whereClause = implode(' AND ', $where);
            
            $total = $this->count($conditions);
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY {$orderBy} {$order} LIMIT {$perPage} OFFSET {$offset}"
            );
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Execute raw query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch single row from raw query
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch all rows from raw query
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
