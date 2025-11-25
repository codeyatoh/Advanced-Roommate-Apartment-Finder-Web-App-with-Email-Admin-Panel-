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
                    $listingModel = new Listing();
                    
                    // Get filter parameters if any
                    $filters = [];
                    if (!empty($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
                    if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
                    if (!empty($_GET['location'])) $filters['location'] = $_GET['location'];
                    if (!empty($_GET['bedrooms'])) $filters['bedrooms'] = $_GET['bedrooms'];
                    
                    // Search listings or get all available
                    $rooms = !empty($filters) ? $listingModel->search($filters) : $listingModel->getAvailable();
                    
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
                    ?>
                    <div style="animation: slideUp 0.3s ease-out; animation-delay: <?php echo $index * 0.05; ?>s; animation-fill-mode: both;">
                        <a href="room_details.php?id=<?php echo $listingId; ?>" style="text-decoration: none; color: inherit; display: block;">
                            <div class="room-card">
                                <!-- Image -->
                                <div class="room-card-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" class="room-card-image">
                                    <button class="room-card-favorite" onclick="event.preventDefault();">
                                        <i data-lucide="heart"></i>
                                    </button>
                                    <div class="room-card-badge">Available <?php echo htmlspecialchars($availableText); ?></div>
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
                                            <?php echo intval($room['current_roommates'] ?? 0); ?> roommates
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

                <style>
                    @media (min-width: 768px) {
                        .filter-bar-main, div[style*="grid-template-columns: 1fr"] {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"] {
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
    </script>
</body>
</html>
