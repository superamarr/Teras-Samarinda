<?php
require_once __DIR__ . '/../config/connection.php';

try {
    $db = Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action_type ENUM('create', 'update', 'delete', 'login', 'logout') NOT NULL,
        module VARCHAR(50) NOT NULL,
        description VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at DESC),
        INDEX idx_module (module)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->query($sql);
    echo "Table 'activity_logs' created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
