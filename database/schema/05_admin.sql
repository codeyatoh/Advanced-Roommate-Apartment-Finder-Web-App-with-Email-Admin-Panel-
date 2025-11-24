-- ============================
-- ADMIN & SYSTEM TABLES
-- Notifications, reports, and admin logs
-- ============================

-- Email notifications (changed from SMS to Email)
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('email','sms') NOT NULL,
    was_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- User reports and complaints
CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    reported_by INT NOT NULL,
    target_user INT NULL,
    target_listing INT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending','reviewed','resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (target_listing) REFERENCES listings(listing_id) ON DELETE SET NULL
);

-- Admin activity logs
CREATE TABLE admin_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_id INT NULL,
    page VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);
