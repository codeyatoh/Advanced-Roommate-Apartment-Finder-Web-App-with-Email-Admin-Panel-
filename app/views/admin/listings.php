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
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Listing Management</h1>
                <p class="page-subtitle">Review and approve property listings</p>
            </div>

            <!-- Search & Filters -->
            <div class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; @media (min-width: 768px) { flex-direction: row; }">
                        <div style="position: relative; flex: 1;">
                            <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                            <input type="text" class="form-input" placeholder="Search listings..." style="padding-left: 2.75rem;">
                        </div>
                        <select class="form-select">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Listings Grid -->
            <div class="listings-grid">
                <?php
                $listings = [
                    [
                        'id' => 1,
                        'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
                        'title' => 'Modern Studio Downtown',
                        'landlord' => 'David Martinez',
                        'landlordAvatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400',
                        'location' => 'San Francisco, CA',
                        'price' => 1200,
                        'status' => 'pending',
                        'submittedDate' => '2 hours ago',
                        'views' => 0,
                    ],
                    [
                        'id' => 2,
                        'image' => 'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=800',
                        'title' => 'Cozy Apartment',
                        'landlord' => 'Lisa Wong',
                        'landlordAvatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400',
                        'location' => 'Oakland, CA',
                        'price' => 950,
                        'status' => 'approved',
                        'submittedDate' => '1 day ago',
                        'views' => 45,
                    ],
                    [
                        'id' => 3,
                        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800',
                        'title' => 'Spacious Loft',
                        'landlord' => 'John Smith',
                        'landlordAvatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                        'location' => 'Berkeley, CA',
                        'price' => 1400,
                        'status' => 'pending',
                        'submittedDate' => '5 hours ago',
                        'views' => 0,
                    ],
                    [
                        'id' => 4,
                        'image' => 'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=800',
                        'title' => 'Bright Room in House',
                        'landlord' => 'Maria Garcia',
                        'landlordAvatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                        'location' => 'San Jose, CA',
                        'price' => 850,
                        'status' => 'rejected',
                        'submittedDate' => '3 days ago',
                        'views' => 12,
                    ],
                ];

                foreach ($listings as $index => $listing): 
                    $statusClass = '';
                    switch ($listing['status']) {
                        case 'approved': $statusClass = 'status-success'; break;
                        case 'pending': $statusClass = 'status-warning'; break;
                        case 'rejected': $statusClass = 'status-error'; break;
                        default: $statusClass = 'status-neutral';
                    }
                ?>
                <div class="glass-card animate-slide-up" style="padding: 1.25rem; animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="listing-card-content">
                        <!-- Image -->
                        <div style="flex-shrink: 0;">
                            <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image">
                        </div>

                        <!-- Content -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div style="flex: 1; min-width: 0;">
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 0.5rem;"><?php echo $listing['title']; ?></h3>
                                    <div style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: rgba(0,0,0,0.6); margin-bottom: 0.75rem;">
                                        <i data-lucide="map-pin" style="width: 1rem; height: 1rem; flex-shrink: 0;"></i>
                                        <span><?php echo $listing['location']; ?></span>
                                    </div>
                                </div>
                                <span class="status-badge <?php echo $statusClass; ?>" style="flex-shrink: 0;">
                                    <?php echo ucfirst($listing['status']); ?>
                                </span>
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
                                        <i data-lucide="dollar-sign" style="width: 1rem; height: 1rem; color: var(--deep-blue);"></i>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Price</p>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000;">$<?php echo $listing['price']; ?>/mo</p>
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
