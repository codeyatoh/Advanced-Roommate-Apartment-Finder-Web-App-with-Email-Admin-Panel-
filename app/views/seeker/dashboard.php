<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-card.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">Welcome back, John! 👋</h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">Here's what's happening with your room search</p>
                </div>

                <!-- Quick Stats -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
                    <?php 
                    $stats = [
                        ['icon' => 'heart', 'value' => '12', 'label' => 'Saved Rooms'],
                        ['icon' => 'message-square', 'value' => '5', 'label' => 'Messages'],
                        ['icon' => 'users', 'value' => '8', 'label' => 'Matches'],
                        ['icon' => 'calendar', 'value' => '3', 'label' => 'Viewings']
                    ];
                    foreach ($stats as $stat): ?>
                    <div class="card card-glass" style="padding: 1.25rem;">
                        <div style="width: 3rem; height: 3rem; background: rgba(16,185,129,0.2); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                            <i data-lucide="<?php echo $stat['icon']; ?>" style="width: 1.5rem; height: 1.5rem; color: #10b981;"></i>
                        </div>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #000000; margin: 0 0 0.125rem 0;"><?php echo $stat['value']; ?></p>
                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;"><?php echo $stat['label']; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                    <!-- Main Content -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- Recommended Rooms -->
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                                <div>
                                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 0.125rem 0;">Recommended for You</h2>
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;">Based on your preferences</p>
                                </div>
                                <button class="btn btn-ghost btn-sm">
                                    View All
                                    <i data-lucide="arrow-right" class="btn-icon"></i>
                                </button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem;">
                                <?php 
                                $rooms = [
                                    ['image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800', 'title' => 'Modern Studio Downtown', 'location' => 'San Francisco, CA', 'price' => 1200, 'bedrooms' => 1, 'bathrooms' => 1, 'roommates' => 0, 'available' => 'Now'],
                                    ['image' => 'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=800', 'title' => 'Cozy Room with View', 'location' => 'Portland, OR', 'price' => 850, 'bedrooms' => 1, 'bathrooms' => 1, 'roommates' => 2, 'available' => 'Feb 1']
                                ];
                                foreach ($rooms as $room): ?>
                                <div class="room-card">
                                    <div class="room-card-image-wrapper">
                                        <img src="<?php echo $room['image']; ?>" alt="<?php echo $room['title']; ?>" class="room-card-image">
                                        <button class="room-card-favorite"><i data-lucide="heart"></i></button>
                                        <div class="room-card-badge">Available <?php echo $room['available']; ?></div>
                                    </div>
                                    <div class="room-card-content">
                                        <h3 class="room-card-title"><?php echo $room['title']; ?></h3>
                                        <div class="room-card-location"><i data-lucide="map-pin"></i><?php echo $room['location']; ?></div>
                                        <div class="room-card-details">
                                            <div class="room-card-detail"><i data-lucide="bed"></i><?php echo $room['bedrooms']; ?> bed</div>
                                            <div class="room-card-detail"><i data-lucide="bath"></i><?php echo $room['bathrooms']; ?> bath</div>
                                            <div class="room-card-detail"><i data-lucide="users"></i><?php echo $room['roommates']; ?> roommates</div>
                                        </div>
                                        <div style="padding-top: 0.75rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                            <div class="room-card-price"><span>$<?php echo $room['price']; ?></span><span class="room-card-price-period">/month</span></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card card-glass" style="padding: 1.25rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">Recent Activity</h3>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <?php 
                                $activities = [
                                    ['icon' => 'heart', 'text' => 'You saved "Modern Studio Downtown"', 'time' => '2 hours ago'],
                                    ['icon' => 'message-square', 'text' => 'New message from Sarah Johnson', 'time' => '5 hours ago'],
                                    ['icon' => 'calendar', 'text' => 'Viewing scheduled for tomorrow', 'time' => '1 day ago']
                                ];
                                foreach ($activities as $activity): ?>
                                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(0, 0, 0, 0.1);">
                                    <div style="width: 2.5rem; height: 2.5rem; background: rgba(16,185,129,0.2); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="<?php echo $activity['icon']; ?>" style="width: 1.25rem; height: 1.25rem; color: #10b981;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <p style="font-size: 0.875rem; color: #000000; font-weight: 500; margin: 0 0 0.125rem 0;"><?php echo $activity['text']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.5); margin: 0;"><?php echo $activity['time']; ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <!-- Roommate Matches -->
                        <div class="card card-glass" style="padding: 1.25rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                                <div>
                                    <h3 style="font-size: 1rem; font-weight: 700; color: #000000; margin: 0 0 0.125rem 0;">Roommate Matches</h3>
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;">Top compatibility</p>
                                </div>
                                <button class="btn btn-ghost btn-sm" style="font-size: 0.75rem;">See All</button>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                                <?php 
                                $matches = [
                                    ['name' => 'Sarah Johnson', 'occupation' => 'Software Engineer', 'compatibility' => 92, 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400'],
                                    ['name' => 'Mike Chen', 'occupation' => 'Graphic Designer', 'compatibility' => 88, 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400']
                                ];
                                foreach ($matches as $match): ?>
                                <div style="display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem; background: var(--glass-bg-subtle); border-radius: 0.75rem; cursor: pointer;">
                                    <img src="<?php echo $match['image']; ?>" alt="<?php echo $match['name']; ?>" style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; object-fit: cover;">
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-weight: 600; font-size: 0.875rem; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 0 0.125rem 0;"><?php echo $match['name']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;"><?php echo $match['occupation']; ?></p>
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                                            <i data-lucide="trending-up" style="width: 0.875rem; height: 0.875rem; color: #10b981;"></i>
                                            <span style="font-size: 0.875rem; font-weight: 700; color: #10b981;"><?php echo $match['compatibility']; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card card-glass" style="padding: 1.25rem;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: #000000; margin: 0 0 0.75rem 0;">Quick Actions</h3>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <button class="btn btn-primary btn-sm" style="width: 100%; justify-content: flex-start;">Browse Rooms</button>
                                <button class="btn btn-glass btn-sm" style="width: 100%; justify-content: flex-start;">Find Roommates</button>
                                <button class="btn btn-glass btn-sm" style="width: 100%; justify-content: flex-start;">View Messages</button>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    @media (min-width: 768px) {
                        div[style*="grid-template-columns: 1fr"][style*="gap: 1.25rem"] {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"][style*="gap: 1.5rem"] {
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
