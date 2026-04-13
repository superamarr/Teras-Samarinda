<?php
require_once __DIR__ . '/../../config/connection.php';

class AboutModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS about_section (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) DEFAULT '',
            subtitle VARCHAR(255) DEFAULT '',
            content TEXT,
            image_left VARCHAR(255) DEFAULT '',
            image_right VARCHAR(255) DEFAULT '',
            button_text VARCHAR(100) DEFAULT 'BACA SELENGKAPNYA',
            button_link VARCHAR(255) DEFAULT '/tentang',
            layout_type VARCHAR(50) DEFAULT 'default',
            vision TEXT,
            mission TEXT,
            page_hero_title VARCHAR(255) DEFAULT '',
            page_hero_subtitle TEXT,
            page_hero_background VARCHAR(255) DEFAULT '',
            welcome_title VARCHAR(255) DEFAULT '',
            welcome_text TEXT,
            welcome_image VARCHAR(255) DEFAULT '',
            story_title VARCHAR(255) DEFAULT '',
            story_text TEXT,
            story_background VARCHAR(255) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->db->query($sql);
        } catch (Exception $e) {
            error_log("AboutModel createTableIfNotExists error: " . $e->getMessage());
        }
    }
    
    public function get() {
        return $this->db->selectOne("SELECT * FROM about_section LIMIT 1");
    }
    
    public function update($data) {
        $current = $this->get();
        
        if ($current) {
            return $this->db->update('about_section', $data, 'id = :id', ['id' => $current['id']]);
        } else {
            return $this->db->insert('about_section', $data);
        }
    }
}
