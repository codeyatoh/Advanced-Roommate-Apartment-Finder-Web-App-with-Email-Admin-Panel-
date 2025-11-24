<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/LandlordProfile.php';

// Check if user is logged in as landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userModel = new User();
$profileModel = new LandlordProfile();

// Fetch user and profile data
$user = $userModel->getById($userId);
$profile = $profileModel->getByUserId($userId);

// Handle if profile doesn't exist yet
if (!$profile) {
    $profile = [];
}

// Helper function to safely get value
function getValue($array, $key, $default = '') {
    return $array[$key] ?? $default;
}

// Decode JSON fields
$operatingHours = !empty($profile['operating_hours']) ? json_decode($profile['operating_hours'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Profile - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/profile-settings.module.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    <div class="profile-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <form id="profileForm" class="profile-container">
            <!-- Header -->
            <div class="profile-header">
                <h1 class="page-title">Business Profile</h1>
                <p class="page-subtitle">Manage your business information and account settings</p>
            </div>

            <!-- Personal Information Section -->
            <section class="profile-section">
                <h2 class="section-title">
                    <i data-lucide="user"></i>
                    Personal Information
                </h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name *</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($user, 'first_name')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($user, 'last_name')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($user, 'email')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($user, 'phone')); ?>" 
                               placeholder="+1 (555) 123-4567">
                    </div>

                    <div class="form-group">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-input">
                            <option value="">Prefer not to say</option>
                            <option value="male" <?php echo getValue($user, 'gender') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo getValue($user, 'gender') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo getValue($user, 'gender') === 'other' ? 'selected' : ''; ?>>Other</option>
                            <option value="prefer_not_to_say" <?php echo getValue($user, 'gender') === 'prefer_not_to_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea id="bio" name="bio" class="form-input" rows="4" 
                              placeholder="Tell us about yourself..."><?php echo htmlspecialchars(getValue($user, 'bio')); ?></textarea>
                </div>
            </section>

            <!-- Business Information Section -->
            <section class="profile-section">
                <h2 class="section-title">
                    <i data-lucide="briefcase"></i>
                    Business Information
                </h2>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" id="company_name" name="company_name" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($profile, 'company_name')); ?>" 
                               placeholder="ABC Property Management">
                    </div>

                    <div class="form-group">
                        <label for="business_license" class="form-label">Business License Number</label>
                        <input type="text" id="business_license" name="business_license" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($profile, 'business_license')); ?>" 
                               placeholder="BL-12345678">
                    </div>

                    <div class="form-group">
                        <label for="website_url" class="form-label">Website URL</label>
                        <input type="url" id="website_url" name="website_url" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($profile, 'website_url')); ?>" 
                               placeholder="https://www.yourcompany.com">
                    </div>

                    <div class="form-group full-width">
                        <label for="office_address" class="form-label">Office Address</label>
                        <input type="text" id="office_address" name="office_address" class="form-input" 
                               value="<?php echo htmlspecialchars(getValue($profile, 'office_address')); ?>" 
                               placeholder="123 Main St, Suite 100, San Francisco, CA 94102">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Company Description</label>
                    <textarea id="description" name="description" class="form-input" rows="4" 
                              placeholder="Describe your company, services, and what makes you unique..."><?php echo htmlspecialchars(getValue($profile, 'description')); ?></textarea>
                </div>
            </section>

            <!-- Operating Hours Section -->
            <section class="profile-section">
                <h2 class="section-title">
                    <i data-lucide="clock"></i>
                    Operating Hours
                </h2>
                
                <div class="form-group">
                    <label for="operating_hours" class="form-label">Business Hours</label>
                    <textarea id="operating_hours" name="operating_hours" class="form-input" rows="3" 
                              placeholder="Mon-Fri: 9:00 AM - 5:00 PM&#10;Sat: 10:00 AM - 2:00 PM&#10;Sun: Closed"><?php echo htmlspecialchars(getValue($profile, 'operating_hours')); ?></textarea>
                    <p class="form-help">Enter your business operating hours</p>
                </div>
            </section>

            <!-- Profile Photo Section -->
            <section class="profile-section">
                <h2 class="section-title">
                    <i data-lucide="image"></i>
                    Profile Photo
                </h2>
                
                <div class="photo-upload-container">
                    <div class="current-photo">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" class="profile-photo-preview">
                        <?php else: ?>
                            <div class="profile-photo-placeholder">
                                <i data-lucide="user" style="width: 4rem; height: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="photo-upload-controls">
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-glass btn-sm" onclick="document.getElementById('profile_photo').click()">
                            <i data-lucide="upload"></i>
                            Upload Photo
                        </button>
                        <p class="form-help">Recommended: Square image, at least 400x400px</p>
                    </div>
                </div>
            </section>

            <!-- Action Buttons -->
            <div class="profile-actions">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i>
                    Save Changes
                </button>
                <button type="button" class="btn btn-ghost" onclick="window.history.back()">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        lucide.createIcons();

        // Handle form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ProfileController.php?action=updateLandlordProfile', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Toastify({
                        text: "Profile updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#10b981",
                    }).showToast();
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Toastify({
                        text: data.message || "Failed to update profile",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                    }).showToast();
                }
            } catch (error) {
                console.error('Error:', error);
                Toastify({
                    text: "An error occurred",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                }).showToast();
            }
        });

        // Handle profile photo preview
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.profile-photo-preview');
                    const placeholder = document.querySelector('.profile-photo-placeholder');
                    
                    if (preview) {
                        preview.src = e.target.result;
                    } else if (placeholder) {
                        placeholder.outerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="profile-photo-preview">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
