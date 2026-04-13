<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';

class AuthController {
    private $model;
    private $auth;
    
    public function __construct() {
        $this->model = new UserModel();
        $this->auth = new Auth();
    }
    
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $username = Sanitize::input($input['username'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            Response::validationError(['username' => 'Username is required', 'password' => 'Password is required']);
        }
        
        $user = $this->model->verifyPassword($username, $password);
        
        if ($user) {
            $this->auth->login($user['id'], $user['username'], $user['role']);
            Response::success('Login successful', [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]);
        } else {
            Response::error('Invalid username or password', 401);
        }
    }
    
    public function logout() {
        $this->auth->logout();
        Response::success('Logout successful');
    }
    
    public function check() {
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getUser();
            Response::success('User is logged in', $user);
        } else {
            Response::unauthorized('User is not logged in');
        }
    }
    
    public function me() {
        if (!$this->auth->check()) {
            Response::unauthorized('Please login');
        }
        
        $userId = $this->auth->getUserId();
        $user = $this->model->getById($userId);
        
        if ($user) {
            unset($user['password']);
            Response::success('User data retrieved', $user);
        } else {
            Response::notFound('User not found');
        }
    }
}