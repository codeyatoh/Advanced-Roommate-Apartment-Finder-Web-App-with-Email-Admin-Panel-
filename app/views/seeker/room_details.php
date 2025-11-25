<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-card.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-details.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <!-- Back Button -->
                <button class="btn btn-ghost btn-sm" style="margin-bottom: 1.5rem;">
                    <i data-lucide="arrow-left" class="btn-icon"></i>
                    Back to Browse
                </button>

                <!-- Image Gallery -->
                <div style="margin-bottom: 3rem; animation: slideUp 0.3s ease-out;">
                    <div class="room-gallery-main">
                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200" alt="Room">
                        <div class="room-gallery-actions">
                            <button class="btn btn-glass btn-md"><i data-lucide="heart" style="width: 1.25rem; height: 1.25rem;"></i></button>
                            <button class="btn btn-glass btn-md"><i data-lucide="share-2" style="width: 1.25rem; height: 1.25rem;"></i></button>
                        </div>
                    </div>
                    <div class="room-gallery-thumbnails">
                        <?php 
                        $images = [
                            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200',
                            'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=1200',
                            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200',
                            'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=1200',
                            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200'
                        ];
                        foreach ($images as $index => $img): ?>
                        <div class="room-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $img; ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    <!-- Main Content -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Title & Location -->
                        <div>
                            <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin: 0 0 0.75rem 0;">Modern Studio in Downtown</h1>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0,0,0,0.6); margin-bottom: 1rem;">
                                <i data-lucide="map-pin" style="width: 1.25rem; height: 1.25rem;"></i>
                                <span style="font-size: 1.125rem;">123 Market Street, San Francisco, CA 94103</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <i data-lucide="star" style="width: 1.25rem; height: 1.25rem; color: #facc15; fill: #facc15;"></i>
                                    <span style="font-weight: 600; color: #000;">4.8</span>
                                    <span style="color: rgba(0,0,0,0.6);">(24 reviews)</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: rgba(16,185,129,0.1); color: #059669; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    <i data-lucide="shield" style="width: 1rem; height: 1rem;"></i>
                                    Verified Property
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <div class="room-stats-grid">
                                <?php 
                                $stats = [
                                    ['icon' => 'bed', 'label' => 'Bedrooms', 'value' => '1'],
                                    ['icon' => 'bath', 'label' => 'Bathrooms', 'value' => '1'],
                                    ['icon' => 'users', 'label' => 'Roommates', 'value' => '0'],
                                    ['icon' => 'calendar', 'label' => 'Available', 'value' => 'Now']
                                ];
                                foreach ($stats as $stat): ?>
                                <div class="room-stat-item">
                                    <div class="room-stat-icon"><i data-lucide="<?php echo $stat['icon']; ?>"></i></div>
                                    <p class="room-stat-label"><?php echo $stat['label']; ?></p>
                                    <p class="room-stat-value"><?php echo $stat['value']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">About this room</h2>
                            <div style="display: flex; flex-direction: column; gap: 1rem; color: rgba(0,0,0,0.7); line-height: 1.6;">
                                <p>Beautiful modern studio apartment in the heart of downtown San Francisco. This fully furnished space features high ceilings, large windows with plenty of natural light, and contemporary finishes throughout.</p>
                                <p>The building offers excellent amenities including a fitness center, rooftop terrace, and 24/7 security. Located within walking distance to public transportation, restaurants, cafes, and shopping centers.</p>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">Amenities & Features</h2>
                            <div class="amenities-grid">
                                <?php 
                                $amenities = [
                                    ['icon' => 'wifi', 'label' => 'High-Speed WiFi'],
                                    ['icon' => 'car', 'label' => 'Parking Included'],
                                    ['icon' => 'coffee', 'label' => 'Full Kitchen'],
                                    ['icon' => 'dumbbell', 'label' => 'Gym Access'],
                                    ['icon' => 'wind', 'label' => 'Air Conditioning'],
                                    ['icon' => 'flame', 'label' => 'Heating']
                                ];
                                foreach ($amenities as $amenity): ?>
                                <div class="amenity-item">
                                    <div class="amenity-icon"><i data-lucide="<?php echo $amenity['icon']; ?>"></i></div>
                                    <span class="amenity-label"><?php echo $amenity['label']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- House Rules -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">House Rules</h2>
                            <div class="house-rules-list">
                                <?php 
                                $rules = [
                                    ['rule' => 'No smoking indoors', 'allowed' => false, 'icon' => 'cigarette'],
                                    ['rule' => 'Pets allowed (cats only)', 'allowed' => true, 'icon' => 'paw-print'],
                                    ['rule' => 'Quiet hours: 10 PM - 8 AM', 'allowed' => true, 'icon' => 'clock']
                                ];
                                foreach ($rules as $rule): ?>
                                <div class="house-rule-item">
                                    <div class="house-rule-icon <?php echo $rule['allowed'] ? 'allowed' : 'not-allowed'; ?>">
                                        <i data-lucide="<?php echo $rule['allowed'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                    </div>
                                    <div class="house-rule-content">
                                        <i data-lucide="<?php echo $rule['icon']; ?>"></i>
                                        <span class="house-rule-text"><?php echo $rule['rule']; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <div class="card card-glass-strong booking-card" style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <div class="booking-price">
                                    <span class="booking-price-amount">₱1,200</span>
                                    <span class="booking-price-period">/month</span>
                                </div>
                                <p class="booking-price-note">Utilities included • Security deposit: ₱1,200</p>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <button class="btn btn-primary btn-lg" style="width: 100%;">
                                    <i data-lucide="message-square" class="btn-icon"></i>
                                    Send Inquiry
                                </button>
                                <button class="btn btn-glass btn-lg" style="width: 100%;">
                                    <i data-lucide="calendar" class="btn-icon"></i>
                                    Schedule Viewing
                                </button>
                            </div>

                            <div style="padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <h3 style="font-weight: 700; color: #000000; margin: 0 0 1rem 0;">Landlord</h3>
                                <div class="landlord-info">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400" alt="Landlord" class="landlord-avatar">
                                    <div>
                                        <p class="landlord-name">David Martinez</p>
                                        <div class="landlord-verified">
                                            <i data-lucide="shield"></i>
                                            <span>Verified Landlord</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="landlord-stats">
                                    <div class="landlord-stat">
                                        <p>8</p>
                                        <p>Properties</p>
                                    </div>
                                    <div class="landlord-stat">
                                        <p>4.9</p>
                                        <p>Rating</p>
                                    </div>
                                    <div class="landlord-stat">
                                        <p>2 years</p>
                                        <p>Member</p>
                                    </div>
                                </div>
                            </div>

                            <div style="padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 1.5rem;">
                                <p class="booking-response-time">Response time: Usually within 2 hours</p>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"][style*="gap: 2rem"] {
                            grid-template-columns: 2fr 1fr !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
