<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-card.module.css">
</head>
<body>
    <?php
    session_start();
    require_once __DIR__ . '/../../models/Rental.php';
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/Payment.php';
    
    // Check if user is logged in as seeker
    $userId = $_SESSION['user_id'] ?? 1; // Fallback for dev
    
    $rentalModel = new Rental();
    $listingModel = new Listing();
    $paymentModel = new Payment();
    
    // Get user's rentals
    $rentals = $rentalModel->getByTenant($userId);
    
    // Enhance rentals with additional data
    foreach ($rentals as &$rental) {
        $listing = $listingModel->getWithImages($rental['listing_id']);
        $rental['listing_details'] = $listing;
        $rental['primary_image'] = !empty($listing['images']) ? $listing['images'][0]['image_url'] : 'https://via.placeholder.com/400x300?text=No+Image';
    }
    
    include __DIR__ . '/../includes/navbar.php';
    ?>
    
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <!-- Header -->
                <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                        My Rentals
                    </h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">
                        Manage your <span style="font-weight: 600; color: #000000;"><?php echo count($rentals); ?> room rental<?php echo count($rentals) != 1 ? 's' : ''; ?></span>
                    </p>
                </div>

                <!-- Tabs -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; gap: 1rem; border-bottom: 2px solid rgba(0,0,0,0.1); overflow-x: auto;">
                        <button class="rental-tab active" data-status="all">
                            All Rentals
                        </button>
                        <button class="rental-tab" data-status="active">
                            Active
                        </button>
                        <button class="rental-tab" data-status="pending">
                            Pending
                        </button>
                        <button class="rental-tab" data-status="completed">
                            Completed
                        </button>
                    </div>
                </div>

                <?php if (empty($rentals)): ?>
                    <!-- Empty State -->
                    <div class="card card-glass-strong" style="padding: 4rem 2rem; text-align: center;">
                        <i data-lucide="home" style="width: 4rem; height: 4rem; margin: 0 auto 1.5rem; color: rgba(0,0,0,0.3);"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #000000; margin-bottom: 0.5rem;">No Rentals Yet</h3>
                        <p style="color: rgba(0,0,0,0.6); margin-bottom: 2rem;">You haven't rented any rooms yet. Start browsing available rooms!</p>
                        <a href="browse_rooms.php" class="btn btn-primary btn-lg" style="text-decoration: none;">
                            <i data-lucide="search" class="btn-icon"></i>
                            Browse Rooms
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Rentals Grid -->
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                        <?php foreach ($rentals as $index => $rental): 
                            $statusColors = [
                                'pending' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'active' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                'completed' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                            ];
                            $status = $rental['status'];
                            $statusColor = $statusColors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        ?>
                        <div class="rental-item" data-status="<?php echo $status; ?>" style="animation: slideUp 0.3s ease-out; animation-delay: <?php echo $index * 0.05; ?>s; animation-fill-mode: both;">
                            <div class="card card-glass-strong" style="overflow: hidden;">
                                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 1.5rem;">
                                    <!-- Image Section -->
                                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem;">
                                        <div style="position: relative; border-radius: 12px; overflow: hidden; height: 150px;">
                                            <img src="<?php echo htmlspecialchars($rental['primary_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($rental['listing_title']); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                            <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: <?php echo $statusColor['bg']; ?>; color: <?php echo $statusColor['text']; ?>; padding: 0.375rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                                <?php echo $status; ?>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                                            <div>
                                                <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                                                    <?php echo htmlspecialchars($rental['listing_title']); ?>
                                                </h3>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0,0,0,0.6); font-size: 0.875rem;">
                                                    <i data-lucide="map-pin" style="width: 1rem; height: 1rem;"></i>
                                                    <?php echo htmlspecialchars($rental['location']); ?>
                                                </div>
                                            </div>

                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); margin-bottom: 0.25rem;">Monthly Rent</p>
                                                    <p style="font-size: 1.125rem; font-weight: 700; color: #000000;">
                                                        ₱<?php echo number_format($rental['rent_amount']); ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); margin-bottom: 0.25rem;">Start Date</p>
                                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000000;">
                                                        <?php echo date('M d, Y', strtotime($rental['start_date'])); ?>
                                                    </p>
                                                </div>
                                                <?php if ($rental['end_date']): ?>
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); margin-bottom: 0.25rem;">End Date</p>
                                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000000;">
                                                        <?php echo date('M d, Y', strtotime($rental['end_date'])); ?>
                                                    </p>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Actions -->
                                            <div style="display: flex; gap: 0.75rem; margin-top: auto;">
                                                <a href="room_details.php?id=<?php echo $rental['listing_id']; ?>" 
                                                   class="btn btn-glass btn-sm" 
                                                   style="text-decoration: none;">
                                                    <i data-lucide="eye" class="btn-icon"></i>
                                                    View Room
                                                </a>
                                                <?php if ($status === 'active'): ?>
                                                <a href="messages.php?user_id=<?php echo $rental['landlord_id']; ?>" 
                                                   class="btn btn-primary btn-sm" 
                                                   style="text-decoration: none;">
                                                    <i data-lucide="message-circle" class="btn-icon"></i>
                                                    Message Landlord
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <style>
                    .rental-tab {
                        background: transparent;
                        border: none;
                        border-bottom: 3px solid transparent;
                        padding: 0.75rem 1.5rem;
                        font-weight: 600;
                        color: rgba(0,0,0,0.5);
                        cursor: pointer;
                        transition: all 0.2s;
                        white-space: nowrap;
                    }
                    
                    .rental-tab:hover {
                        color: rgba(0,0,0,0.8);
                    }
                    
                    .rental-tab.active {
                        color: #667eea;
                        border-bottom-color: #667eea;
                    }

                    @media (max-width: 768px) {
                        div[style*="grid-template-columns: 200px 1fr"] {
                            grid-template-columns: 1fr !important;
                        }
                    }
                    
                    @media (min-width: 768px) {
                        div[style*="grid-template-columns: 1fr"] {
                            grid-template-columns: 1fr !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Tab filtering
        const tabs = document.querySelectorAll('.rental-tab');
        const rentalItems = document.querySelectorAll('.rental-item');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const status = tab.dataset.status;

                // Filter rentals
                rentalItems.forEach(item => {
                    if (status === 'all' || item.dataset.status === status) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
