<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
</head>
<body>
    <?php
    // Start session and check authentication
    session_start();
    
    // For now, using hardcoded landlord ID - should come from session in production
    $landlordId = $_SESSION['user_id'] ?? 2;
    
    // Load Controller
    require_once __DIR__ . '/../../controllers/landlord/DashboardController.php';
    $dashboardController = new DashboardController();
    $data = $dashboardController->getDashboardData($landlordId);

   // Extract data
    $stats = $data['stats'];
    $pendingViewings = $data['pending_viewings'];
    $recentInquiries = $data['recent_inquiries'];
    $recentActivity = $data['recent_activity'];
    $performance = $data['performance'];
    $monthlyRevenue = $performance['monthly_revenue'];
    $occupancyRate = $performance['occupancy_rate'];
    $occupiedBedrooms = $performance['occupied_bedrooms'];
    $totalBedrooms = $performance['total_bedrooms'];
    
    // Assuming it does or I'll need to instantiate User model here or update controller.
    // To be safe, I'll instantiate User model here for helper lookups if needed, 
    // but ideally controller should return everything.
    require_once __DIR__ . '/../../models/User.php';
    $userModel = new User();
    ?>
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">Landlord Dashboard</h1>
                    <p class="page-subtitle">Manage your properties and tenant inquiries</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="home" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo intval($stats['active_listings']); ?></p>
                    <p class="stat-label">Active Listings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="message-square" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo intval($stats['new_inquiries']); ?></p>
                    <p class="stat-label">New Inquiries</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="calendar" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo intval($stats['pending_viewings']); ?></p>
                    <p class="stat-label">Pending Viewings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="eye" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo intval($stats['total_listings']); ?></p>
                    <p class="stat-label">Total Listings</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Recent Inquiries -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <div>
                                <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 0.125rem;">Recent Inquiries</h2>
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);">New messages from potential tenants</p>
                            </div>
                        </div>
                        <?php if (empty($recentInquiries)): ?>
                            <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 2rem;">No recent inquiries</p>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach ($recentInquiries as $inquiry): 
                                    // Fetch sender details if not in inquiry array (Message model usually returns sender_id)
                                    // getLandlordInquiries returns: other_user_id, listing_id, last_message, last_message_time, unread_count
                                    $sender = $userModel->getById($inquiry['other_user_id']);
                                    $senderName = $sender ? $sender['first_name'] . ' ' . $sender['last_name'] : 'Unknown User';
                                    $timeAgo = 'Just now'; // Simplified
                                    $seconds = time() - strtotime($inquiry['last_message_time']);
                                    if ($seconds < 60) $timeAgo = 'Just now';
                                    elseif ($seconds < 3600) $timeAgo = floor($seconds/60) . 'm ago';
                                    elseif ($seconds < 86400) $timeAgo = floor($seconds/3600) . 'h ago';
                                    else $timeAgo = floor($seconds/86400) . 'd ago';
                                ?>
                                <a href="inquiries.php?user_id=<?php echo $inquiry['other_user_id']; ?>" class="inquiry-item" style="text-decoration: none; color: inherit;">
                                    <img src="<?php echo $sender['profile_photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($senderName); ?>" class="inquiry-avatar" alt="<?php echo htmlspecialchars($senderName); ?>">
                                    <div class="inquiry-content">
                                        <div class="inquiry-header">
                                            <span class="inquiry-name"><?php echo htmlspecialchars($senderName); ?></span>
                                            <span class="inquiry-time"><?php echo $timeAgo; ?></span>
                                        </div>
                                        <p class="inquiry-message"><?php echo htmlspecialchars($inquiry['last_message']); ?></p>
                                    </div>
                                    <?php if ($inquiry['unread_count'] > 0): ?>
                                        <div class="unread-indicator"></div>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Performance Overview -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Performance Overview</h2>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i data-lucide="coins" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Monthly Revenue</p>
                                </div>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #000;">₱<?php echo number_format($monthlyRevenue, 0); ?></p>
                                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                                    <span style="font-size: 0.75rem; color: rgba(0,0,0,0.6);">From <?php echo $occupiedBedrooms; ?> bedrooms occupied</span>
                                </div>
                            </div>
                            <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i data-lucide="users" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Occupancy Rate</p>
                                </div>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #000;"><?php echo $occupancyRate; ?>%</p>
                                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                                    <span style="font-size: 0.75rem; color: rgba(0,0,0,0.6);"><?php echo $occupiedBedrooms; ?>/<?php echo $totalBedrooms; ?> bedrooms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Pending Viewings -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Pending Viewings</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php
                            if (empty($pendingViewings)):
                            ?>
                                <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 2rem; font-size: 0.875rem;">No pending viewings</p>
                            <?php
                            else:
                                foreach ($pendingViewings as $viewing):
                                    $tenant = $userModel->getById($viewing['seeker_id']);
                                    $appointmentDate = new DateTime($viewing['appointment_date'] . ' ' . $viewing['appointment_time']);
                                    $dateFormatted = $appointmentDate->format('M j, Y');
                                    $timeFormatted = $appointmentDate->format('g:i A');
                            ?>
                            <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: flex-start; gap: 0.625rem; margin-bottom: 0.75rem;">
                                    <div style="width: 2.25rem; height: 2.25rem; background-color: rgba(30, 58, 138, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="calendar" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-weight: 600; font-size: 0.875rem; color: #000; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($viewing['listing_title'] ?? 'Listing'); ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);"><?php echo $dateFormatted; ?>, <?php echo $timeFormatted; ?></p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="appointments.php" class="btn btn-primary btn-sm" style="flex: 1; font-size: 0.75rem; text-decoration: none; text-align: center;">View</a>
                                </div>
                            </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Recent Activity</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php if (empty($recentActivity)): ?>
                                <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 1rem; font-size: 0.875rem;">No recent activity</p>
                            <?php else: ?>
                                <?php foreach ($recentActivity as $activity): 
                                    $timeAgo = 'Just now';
                                    $seconds = time() - strtotime($activity['created_at']);
                                    if ($seconds < 60) $timeAgo = 'Just now';
                                    elseif ($seconds < 3600) $timeAgo = floor($seconds/60) . 'm ago';
                                    elseif ($seconds < 86400) $timeAgo = floor($seconds/3600) . 'h ago';
                                    else $timeAgo = floor($seconds/86400) . 'd ago';
                                    
                                    $icon = 'activity';
                                    if (strpos($activity['action'], 'login') !== false) $icon = 'log-in';
                                    elseif (strpos($activity['action'], 'listing') !== false) $icon = 'home';
                                    elseif (strpos($activity['action'], 'appointment') !== false) $icon = 'calendar';
                                ?>
                                <div style="display: flex; gap: 0.75rem; align-items: start; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <i data-lucide="<?php echo $icon; ?>" style="width: 1rem; height: 1rem; color: var(--deep-blue); margin-top: 0.25rem;"></i>
                                    <div>
                                        <p style="font-size: 0.8rem; color: #000; margin: 0;"><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <p style="font-size: 0.7rem; color: rgba(0,0,0,0.5); margin: 0;"><?php echo $timeAgo; ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php include __DIR__ . '/../includes/report_widget.php'; ?>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
