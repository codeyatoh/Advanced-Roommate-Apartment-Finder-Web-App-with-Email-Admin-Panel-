-- =============================================================================
-- RoomFinder Complete Database Schema
-- Generated based on frontend analysis and user requirements
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables to ensure a clean installation (reverse dependency order)
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS saved_listings;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS roommate_matches;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS landlord_profiles;
DROP TABLE IF EXISTS seeker_profiles;
DROP TABLE IF EXISTS listing_images;
DROP TABLE IF EXISTS listings;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- 1. USERS TABLE
-- Core user management for all roles. Includes the requested 'gender' field.
-- =============================================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('room_seeker', 'landlord', 'admin') NOT NULL DEFAULT 'room_seeker',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL, -- Added as requested
    profile_photo VARCHAR(255),
    bio TEXT,
    payment_methods TEXT DEFAULT NULL, -- Stores landlord payment methods as JSON
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================================================
-- 2. LISTINGS TABLE
-- Stores property details. Uses JSON for flexible amenities and house rules.
-- =============================================================================
CREATE TABLE listings (
    listing_id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL, -- Monthly Rent
    security_deposit DECIMAL(10,2),
    location VARCHAR(255) NOT NULL, -- Full Address
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    available_from DATE,
    utilities_included TINYINT(1) DEFAULT 0,
    
    -- Property Details
    room_type ENUM('apartment', 'studio', 'shared_room', 'private_room') NOT NULL DEFAULT 'private_room',
    bedrooms INT,
    bathrooms DECIMAL(3,1), -- Allows for 1.5 baths
    current_roommates INT,
    
    -- Flexible Data (JSON)
    amenities JSON, -- Stores array of checked amenities (e.g., ["wifi", "gym"])
    house_rules_data JSON, -- Stores rules and details (e.g., {"smoking": false, "pets": {"allowed": true, "details": "cats only"}})
    
    availability_status ENUM('available', 'occupied', 'pending') DEFAULT 'pending',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    admin_note TEXT,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =============================================================================
-- 3. LISTING IMAGES TABLE
-- Stores multiple images per listing.
-- =============================================================================
CREATE TABLE listing_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE
);

-- =============================================================================
-- 4. SEEKER PROFILES TABLE
-- Stores detailed preferences for room seekers (from profile_settings.php).
-- =============================================================================
CREATE TABLE seeker_profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    -- Basic Info
    budget DECIMAL(10,2),
    move_in_date DATE,
    occupation VARCHAR(100),
    preferred_location VARCHAR(255),
    
    -- Lifestyle Preferences (Enums based on form options)
    sleep_schedule ENUM('early', 'night', 'flexible'),
    social_level ENUM('introverted', 'ambivert', 'extroverted'),
    guests_preference ENUM('never', 'rarely', 'occasionally', 'often'),
    cleanliness ENUM('very_clean', 'clean', 'average', 'relaxed'),
    work_schedule ENUM('9-5', 'remote', 'shift', 'student'),
    noise_level ENUM('quiet', 'moderate', 'lively'),
    
    -- Flexible Preferences (JSON)
    preferences JSON, -- Stores tags like ["nonSmoker", "petFriendly"]
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
);

-- =============================================================================
-- 5. LANDLORD PROFILES TABLE
-- Stores business and verification info for landlords.
-- =============================================================================
CREATE TABLE landlord_profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    company_name VARCHAR(150),
    business_license VARCHAR(100),
    office_address VARCHAR(255),
    website_url VARCHAR(255),
    description TEXT,
    
    -- Business details
    operating_hours JSON, -- e.g. {"mon-fri": "9am-5pm", "sat": "10am-2pm"}
    verification_documents JSON, -- Array of document URLs
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
);

-- =============================================================================
-- 6. MESSAGES TABLE
-- Handles communication between users.
-- =============================================================================
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    listing_id INT, -- Optional: link message to a specific listing context
    message_content TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE SET NULL
);

