<?php
require_once __DIR__ . '/../../config/connection.php';

class ActivityModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($status = null) {
        $sql = "SELECT * FROM activities";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY sort_order ASC, created_at ASC";
        return $this->db->select($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM activities WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        $maxOrder = $this->db->selectOne("SELECT MAX(sort_order) as max_order FROM activities");
        $data['sort_order'] = ($maxOrder['max_order'] ?? 0) + 1;
        return $this->db->insert('activities', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('activities', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('activities', 'id = :id', ['id' => $id]);
    }
    
    public function updateSortOrder($id, $sortOrder) {
        return $this->db->update('activities', ['sort_order' => $sortOrder], 'id = :id', ['id' => $id]);
    }
}