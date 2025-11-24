<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Monitoring - RoomFinder Admin</title>
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
                <h1 class="page-title">Notification Monitoring</h1>
                <p class="page-subtitle">Track email and SMS delivery status</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #3b82f6;">
                            <i data-lucide="mail" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">234</p>
                    <p class="stat-label">Emails Sent Today</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #22c55e;">
                            <i data-lucide="message-square" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">156</p>
                    <p class="stat-label">SMS Sent Today</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #ef4444;">
                            <i data-lucide="x-circle" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">8</p>
                    <p class="stat-label">Failed Deliveries</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #eab308;">
                            <i data-lucide="clock" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">12</p>
                    <p class="stat-label">Pending Queue</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; @media (min-width: 768px) { flex-direction: row; }">
                        <div style="position: relative; flex: 1;">
                            <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                            <input type="text" class="form-input" placeholder="Search notifications..." style="padding-left: 2.75rem;">
                        </div>
                        <select class="form-select">
                            <option>All Types</option>
                            <option>Email</option>
                            <option>SMS</option>
                        </select>
                        <select class="form-select">
                            <option>All Status</option>
                            <option>Delivered</option>
                            <option>Failed</option>
                            <option>Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notifications Table -->
            <div class="glass-card animate-slide-up" style="padding: 0; overflow: hidden;">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Recipient</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Delivered At</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $notifications = [
                                [
                                    'id' => 1,
                                    'type' => 'email',
                                    'recipient' => 'sarah.j@email.com',
                                    'recipientName' => 'Sarah Johnson',
                                    'subject' => 'Your listing has been approved',
                                    'status' => 'delivered',
                                    'sentAt' => '2 minutes ago',
                                    'deliveredAt' => '2 minutes ago',
                                ],
                                [
                                    'id' => 2,
                                    'type' => 'sms',
                                    'recipient' => '+1 (555) 123-4567',
                                    'recipientName' => 'Mike Chen',
                                    'subject' => 'Viewing appointment confirmed',
                                    'status' => 'delivered',
                                    'sentAt' => '15 minutes ago',
                                    'deliveredAt' => '15 minutes ago',
                                ],
                                [
                                    'id' => 3,
                                    'type' => 'email',
                                    'recipient' => 'david.m@email.com',
                                    'recipientName' => 'David Martinez',
                                    'subject' => 'New inquiry for your property',
                                    'status' => 'failed',
                                    'sentAt' => '1 hour ago',
                                    'error' => 'Invalid email address',
                                ],
                                [
                                    'id' => 4,
                                    'type' => 'sms',
                                    'recipient' => '+1 (555) 234-5678',
                                    'recipientName' => 'Emily Rodriguez',
                                    'subject' => 'Payment reminder',
                                    'status' => 'pending',
                                    'sentAt' => '5 minutes ago',
                                ],
                                [
                                    'id' => 5,
                                    'type' => 'email',
                                    'recipient' => 'lisa.w@email.com',
                                    'recipientName' => 'Lisa Wong',
                                    'subject' => 'Welcome to RoomFinder',
                                    'status' => 'delivered',
                                    'sentAt' => '3 hours ago',
                                    'deliveredAt' => '3 hours ago',
                                ],
                            ];

                            foreach ($notifications as $notification): 
                                $statusClass = '';
                                $statusIcon = '';
                                switch ($notification['status']) {
                                    case 'delivered': 
                                        $statusClass = 'status-success'; 
                                        $statusIcon = 'check-circle';
                                        break;
                                    case 'failed': 
                                        $statusClass = 'status-error'; 
                                        $statusIcon = 'x-circle';
                                        break;
                                    case 'pending': 
                                        $statusClass = 'status-warning'; 
                                        $statusIcon = 'clock';
                                        break;
                                }
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <?php if ($notification['type'] === 'email'): ?>
                                            <i data-lucide="mail" style="width: 1.25rem; height: 1.25rem; color: #2563eb;"></i>
                                        <?php else: ?>
                                            <i data-lucide="message-square" style="width: 1.25rem; height: 1.25rem; color: #16a34a;"></i>
                                        <?php endif; ?>
                                        <span style="font-size: 0.875rem; font-weight: 600; color: #000; text-transform: capitalize;"><?php echo $notification['type']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000;"><?php echo $notification['recipientName']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);"><?php echo $notification['recipient']; ?></p>
                                    </div>
                                </td>
                                <td>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);"><?php echo $notification['subject']; ?></p>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i data-lucide="<?php echo $statusIcon; ?>" style="width: 1rem; height: 1rem; <?php echo $notification['status'] === 'failed' ? 'color: #dc2626;' : ($notification['status'] === 'pending' ? 'color: #ca8a04;' : 'color: #16a34a;'); ?>"></i>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($notification['status']); ?>
                                        </span>
                                    </div>
                                    <?php if (isset($notification['error'])): ?>
                                        <p style="font-size: 0.75rem; color: #dc2626; margin-top: 0.25rem;"><?php echo $notification['error']; ?></p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);"><?php echo $notification['sentAt']; ?></p>
                                </td>
                                <td>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);"><?php echo isset($notification['deliveredAt']) ? $notification['deliveredAt'] : '-'; ?></p>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <?php if ($notification['status'] === 'failed'): ?>
                                            <button class="btn btn-primary btn-sm">
                                                <i data-lucide="send" style="width: 1rem; height: 1rem;"></i>
                                                Retry
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-ghost btn-sm">View</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