-- =============================================================================
-- 7. APPOINTMENTS TABLE
-- Manages viewing schedules (from appointments.php).
-- =============================================================================
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    landlord_id INT NOT NULL,
    listing_id INT NOT NULL,
    
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'declined', 'completed', 'cancelled') DEFAULT 'pending',
    message TEXT, -- Optional note from seeker
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (seeker_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE
);

-- =============================================================================
-- 8. REPORTS TABLE
-- Handles user reports (from report_widget.php).
-- =============================================================================
CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    
    -- Polymorphic-like association (can report a user or a listing)
    reported_user_id INT,
    reported_listing_id INT,
    
    report_type ENUM('listing', 'user', 'message') NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (reporter_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reported_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (reported_listing_id) REFERENCES listings(listing_id) ON DELETE SET NULL
);

-- =============================================================================
-- 9. ROOMMATE MATCHES TABLE
-- Tracks pass/match actions between room seekers for roommate matching feature.
-- =============================================================================
CREATE TABLE roommate_matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,           -- User who performed the action
    target_seeker_id INT NOT NULL,    -- User being evaluated
    action ENUM('pass', 'match') NOT NULL,
    is_mutual TINYINT(1) DEFAULT 0,   -- Set to 1 when both users match each other
    is_notification_read TINYINT(1) DEFAULT 0, -- For notification badge
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (seeker_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_seeker_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Ensure each user can only pass/match another user once
    UNIQUE KEY unique_match (seeker_id, target_seeker_id),
    
    -- Index for faster queries
    INDEX idx_seeker (seeker_id),
    INDEX idx_target (target_seeker_id),
    INDEX idx_mutual (is_mutual)
);

-- =============================================================================
-- 10. NOTIFICATIONS TABLE
-- Stores user notifications for matches, messages, appointments, inquiries, etc.
-- Foreign keys removed to avoid import errors - referential integrity maintained by app
-- =============================================================================
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('match', 'message', 'appointment', 'inquiry', 'system', 'rent_request', 'payment_received', 'email') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id INT NULL COMMENT 'ID of related entity (match_id, message_id, etc)',
    related_user_id INT NULL COMMENT 'ID of user who triggered the notification',
    link VARCHAR(255) NULL,
    status ENUM('pending', 'sent', 'delivered', 'failed', 'read') DEFAULT 'pending',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    -- Indexes for better performance
    INDEX idx_user_id (user_id),
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_status (status),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- 11. RENTALS TABLE
-- Manages rental agreements between landlords and tenants.
-- =============================================================================
CREATE TABLE rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    tenant_id INT NOT NULL,
    landlord_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    rent_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =============================================================================
-- 12. PAYMENTS TABLE
-- Tracks rent payments.
-- =============================================================================
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    rental_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('stripe', 'cash', 'bank_transfer', 'ewallet') NOT NULL,
    transaction_id VARCHAR(255) NULL,
    proof_image VARCHAR(255) NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rental_id) REFERENCES rentals(rental_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =============================================================================
-- 13. SAVED LISTINGS TABLE
-- Stores listings saved by users (favorites).
-- =============================================================================
CREATE TABLE saved_listings (
    save_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, listing_id)
);

-- =============================================================================
-- 14. ACTIVITY LOGS TABLE
-- Tracks user activities for security and history.
-- =============================================================================
CREATE TABLE activity_logs (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =============================================================================
-- 15. PASSWORD RESET TOKENS TABLE
-- Stores secure tokens for password reset requests.
-- =============================================================================
CREATE TABLE password_reset_tokens (
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
);

-- =============================================================================
-- END OF SCHEMA
-- Database is ready for use. All 15 tables included.
-- =============================================================================

-- =============================================================================
-- DEFAULT ADMIN USER (For initial setup only)
-- =============================================================================
-- Email: admin@roomfinder.com
-- Password: password
-- IMPORTANT: Change this password immediately after first login!
-- =============================================================================

INSERT INTO users (role, first_name, last_name, email, password_hash, is_verified, is_active) 
VALUES (
    'admin', 
    'Admin', 
    'User', 
    'admin@roomfinder.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    1, 
    1
);
