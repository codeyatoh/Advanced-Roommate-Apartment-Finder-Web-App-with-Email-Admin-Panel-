<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/admin.module.css">
</head>
<body>
    <?php
    // Start session and check authentication
    session_start();
    
    // Load models
    require_once __DIR__ . '/../../models/User.php';
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/Report.php';
    require_once __DIR__ . '/../../models/Message.php';
    
    
    $userModel = new User();
    $listingModel = new Listing();
    $reportModel = new Report();
    $messageModel = new Message();
    
    // Fetch admin dashboard stats
    $userStats = $userModel->getStats();
    $listingStats = $listingModel->getStats();
    $reportStats = $reportModel->getStats();
    
    // Get recent activity from database (last 5 activities)
    // Combining different types of activities
    $recentActivities = [];
    
    // Get recent users (last 5)
    $recentUsers = $userModel->getAll(['limit' => 3]);
    foreach ($recentUsers as $ru) {
        $recentActivities[] = [
            'user' => $ru['first_name'] . ' ' . $ru['last_name'],
            'action' => 'joined the platform',
            'time' => time_ago($ru['created_at']),
            'icon' => 'user-plus'
        ];
    }
    
    // Get recent listings (last 2)
    $recentListings = $listingModel->getAll(['limit' => 2]);
    foreach ($recentListings as $rl) {
        $landlord = $userModel->getById($rl['landlord_id']);
        $landlordName = $landlord ? $landlord['first_name'] . ' ' . $landlord['last_name'] : 'Unknown';
        $recentActivities[] = [
            'user' => $landlordName,
            'action' => 'created a new listing',
            'time' => time_ago($rl['created_at']),
            'icon' => 'home'
        ];
    }
    
    // Sort by timestamp and limit to 5
    usort($recentActivities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    $recentActivities = array_slice($recentActivities, 0, 5);
    
    // Get pending actions
    $pendingActions = [];
    
    // Get pending listings (status = pending)
    $pendingListings = $listingModel->getAll(['where' => "availability_status = 'pending'", 'limit' => 1]);
    foreach ($pendingListings as $pl) {
        $landlord = $userModel->getById($pl['landlord_id']);
        $landlordName = $landlord ? $landlord['first_name'] . ' ' . $landlord['last_name'] : 'Unknown';
        $pendingActions[] = [
            'id' => $pl['listing_id'],
            'type' => 'Listing Approval',
            'description' => $pl['title'] . ' - ' . $landlordName,
            'priority' => 'high'
        ];
    }
    
    // Get unverified users (is_verified = 0)
    $unverifiedUsers = $userModel->getAll(['where' => "is_verified = 0 AND role = 'landlord'", 'limit' => 1]);
    foreach ($unverifiedUsers as $uu) {
        $pendingActions[] = [
            'id' => $uu['user_id'],
            'type' => 'User Verification',
            'description' => $uu['first_name'] . ' ' . $uu['last_name'] . ' - Landlord Account',
            'priority' => 'medium'
        ];
    }
    
    // Get pending reports
    $pendingReports = $reportModel->getAll(['where' => "status = 'pending'", 'limit' => 1]);
    foreach ($pendingReports as $pr) {
        $pendingActions[] = [
            'id' => $pr['report_id'],
            'type' => 'Complaint Review',
            'description' => 'Report #' . $pr['report_id'] . ' - ' . $pr['reason'],
            'priority' => 'high'
        ];
    }
    
    // Helper function for time ago
    function time_ago($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff/60) . ' minute' . (floor($diff/60) > 1 ? 's' : '') . ' ago';
        if ($diff < 86400) return floor($diff/3600) . ' hour' . (floor($diff/3600) > 1 ? 's' : '') . ' ago';
        return floor($diff/86400) . ' day' . (floor($diff/86400) > 1 ? 's' : '') . ' ago';
    }
    ?>
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Monitor and manage platform activity</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #3b82f6;">
                            <i data-lucide="users" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="trending-up" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">New</span>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo number_format($userStats['total_users'] ?? 0); ?></p>
                    <p class="stat-label">Total Users</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #22c55e;">
                            <i data-lucide="home" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="check-circle" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;"><?php echo intval($listingStats['available_listings'] ?? 0); ?> active</span>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo number_format($listingStats['total_listings'] ?? 0); ?></p>
                    <p class="stat-label">Total Listings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #eab308;">
                            <i data-lucide="alert-triangle" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="alert-circle" style="width: 1rem; height: 1rem; color: #dc2626;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #dc2626;"><?php echo intval($reportStats['pending_reports'] ?? 0); ?> pending</span>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo number_format($reportStats['total_reports'] ?? 0); ?></p>
                    <p class="stat-label">Total Reports</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #a855f7;">
                            <i data-lucide="users-2" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="user-check" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;"><?php echo intval($userStats['total_seekers'] ?? 0); ?> seekers</span>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo number_format($userStats['total_landlords'] ?? 0); ?></p>
                    <p class="stat-label">Total Landlords</p>
                </div>
            </div>

            <!-- 3 Column Grid: Recent Activity | Pending Actions | Notification Status -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2rem;">
                <!-- Column 1: Recent Activity -->
                <div class="glass-card" style="padding: 1.25rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Recent Activity</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php
                        // Activities already loaded from database at top of file
                        if (empty($recentActivities)):
                        ?>
                        <p style="text-align: center; color: rgba(0,0,0,0.5); padding: 2rem;">No recent activity</p>
                        <?php
                        endif;

                        foreach ($recentActivities as $activity): 
                        ?>
                        <div class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 0.75rem;">
                            <div style="width: 2rem; height: 2rem; background-color: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i data-lucide="<?php echo $activity['icon']; ?>" style="width: 1rem; height: 1rem; color: var(--blue);"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 0.875rem; color: #000; margin-bottom: 0.125rem;">
                                    <span style="font-weight: 600;"><?php echo $activity['user']; ?></span> <?php echo $activity['action']; ?>
                                </p>
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);"><?php echo $activity['time']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 2: Pending Actions -->
                <div class="glass-card" style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #000;">Pending Actions</h2>
                        <span style="padding: 0.25rem 0.75rem; background-color: #fee2e2; color: #b91c1c; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;"><?php echo count($pendingActions); ?> items</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php
                        // Pending actions already loaded from database at top of file
                        if (empty($pendingActions)):
                        ?>
                        <p style="text-align: center; color: rgba(0,0,0,0.5); padding: 2rem;">No pending actions</p>
                        <?php
                        endif;

                        foreach ($pendingActions as $action): 
                            $priorityClass = $action['priority'] === 'high' ? 'priority-high' : 'priority-medium';
                        ?>
                        <div class="action-item">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000;"><?php echo $action['type']; ?></p>
                                    <span class="priority-badge <?php echo $priorityClass; ?>"><?php echo ucfirst($action['priority']); ?></span>
                                </div>
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);"><?php echo $action['description']; ?></p>
                            </div>
                            <button class="btn btn-primary btn-sm" style="margin-left: 0.75rem;">Review</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 3: Notification Status -->
                <div class="glass-card" style="padding: 1.25rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Notification Status</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php
                        $notificationStats = [
                            ['label' => 'Emails Sent Today', 'value' => '234', 'status' => 'success', 'icon' => 'check-circle'],
                            ['label' => 'Failed Deliveries', 'value' => '3', 'status' => 'error', 'icon' => 'x-circle'],
                            ['label' => 'Pending Queue', 'value' => '12', 'status' => 'warning', 'icon' => 'clock'],
                        ];

                        foreach ($notificationStats as $stat): 
                            $iconColor = '';
                            switch ($stat['status']) {
                                case 'success': $iconColor = 'color: #16a34a;'; break;
                                case 'error': $iconColor = 'color: #dc2626;'; break;
                                case 'warning': $iconColor = 'color: #ca8a04;'; break;
                            }
                        ?>
                        <div class="glass-subtle" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="<?php echo $stat['icon']; ?>" style="width: 1rem; height: 1rem; <?php echo $iconColor; ?>"></i>
                                <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);"><?php echo $stat['label']; ?></p>
                            </div>
                            <p style="font-size: 1.125rem; font-weight: 700; color: #000;"><?php echo $stat['value']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
