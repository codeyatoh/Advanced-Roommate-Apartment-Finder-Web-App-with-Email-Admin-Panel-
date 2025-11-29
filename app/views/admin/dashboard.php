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
    // Start session and load models
    session_start();
    require_once __DIR__ . '/../../models/User.php';
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/Report.php';
    require_once __DIR__ . '/../../models/Notification.php';

    $userModel = new User();
    $listingModel = new Listing();
    $reportModel = new Report();
    $notificationModel = new Notification();

    // Fetch Stats
    $userStats = $userModel->getStats();
    $listingStats = $listingModel->getStats();
    $pendingReportsCount = $reportModel->getPendingCount();
    
    // Fetch Recent Data
    $recentUsers = $userModel->getRecent(5);

    // Fetch Notification Stats
    $realNotificationStats = $notificationModel->getDashboardStats();
    
    $notificationStats = [
        ['label' => 'Emails Sent Today', 'value' => $realNotificationStats['emails_sent_today'], 'status' => 'success', 'icon' => 'check-circle'],
        ['label' => 'Failed Deliveries', 'value' => $realNotificationStats['failed_deliveries'], 'status' => 'error', 'icon' => 'x-circle'],
        ['label' => 'Pending Queue', 'value' => $realNotificationStats['pending_queue'], 'status' => 'warning', 'icon' => 'clock'],
    ];
    ?>

    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Overview of system performance and activities</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid animate-slide-up">
                <!-- Total Users -->
                <div class="glass-card stat-card">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                        <div>
                            <p class="stat-value"><?php echo $userStats['total_users'] ?? 0; ?></p>
                            <p class="stat-label">Total Users</p>
                        </div>
                        <div class="stat-icon-wrapper" style="background-color: var(--deep-blue);">
                            <i data-lucide="users" class="stat-icon"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; font-size: 0.75rem; color: rgba(0,0,0,0.5);">
                        <span style="color: #16a34a; font-weight: 600;"><?php echo $userStats['active_users'] ?? 0; ?></span> active users
                    </div>
                </div>

                <!-- Active Listings -->
                <div class="glass-card stat-card">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                        <div>
                            <p class="stat-value"><?php echo $listingStats['available_listings'] ?? 0; ?></p>
                            <p class="stat-label">Active Listings</p>
                        </div>
                        <div class="stat-icon-wrapper" style="background-color: #0ea5e9;">
                            <i data-lucide="home" class="stat-icon"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; font-size: 0.75rem; color: rgba(0,0,0,0.5);">
                        <span style="color: #16a34a; font-weight: 600;"><?php echo $listingStats['occupied_listings'] ?? 0; ?></span> occupied
                    </div>
                </div>

                <!-- Pending Listings -->
                <div class="glass-card stat-card">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                        <div>
                            <p class="stat-value"><?php echo $listingStats['pending_listings'] ?? 0; ?></p>
                            <p class="stat-label">Pending Listings</p>
                        </div>
                        <div class="stat-icon-wrapper" style="background-color: #f59e0b;">
                            <i data-lucide="file-clock" class="stat-icon"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <a href="listings.php?status=Pending" style="font-size: 0.75rem; color: #f59e0b; font-weight: 600; text-decoration: none;">Review Listings &rarr;</a>
                    </div>
                </div>

                <!-- Pending Reports -->
                <div class="glass-card stat-card">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                        <div>
                            <p class="stat-value"><?php echo $pendingReportsCount ?? 0; ?></p>
                            <p class="stat-label">Pending Reports</p>
                        </div>
                        <div class="stat-icon-wrapper" style="background-color: #ef4444;">
                            <i data-lucide="flag" class="stat-icon"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <a href="reports.php" style="font-size: 0.75rem; color: #ef4444; font-weight: 600; text-decoration: none;">View Reports &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid animate-slide-up" style="animation-delay: 0.1s;">
                
                <!-- Notification Status -->
                <div class="glass-card" style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #000;">Notification Status</h3>
                        <button class="btn btn-ghost btn-sm">
                            <i data-lucide="refresh-cw" style="width: 1rem; height: 1rem;"></i>
                        </button>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($notificationStats as $stat): 
                            $iconColor = '';
                            switch ($stat['status']) {
                                case 'success': $iconColor = 'color: #16a34a;'; break;
                                case 'error': $iconColor = 'color: #dc2626;'; break;
                                case 'warning': $iconColor = 'color: #ca8a04;'; break;
                            }
                        ?>
                        <div class="glass-subtle" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="<?php echo $stat['icon']; ?>" style="width: 1rem; height: 1rem; <?php echo $iconColor; ?>"></i>
                                </div>
                                <p style="font-size: 0.875rem; font-weight: 500; color: rgba(0,0,0,0.8);"><?php echo $stat['label']; ?></p>
                            </div>
                            <p style="font-size: 1rem; font-weight: 700; color: #000;"><?php echo $stat['value']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="glass-card" style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #000;">New Users</h3>
                        <a href="users.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php if (empty($recentUsers)): ?>
                            <p style="color: rgba(0,0,0,0.5); font-size: 0.875rem;">No recent users.</p>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): 
                                $avatar = !empty($user['profile_photo']) 
                                    ? $user['profile_photo'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) . '&background=random';
                                
                                // Set role display and color
                                $roleDisplay = 'Seeker';
                                $roleColor = '#7c3aed';
                                
                                if ($user['role'] === 'landlord') {
                                    $roleDisplay = 'Landlord';
                                    $roleColor = '#2563eb';
                                } elseif ($user['role'] === 'admin') {
                                    $roleDisplay = 'Admin';
                                    $roleColor = '#dc2626';
                                }
                            ?>
                            <div class="activity-item">
                                <img src="<?php echo $avatar; ?>" alt="User" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                <div style="flex: 1;">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000; margin: 0;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); margin: 0;"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 600; color: <?php echo $roleColor; ?>; background: <?php echo $roleColor; ?>15; padding: 0.25rem 0.5rem; border-radius: 9999px;">
                                    <?php echo $roleDisplay; ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
