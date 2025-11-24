<?php
// Start session and check authentication
session_start();

// For now, using hardcoded user ID - should come from session in production
$userId = $_SESSION['user_id'] ?? 1;
$userName = $_SESSION['first_name'] ?? 'John';

// Load models
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Listing.php';
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/Appointment.php';

$userModel = new User();
$listingModel = new Listing();
$messageModel = new Message();
$appointmentModel = new Appointment();

// Fetch dashboard data
$unreadMessages = $messageModel->getUnreadCount($userId);
$upcomingAppointments = $appointmentModel->getUpcoming($userId, 'seeker');
$recommendedListings = $listingModel->getAvailable(2); // Get 2 listings
?>
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
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">Here's what's happening with your room search</p>
                </div>

                <!-- Quick Stats -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
                    <?php 
                    $stats = [
                        ['icon' => 'heart', 'value' => '0', 'label' => 'Saved Rooms'], // TODO: Implement favorites
                        ['icon' => 'message-square', 'value' => $unreadMessages, 'label' => 'Messages'],
                        ['icon' => 'users', 'value' => '0', 'label' => 'Matches'], // TODO: Implement matching
                        ['icon' => 'calendar', 'value' => count($upcomingAppointments), 'label' => 'Viewings']
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
                                if (empty($recommendedListings)) {
                                    echo '<p style="color: rgba(0,0,0,0.5); text-align: center; padding: 2rem;">No listings available at the moment.</p>';
                                }
                                
                                foreach ($recommendedListings as $room): 
                                    $image = $room['primary_image'] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800';
                                    $availableText = !empty($room['available_from']) ? date('M j', strtotime($room['available_from'])) : 'Now';
                                ?>
                                <div class="room-card">
                                    <div class="room-card-image-wrapper">
                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" class="room-card-image">
                                        <button class="room-card-favorite"><i data-lucide="heart"></i></button>
                                        <div class="room-card-badge">Available <?php echo htmlspecialchars($availableText); ?></div>
                                    </div>
                                    <div class="room-card-content">
                                        <h3 class="room-card-title"><?php echo htmlspecialchars($room['title']); ?></h3>
                                        <div class="room-card-location"><i data-lucide="map-pin"></i><?php echo htmlspecialchars($room['location']); ?></div>
                                        <div class="room-card-details">
                                            <div class="room-card-detail"><i data-lucide="bed"></i><?php echo intval($room['bedrooms'] ?? 1); ?> bed</div>
                                            <div class="room-card-detail"><i data-lucide="bath"></i><?php echo intval($room['bathrooms'] ?? 1); ?> bath</div>
                                            <div class="room-card-detail"><i data-lucide="users"></i><?php echo intval($room['current_roommates'] ?? 0); ?> roommates</div>
                                        </div>
                                        <div style="padding-top: 0.75rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                            <div class="room-card-price"><span>$<?php echo number_format($room['price'], 0); ?></span><span class="room-card-price-period">/month</span></div>
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
                                <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 2rem;">
                                    Activity tracking coming soon...
                                </p>
                                <?php
                                // TODO: Implement activity logging system
                                ?>
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
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;">Other room seekers</p>
                                </div>
                                <button class="btn btn-ghost btn-sm" style="font-size: 0.75rem;">See All</button>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                                <?php 
                                // Fetch other room seekers from database
                                $matches = $userModel->getOtherSeekers($userId, 3);
                                
                                if (empty($matches)) {
                                    echo '<p style="color: rgba(0,0,0,0.5); text-align: center; padding: 1rem;">No other seekers found yet.</p>';
                                }
                                
                                foreach ($matches as $match): 
                                    $fullName = htmlspecialchars($match['first_name'] . ' ' . $match['last_name']);
                                    $occupation = htmlspecialchars($match['occupation'] ?? 'Room Seeker');
                                    $photo = !empty($match['profile_photo']) 
                                        ? htmlspecialchars($match['profile_photo']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=10b981&color=fff';
                                ?>
                                <div style="display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem; background: var(--glass-bg-subtle); border-radius: 0.75rem; cursor: pointer;">
                                    <img src="<?php echo $photo; ?>" alt="<?php echo $fullName; ?>" style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; object-fit: cover;">
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-weight: 600; font-size: 0.875rem; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 0 0.125rem 0;"><?php echo $fullName; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;"><?php echo $occupation; ?></p>
                                    </div>
                                    <div>
                                        <i data-lucide="message-square" style="width: 1rem; height: 1rem; color: #10b981;"></i>
                                    </div>
                                </div>
                                <?php endforeach; ?>
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
        <?php include __DIR__ . '/../includes/report_widget.php'; ?>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
