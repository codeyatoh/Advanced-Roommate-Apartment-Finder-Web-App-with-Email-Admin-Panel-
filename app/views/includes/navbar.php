<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'guest';

// Get notification count
$notificationCount = 0;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../models/Notification.php';
    $notificationModel = new Notification();
    $notificationCount = $notificationModel->getUnreadCount($_SESSION['user_id']);
}

// Determine profile link based on role
$profile_link = '#';
if ($role === 'room_seeker') {
    $profile_link = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/profile.php';
} elseif ($role === 'landlord') {
    $profile_link = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/profile.php'; // Using seeker profile for now
} elseif ($role === 'admin') {
    $profile_link = '#'; // Placeholder until admin profile is created
}
?>

<nav class="navbar">
    <!-- Load Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">
    
    <div class="navbar-container">
        <!-- Logo (Text Only) -->
        <a href="#" class="navbar-logo">
            <span>RoomFinder</span>
        </a>

        <!-- Navigation Links with Icons -->
        <div class="navbar-menu">
            <?php if ($role === 'room_seeker'): ?>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php" class="navbar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/browse_rooms.php" class="navbar-link <?php echo $current_page === 'browse_rooms.php' ? 'active' : ''; ?>">
                    <i data-lucide="search" class="nav-icon"></i>
                    <span>Browse</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/roommate_finder.php" class="navbar-link <?php echo $current_page === 'roommate_finder.php' ? 'active' : ''; ?>">
                    <i data-lucide="users" class="nav-icon"></i>
                    <span>Roommates</span>
                    <?php if ($notificationCount > 0): ?>
                    <span class="notification-badge"><?php echo $notificationCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/appointments.php" class="navbar-link <?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <i data-lucide="calendar" class="nav-icon"></i>
                    <span>Appointments</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/messages.php" class="navbar-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                    <i data-lucide="message-square" class="nav-icon"></i>
                    <span>Messages</span>
                </a>
            <?php elseif ($role === 'landlord'): ?>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/dashboard.php" class="navbar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/listings.php" class="navbar-link <?php echo $current_page === 'listings.php' ? 'active' : ''; ?>">
                    <i data-lucide="home" class="nav-icon"></i>
                    <span>Listings</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/inquiries.php" class="navbar-link <?php echo $current_page === 'inquiries.php' ? 'active' : ''; ?>">
                    <i data-lucide="message-square" class="nav-icon"></i>
                    <span>Inquiries</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/appointments.php" class="navbar-link <?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <i data-lucide="calendar" class="nav-icon"></i>
                    <span>Appointments</span>
                </a>
            <?php elseif ($role === 'admin'): ?>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/dashboard.php" class="navbar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/users.php" class="navbar-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                    <i data-lucide="users" class="nav-icon"></i>
                    <span>Users</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/listings.php" class="navbar-link <?php echo $current_page === 'listings.php' ? 'active' : ''; ?>">
                    <i data-lucide="home" class="nav-icon"></i>
                    <span>Listings</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/reports.php" class="navbar-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>">
                    <i data-lucide="flag" class="nav-icon"></i>
                    <span>Reports</span>
                </a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/notifications.php" class="navbar-link <?php echo $current_page === 'notifications.php' ? 'active' : ''; ?>">
                    <i data-lucide="bell" class="nav-icon"></i>
                    <span>Notifications</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- User Actions -->
        <div class="navbar-actions">
            <?php if (isset($_SESSION['user_id'])): 
                // Get notifications for dropdown
                $notifications = $notificationModel->getUnread($_SESSION['user_id'], 5);
            ?>
                <!-- Notification Dropdown -->
                <div class="notification-dropdown-container">
                    <button type="button" class="btn-notification" id="notificationBtn">
                        <i data-lucide="bell" style="width: 1.25rem; height: 1.25rem;"></i>
                        <?php if ($notificationCount > 0): ?>
                        <span class="notification-count-badge"><?php echo $notificationCount > 9 ? '9+' : $notificationCount; ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-dropdown-header">
                            <h3>Notifications</h3>
                            <?php if ($notificationCount > 0): ?>
                            <button type="button" class="btn-mark-read" id="markAllReadBtn">Mark all as read</button>
                            <?php endif; ?>
                        </div>
                        <div class="notification-list">
                            <?php if (empty($notifications)): ?>
                            <div class="notification-empty">
                                <i data-lucide="bell-off" style="width: 2rem; height: 2rem; color: rgba(0,0,0,0.3);"></i>
                                <p>No new notifications</p>
                            </div>
                            <?php else: ?>
                                <?php foreach ($notifications as $notif): 
                                    // Icon based on type (PHP 7.x compatible)
                                    switch($notif['type']) {
                                        case 'match':
                                            $icon = 'heart';
                                            break;
                                        case 'message':
                                            $icon = 'message-circle';
                                            break;
                                        case 'appointment':
                                            $icon = 'calendar';
                                            break;
                                        case 'inquiry':
                                            $icon = 'mail';
                                            break;
                                        default:
                                            $icon = 'bell';
                                    }
                                    
                                    // Icon color based on type (PHP 7.x compatible)
                                    switch($notif['type']) {
                                        case 'match':
                                            $iconColor = '#10b981';
                                            break;
                                        case 'message':
                                            $iconColor = '#3b82f6';
                                            break;
                                        case 'appointment':
                                            $iconColor = '#f59e0b';
                                            break;
                                        case 'inquiry':
                                            $iconColor = '#8b5cf6';
                                            break;
                                        default:
                                            $iconColor = '#6b7280';
                                    }
                                    
                                    // Format time
                                    $time = new DateTime($notif['created_at']);
                                    $now = new DateTime();
                                    $diff = $now->diff($time);
                                    if ($diff->days > 0) {
                                        $timeAgo = $diff->days . 'd ago';
                                    } elseif ($diff->h > 0) {
                                        $timeAgo = $diff->h . 'h ago';
                                    } else {
                                        $timeAgo = max(1, $diff->i) . 'm ago';
                                    }
                                ?>
                                <div class="notification-item" data-id="<?php echo $notif['notification_id']; ?>">
                                    <div class="notification-icon" style="background-color: <?php echo $iconColor; ?>15;">
                                        <i data-lucide="<?php echo $icon; ?>" style="width: 1.25rem; height: 1.25rem; color: <?php echo $iconColor; ?>;"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-title"><?php echo htmlspecialchars($notif['title']); ?></p>
                                        <p class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></p>
                                        <span class="notification-time"><?php echo $timeAgo; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <a href="#" class="notification-view-all">View all notifications</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="navbar-user">
                    <a href="<?php echo $profile_link; ?>" class="btn btn-outline-profile">
                        <i data-lucide="settings" style="width: 1.125rem; height: 1.125rem;"></i>
                        <span>Profile</span>
                    </a>
                </div>
                <!-- Removed Logout Icon Button as Profile usually contains logout or it's separate -->
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AuthController.php?action=logout" class="btn btn-ghost btn-sm" title="Logout" style="color: #ef4444;">
                    <i data-lucide="log-out" style="width: 1.25rem; height: 1.25rem;"></i>
                </a>
            <?php else: ?>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php" class="btn btn-ghost">Login</a>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Navbar Styles */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4.5rem; /* Slightly taller */
    background: var(--glass-bg-strong);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--glass-border);
    z-index: 50;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.navbar-container {
    max-width: 1280px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
}

