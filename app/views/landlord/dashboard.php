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
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">Landlord Dashboard</h1>
                    <p class="page-subtitle">Manage your properties and tenant inquiries</p>
                </div>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/add_listing.php" class="btn btn-primary btn-md">
                    <i data-lucide="plus" style="width: 20px; height: 20px;"></i>
                    Add Listing
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="home" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">8</p>
                    <p class="stat-label">Active Listings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="message-square" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">12</p>
                    <p class="stat-label">New Inquiries</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="calendar" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">5</p>
                    <p class="stat-label">Pending Viewings</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="stat-icon-wrapper">
                            <i data-lucide="eye" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">234</p>
                    <p class="stat-label">Total Views</p>
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
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/inquiries.php" class="btn btn-ghost btn-sm" style="font-size: 0.75rem;">View All</a>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php
                            $recentInquiries = [
                                [
                                    'id' => 1,
                                    'tenant' => 'Sarah Johnson',
                                    'property' => 'Modern Studio Downtown',
                                    'message' => 'Hi, I am interested in viewing this property...',
                                    'time' => '2 hours ago',
                                    'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                                    'unread' => true,
                                ],
                                [
                                    'id' => 2,
                                    'tenant' => 'Mike Chen',
                                    'property' => 'Cozy Apartment',
                                    'message' => 'Is this still available for February?',
                                    'time' => '5 hours ago',
                                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                                    'unread' => true,
                                ],
                                [
                                    'id' => 3,
                                    'tenant' => 'Emily Rodriguez',
                                    'property' => 'Spacious Loft',
                                    'message' => 'Thank you for the quick response!',
                                    'time' => '1 day ago',
                                    'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400',
                                    'unread' => false,
                                ],
                            ];

                            foreach ($recentInquiries as $inquiry): ?>
                            <div class="inquiry-item">
                                <img src="<?php echo $inquiry['avatar']; ?>" alt="<?php echo $inquiry['tenant']; ?>" class="inquiry-avatar">
                                <div class="inquiry-content">
                                    <div class="inquiry-header">
                                        <p class="inquiry-name"><?php echo $inquiry['tenant']; ?></p>
                                        <span class="inquiry-time"><?php echo $inquiry['time']; ?></span>
                                    </div>
                                    <p class="inquiry-property"><?php echo $inquiry['property']; ?></p>
                                    <p class="inquiry-message"><?php echo $inquiry['message']; ?></p>
                                </div>
                                <?php if ($inquiry['unread']): ?>
                                <div class="unread-indicator"></div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Performance Overview -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Performance Overview</h2>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i data-lucide="dollar-sign" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Monthly Revenue</p>
                                </div>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #000;">$9,600</p>
                                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                                    <i data-lucide="trending-up" style="width: 0.75rem; height: 0.75rem; color: #16a34a;"></i>
                                    <span style="font-size: 0.75rem; color: #16a34a;">+12%</span>
                                </div>
                            </div>
                            <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i data-lucide="users" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Occupancy Rate</p>
                                </div>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #000;">87%</p>
                                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                                    <i data-lucide="trending-up" style="width: 0.75rem; height: 0.75rem; color: #16a34a;"></i>
                                    <span style="font-size: 0.75rem; color: #16a34a;">+5%</span>
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
                        <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                            <?php
                            $upcomingViewings = [
                                [
                                    'id' => 1,
                                    'tenant' => 'John Doe',
                                    'property' => 'Modern Studio Downtown',
                                    'date' => 'Tomorrow',
                                    'time' => '2:00 PM',
                                    'status' => 'pending',
                                ],
                                [
                                    'id' => 2,
                                    'tenant' => 'Jane Smith',
                                    'property' => 'Cozy Apartment',
                                    'date' => 'Feb 2, 2024',
                                    'time' => '10:00 AM',
                                    'status' => 'confirmed',
                                ],
                            ];

                            foreach ($upcomingViewings as $viewing): ?>
                            <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem;">
                                <div style="display: flex; align-items: flex-start; gap: 0.625rem; margin-bottom: 0.75rem;">
                                    <div style="width: 2.25rem; height: 2.25rem; background-color: rgba(30, 58, 138, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="calendar" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-weight: 600; font-size: 0.875rem; color: #000; margin-bottom: 0.25rem;"><?php echo $viewing['tenant']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem;"><?php echo $viewing['property']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);"><?php echo $viewing['date']; ?>, <?php echo $viewing['time']; ?></p>
                                    </div>
                                </div>
                                <?php if ($viewing['status'] === 'pending'): ?>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="btn btn-primary btn-sm" style="flex: 1; font-size: 0.75rem;">Approve</button>
                                    <button class="btn btn-ghost btn-sm" style="flex: 1; font-size: 0.75rem;">Decline</button>
                                </div>
                                <?php elseif ($viewing['status'] === 'confirmed'): ?>
                                <div style="padding: 0.25rem 0.5rem; background-color: #dcfce7; color: #15803d; border-radius: 0.25rem; font-size: 0.75rem; text-align: center; font-weight: 500;">
                                    Confirmed
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-card" style="padding: 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #000; margin-bottom: 0.75rem;">Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/add_listing.php" class="btn btn-primary btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="plus" style="width: 1rem; height: 1rem;"></i>
                                Add New Listing
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/inquiries.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="message-square" style="width: 1rem; height: 1rem;"></i>
                                View All Inquiries
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/appointments.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="calendar" style="width: 1rem; height: 1rem;"></i>
                                Manage Appointments
                            </a>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/listings.php" class="btn btn-glass btn-sm" style="justify-content: flex-start; width: 100%;">
                                <i data-lucide="home" style="width: 1rem; height: 1rem;"></i>
                                View All Listings
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
