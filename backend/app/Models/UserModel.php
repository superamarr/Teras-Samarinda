<?php
require_once __DIR__ . '/../../config/connection.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->select("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
    }

    public function getByUsername($username) {
        return $this->db->selectOne("SELECT * FROM users WHERE username = :username", ['username' => $username]);
    }
    
    public function getById($id) {
        return $this->db->selectOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }
    
    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('users', $data);
    }
    
    public function update($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
    
    public function verifyPassword($username, $password) {
        $user = $this->getByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}