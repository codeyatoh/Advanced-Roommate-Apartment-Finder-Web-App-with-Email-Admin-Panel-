<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'guest';

// Determine profile link based on role
$profile_link = '#';
if ($role === 'room_seeker') {
    $profile_link = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/profile.php';
} elseif ($role === 'landlord') {
    // $profile_link = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/profile.php';
    $profile_link = '#'; // Placeholder until landlord profile is created
} elseif ($role === 'admin') {
    // $profile_link = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/profile.php';
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
            <?php if (isset($_SESSION['user_id'])): ?>
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

</style>
