<?php
class Sanitize {
    public static function input($data) {
        if (is_array($data)) {
            return array_map([self::class, 'input'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        
        return $data;
    }
    
    public static function string($data) {
        return preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $data);
    }
    
    public static function email($data) {
        return filter_var($data, FILTER_SANITIZE_EMAIL);
    }
    
    public static function url($data) {
        return filter_var($data, FILTER_SANITIZE_URL);
    }
    
    public static function integer($data) {
        return (int) filter_var($data, FILTER_SANITIZE_NUMBER_INT);
    }
    
    public static function float($data) {
        return (float) filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT);
    }
    
    public static function filename($data) {
        $data = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $data);
        $data = preg_replace('/\.+/', '.', $data);
        return trim($data, '.');
    }
    
    public static function html($data) {
        return strip_tags($data, '<p><br><strong><em><ul><ol><li><a><img>');
    }
    
    public static function all($data) {
        if (is_array($data)) {
            return array_map([self::class, 'all'], $data);
        }
        
        $data = self::input($data);
        return $data;
    }
}