<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/SeekerProfile.php';

// Get current user
$userId = $_SESSION['user_id'] ?? 1;

$userModel = new User();
$profileModel = new SeekerProfile();

// Fetch user data
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
                        <?php 
                        $profilePhoto = getValue($user, 'profile_photo');
                        $displayName = htmlspecialchars(getValue($user, 'first_name') . ' ' . getValue($user, 'last_name'));
                        $photoUrl = !empty($profilePhoto) ? htmlspecialchars($profilePhoto) :  'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=10b981&color=fff&size=200';
                        ?>
                        <div style="margin-bottom: 1.5rem;">
                            <h2 class="profile-name"><?php echo $displayName; ?></h2>
                            <p class="profile-occupation"><?php echo htmlspecialchars(getValue($profile, 'occupation', 'Update your occupation')); ?></p>
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
                        <textarea name="bio" class="form-textarea" rows="3" placeholder="Tell potential roommates about yourself..."><?php echo htmlspecialchars(getValue($user, 'bio')); ?></textarea>
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
                                <label class="form-label">Location</label>
                                <div class="input-wrapper">
                                    <i data-lucide="map-pin" class="input-icon"></i>
                                    <input type="text" name="location" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'preferred_location')); ?>">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Occupation</label>
                                <div class="input-wrapper">
                                    <i data-lucide="briefcase" class="input-icon"></i>
                                    <input type="text" name="occupation" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'occupation')); ?>">
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

                    <!-- 6. Room Details Section -->
                    <div class="profile-card">
                        <h3 class="card-title">Room Details</h3>
                        <div class="form-group-stack">
                            <div>
                                <label class="form-label">Monthly Budget ($)</label>
                                <div class="input-wrapper">
                                    <i data-lucide="coins" class="input-icon"></i>
                                    <input type="number" name="budget" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'budget')); ?>" step="50">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Move-in Date</label>
                                <div class="input-wrapper">
                                    <i data-lucide="calendar" class="input-icon"></i>
                                    <input type="date" name="move_in_date" class="form-input" value="<?php echo htmlspecialchars(getValue($profile, 'move_in_date')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="column">
                    <!-- 4. Lifestyle Section -->
                    <div class="profile-card">
                        <h3 class="card-title">Lifestyle</h3>
                        <div class="form-group-stack">
                            <div>
                                <label class="form-label">Sleep Schedule</label>
                                <select name="sleep_schedule" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="early" <?php echo getValue($profile, 'sleep_schedule') === 'early' ? 'selected' : ''; ?>>Early Bird</option>
                                    <option value="night" <?php echo getValue($profile, 'sleep_schedule') === 'night' ? 'selected' : ''; ?>>Night Owl</option>
                                    <option value="flexible" <?php echo getValue($profile, 'sleep_schedule') === 'flexible' ? 'selected' : ''; ?>>Flexible</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Social Level</label>
                                <select name="social_level" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="introverted" <?php echo getValue($profile, 'social_level') === 'introverted' ? 'selected' : ''; ?>>Introverted</option>
                                    <option value="ambivert" <?php echo getValue($profile, 'social_level') === 'ambivert' ? 'selected' : ''; ?>>Ambivert</option>
                                    <option value="extroverted" <?php echo getValue($profile, 'social_level') === 'extroverted' ? 'selected' : ''; ?>>Extroverted</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Guests</label>
                                <select name="guests" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="never" <?php echo getValue($profile, 'guests_preference') === 'never' ? 'selected' : ''; ?>>Never</option>
                                    <option value="rarely" <?php echo getValue($profile, 'guests_preference') === 'rarely' ? 'selected' : ''; ?>>Rarely</option>
                                    <option value="occasionally" <?php echo getValue($profile, 'guests_preference') === 'occasionally' ? 'selected' : ''; ?>>Occasionally</option>
                                    <option value="often" <?php echo getValue($profile, 'guests_preference') === 'often' ? 'selected' : ''; ?>>Often</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Cleanliness</label>
                                <select name="cleanliness" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="very_clean" <?php echo getValue($profile, 'cleanliness') === 'very_clean' ? 'selected' : ''; ?>>Very Clean</option>
                                    <option value="clean" <?php echo getValue($profile, 'cleanliness') === 'clean' ? 'selected' : ''; ?>>Clean</option>
                                    <option value="average" <?php echo getValue($profile, 'cleanliness') === 'average' ? 'selected' : ''; ?>>Average</option>
                                    <option value="relaxed" <?php echo getValue($profile, 'cleanliness') === 'relaxed' ? 'selected' : ''; ?>>Relaxed</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Work Schedule</label>
                                <select name="work_schedule" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="9-5" <?php echo getValue($profile, 'work_schedule') === '9-5' ? 'selected' : ''; ?>>9-5 Office</option>
                                    <option value="remote" <?php echo getValue($profile, 'work_schedule') === 'remote' ? 'selected' : ''; ?>>Remote/WFH</option>
                                    <option value="shift" <?php echo getValue($profile, 'work_schedule') === 'shift' ? 'selected' : ''; ?>>Shift Work</option>
                                    <option value="student" <?php echo getValue($profile, 'work_schedule') === 'student' ? 'selected' : ''; ?>>Student</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Noise Level</label>
                                <select name="noise_level" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="quiet" <?php echo getValue($profile, 'noise_level') === 'quiet' ? 'selected' : ''; ?>>Quiet</option>
                                    <option value="moderate" <?php echo getValue($profile, 'noise_level') === 'moderate' ? 'selected' : ''; ?>>Moderate</option>
                                    <option value="lively" <?php echo getValue($profile, 'noise_level') === 'lively' ? 'selected' : ''; ?>>Lively</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 5. My Preferences -->
                    <div class="profile-card">
                        <h3 class="card-title">My Preferences <span style="color: rgba(0,0,0,0.5); font-size: 0.875rem; font-weight: 400;">(<span id="selectedCount">0</span>/6 selected)</span></h3>
                        <p style="color: rgba(0,0,0,0.6); font-size: 0.875rem; margin-bottom: 1rem;">Select up to 6 preferences that matter most to you</p>
                        <?php
                        $preferences = json_decode(getValue($profile, 'preferences', '[]'), true) ?: [];
                        
                        // Preferences with icons (icon for display only, value goes to DB)
                        $allPreferences = [
                            'very_clean' => ['label' => 'Very Clean', 'icon' => 'sparkles'],
                            'pet_friendly' => ['label' => 'Pet-friendly', 'icon' => 'dog'],
                            'night_owl' => ['label' => 'Night Owl', 'icon' => 'moon'],
                            'student_friendly' => ['label' => 'Student-friendly', 'icon' => 'graduation-cap'],
                            'quiet' => ['label' => 'Quiet Environment', 'icon' => 'volume-x'],
                            'social' => ['label' => 'Social/Outgoing', 'icon' => 'users'],
                            'cooking' => ['label' => 'Enjoys Cooking', 'icon' => 'chef-hat'],
                            'working_professional' => ['label' => 'Working Professional', 'icon' => 'briefcase'],
                            'non_smoker' => ['label' => 'Non-smoker', 'icon' => 'cigarette-off'],
                            'early_bird' => ['label' => 'Early Bird', 'icon' => 'sunrise'],
                            'fitness' => ['label' => 'Fitness Enthusiast', 'icon' => 'dumbbell'],
                            'vegan' => ['label' => 'Vegan/Vegetarian', 'icon' => 'leaf'],
                            'organized' => ['label' => 'Organized', 'icon' => 'folder-check'],
                            'relaxed' => ['label' => 'Relaxed/Easygoing', 'icon' => 'smile']
                        ];
                        ?>
                        <div class="preferences-grid">
                            <?php foreach ($allPreferences as $value => $pref): ?>
                            <label class="preference-checkbox">
                                <input type="checkbox" name="preferences[]" value="<?php echo $value; ?>" <?php echo in_array($value, $preferences) ? 'checked' : ''; ?>>
                                <span>
                                    <i data-lucide="<?php echo $pref['icon']; ?>" style="width: 1rem; height: 1rem; margin-right: 0.375rem;"></i>
                                    <?php echo $pref['label']; ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Change Password Section -->
                    <div class="profile-card">
                        <h3 class="card-title">Change Password</h3>
                        <p style="color: rgba(0,0,0,0.6); font-size: 0.875rem; margin-bottom: 1rem;">Update your password to keep your account secure</p>
                        <div class="form-group-stack">
                            <div>
                                <label class="form-label">Current Password</label>
                                <div class="input-wrapper">
                                    <i data-lucide="lock" class="input-icon"></i>
                                    <input type="password" name="current_password" class="form-input" placeholder="Enter current password">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">New Password</label>
                                <div class="input-wrapper">
                                    <i data-lucide="lock" class="input-icon"></i>
                                    <input type="password" name="new_password" class="form-input" placeholder="Enter new password">
                                </div>
                                <p style="color: rgba(0,0,0,0.5); font-size: 0.75rem; margin-top: 0.25rem;">Must be at least 8 characters</p>
                            </div>

                            <div>
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-wrapper">
                                    <i data-lucide="lock" class="input-icon"></i>
                                    <input type="password" name="confirm_password" class="form-input" placeholder="Re-enter new password">
                                </div>
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
                                Your profile is <span id="completionPercent">0</span>% complete
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

        // Photo preview
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

        // Preferences counter
        const preferenceCheckboxes = document.querySelectorAll('input[name="preferences[]"]');
        const selectedCount = document.getElementById('selectedCount');
        
        function updatePreferenceCount() {
            const checked = Array.from(preferenceCheckboxes).filter(cb => cb.checked);
            selectedCount.textContent = checked.length;
            
            // Disable unchecked if 6 selected
            if (checked.length >= 6) {
                preferenceCheckboxes.forEach(cb => {
                    if (!cb.checked) cb.disabled = true;
                });
            } else {
                preferenceCheckboxes.forEach(cb => cb.disabled = false);
            }
        }
        
        preferenceCheckboxes.forEach(cb => {
            cb.addEventListener('change', updatePreferenceCount);
        });
        updatePreferenceCount();

        // Form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ProfileController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Toastify({
                        text: "Profile updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #10b981, #059669)",
                    }).showToast();
                    
                    // Update profile completion
                    if (result.completion) {
                        document.getElementById('completionPercent').textContent = result.completion;
                    }
                    
                    // Reload after 1 second
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Toastify({
                        text: result.message || "Error updating profile",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                    }).showToast();
                }
            } catch (error) {
                console.error('Error:', error);
                Toastify({
                    text: "Network error. Please try again.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                }).showToast();
            }
        });

        // Calculate profile completion on load
        fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ProfileController.php?action=get_completion')
            .then(r => r.json())
            .then(data => {
                if (data.completion !== undefined) {
                    document.getElementById('completionPercent').textContent = data.completion;
                }
            });
    </script>
</body>
</html>
