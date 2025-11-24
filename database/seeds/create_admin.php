<?php
require_once '../../app/config/database.php';

$database = new Database();
$db = $database->getConnection();

$first_name = "System";
$last_name = "Admin";
$email = "admin@example.com";
$password = "admin123";
$role = "admin";
$is_verified = 1;

// Check if admin already exists
$query = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "Admin user already exists.\n";
} else {
    // Create admin user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified) VALUES (:first_name, :last_name, :email, :password_hash, :role, :is_verified)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":first_name", $first_name);
    $stmt->bindParam(":last_name", $last_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password_hash", $password_hash);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":is_verified", $is_verified);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!\n";
        echo "Email: " . $email . "\n";
        echo "Password: " . $password . "\n";
    } else {
        echo "Error creating admin user.\n";
    }
}
?>
