<?php
require_once __DIR__ . '/../models/SystemModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Auth.php';

class SystemController {
    private $model;
    private $auth;
    
    public function __construct() {
        $this->model = new SystemModel();
        $this->auth = new Auth();
    }
    
    public function get() {
        $settings = $this->model->getSettings();
        if (!$settings) {
            $settings = ['website_title' => 'TeraSamarinda', 'maintenance_mode' => 0];
        } else {
            // Cast correctly
            $settings['maintenance_mode'] = (int)$settings['maintenance_mode'];
        }
        Response::success('System settings retrieved', $settings);
    }
    
    public function update() {
        $this->auth->requireSuperAdmin();
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $data = [
            'website_title' => $input['website_title'] ?? 'TeraSamarinda',
            'maintenance_mode' => isset($input['maintenance_mode']) ? (int)$input['maintenance_mode'] : 0
        ];
        
        $result = $this->model->updateSettings($data);
        if ($result) {
            Response::success('Settings updated successfully');
        } else {
            Response::error('Failed to update settings');
        }
    }
}
