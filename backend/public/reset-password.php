<?php
header('Content-Type: text/plain');

require_once __DIR__ . '/../config/connection.php';

$newPassword = '123456';
$username = 'Admin1';

// Generate bcrypt hash
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

echo "Generated hash: $hash\n\n";

// Update database
$db = Database::getInstance();
$sql = "UPDATE users SET password = :password WHERE username = :username";
$db->query($sql, [
    'password' => $hash,
    'username' => $username
]);

echo "Password for '$username' has been updated to: $newPassword\n";
echo "You can now login with:\n";
echo "  Username: $username\n";
echo "  Password: $newPassword\n";
