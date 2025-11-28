<?php
session_start();
require_once __DIR__ . '/../../models/User.php';

// Get user ID from URL
$userId = $_GET['user_id'] ?? 1;
$userModel = new User();
$user = $userModel->getUserWithProfile($userId);

// Redirect if user not found
if (!$user) {
    header('Location: roommate_finder.php');
    exit;
}

// Parse profile data
$profileData = $user['profile'] ?? [];
$preferences = !empty($profileData['preferences']) ? json_decode($profileData['preferences'], true) : [];

// Calculate Age
$age = 'N/A';
if (!empty($user['birthdate'])) {
    $birthDate = new DateTime($user['birthdate']);
    $today = new DateTime();
    $age = $birthDate->diff($today)->y;
}

// Map data to view structure
$profile = [
    'id' => $user['user_id'],
    'name' => $user['first_name'] . ' ' . $user['last_name'],
    'age' => $age,
    'occupation' => $profileData['occupation'] ?? 'Room Seeker',
    'location' => $profileData['preferred_location'] ?? 'Not specified',
    'memberSince' => date('F Y', strtotime($user['created_at'])),
    'responseRate' => 100, // Placeholder
    'responseTime' => 'Within a day', // Placeholder
    'images' => [
        !empty($user['profile_photo']) ? $user['profile_photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) . '&background=10b981&color=fff&size=800'
    ],
    'bio' => $user['bio'] ?? 'No bio available.',
    'compatibility' => [
        'overall' => 95, // Placeholder logic for now
        'lifestyle' => 90,
        'cleanliness' => 85,
        'schedule' => 92,
        'social' => 88,
    ],
    'interests' => [], // Needs a separate table or JSON field
    'lifestyle' => [
        ['icon' => 'sun', 'label' => 'Sleep Schedule', 'value' => $profileData['sleep_schedule'] ?? 'Not specified'],
        ['icon' => 'check-circle', 'label' => 'Cleanliness', 'value' => $profileData['cleanliness'] ?? 'Not specified'],
        ['icon' => 'users', 'label' => 'Social Level', 'value' => $profileData['social_level'] ?? 'Not specified'],
        ['icon' => 'briefcase', 'label' => 'Work Schedule', 'value' => $profileData['work_schedule'] ?? 'Not specified'],
        ['icon' => 'volume-2', 'label' => 'Noise Level', 'value' => $profileData['noise_level'] ?? 'Not specified'],
    ],
    'preferences' => [], // Populated below
    'lookingFor' => [
        'moveInDate' => $profileData['move_in_date'] ?? 'Flexible',
        'budget' => '$' . ($profileData['budget'] ?? '0'),
        'location' => $profileData['preferred_location'] ?? 'Any',
        'roomType' => 'Private Room', // Placeholder
        'leaseTerm' => '12 Months', // Placeholder
    ]
];

// Populate Preferences from JSON
if (is_array($preferences)) {
    foreach ($preferences as $pref) {
        $icon = 'check-circle'; // Default
        $lowerPref = strtolower($pref);
        
        if (strpos($lowerPref, 'smoke') !== false) $icon = 'cigarette-off';
        elseif (strpos($lowerPref, 'pet') !== false) $icon = 'paw-print';
        elseif (strpos($lowerPref, 'drink') !== false) $icon = 'coffee';
        elseif (strpos($lowerPref, 'clean') !== false) $icon = 'sparkles';
        elseif (strpos($lowerPref, 'student') !== false) $icon = 'graduation-cap';
        elseif (strpos($lowerPref, 'work') !== false) $icon = 'briefcase';
        elseif (strpos($lowerPref, 'cook') !== false) $icon = 'chef-hat';
        
        $profile['preferences'][] = ['icon' => $icon, 'label' => $pref, 'value' => true];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $profile['name']; ?> - Match Profile</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/match-profile.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="match-profile-container">
            <div class="match-profile-content">
                <!-- Back Button -->
                <button class="btn btn-ghost btn-sm" onclick="history.back()" style="margin-bottom: 1.5rem;">
                    <i data-lucide="arrow-left" class="btn-icon"></i>
                    Back to Matches
                </button>

                <!-- Main Profile Card -->
                <div class="card card-glass" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
                    <div class="profile-hero-grid">
                        <!-- Left Column - Image -->
                        <div class="hero-image-section" style="position: relative; height: 400px;">
                            <img src="<?php echo $profile['images'][0]; ?>" alt="<?php echo $profile['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            
                            <!-- Match Badge -->
                            <div style="position: absolute; top: 1rem; right: 1rem; background: white; padding: 0.5rem 1rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <i data-lucide="trending-up" style="width: 1rem; height: 1rem; color: var(--green);"></i>
                                <span style="font-weight: 700; color: var(--green);"><?php echo $profile['compatibility']['overall']; ?>% Match</span>
                            </div>
                        </div>

                        <!-- Right Column - Info -->
                        <div class="profile-info-section">
                            <!-- Header -->
                            <div class="profile-header">
                                <h1 class="profile-name-main"><?php echo $profile['name']; ?>, <?php echo $profile['age']; ?></h1>
                                <div class="profile-meta-list">
                                    <div class="meta-item-small">
                                        <i data-lucide="briefcase"></i>
                                        <span><?php echo $profile['occupation']; ?></span>
                                    </div>
                                    <div class="meta-item-small">
                                        <i data-lucide="map-pin"></i>
                                        <span><?php echo $profile['location']; ?></span>
                                    </div>
                                    <div class="meta-item-small">
                                        <i data-lucide="calendar"></i>
                                        <span>Member since <?php echo $profile['memberSince']; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Response Stats -->
                            <div class="response-stats-compact">
                                <div>
                                    <p class="stat-value-main"><?php echo $profile['responseRate']; ?>%</p>
                                    <p class="stat-label-main">Response Rate</p>
                                </div>
                                <div class="stat-divider-main"></div>
                                <div>
                                    <p class="stat-value-main"><?php echo $profile['responseTime']; ?></p>
                                    <p class="stat-label-main">Avg Response</p>
                                </div>
                            </div>

                            <!-- Compatibility Grid -->
                            <div class="compatibility-section">
                                <h3 class="section-subtitle">Compatibility</h3>
                                <div class="compatibility-grid">
                                    <?php 
                                    $categories = [
                                        ['label' => 'Lifestyle', 'score' => $profile['compatibility']['lifestyle'], 'icon' => 'home'],
                                        ['label' => 'Cleanliness', 'score' => $profile['compatibility']['cleanliness'], 'icon' => 'check-circle'],
                                        ['label' => 'Schedule', 'score' => $profile['compatibility']['schedule'], 'icon' => 'clock'],
                                        ['label' => 'Social', 'score' => $profile['compatibility']['social'], 'icon' => 'users'],
                                    ];
                                    foreach($categories as $cat): ?>
                                    <div class="compat-item">
                                        <div class="compat-icon">
                                            <i data-lucide="<?php echo $cat['icon']; ?>"></i>
                                        </div>
                                        <p class="compat-score"><?php echo $cat['score']; ?>%</p>
                                        <p class="compat-label"><?php echo $cat['label']; ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="profile-actions">
                                <button class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;" onclick="window.location.href='messages.php?user_id=<?php echo $profile['id']; ?>'">
                                    <i data-lucide="message-square" class="btn-icon"></i>
                                    Message
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Details Sections -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <!-- About -->
                    <div>
                        <h2 class="section-title">About</h2>
                        <p style="color: rgba(0,0,0,0.7); line-height: 1.75; font-size: 1.125rem;">
                            <?php echo $profile['bio']; ?>
                        </p>
                    </div>

                    <!-- Interests -->
                    <div>
                        <h2 class="section-title">Interests</h2>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            <?php foreach($profile['interests'] as $interest): ?>
                            <span style="padding: 0.5rem 1rem; background: rgba(0,0,0,0.05); color: #000; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                                <?php echo $interest; ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Lifestyle -->
                    <div>
                        <h2 class="section-title">Lifestyle</h2>
                        <div class="lifestyle-grid">
                            <?php foreach($profile['lifestyle'] as $item): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i data-lucide="<?php echo $item['icon']; ?>" style="width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                                    <span style="color: rgba(0,0,0,0.7);"><?php echo $item['label']; ?></span>
                                </div>
                                <span style="font-weight: 500; color: #000;"><?php echo $item['value']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Preferences -->
                    <div>
                        <h2 class="section-title">Preferences</h2>
                        <div class="preferences-grid">
                            <?php foreach($profile['preferences'] as $pref): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i data-lucide="<?php echo $pref['icon']; ?>" style="width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                                    <span style="color: #000;"><?php echo $pref['label']; ?></span>
                                </div>
                                <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem; color: var(--green);"></i>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Looking For -->
                    <div>
                        <h2 class="section-title">Looking For</h2>
                        <div class="looking-for-grid">
                            <div>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;">Move-in Date</p>
                                <p style="font-weight: 500; color: #000;"><?php echo $profile['lookingFor']['moveInDate']; ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;">Budget</p>
                                <p style="font-weight: 500; color: #000;"><?php echo $profile['lookingFor']['budget']; ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;">Location</p>
                                <p style="font-weight: 500; color: #000;"><?php echo $profile['lookingFor']['location']; ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;">Room Type</p>
                                <p style="font-weight: 500; color: #000;"><?php echo $profile['lookingFor']['roomType']; ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;">Lease Term</p>
                                <p style="font-weight: 500; color: #000;"><?php echo $profile['lookingFor']['leaseTerm']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Image Carousel Logic
        const images = <?php echo json_encode($profile['images']); ?>;
        let currentIndex = 0;
        const heroImage = document.getElementById('heroImage');
        const indicators = document.querySelectorAll('.indicator');

        function updateImage() {
            heroImage.src = images[currentIndex];
            indicators.forEach((ind, idx) => {
                if (idx === currentIndex) ind.classList.add('active');
                else ind.classList.remove('active');
            });
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            updateImage();
        }

        function prevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateImage();
        }

        function setImage(index) {
            currentIndex = index;
            updateImage();
        }
    </script>
</body>
</html>
