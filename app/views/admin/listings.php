<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Management - RoomFinder Admin</title>
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
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/User.php';
    
    $listingModel = new Listing();
    $userModel = new User();
    
    // Get all listings from database with landlord info
    $sql = "SELECT l.*, 
                u.first_name, u.last_name, u.profile_photo,
                (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image
            FROM listings l
            LEFT JOIN users u ON l.landlord_id = u.user_id
            ORDER BY l.created_at DESC";
    $stmt = $listingModel->getConnection()->prepare($sql);
    $stmt->execute();
    $listingsData = $stmt->fetchAll();
    
    // Helper function for time ago
    function time_elapsed($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'just now';
        if ($diff < 3600) {
            $mins = floor($diff/60);
            return $mins . ' ' . ($mins == 1 ? 'hour' : 'hours') . ' ago';
        }
        if ($diff < 86400) {
            $hours = floor($diff/3600);
            return $hours . ' ' . ($hours == 1 ? 'hour' : 'hours') . ' ago';
        }
        $days = floor($diff/86400);
        return $days . ' ' . ($days == 1 ? 'day' : 'days') . ' ago';
    }
    
    // Format listing data
    $listings = [];
    foreach ($listingsData as $listingData) {
        $listings[] = [
            'id' => $listingData['listing_id'],
            'image' => $listingData['primary_image'] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
            'title' => $listingData['title'],
            'landlord' => $listingData['first_name'] . ' ' . $listingData['last_name'],
            'landlordAvatar' => $listingData['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($listingData['first_name'] . ' ' . $listingData['last_name']) . '&background=3b82f6&color=fff',
            'location' => $listingData['location'],
            'price' => $listingData['price'],
            'status' => $listingData['availability_status'], // pending, available (approved), occupied (rejected for this view)
            'submittedDate' => time_elapsed($listingData['created_at']),
            'views' => 0, // listing_views table doesn't exist yet
        ];
    }
    ?>
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Listing Management</h1>
                <p class="page-subtitle">Review and approve property listings</p>
            </div>

            <!-- Search & Filters -->
            <div class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem; background: transparent; border: none; box-shadow: none;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" class="search-input-clean" placeholder="Search listings...">
                    </div>
                    <div class="search-actions">
                        <button class="btn-filters" onclick="document.getElementById('filterOptions').style.display = document.getElementById('filterOptions').style.display === 'none' ? 'flex' : 'none'">
                            <i data-lucide="sliders-horizontal" style="width: 1rem; height: 1rem;"></i>
                            Filters
                        </button>
                        <button class="btn-search">
                            Search
                        </button>
                    </div>
                </div>
                
                <!-- Expanded Filters -->
                <div id="filterOptions" style="display: none; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select class="form-select-sm" style="flex: 1;">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Listings Grid -->
            <div class="listings-grid">
                <?php
                // Listings already loaded from database above
                foreach ($listings as $index => $listing): 
                    $statusClass = '';
                    // Map database status to display status
                    $displayStatus = $listing['status'];
                    switch ($listing['status']) {
                        case 'available': 
                            $statusClass = 'status-success'; 
                            $displayStatus = 'approved';
                            break;
                        case 'pending': 
                            $statusClass = 'status-warning';
                            $displayStatus = 'pending';
                            break;
                        case 'occupied': 
                            $statusClass = 'status-error';
                            $displayStatus = 'rejected';
                            break;
                        default: $statusClass = 'status-neutral';
                    }
                ?>
                <div class="glass-card animate-slide-up" style="padding: 1.25rem; animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="listing-card-content">
                        <!-- Image -->
                        <div style="flex-shrink: 0;">
                            <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image">
                        </div>
                            <!-- Landlord Info -->
                            <div class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.75rem;">
                                <img src="<?php echo $listing['landlordAvatar']; ?>" alt="<?php echo $listing['landlord']; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                <div style="flex: 1; min-width: 0;">
                                    <p style="font-weight: 600; font-size: 0.875rem; color: #000;"><?php echo $listing['landlord']; ?></p>
                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);">Landlord</p>
                                </div>
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); flex-shrink: 0;">Submitted <?php echo $listing['submittedDate']; ?></p>
                            </div>

                            <!-- Details -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 2rem; height: 2rem; background-color: rgba(30, 58, 138, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="coins" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Price</p>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000;">₱<?php echo $listing['price']; ?>/mo</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 2rem; height: 2rem; background-color: rgba(30, 58, 138, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="eye" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Views</p>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000;"><?php echo $listing['views']; ?></p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 2rem; height: 2rem; background-color: rgba(30, 58, 138, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="calendar" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Submitted</p>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000;"><?php echo $listing['submittedDate']; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <?php if ($listing['status'] === 'pending'): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <button class="btn btn-primary btn-sm" style="flex: 1;" onclick="handleApprove(<?php echo $listing['id']; ?>)">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                    Approve Listing
                                </button>
                                <button class="btn btn-ghost btn-sm" style="flex: 1; color: #dc2626;" onclick="handleReject(<?php echo $listing['id']; ?>)">
                                    <i data-lucide="x" style="width: 1rem; height: 1rem;"></i>
                                    Reject
                                </button>
                                <button class="btn btn-glass btn-sm" style="flex: 1;">
                                    <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                    View Details
                                </button>
                            </div>
                            <?php elseif ($listing['status'] === 'approved'): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <button class="btn btn-glass btn-sm" style="flex: 1;">View Details</button>
                                <button class="btn btn-ghost btn-sm" style="color: #dc2626;">Unpublish</button>
                            </div>
                            <?php elseif ($listing['status'] === 'rejected'): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <button class="btn btn-primary btn-sm" style="flex: 1;" onclick="handleApprove(<?php echo $listing['id']; ?>)">Approve Now</button>
                                <button class="btn btn-glass btn-sm">View Details</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function handleApprove(id) {
            console.log('Approve listing:', id);
            // Add approval logic here
        }

        function handleReject(id) {
            console.log('Reject listing:', id);
            // Add rejection logic here
        }
    </script>
</body>
</html>
