<?php
require_once __DIR__ . '/../app/Models/ActivityLogModel.php';

class ActivityLogger {
    private static $model = null;
    
    private static function getModel() {
        if (self::$model === null) {
            self::$model = new ActivityLogModel();
        }
        return self::$model;
    }
    
    public static function log($actionType, $module, $description, $userId = null) {
        return self::getModel()->log([
            'user_id' => $userId ?? self::getCurrentUserId(),
            'action_type' => $actionType,
            'module' => $module,
            'description' => $description,
        ]);
    }
    
    public static function create($module, $description, $userId = null) {
        return self::log('create', $module, $description, $userId);
    }
    
    public static function update($module, $description, $userId = null) {
        return self::log('update', $module, $description, $userId);
    }
    
    public static function delete($module, $description, $userId = null) {
        return self::log('delete', $module, $description, $userId);
    }
    
    private static function getCurrentUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }
}
