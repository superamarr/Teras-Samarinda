<?php
require_once __DIR__ . '/../../config/connection.php';

class EventModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($status = null, $limit = null) {
        $sql = "SELECT * FROM events";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY status = 'Aktif' DESC, date DESC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params['limit'] = (int)$limit;
        }
        
        return $this->db->select($sql, $params);
    }
    
    public function getActiveOrLatest($limit = 3) {
        // 1. Get explicitly featured events (regardless of status if marked as featured)
        $featuredEvents = $this->db->select("SELECT * FROM events WHERE is_featured = 1 ORDER BY status = 'Aktif' DESC, date DESC, created_at DESC LIMIT :limit", ['limit' => (int)$limit]);
        
        // 2. If we have featured events, return ONLY those (even if less than limit)
        // This gives the Admin explicit control: if they pick 1, they get 1.
        if (count($featuredEvents) > 0) {
            return $featuredEvents;
        }
        
        // 3. Fallback: If zero featured events, return the latest active events
        $latestEvents = $this->db->select("SELECT * FROM events WHERE status = 'Aktif' ORDER BY date DESC, created_at DESC LIMIT :limit", ['limit' => (int)$limit]);
        
        if (empty($latestEvents)) {
            return $this->db->select("SELECT * FROM events ORDER BY created_at DESC LIMIT :limit", ['limit' => (int)$limit]);
        }
        
        return $latestEvents;
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM events WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        return $this->db->insert('events', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('events', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('events', 'id = :id', ['id' => $id]);
    }
}