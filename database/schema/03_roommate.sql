-- ============================
-- ROOMMATE MATCHING SYSTEM
-- Profiles and matching for finding compatible roommates
-- ============================

-- Roommate profiles with preferences
CREATE TABLE roommate_profile (
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

-- Roommate matches with compatibility scores
CREATE TABLE roommate_matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    compatibility_score INT NOT NULL,
    status ENUM('pending','accepted','declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(user_id) ON DELETE CASCADE
);
