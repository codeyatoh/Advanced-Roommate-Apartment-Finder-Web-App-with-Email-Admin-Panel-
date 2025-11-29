<?php
/**
 * Database Migration: Add status column to notifications table
 */

require_once __DIR__ . '/app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h2>Starting migration...</h2>\n";
    
    // Check if status column exists
    $sql = "SHOW COLUMNS FROM notifications LIKE 'status'";
    $stmt = $conn->query($sql);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Column 'status' already exists. Skipping.</p>\n";
    } else {
        // Add status column
        $sql = "ALTER TABLE notifications ADD COLUMN status VARCHAR(20) DEFAULT 'unread' AFTER message";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Column 'status' added successfully!</p>\n";
    }

    // Check if type column exists (just in case)
    $sql = "SHOW COLUMNS FROM notifications LIKE 'type'";
    $stmt = $conn->query($sql);
    
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE notifications ADD COLUMN type VARCHAR(20) DEFAULT 'email' AFTER user_id";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Column 'type' added successfully!</p>\n";
    }
    
    echo "<p style='color: green;'>✓ Migration completed!</p>\n";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>\n";
    exit(1);
}
