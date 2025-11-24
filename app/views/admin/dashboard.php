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

            <!-- Main Content Grid -->
            <div class="content-grid">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000;">Pending Actions</h2>
                            <span style="padding: 0.25rem 0.75rem; background-color: #fee2e2; color: #b91c1c; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">3 items</span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php
                            $pendingActions = [
                                ['id' => 1, 'type' => 'Listing Approval', 'description' => 'Modern Studio Downtown - David Martinez', 'priority' => 'high'],
                                ['id' => 2, 'type' => 'User Verification', 'description' => 'Sarah Johnson - Landlord Account', 'priority' => 'medium'],
                                ['id' => 3, 'type' => 'Complaint Review', 'description' => 'Report #1234 - Inappropriate Content', 'priority' => 'high'],
                            ];

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
                </div>

                <!-- Right Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Notification Status -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Notification Status</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php
                            $notificationStats = [
                                ['label' => 'Emails Sent Today', 'value' => '234', 'status' => 'success', 'icon' => 'check-circle'],
                                ['label' => 'SMS Sent Today', 'value' => '156', 'status' => 'success', 'icon' => 'check-circle'],
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
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
