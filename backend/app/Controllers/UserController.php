<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';

class UserController {
    private $model;
    private $auth;
    
    public function __construct() {
        $this->model = new UserModel();
        $this->auth = new Auth();
    }
    
    public function getAll() {
        $this->auth->requireSuperAdmin();
        $users = $this->model->getAll();
        Response::success('Users retrieved successfully', $users);
    }
    
    public function create() {
        $this->auth->requireSuperAdmin();
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $data = [
            'username' => Sanitize::input($input['username'] ?? ''),
            'email' => Sanitize::input($input['email'] ?? ''),
            'role' => Sanitize::input($input['role'] ?? 'admin'),
            'password' => $input['password'] ?? ''
        ];
        
        if (empty($data['username']) || empty($data['password'])) {
            Response::validationError(['username' => 'Username is required', 'password' => 'Password is required']);
        }
        
        $existing = $this->model->getByUsername($data['username']);
        if ($existing) {
            Response::error('Username already exists', 400);
        }
        
        $result = $this->model->create($data);
        if ($result) {
            Response::success('User created successfully', ['id' => $result], 201);
        } else {
            Response::error('Failed to create user');
        }
    }
    
    public function update($id) {
        $this->auth->requireSuperAdmin();
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
            if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
                $input = $_POST;
            }
        }
        
        $user = $this->model->getById($id);
        if (!$user) {
            Response::notFound('User not found');
        }
        
        $data = [];
        if (isset($input['role'])) $data['role'] = Sanitize::input($input['role']);
        if (isset($input['email'])) $data['email'] = Sanitize::input($input['email']);
        if (!empty($input['password'])) $data['password'] = $input['password'];
        
        if (empty($data)) {
            Response::success('No changes to make');
        }
        
        $result = $this->model->update($id, $data);
        if ($result) {
            Response::success('User updated successfully');
        } else {
            Response::error('Failed to update user');
        }
    }
    
    public function delete($id) {
        $this->auth->requireSuperAdmin();
        
        // Cannot delete self
        if ($id == $this->auth->getUserId()) {
            Response::error('Cannot delete your own account', 400);
        }
        
        $user = $this->model->getById($id);
        if (!$user) {
            Response::notFound('User not found');
        }
        
        $result = $this->model->delete($id);
        if ($result) {
            Response::success('User deleted successfully');
        } else {
            Response::error('Failed to delete user');
        }
    }
}
