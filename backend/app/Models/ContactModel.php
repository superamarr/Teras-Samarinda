<?php
require_once __DIR__ . '/../../config/connection.php';

class ContactModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS contact_section (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) DEFAULT '',
            phone VARCHAR(50) DEFAULT '',
            whatsapp VARCHAR(50) DEFAULT '',
            address TEXT,
            facebook VARCHAR(255) DEFAULT '',
            instagram VARCHAR(255) DEFAULT '',
            youtube VARCHAR(255) DEFAULT '',
            map_embed TEXT,
            operating_hours VARCHAR(255) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->db->query($sql);
        } catch (Exception $e) {
            error_log("ContactModel createTableIfNotExists error: " . $e->getMessage());
        }
    }
    
    public function get() {
        return $this->db->selectOne("SELECT * FROM contact_section LIMIT 1");
    }
    
    public function update($data) {
        $current = $this->get();
        
        if ($current) {
            return $this->db->update('contact_section', $data, 'id = :id', ['id' => $current['id']]);
        } else {
            return $this->db->insert('contact_section', $data);
        }
    }
}
