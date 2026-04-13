<?php
require_once __DIR__ . '/../../config/connection.php';

class SectionSettingsModel {
    private $db;
    private $table;
    
    public function __construct($table) {
        $this->db = Database::getInstance();
        $this->table = $table;
    }
    
    public function get() {
        return $this->db->selectOne("SELECT * FROM `{$this->table}` LIMIT 1");
    }
    
    public function update($data) {
        $current = $this->get();
        
        if ($current) {
            return $this->db->update($this->table, $data, 'id = :id', ['id' => $current['id']]);
        } else {
            return $this->db->insert($this->table, $data);
        }
    }
}
