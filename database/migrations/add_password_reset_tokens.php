<?php
/**
 * Database Migration: Add password_reset_tokens table
 * Run this file once to add the password reset functionality to existing database
 */

require_once __DIR__ . '/app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h2>Starting migration...</h2>";
    
    // Check if table already exists
    $sql = "SHOW TABLES LIKE 'password_reset_tokens'";
    $stmt = $conn->query($sql);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Table 'password_reset_tokens' already exists. Skipping.</p>";
        exit;
    }
    
    // Create password_reset_tokens table
    $sql = "CREATE TABLE password_reset_tokens (
        token_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX idx_token (token),
        INDEX idx_expires (expires_at),
        INDEX idx_user_unused (user_id, used)
    )";
    
    $conn->exec($sql);
    
    echo "<p style='color: green;'>✓ Table 'password_reset_tokens' created successfully!</p>";
    echo "<p style='color: green;'>✓ Migration completed!</p>";
    echo "<br><p><a href='app/views/public/login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>";
    exit(1);
}
