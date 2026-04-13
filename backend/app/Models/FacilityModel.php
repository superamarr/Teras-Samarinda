<?php
require_once __DIR__ . '/../../config/connection.php';

class FacilityModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($status = null) {
        $sql = "SELECT * FROM facilities";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY sort_order ASC, created_at ASC";
        return $this->db->select($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM facilities WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        $maxOrder = $this->db->selectOne("SELECT MAX(sort_order) as max_order FROM facilities");
        $data['sort_order'] = ($maxOrder['max_order'] ?? 0) + 1;
        return $this->db->insert('facilities', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('facilities', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('facilities', 'id = :id', ['id' => $id]);
    }
    
    public function updateSortOrder($id, $sortOrder) {
        return $this->db->update('facilities', ['sort_order' => $sortOrder], 'id = :id', ['id' => $id]);
    }
}