<?php
require_once __DIR__ . '/../../config/connection.php';

class HeroModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function get() {
        try {
            return $this->db->selectOne("SELECT * FROM hero_section LIMIT 1");
        } catch (Exception $e) {
            error_log("HeroModel get error: " . $e->getMessage());
            return null;
        }
    }
    
    public function update($data) {
        try {
            $current = $this->get();
            
            if ($current && isset($current['id'])) {
                return $this->db->update('hero_section', $data, 'id = :id', ['id' => $current['id']]);
            } else {
                return $this->db->insert('hero_section', $data);
            }
        } catch (Exception $e) {
            error_log("HeroModel update error: " . $e->getMessage());
            // If column not found, try to create/fix table
            if (strpos($e->getMessage(), '1054 Unknown column') !== false) {
                $this->createTableIfNotExists();
                // Retry once
                return $this->db->insert('hero_section', $data);
            }
            return false;
        }
    }
    
    public function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS hero_section (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title_line1 VARCHAR(255) DEFAULT 'Jelajahi Ikon Baru',
            title_line2 VARCHAR(255) DEFAULT 'Kota Samarinda',
            title_line1_italic TEXT,
            title_line2_italic TEXT,
            subtitle TEXT,
            cta_text VARCHAR(100) DEFAULT 'LIHAT EVENT',
            cta_link VARCHAR(255) DEFAULT '/events',
            cta_text_secondary VARCHAR(100) DEFAULT 'BOOKING EVENT',
            cta_link_secondary VARCHAR(255) DEFAULT 'https://wa.me/6281522650048',
            use_video TINYINT(1) DEFAULT 0,
            video_file VARCHAR(255) DEFAULT '',
            background_image VARCHAR(255) DEFAULT ''
        )";
        try {
            $this->db->query($sql);
            
            // Explicitly check for newer columns that might be missing in older versions of the table
            $columnsToAdd = [
                'title_line1_italic' => 'TEXT',
                'title_line2_italic' => 'TEXT'
            ];
            
            foreach ($columnsToAdd as $col => $type) {
                try {
                    $this->db->query("SELECT $col FROM hero_section LIMIT 1");
                } catch (Exception $e) {
                    // Column doesn't exist, add it
                    $this->db->query("ALTER TABLE hero_section ADD COLUMN $col $type");
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("HeroModel createTable error: " . $e->getMessage());
            return false;
        }
    }
}