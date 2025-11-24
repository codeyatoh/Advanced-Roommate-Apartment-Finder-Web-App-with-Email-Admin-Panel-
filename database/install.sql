-- ============================
-- ROOMMATE FINDER DATABASE - COMPLETE INSTALLATION
-- ============================
-- This file creates all tables in the correct order
-- Run this file to set up the entire database schema
-- ============================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS roommate_finder;
USE roommate_finder;

-- ============================
-- 1. USERS TABLE
-- ============================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('room_seeker', 'landlord', 'admin') NOT NULL DEFAULT 'room_seeker',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    gender ENUM('male','female','other') NULL,
    birthdate DATE NULL,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- ============================
-- 2. ROOM LISTINGS (APARTMENTS / ROOMS)
-- ============================
CREATE TABLE IF NOT EXISTS listings (
    listing_id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    room_type ENUM('apartment','studio','shared_room','private_room') NOT NULL,
    availability_status ENUM('available','occupied','pending') DEFAULT 'available',
    rules TEXT,
    utilities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 3. LISTING IMAGES
-- ============================
CREATE TABLE IF NOT EXISTS listing_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE
);

-- ============================
-- 4. ROOMMATE PROFILE (FOR FIND ROOMMATE)
-- ============================
CREATE TABLE IF NOT EXISTS roommate_profile (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    max_budget DECIMAL(10,2),
    preferred_location VARCHAR(255),
    lifestyle ENUM('quiet', 'moderate', 'active'),
    cleanliness ENUM('low','medium','high'),
    sleep_schedule ENUM('early_bird','night_owl','flexible'),
    smoker ENUM('yes','no'),
    pets ENUM('yes','no'),
    looking_for VARCHAR(255),
    about_me TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 5. ROOMMATE MATCHES
-- ============================
CREATE TABLE IF NOT EXISTS roommate_matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    compatibility_score INT NOT NULL,
    status ENUM('pending','accepted','declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 6. USER FAVORITES (SAVE ROOM)
-- ============================
CREATE TABLE IF NOT EXISTS favorites (
    fav_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE
);

-- ============================
-- 7. INQUIRIES (USER → LANDLORD)
-- ============================
CREATE TABLE IF NOT EXISTS inquiries (
    inquiry_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread','read','responded') DEFAULT 'unread',
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 8. APPOINTMENTS / VIEWING SCHEDULE
-- ============================
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    user_id INT NOT NULL,
    landlord_id INT NOT NULL,
    schedule_datetime DATETIME NOT NULL,
    status ENUM('pending','approved','declined','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 9. MESSAGES (CHAT SYSTEM)
-- ============================
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 10. NOTIFICATIONS (EMAIL LOGS)
-- ============================
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('email','sms') NOT NULL,
    was_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================
-- 11. REPORTS / COMPLAINTS
-- ============================
CREATE TABLE IF NOT EXISTS reports (
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

-- ============================
-- 12. ADMIN LOGS
-- ============================
CREATE TABLE IF NOT EXISTS admin_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_id INT NULL,
    page VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Installation complete!
SELECT 'Database schema created successfully!' AS status;
