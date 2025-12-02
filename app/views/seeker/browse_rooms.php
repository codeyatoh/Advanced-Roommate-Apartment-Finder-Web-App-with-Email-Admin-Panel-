<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Rooms - RoomFinder</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/filter-bar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-card.module.css">
</head>
<body>
    <?php
    // Load Listing model
    require_once __DIR__ . '/../../models/Listing.php';
    
    $listingModel = new Listing();
    
    // Get filter parameters
    $location = $_GET['location'] ?? '';
    $minPrice = $_GET['min_price'] ?? null;
    $maxPrice = $_GET['max_price'] ?? null;
    $bedrooms = $_GET['bedrooms'] ?? null;
    
    // Fetch rooms from database
    if (!empty($location) || !empty($minPrice) || !empty($maxPrice) || !empty($bedrooms)) {
        // Use search if filters are applied
        $rooms = $listingModel->search([
            'location' => $location,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'bedrooms' => $bedrooms
        ]);
    } else {
        // Get all available listings
        $rooms = $listingModel->getAvailable();
    }
    ?>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <!-- Navbar -->
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <!-- Tabs Header -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; gap: 1rem; border-bottom: 2px solid rgba(0,0,0,0.1);">
                        <button class="browse-tab active" data-tab="available" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            <i data-lucide="search" style="width: 1.125rem; height: 1.125rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;"></i>
                            Available Rooms
                        </button>
                        <button class="browse-tab" data-tab="my-rentals" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            <i data-lucide="key" style="width: 1.125rem; height: 1.125rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;"></i>
                            My Rentals
                        </button>
                    </div>
                </div>

                <!-- Available Rooms Tab Content -->
                <div id="available-tab-content" class="tab-content">
                    <!-- Header -->
                    <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                        <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                            Browse Rooms
                        </h1>
                        <p style="color: rgba(0, 0, 0, 0.6);">
                            Find your perfect room from <span style="font-weight: 600; color: #000000;"><?php echo count($rooms); ?> available listings</span>
                        </p>
                    </div>

                <!-- Filter Bar -->
                <div style="margin-bottom: 2rem;">
                    <div class="card card-glass-strong filter-bar">
                        <div class="filter-bar-content">
                            <!-- Main Search -->
                            <div class="filter-bar-main">
                                <div class="form-input-wrapper" style="flex: 1;">
                                    <i data-lucide="map-pin" class="form-input-icon"></i>
                                    <input type="text" class="form-input" placeholder="Location..." style="padding-left: 3rem;">
                                </div>
                                <button class="btn btn-glass btn-md" id="filterToggleBtn">
                                    <i data-lucide="sliders-horizontal" class="btn-icon"></i>
                                    Filters
                                </button>
                                <button class="btn btn-primary btn-md">Search</button>
                            </div>

                            <!-- Expanded Filters -->
                            <div id="filterOptions" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                                    <!-- Price Range -->
                                    <div class="form-group">
                                        <label class="form-label">Price Range</label>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <div class="form-input-wrapper">
                                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: rgba(0,0,0,0.5); font-weight: 500;">₱</span>
                                                <input type="number" class="form-input" placeholder="Min" style="padding-left: 2rem;">
                                            </div>
                                            <div class="form-input-wrapper">
                                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: rgba(0,0,0,0.5); font-weight: 500;">$</span>
                                                <input type="number" class="form-input" placeholder="Max" style="padding-left: 2rem;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bedrooms -->
                                    <div class="form-group">
                                        <label class="form-label">Bedrooms</label>
                                        <div class="form-input-wrapper">
                                            <select class="form-input" style="appearance: none; cursor: pointer;">
                                                <option value="">Any</option>
                                                <option value="1">1 Bedroom</option>
                                                <option value="2">2 Bedrooms</option>
                                                <option value="3">3+ Bedrooms</option>
                                            </select>
                                            <i data-lucide="chevron-down" class="form-input-icon" style="right: 1rem; left: auto; pointer-events: none;"></i>
                                        </div>
                                    </div>

                                    <!-- Roommates -->
                                    <div class="form-group">
                                        <label class="form-label">Roommates</label>
                                        <div class="form-input-wrapper">
                                            <select class="form-input" style="appearance: none; cursor: pointer;">
                                                <option value="">Any</option>
                                                <option value="0">No Roommates</option>
                                                <option value="1">1 Roommate</option>
                                                <option value="2">2+ Roommates</option>
                                            </select>
                                            <i data-lucide="chevron-down" class="form-input-icon" style="right: 1rem; left: auto; pointer-events: none;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const filterToggleBtn = document.getElementById('filterToggleBtn');
                        const filterOptions = document.getElementById('filterOptions');

                        if (filterToggleBtn && filterOptions) {
                            filterToggleBtn.addEventListener('click', () => {
                                const isHidden = filterOptions.style.display === 'none';
                                filterOptions.style.display = isHidden ? 'block' : 'none';
                                
                                // Optional: Add active state to button
                                if (isHidden) {
                                    filterToggleBtn.style.background = 'rgba(255, 255, 255, 0.5)';
                                    filterToggleBtn.style.borderColor = 'var(--primary)';
                                } else {
                                    filterToggleBtn.style.background = '';
                                    filterToggleBtn.style.borderColor = '';
                                }
                            });
                        }
                    });
                </script>

                <!-- Results Grid -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                <?php 
                    // Fetch listings from database
                    require_once __DIR__ . '/../../models/Listing.php';
                    require_once __DIR__ . '/../../models/SavedListing.php';
                    
                    $listingModel = new Listing();
                    $savedListingModel = new SavedListing();
                    
                    // Get current user ID
                    $userId = $_SESSION['user_id'] ?? null;
                    
                    // Get filter parameters if any
                    $filters = [];
                    if (!empty($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
                    if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
                    if (!empty($_GET['location'])) $filters['location'] = $_GET['location'];
                    if (!empty($_GET['bedrooms'])) $filters['bedrooms'] = $_GET['bedrooms'];
                    
                    // Search listings or get all available
                    $rooms = !empty($filters) ? $listingModel->search($filters) : $listingModel->getAvailable();

                    // Pagination for Rooms
                    $roomsPage = isset($_GET['rooms_page']) ? max(1, intval($_GET['rooms_page'])) : 1;
                    $roomsLimit = 5;
                    $totalRooms = count($rooms);
                    $totalRoomsPages = ceil($totalRooms / $roomsLimit);
                    $roomsOffset = ($roomsPage - 1) * $roomsLimit;
                    $rooms = array_slice($rooms, $roomsOffset, $roomsLimit);
                    
                    // Handle empty results
                    if (empty($rooms)) {
                        echo '<div style="text-align: center; padding: 3rem;">';
                        echo '<i data-lucide="search" style="width: 3rem; height: 3rem; color: rgba(0,0,0,0.3); margin: 0 auto 1rem;"></i>';
                        echo '<p style="color: rgba(0,0,0,0.5);">No listings found matching your criteria.</p>';
                        echo '</div>';
                    }
                    
                    
                    foreach ($rooms as $index => $room): 
                        // Handle image from database or fallback
                        $image = $room['primary_image'] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800';
                        $listingId = $room['listing_id'] ?? '';
                        $availableText = !empty($room['available_from']) ? date('M j', strtotime($room['available_from'])) : 'Now';
                        
                        // Check if saved
                        $isSaved = $userId ? $savedListingModel->isSaved($userId, $listingId) : false;
                        $heartClass = $isSaved ? 'fill-current text-red-500' : '';
                        $heartColor = $isSaved ? '#ef4444' : 'currentColor';
                        $heartFill = $isSaved ? '#ef4444' : 'none';
                    ?>
                    <div style="animation: slideUp 0.3s ease-out; animation-delay: <?php echo $index * 0.05; ?>s; animation-fill-mode: both;">
                        <a href="room_details.php?id=<?php echo $listingId; ?>" style="text-decoration: none; color: inherit; display: block;">
                            <div class="room-card">
                                <!-- Image -->
                                <div class="room-card-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" class="room-card-image">
                                    <button class="room-card-favorite" onclick="toggleFavorite(event, <?php echo $listingId; ?>, this)">
                                        <i data-lucide="heart" style="color: <?php echo $heartColor; ?>; fill: <?php echo $heartFill; ?>;"></i>
                                    </button>
                                    <?php 
                                    $bedsAvailable = ($room['bedrooms'] ?? 1) - ($room['current_roommates'] ?? 0);
                                    if ($bedsAvailable <= 0): 
                                    ?>
                                        <div class="room-card-badge" style="background: #ef4444; right: 1rem; left: auto;">FULL</div>
                                    <?php else: ?>
                                        <div class="room-card-badge">Available <?php echo htmlspecialchars($availableText); ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="room-card-content">
                                    <h3 class="room-card-title"><?php echo htmlspecialchars($room['title']); ?></h3>
                                    <div class="room-card-location">
                                        <i data-lucide="map-pin"></i>
                                        <?php echo htmlspecialchars($room['location']); ?>
                                    </div>

                                    <!-- Details -->
                                    <div class="room-card-details">
                                        <div class="room-card-detail">
                                            <i data-lucide="bed"></i>
                                            <?php echo intval($room['bedrooms'] ?? 1); ?> bed
                                        </div>
                                        <div class="room-card-detail">
                                            <i data-lucide="bath"></i>
                                            <?php echo intval($room['bathrooms'] ?? 1); ?> bath
                                        </div>
                                        <div class="room-card-detail">
                                            <i data-lucide="users"></i>
                                            <?php 
                                            $bedsAvailable = ($room['bedrooms'] ?? 1) - ($room['current_roommates'] ?? 0);
                                            if ($bedsAvailable <= 0) {
                                                echo '<span style="color: #ef4444; font-weight: 600;">FULL</span>';
                                            } else {
                                                echo $bedsAvailable . ' bed' . ($bedsAvailable != 1 ? 's' : '') . ' available';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div style="padding-top: 0.75rem; border-top: 1px solid rgba(0, 0, 0, 0.1);">
                                        <div class="room-card-price">
                                            <span>₱<?php echo number_format($room['price'], 0); ?></span>
                                            <span class="room-card-price-period">/month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Controls for Rooms -->
                <?php if ($totalRoomsPages > 1): ?>
                <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                    <!-- Previous Button -->
                    <?php if ($roomsPage > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['rooms_page' => $roomsPage - 1, 'tab' => 'available'])); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                        <?php for ($i = 1; $i <= max(1, $totalRoomsPages); $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['rooms_page' => $i, 'tab' => 'available'])); ?>" 
                               class="btn btn-sm" 
                               style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $roomsPage ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <!-- Next Button -->
                    <?php if ($roomsPage < $totalRoomsPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['rooms_page' => $roomsPage + 1, 'tab' => 'available'])); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
            </div> <!-- Close available-tab-content -->

            <!-- My Rentals Tab Content -->
            <div id="my-rentals-tab-content" class="tab-content" style="display: none;">
                <?php
                // Get user's rentals
                require_once __DIR__ . '/../../models/Rental.php';
                $rentalModel = new Rental();
                $userId = $_SESSION['user_id'] ?? null;
                $rentals = [];
                
                if ($userId) {
                    $rentals = $rentalModel->getByTenant($userId);
                    
                    // Enhance rentals with listing details
                    foreach ($rentals as &$rental) {
                        $listing = $listingModel->getWithImages($rental['listing_id']);
                        $rental['listing_details'] = $listing;
                        $rental['primary_image'] = !empty($listing['images']) ? $listing['images'][0]['image_url'] : 'https://via.placeholder.com/400x300?text=No+Image';
                    }
                }

                // Pagination for Rentals
                $rentalsPage = isset($_GET['rentals_page']) ? max(1, intval($_GET['rentals_page'])) : 1;
                $rentalsLimit = 5;
                $totalRentals = count($rentals);
                $totalRentalsPages = ceil($totalRentals / $rentalsLimit);
                $rentalsOffset = ($rentalsPage - 1) * $rentalsLimit;
                $rentals = array_slice($rentals, $rentalsOffset, $rentalsLimit);
                ?>
                
                <!-- Header -->
                <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                        My Rentals
                    </h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">
                        Manage your <span style="font-weight: 600; color: #000000;"><?php echo count($rentals); ?> room rental<?php echo count($rentals) != 1 ? 's' : ''; ?></span>
                    </p>
                </div>

                <!-- Status Filter Tabs -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; gap: 1rem; border-bottom: 2px solid rgba(0,0,0,0.1); overflow-x: auto;">
                        <button class="rental-status-tab active" data-status="all" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 0.75rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            All Rentals
                        </button>
                        <button class="rental-status-tab" data-status="active" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 0.75rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            Active
                        </button>
                        <button class="rental-status-tab" data-status="pending" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 0.75rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            Pending
                        </button>
                        <button class="rental-status-tab" data-status="completed" style="background: transparent; border: none; border-bottom: 3px solid transparent; padding: 0.75rem 1.5rem; font-weight: 600; color: rgba(0,0,0,0.5); cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                            Completed
                        </button>
                    </div>
                </div>

                <?php if (empty($rentals)): ?>
                    <!-- Empty State -->
                    <div class="card card-glass-strong" style="padding: 4rem 2rem; text-align: center;">
                        <i data-lucide="home" style="width: 4rem; height: 4rem; margin: 0 auto 1.5rem; color: rgba(0,0,0,0.3);"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #000000; margin-bottom: 0.5rem;">No Rentals Yet</h3>
                        <p style="color: rgba(0,0,0,0.6); margin-bottom: 2rem;">You haven't rented any rooms yet. Browse available rooms to find your perfect match!</p>
                        <button class="browse-tab" data-tab="available" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #667eea; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; text-decoration: none;">
                            <i data-lucide="search" style="width: 1.125rem; height: 1.125rem;"></i>
                            Browse Rooms
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Rentals Grid -->
                    <div class="rentals-grid">
                        <?php foreach ($rentals as $index => $rental): 
                            $statusColors = [
                                'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Pending'],
                                'active' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Active'],
                                'completed' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'label' => 'Completed'],
                                'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Cancelled']
                            ];
                            $status = $rental['status'];
                            $statusInfo = $statusColors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => ucfirst($status)];
                        ?>
                        <div class="rental-item" data-status="<?php echo $status; ?>">
                            <div class="card card-glass-strong rental-card">
                                <!-- Image Section -->
                                <div class="rental-card-image">
                                    <img src="<?php echo htmlspecialchars($rental['primary_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($rental['listing_title']); ?>">
                                    <div class="rental-status-badge" style="background: <?php echo $statusInfo['bg']; ?>; color: <?php echo $statusInfo['text']; ?>;">
                                        <?php echo $statusInfo['label']; ?>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="rental-card-content">
                                    <!-- Title & Location -->
                                    <div class="rental-card-header">
                                        <h3 class="rental-card-title">
                                            <?php echo htmlspecialchars($rental['listing_title']); ?>
                                        </h3>
                                        <div class="rental-card-location">
                                            <i data-lucide="map-pin"></i>
                                            <span><?php echo htmlspecialchars($rental['location']); ?></span>
                                        </div>
                                    </div>

                                    <!-- Details Grid -->
                                    <div class="rental-card-details">
                                        <div class="rental-detail-item">
                                            <span class="rental-detail-label">Monthly Rent</span>
                                            <span class="rental-detail-value">₱<?php echo number_format($rental['rent_amount']); ?></span>
                                        </div>
                                        <div class="rental-detail-item">
                                            <span class="rental-detail-label">Start Date</span>
                                            <span class="rental-detail-value"><?php echo date('M d, Y', strtotime($rental['start_date'])); ?></span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="rental-card-actions">
                                        <a href="room_details.php?id=<?php echo $rental['listing_id']; ?>" class="btn btn-glass btn-sm">
                                            <i data-lucide="eye"></i>
                                            <span>View Room</span>
                                        </a>
                                        <?php if ($status === 'active'): ?>
                                        <a href="messages.php?user_id=<?php echo $rental['landlord_id']; ?>" class="btn btn-primary btn-sm">
                                            <i data-lucide="message-circle"></i>
                                            <span>Message</span>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination Controls for Rentals -->
                    <?php if ($totalRentalsPages > 1): ?>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                        <!-- Previous Button -->
                        <?php if ($rentalsPage > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['rentals_page' => $rentalsPage - 1, 'tab' => 'my-rentals'])); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                            <?php for ($i = 1; $i <= max(1, $totalRentalsPages); $i++): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['rentals_page' => $i, 'tab' => 'my-rentals'])); ?>" 
                                   class="btn btn-sm" 
                                   style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $rentalsPage ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <!-- Next Button -->
                        <?php if ($rentalsPage < $totalRentalsPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['rentals_page' => $rentalsPage + 1, 'tab' => 'my-rentals'])); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                <?php endif; ?>
            </div> <!-- Close my-rentals-tab-content -->

                <style>
                    /* Rentals Grid - Responsive */
                    .rentals-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 1.5rem;
                    }

                    /* Rental Card */
                    .rental-card {
                        overflow: hidden;
                        transition: transform 0.2s, box-shadow 0.2s;
                    }

                    .rental-card:hover {
                        transform: translateY(-4px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
                    }

                    /* Image Section */
                    .rental-card-image {
                        position: relative;
                        width: 100%;
                        height: 200px;
                        overflow: hidden;
                    }

                    .rental-card-image img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.3s;
                    }

                    .rental-card:hover .rental-card-image img {
                        transform: scale(1.05);
                    }

                    .rental-status-badge {
                        position: absolute;
                        top: 1rem;
                        right: 1rem;
                        padding: 0.5rem 1rem;
                        border-radius: 999px;
                        font-size: 0.75rem;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    }

                    /* Content Section */
                    .rental-card-content {
                        padding: 1.5rem;
                        display: flex;
                        flex-direction: column;
                        gap: 1.25rem;
                    }

                    /* Header */
                    .rental-card-header {
                        display: flex;
                        flex-direction: column;
                        gap: 0.5rem;
                        padding-bottom: 1rem;
                        border-bottom: 1px solid rgba(0,0,0,0.08);
                    }

                    .rental-card-title {
                        font-size: 1.25rem;
                        font-weight: 700;
                        color: #111827;
                        margin: 0;
                        line-height: 1.4;
                    }

                    .rental-card-location {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: #6b7280;
                        font-size: 0.875rem;
                    }

                    .rental-card-location i {
                        width: 1rem;
                        height: 1rem;
                        flex-shrink: 0;
                    }

                    /* Details Grid */
                    .rental-card-details {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 1.25rem;
                    }

                    .rental-detail-item {
                        display: flex;
                        flex-direction: column;
                        gap: 0.375rem;
                    }

                    .rental-detail-label {
                        font-size: 0.75rem;
                        font-weight: 500;
                        color: #9ca3af;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }

                    .rental-detail-value {
                        font-size: 1.125rem;
                        font-weight: 700;
                        color: #111827;
                    }

                    /* Actions */
                    .rental-card-actions {
                        display: flex;
                        gap: 0.75rem;
                        padding-top: 1rem;
                        border-top: 1px solid rgba(0,0,0,0.08);
                    }

                    .rental-card-actions .btn {
                        flex: 1;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        padding: 0.75rem 1rem;
                        font-weight: 600;
                        text-decoration: none;
                        transition: all 0.2s;
                    }

                    .rental-card-actions .btn i {
                        width: 1.125rem;
                        height: 1.125rem;
                    }

                    /* Responsive Breakpoints */
                    @media (min-width: 640px) {
                        .rentals-grid {
                            grid-template-columns: repeat(2, 1fr);
                        }

                        .rental-card-image {
                            height: 220px;
                        }
                    }

                    @media (min-width: 1024px) {
                        .rentals-grid {
                            grid-template-columns: repeat(3, 1fr);
                        }

                        .rental-card-image {
                            height: 240px;
                        }

                        .rental-card-actions .btn {
                            padding: 0.875rem 1.25rem;
                        }
                    }

                    @media (min-width: 1280px) {
                        .rentals-grid {
                            gap: 2rem;
                        }
                    }

                    /* Mobile Optimization */
                    @media (max-width: 639px) {
                        .rental-card-content {
                            padding: 1.25rem;
                        }

                        .rental-card-title {
                            font-size: 1.125rem;
                        }

                        .rental-card-details {
                            grid-template-columns: 1fr;
                            gap: 1rem;
                        }

                        .rental-card-actions {
                            flex-direction: column;
                        }

                        .rental-card-actions .btn {
                            width: 100%;
                        }
                    }

                    /* Filter Bar Responsive */
                    @media (min-width: 768px) {
                        .filter-bar-main, div[style*="grid-template-columns: 1fr"] {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                    
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"]:not(.rentals-grid) {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function toggleFavorite(event, listingId, btn) {
            event.preventDefault(); // Prevent link click
            event.stopPropagation();

            fetch('../../controllers/seeker/FavoritesController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ listing_id: listingId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.querySelector('svg'); // Lucide replaces <i> with <svg>
                    if (data.action === 'added') {
                        icon.setAttribute('fill', '#ef4444');
                        icon.setAttribute('color', '#ef4444');
                        // Optional: Show toast
                    } else {
                        icon.setAttribute('fill', 'none');
                        icon.setAttribute('color', 'currentColor');
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        // Tab switching between Available Rooms and My Rentals
        const browseTabs = document.querySelectorAll('.browse-tab');
        const tabContents = document.querySelectorAll('.tab-content');

        browseTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                // Prevent form submission if inside a form
                e.preventDefault();
                
                // Update active tab
                browseTabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.color = 'rgba(0,0,0,0.5)';
                    t.style.borderBottomColor = 'transparent';
                });
                tab.classList.add('active');
                tab.style.color = '#667eea';
                tab.style.borderBottomColor = '#667eea';

                // Show corresponding content
                const targetTab = tab.dataset.tab;
                tabContents.forEach(content => {
                    if (content.id === `${targetTab}-tab-content`) {
                        content.style.display = 'block';
                    } else {
                        content.style.display = 'none';
                    }
                });

                // Reinitialize lucide icons for newly shown content
                lucide.createIcons();
            });
        });

        // Rental status filtering (inside My Rentals tab)
        const rentalStatusTabs = document.querySelectorAll('.rental-status-tab');
        const rentalItems = document.querySelectorAll('.rental-item');

        rentalStatusTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active status tab
                rentalStatusTabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.color = 'rgba(0,0,0,0.5)';
                    t.style.borderBottomColor = 'transparent';
                });
                tab.classList.add('active');
                tab.style.color = '#667eea';
                tab.style.borderBottomColor = '#667eea';

                const status = tab.dataset.status;

                // Filter rental items
                rentalItems.forEach(item => {
                    if (status === 'all' || item.dataset.status === status) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Check URL for active tab
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            
            if (activeTab) {
                const tabBtn = document.querySelector(`.browse-tab[data-tab="${activeTab}"]`);
                if (tabBtn) {
                    // Trigger click to switch tab
                    tabBtn.click();
                }
            }
        });
    </script>
</body>
</html>
