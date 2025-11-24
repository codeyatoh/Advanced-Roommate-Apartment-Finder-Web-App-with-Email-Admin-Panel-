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
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">+12%</span>
                        </div>
                    </div>
                    <p class="stat-value">1,234</p>
                    <p class="stat-label">Total Users</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #22c55e;">
                            <i data-lucide="home" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="trending-up" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">+8%</span>
                        </div>
                    </div>
                    <p class="stat-value">456</p>
                    <p class="stat-label">Total Listings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #eab308;">
                            <i data-lucide="alert-triangle" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="trending-down" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">-5%</span>
                        </div>
                    </div>
                    <p class="stat-value">23</p>
                    <p class="stat-label">Active Complaints</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #a855f7;">
                            <i data-lucide="mail" class="stat-icon"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i data-lucide="trending-up" style="width: 1rem; height: 1rem; color: #16a34a;"></i>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">+15%</span>
                        </div>
                    </div>
                    <p class="stat-value">8,901</p>
                    <p class="stat-label">Messages Sent</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Recent Activity -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Recent Activity</h2>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php
                            $recentActivity = [
                                ['id' => 1, 'type' => 'user', 'action' => 'New user registered', 'user' => 'Sarah Johnson', 'time' => '5 minutes ago', 'icon' => 'users'],
                                ['id' => 2, 'type' => 'listing', 'action' => 'New listing submitted', 'user' => 'David Martinez', 'time' => '15 minutes ago', 'icon' => 'home'],
                                ['id' => 3, 'type' => 'report', 'action' => 'New complaint filed', 'user' => 'Mike Chen', 'time' => '1 hour ago', 'icon' => 'alert-triangle'],
                                ['id' => 4, 'type' => 'notification', 'action' => 'Email notification sent', 'user' => 'System', 'time' => '2 hours ago', 'icon' => 'mail'],
                            ];

                            foreach ($recentActivity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon-wrapper">
                                    <i data-lucide="<?php echo $activity['icon']; ?>" class="activity-icon"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000; margin-bottom: 0.25rem;"><?php echo $activity['action']; ?></p>
                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);"><?php echo $activity['user']; ?> • <?php echo $activity['time']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Pending Actions -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
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

                    <!-- Quick Actions -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/users.php" class="btn btn-primary btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="users" style="width: 1rem; height: 1rem;"></i>
                                Manage Users
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/listings.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="home" style="width: 1rem; height: 1rem;"></i>
                                Review Listings
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/reports.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="alert-triangle" style="width: 1rem; height: 1rem;"></i>
                                View Reports
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/notifications.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="message-square" style="width: 1rem; height: 1rem;"></i>
                                Notification Logs
                            </a>
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
