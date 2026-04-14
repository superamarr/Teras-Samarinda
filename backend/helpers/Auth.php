<?php
class Auth {
    private $sessionName = 'tera_admin';
    private $sessionTimeout = 3600;
    
    public function __construct() {
        $sessionName = getenv('SESSION_NAME');
        if ($sessionName !== false && $sessionName !== '') {
            $this->sessionName = $sessionName;
        }

        $sessionTimeout = getenv('SESSION_TIMEOUT');
        if ($sessionTimeout !== false && ctype_digit((string) $sessionTimeout)) {
            $this->sessionTimeout = (int) $sessionTimeout;
        }

        if (session_status() === PHP_SESSION_NONE) {
            $secure = filter_var(getenv('SESSION_SECURE') !== false ? getenv('SESSION_SECURE') : 'false', FILTER_VALIDATE_BOOLEAN);
            $httpOnly = filter_var(getenv('SESSION_HTTP_ONLY') !== false ? getenv('SESSION_HTTP_ONLY') : 'true', FILTER_VALIDATE_BOOLEAN);
            $sameSite = getenv('SESSION_SAMESITE') !== false && getenv('SESSION_SAMESITE') !== ''
                ? getenv('SESSION_SAMESITE')
                : 'Lax';
            $path = getenv('SESSION_PATH') !== false && getenv('SESSION_PATH') !== '' ? getenv('SESSION_PATH') : '/';
            $domain = getenv('SESSION_DOMAIN') !== false && getenv('SESSION_DOMAIN') !== '' ? getenv('SESSION_DOMAIN') : '';

            if (strtolower($sameSite) === 'none' && !$secure) {
                // Browser modern akan menolak SameSite=None jika Secure=false.
                $secure = true;
            }

            session_name($this->sessionName);
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite,
            ]);
        }

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
            if ((time() - $_SESSION[$this->sessionName]['login_time']) > $this->sessionTimeout) {
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