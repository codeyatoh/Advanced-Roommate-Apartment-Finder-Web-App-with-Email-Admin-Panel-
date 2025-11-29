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
    <?php
    // Start session and load models
    session_start();
    require_once __DIR__ . '/../../models/User.php';
    require_once __DIR__ . '/../../models/Notification.php';
    
    $userModel = new User();
    $notificationModel = new Notification();
    
    // Get notification statistics from database
    $today = date('Y-m-d');
    $conn = $userModel->getConnection();
    
    // Initialize default values
    $emailsSentToday = 0;
    $smsSentToday = 0;
    $failedDeliveries = 0;
    $pendingQueue = 0;
    $adminNotifications = [];

    // Get filters
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? 'email';
    $status = $_GET['status'] ?? 'All Status';
    
    try {
        // Count emails sent today (only successful ones)
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE DATE(created_at) = :today AND type = 'email' AND status IN ('sent', 'delivered')";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':today', $today);
        $stmt->execute();
        $result = $stmt->fetch();
        $emailsSentToday = $result['count'] ?? 0;
        
        // Count SMS sent today (no SMS in current schema - set to 0)
        $smsSentToday = 0;
        
        // Count failed deliveries
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE status = 'failed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $failedDeliveries = $result['count'] ?? 0;
        
        // Count pending notifications
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $pendingQueue = $result['count'] ?? 0;
        
        // Get notifications with filters
        $notificationsData = $notificationModel->searchNotifications([
            'search' => $search,
            'type' => $type,
            'status' => $status
        ]);
        
        // Helper function for time ago
        function notif_time_ago($datetime) {
            $time = strtotime($datetime);
            $now = time();
            $diff = $now - $time;
            
            if ($diff < 60) return 'just now';
            if ($diff < 3600) {
                $mins = floor($diff/60);
                return $mins . ' ' . ($mins == 1 ? 'minute' : 'minutes') . ' ago';
            }
            if ($diff < 86400) {
                $hours = floor($diff/3600);
                return $hours . ' ' . ($hours == 1 ? 'hour' : 'hours') . ' ago';
            }
            $days = floor($diff/86400);
            return $days . ' ' . ($days == 1 ? 'day' : 'days') . ' ago';
        }
        
        // Pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 5;
        $totalNotifications = count($notificationsData);
        $totalPages = ceil($totalNotifications / $limit);
        $offset = ($page - 1) * $limit;
        $notificationsData = array_slice($notificationsData, $offset, $limit);
        
        // Format notification data
        foreach ($notificationsData as $notifData) {
            $recipientName = ($notifData['first_name'] ?? 'Unknown') . ' ' . ($notifData['last_name'] ?? 'User');
            $recipient = $notifData['type'] === 'email' ? ($notifData['user_email'] ?? 'N/A') : ($notifData['phone'] ?? 'N/A');
            
            $adminNotifications[] = [
                'id' => $notifData['notification_id'],
                'type' => $notifData['type'] ?? 'email',
                'recipient' => $recipient,
                'recipientName' => $recipientName,
                'subject' => $notifData['message'] ?? 'No subject',
                'status' => $notifData['status'],
                'sentAt' => notif_time_ago($notifData['created_at']),
                'deliveredAt' => ($notifData['status'] === 'sent' || $notifData['status'] === 'delivered') ? notif_time_ago($notifData['created_at']) : null,
                'error' => $notifData['status'] === 'failed' ? 'Delivery failed' : null,
            ];
        }
    } catch (Throwable $e) {
        // Table doesn't exist yet - use default values (all zeros)
        error_log("Notifications error: " . $e->getMessage());
        // Stats already initialized to 0 above
        // $adminNotifications already initialized to empty array above
    }
    ?>
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
                    <p class="stat-value"><?php echo $emailsSentToday; ?></p>
                    <p class="stat-label">Emails Sent Today</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #ef4444;">
                            <i data-lucide="x-circle" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo $failedDeliveries; ?></p>
                    <p class="stat-label">Failed Deliveries</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #eab308;">
                            <i data-lucide="clock" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value"><?php echo $pendingQueue; ?></p>
                    <p class="stat-label">Pending Queue</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <form method="GET" action="notifications.php" class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem; background: transparent; border: none; box-shadow: none;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" name="search" class="search-input-clean" placeholder="Search notifications..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="search-actions">
                        <button type="button" class="btn-filters" onclick="document.getElementById('filterOptions').style.display = document.getElementById('filterOptions').style.display === 'none' ? 'flex' : 'none'">
                            <i data-lucide="sliders-horizontal" style="width: 1rem; height: 1rem;"></i>
                            Filters
                        </button>
                        <button type="submit" class="btn-search">
                            Search
                        </button>
                    </div>
                </div>
                
                <!-- Expanded Filters -->
                <div id="filterOptions" style="display: <?php echo ($type !== 'All Types' || $status !== 'All Status') ? 'flex' : 'none'; ?>; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select name="type" class="form-select-sm" style="flex: 1; display: none;"> <!-- Hidden as only Email is supported -->
                        <option selected>Email</option>
                    </select>
                    <select name="status" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $status === 'All Status' ? 'selected' : ''; ?>>All Status</option>
                        <option <?php echo $status === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option <?php echo $status === 'Failed' ? 'selected' : ''; ?>>Failed</option>
                        <option <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                </div>
            </form>

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
                            // Notifications already loaded from database above
                            foreach ($adminNotifications as $notification): 
                                $statusClass = '';
                                $statusIcon = '';
                                switch ($notification['status']) {
                                    case 'sent': // Treat sent as success/delivered for now
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
                                    default:
                                        $statusClass = 'status-neutral';
                                        $statusIcon = 'help-circle';
                                }
                                
                                // Extract OTP if present
                                $otp = '';
                                if (preg_match('/\d{6}/', $notification['subject'], $matches)) {
                                    $otp = $matches[0];
                                }
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i data-lucide="mail" style="width: 1.25rem; height: 1.25rem; color: #2563eb;"></i>
                                        <span style="font-size: 0.875rem; font-weight: 600; color: #000; text-transform: capitalize;">Email</span>
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
                                        <?php if ($notification['status'] === 'failed' && $otp): ?>
                                            <button class="btn btn-primary btn-sm" onclick="retryEmail(this, <?php echo $notification['id']; ?>, '<?php echo $notification['recipient']; ?>', '<?php echo addslashes($notification['recipientName']); ?>', '<?php echo $otp; ?>')">
                                                <i data-lucide="refresh-cw" style="width: 1rem; height: 1rem;"></i>
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
            
            <!-- Pagination Controls -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="chevron-left" style="width: 1rem; height: 1rem;"></i>
                    Prev
                </a>
                <?php else: ?>
                <button class="btn btn-glass btn-sm" disabled style="opacity: 0.5; cursor: not-allowed; display: flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="chevron-left" style="width: 1rem; height: 1rem;"></i>
                    Prev
                </button>
                <?php endif; ?>

                <!-- Page Numbers -->
                <div style="display: flex; gap: 0.25rem;">
                    <?php 
                    $range = 1; // Number of pages around current page
                    $showDots = true;
                    
                    for ($i = 1; $i <= max(1, $totalPages); $i++):
                        // Show first, last, and pages around current
                        if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)) {
                    ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&status=<?php echo urlencode($status); ?>" 
                           class="btn btn-sm" 
                           style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $page ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php
                            $showDots = true;
                        } elseif ($showDots) {
                            echo '<span style="display: flex; align-items: center; justify-content: center; width: 2rem; color: #6b7280;">...</span>';
                            $showDots = false;
                        }
                    endfor; 
                    ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                    Next
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                </a>
                <?php else: ?>
                <button class="btn btn-glass btn-sm" disabled style="opacity: 0.5; cursor: not-allowed; display: flex; align-items: center; gap: 0.25rem;">
                    Next
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Load EmailJS config
    $emailJsConfig = require_once __DIR__ . '/../../../config/emailjs.php';
    ?>
    
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    
    <script>
        lucide.createIcons();
        
        // Initialize EmailJS
        emailjs.init('<?php echo $emailJsConfig['public_key']; ?>');
        
        async function retryEmail(btn, notificationId, email, name, otp) {
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" style="width: 1rem; height: 1rem;"></i> Sending...';
            btn.disabled = true;
            lucide.createIcons();
            
            try {
                // Send via EmailJS
                await emailjs.send(
                    '<?php echo $emailJsConfig['service_id']; ?>',
                    '<?php echo $emailJsConfig['otp_template_id']; ?>',
                    {
                        to_email: email,
                        to_name: name,
                        otp_code: otp
                    }
                );
                
                // Update status in backend
                await fetch('../../controllers/NotificationController.php?action=updateStatus', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        notification_id: notificationId,
                        status: 'sent'
                    })
                });
                
                Toastify({
                    text: "Email resent successfully!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981",
                }).showToast();
                
                // Reload page after short delay
                setTimeout(() => location.reload(), 1500);
                
            } catch (error) {
                console.error('Retry failed:', error);
                
                Toastify({
                    text: "Failed to resend email.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                }).showToast();
                
                btn.innerHTML = originalContent;
                btn.disabled = false;
                lucide.createIcons();
            }
        }
    </script>
</body>
</html>
