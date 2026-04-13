<?php
require_once __DIR__ . '/../../config/connection.php';

class BookingModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($status = null) {
        if ($status) {
            return $this->db->select("SELECT * FROM bookings WHERE status = :status ORDER BY created_at DESC", ['status' => $status]);
        }
        return $this->db->select("SELECT * FROM bookings ORDER BY created_at DESC");
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM bookings WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        return $this->db->insert('bookings', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('bookings', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('bookings', 'id = :id', ['id' => $id]);
    }
    
    public function getCount($status = null) {
        if ($status) {
            return $this->db->selectOne("SELECT COUNT(*) as total FROM bookings WHERE status = :status", ['status' => $status]);
        }
        return $this->db->selectOne("SELECT COUNT(*) as total FROM bookings");
    }
}