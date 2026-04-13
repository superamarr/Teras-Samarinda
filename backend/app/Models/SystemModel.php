<?php
require_once __DIR__ . '/../../config/connection.php';

class SystemModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getSettings() {
        return $this->db->selectOne("SELECT * FROM system_settings WHERE settings_key = 'default'");
    }
    
    public function updateSettings($data) {
        return $this->db->update('system_settings', $data, "settings_key = 'default'");
    }
}
