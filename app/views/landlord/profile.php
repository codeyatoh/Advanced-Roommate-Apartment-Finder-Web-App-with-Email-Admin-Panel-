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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - RoomFinder</title>
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
                <h1 class="page-title">Profile Settings</h1>
                <p class="page-subtitle">Manage your account settings and preferences</p>
            </div>

            <!-- 2 Column Layout -->
            <div class="profile-grid">
                <!-- Left Column -->
                <div class="column">
                    <!-- 1. Profile Section -->
                    <div class="profile-card">
                        <div style="margin-bottom: 1.5rem;">
                            <?php 
                            $profilePhoto = getValue($user, 'profile_photo');
                            $displayName = htmlspecialchars(getValue($user, 'first_name') . ' ' . getValue($user, 'last_name'));
                            $photoUrl = !empty($profilePhoto) ? htmlspecialchars($profilePhoto) :  'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=10b981&color=fff&size=200';
                            ?>
                            <h2 class="profile-name"><?php echo $displayName; ?></h2>
                            <p class="profile-occupation"><?php echo htmlspecialchars(getValue($profile, 'company_name', 'Property Manager')); ?></p>
                        </div>

                        <div>
                            <h3 class="card-title" style="font-size: 0.875rem; margin-bottom: 0.75rem;">Profile picture</h3>
                            <div class="profile-upload-box" onclick="document.getElementById('photoInput').click()">
                                <div class="profile-image-container-new">
                                    <img src="<?php echo $photoUrl; ?>" alt="Profile" class="profile-image-new" id="profilePreview">
                                    <div class="camera-badge-new">
                                        <i data-lucide="camera" style="width: 14px; height: 14px;"></i>
                                    </div>
                                </div>
                                <div class="upload-text-content">
                                    <span class="upload-title">Drop your photo here or <span class="upload-link">Select a file</span></span>
                                    <span class="upload-subtitle">Supports: JPG, PNG</span>
                                </div>
                                <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- 2. About Me Section -->
                    <div class="profile-card">
                        <h3 class="card-title">About Me</h3>
                        <textarea name="bio" class="form-textarea" rows="3" placeholder="Tell others about yourself..."><?php echo htmlspecialchars(getValue($user, 'bio')); ?></textarea>
                    </div>

                    <!-- 3. Personal Information -->
                    <div class="profile-card">
                        <h3 class="card-title">Personal Information</h3>
                        <div class="form-group-stack">
                            <div>
                                <label class="form-label">First Name</label>
                                <div class="input-wrapper">
                                    <i data-lucide="user" class="input-icon"></i>
                                    <input type="text" name="first_name" class="form-input" value="<?php echo htmlspecialchars(getValue($user, 'first_name')); ?>" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Last Name</label>
                                <div class="input-wrapper">
                                    <i data-lucide="user" class="input-icon"></i>
                                    <input type="text" name="last_name" class="form-input" value="<?php echo htmlspecialchars(getValue($user, 'last_name')); ?>" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Email Address</label>
                                <div class="input-wrapper">
                                    <i data-lucide="mail" class="input-icon"></i>
                                    <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars(getValue($user, 'email')); ?>" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Phone Number</label>
                                <div class="input-wrapper">
                                    <i data-lucide="phone" class="input-icon"></i>
                                    <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars(getValue($user, 'phone')); ?>">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Gender</label>
                                <div class="input-wrapper">
                                    <i data-lucide="user" class="input-icon"></i>
                                    <select name="gender" class="form-input" style="appearance: none; cursor: pointer; padding-right: 2.5rem;">
                                        <option value="">Prefer not to say</option>
                                        <option value="male" <?php echo getValue($user, 'gender') === 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo getValue($user, 'gender') === 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo getValue($user, 'gender') === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <i data-lucide="chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: rgba(0,0,0,0.4); pointer-events: none; width: 1.25rem; height: 1.25rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="column">
                    <!-- Business Information -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i data-lucide="briefcase" style="width: 18px; height: 18px;"></i>
                            Business Information
                        </h3>
                        <div class="form-group-stack">
                            <div>
                                <label class="form-label">Company Name</label>
                                <div class="input-wrapper">
                                    <i data-lucide="building" class="input-icon"></i>
                                    <input type="text" name="company_name" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'company_name')); ?>" placeholder="ABC Property Management">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Business License Number</label>
                                <div class="input-wrapper">
                                    <i data-lucide="file-text" class="input-icon"></i>
                                    <input type="text" name="business_license" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'business_license')); ?>" placeholder="BL-12345678">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Website URL</label>
                                <div class="input-wrapper">
                                    <i data-lucide="globe" class="input-icon"></i>
                                    <input type="url" name="website_url" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'website_url')); ?>" placeholder="https://www.yourcompany.com">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Office Address</label>
                                <div class="input-wrapper">
                                    <i data-lucide="map-pin" class="input-icon"></i>
                                    <input type="text" name="office_address" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'office_address')); ?>" placeholder="123 Main St, Suite 100, San Francisco, CA 94102">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Description -->
                    <div class="profile-card">
                        <h3 class="card-title">Company Description</h3>
                        <textarea name="description" class="form-textarea" rows="5" placeholder="Describe your company, services, and what makes you unique..."><?php echo htmlspecialchars(getValue($profile, 'description')); ?></textarea>
                    </div>

                    <!-- Operating Hours -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                            Operating Hours
                        </h3>
                        <textarea name="operating_hours" class="form-textarea" rows="4" placeholder="Mon-Fri: 9:00 AM - 5:00 PM&#10;Sat: 10:00 AM - 2:00 PM&#10;Sun: Closed"><?php echo htmlspecialchars(getValue($profile, 'operating_hours')); ?></textarea>
                        <p class="field-hint">Enter your business operating hours</p>
                    </div>
                </div>
            </div>

                    <!-- Save Button -->
                    <div class="profile-card" style="background: linear-gradient(135deg, var(--blue), var(--deepBlue)); padding: 1.5rem;">
                        <div style="text-align: center;">
                            <button type="submit" class="btn btn-light" style="min-width: 200px; font-weight: 600;">
                                <i data-lucide="save" style="width: 1.125rem; height: 1.125rem;"></i>
                                Save Changes
                            </button>
                            <p style="color: rgba(255,255,255,0.8); font-size: 0.75rem; margin: 0.75rem 0 0 0;">
                                Keep your business information up to date
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        lucide.createIcons();

        // Handle profile photo preview
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

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
                    text: "An error occurred while saving",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                }).showToast();
            }
        });
    </script>
</body>
</html>
