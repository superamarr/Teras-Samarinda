<?php
require_once __DIR__ . '/../../config/connection.php';

class GalleryModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($options = []) {
        $category = $options['category'] ?? null;
        $featured = $options['featured'] ?? null;
        $limit = $options['limit'] ?? null;
        
        $sql = "SELECT * FROM gallery";
        $params = [];
        $where = [];
        
        if ($category) {
            $where[] = "category = :category";
            $params['category'] = $category;
        }
        
        if ($featured !== null) {
            $where[] = "featured = :featured";
            $params['featured'] = $featured;
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY sort_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return $this->db->select($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM gallery WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        $maxOrder = $this->db->selectOne("SELECT MAX(sort_order) as max_order FROM gallery");
        $data['sort_order'] = ($maxOrder['max_order'] ?? 0) + 1;
        return $this->db->insert('gallery', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('gallery', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('gallery', 'id = :id', ['id' => $id]);
    }
    
    public function toggleFeatured($id) {
        $current = $this->getById($id);
        if ($current) {
            $newFeatured = $current['featured'] ? 0 : 1;
            return $this->db->update('gallery', ['featured' => $newFeatured], 'id = :id', ['id' => $id]);
        }
        return false;
    }
    
    public function updateSortOrder($id, $sortOrder) {
        return $this->db->update('gallery', ['sort_order' => $sortOrder], 'id = :id', ['id' => $id]);
    }
}