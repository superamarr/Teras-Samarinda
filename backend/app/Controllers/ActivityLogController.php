<?php
require_once __DIR__ . '/../models/ActivityLogModel.php';
require_once __DIR__ . '/../../helpers/Response.php';

class ActivityLogController {
    private $model;
    
    public function __construct() {
        $this->model = new ActivityLogModel();
    }
    
    public function getRecent() {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $logs = $this->model->getRecent($limit);
            Response::json(['success' => true, 'data' => $logs]);
        } catch (Exception $e) {
            Response::json(['success' => false, 'message' => 'Failed to get activity logs'], 500);
        }
    }
}