/* Logo */
.navbar-logo {
    font-family: 'Pacifico', cursive;
    font-size: 1.75rem;
    color: #3b82f6; /* Blueish color from image */
    text-decoration: none;
    display: flex;
    align-items: center;
}

/* Menu */
.navbar-menu {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.navbar-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #4b5563; /* Gray-600 */
    text-decoration: none;
    transition: all 0.2s;
    font-size: 0.95rem;
}

.navbar-link:hover {
    color: #111827; /* Gray-900 */
}

.navbar-link.active {
    color: #3b82f6; /* Blue-500 */
    font-weight: 600;
}

.nav-icon {
    width: 1.125rem;
    height: 1.125rem;
}

/* Actions */
.navbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-outline-profile {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background: white;
    color: #374151;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-outline-profile:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

/* Notification Badge */
.notification-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.25rem;
    height: 1.25rem;
    padding: 0 0.375rem;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 9999px;
    margin-left: 0.25rem;
}

/* Notification Dropdown */
.notification-dropdown-container {
    position: relative;
}

.btn-notification {
    position: relative;
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-notification:hover {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.2);
}

.notification-count-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    min-width: 1.125rem;
    height: 1.125rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 0.25rem;
    border: 2px solid white;
}

.notification-dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    width: 400px;
    max-height: 500px;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.1);
    display: none;
    flex-direction: column;
    z-index: 1000;
    overflow: hidden;
}

.notification-dropdown.show {
    display: flex;
}

.notification-dropdown-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notification-dropdown-header h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: #000;
}

.btn-mark-read {
    background: transparent;
    border: none;
    color: var(--deep-blue);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: background 0.2s;
}

.btn-mark-read:hover {
    background: rgba(30, 58, 138, 0.1);
}

.notification-list {
    flex: 1;
    overflow-y: auto;
    max-height: 400px;
}

.notification-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: rgba(0, 0, 0, 0.5);
}

.notification-empty p {
    margin: 0.5rem 0 0 0;
    font-size: 0.875rem;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.notification-item:hover {
    background: rgba(0, 0, 0, 0.02);
}

.notification-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #000;
    margin: 0 0 0.25rem 0;
}

.notification-message {
    font-size: 0.75rem;
    color: rgba(0, 0, 0, 0.7);
    margin: 0 0 0.25rem 0;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.7rem;
    color: rgba(0, 0, 0, 0.5);
}

.notification-view-all {
    display: block;
    text-align: center;
    padding: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--deep-blue);
    text-decoration: none;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    transition: background 0.2s;
}

.notification-view-all:hover {
    background: rgba(30, 58, 138, 0.05);
}

</style>

<script>
// Notification Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    if (notificationBtn && notificationDropdown) {
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && e.target !== notificationBtn) {
                notificationDropdown.classList.remove('show');
            }
        });
        
        // Mark single notification as read
        const notificationItems = notificationDropdown.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function() {
                const notifId = this.dataset.id;
                if (notifId) {
                    markAsRead(notifId);
                    this.style.opacity = '0.5';
                }
            });
        });
        
        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                markAllAsRead();
            });
        }
    }
    
    function markAsRead(notifId) {
        fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/NotificationController.php?action=markAsRead&id=' + notifId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optionally reload or update UI
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function markAllAsRead() {
        fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/NotificationController.php?action=markAllAsRead')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
</script>
