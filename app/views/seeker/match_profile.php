<?php
// Mock Data
$profile = [
    'id' => 1,
    'name' => 'Sarah Johnson',
    'age' => 25,
    'occupation' => 'Software Engineer',
    'location' => 'San Francisco, CA',
    'memberSince' => 'January 2024',
    'responseRate' => 95,
    'responseTime' => '2 hours',
    'images' => [
        'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=800',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=800',
        'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=800',
    ],
    'bio' => "Hey! I'm Sarah, a software engineer who loves staying active and exploring the city. I work from home a few days a week, so I appreciate a quiet space during the day. In my free time, you'll find me at yoga class, trying new coffee shops, or hiking on weekends. I'm looking for a roommate who values cleanliness, open communication, and creating a positive living environment together.",
    'compatibility' => [
        'overall' => 92,
        'lifestyle' => 95,
        'cleanliness' => 90,
        'schedule' => 88,
        'social' => 94,
    ],
    'interests' => [
        'Hiking', 'Yoga', 'Coffee', 'Cooking', 'Reading', 'Photography', 'Travel', 'Podcasts'
    ],
    'lifestyle' => [
        ['icon' => 'sun', 'label' => 'Sleep Schedule', 'value' => 'Early Bird'],
        ['icon' => 'check-circle', 'label' => 'Cleanliness', 'value' => 'Very Clean'],
        ['icon' => 'users', 'label' => 'Social Level', 'value' => 'Balanced'],
        ['icon' => 'briefcase', 'label' => 'Work Schedule', 'value' => 'Hybrid (WFH 3 days)'],
        ['icon' => 'users', 'label' => 'Guests', 'value' => 'Occasionally'],
        ['icon' => 'volume-2', 'label' => 'Noise Level', 'value' => 'Quiet'],
        ['icon' => 'thermometer', 'label' => 'Temperature', 'value' => 'Moderate'],
    ],
    'preferences' => [
        ['icon' => 'cigarette', 'label' => 'Non-smoker', 'value' => true],
        ['icon' => 'paw-print', 'label' => 'Pet-friendly', 'value' => true],
        ['icon' => 'coffee', 'label' => 'Social drinker', 'value' => true],
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
                                <button class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;" onclick="window.location.href='messages.php'">
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
                                <?php if($pref['value']): ?>
                                <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem; color: var(--green);"></i>
                                <?php else: ?>
                                <i data-lucide="x-circle" style="width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.2);"></i>
                                <?php endif; ?>
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
