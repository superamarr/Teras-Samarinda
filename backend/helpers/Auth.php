<?php
class Auth {
    private $sessionName = 'tera_admin';
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login($userId, $username, $role = 'admin') {
        $_SESSION[$this->sessionName] = [
            'id' => $userId,
            'username' => $username,
            'role' => $role,
            'login_time' => time()
        ];
        
        session_regenerate_id(true);
        return true;
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION[$this->sessionName]);
    }
    
    public function getUser() {
        return $_SESSION[$this->sessionName] ?? null;
    }
    
    public function getUserId() {
        return $_SESSION[$this->sessionName]['id'] ?? null;
    }
    
    public function getUsername() {
        return $_SESSION[$this->sessionName]['username'] ?? null;
    }
    
    public function check() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if (isset($_SESSION[$this->sessionName]['login_time'])) {
            $sessionTimeout = 3600;
            if ((time() - $_SESSION[$this->sessionName]['login_time']) > $sessionTimeout) {
                $this->logout();
                return false;
            }
            $_SESSION[$this->sessionName]['login_time'] = time();
        }
        
        return true;
    }
    
    public function requireLogin() {
        if (!$this->check()) {
            Response::unauthorized('Please login to continue');
        }
    }
    
    public function requireSuperAdmin() {
        if (!$this->check()) {
            Response::unauthorized('Please login to continue');
        }
        $user = $this->getUser();
        if (!$user || $user['role'] !== 'superadmin') {
            Response::error('Access Denied. Superadmin only.', 403);
        }
    }
}