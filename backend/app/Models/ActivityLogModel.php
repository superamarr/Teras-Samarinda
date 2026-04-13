<?php
require_once __DIR__ . '/../../config/connection.php';

class ActivityLogModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function log($data) {
        return $this->db->insert('activity_logs', [
            'user_id' => $data['user_id'] ?? null,
            'action_type' => $data['action_type'],
            'module' => $data['module'],
            'description' => $data['description'],
        ]);
    }
    
    public function getRecent($limit = 10) {
        return $this->db->select(
            "SELECT al.*, u.username 
             FROM activity_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }
    
    public function delete($id) {
        return $this->db->delete('activity_logs', 'id = :id', ['id' => $id]);
    }
    
    public function clearOld($daysOld = 30) {
        return $this->db->delete(
            'activity_logs', 
            'created_at < DATE_SUB(NOW(), INTERVAL :days DAY)',
            ['days' => $daysOld]
        );
    }
}
