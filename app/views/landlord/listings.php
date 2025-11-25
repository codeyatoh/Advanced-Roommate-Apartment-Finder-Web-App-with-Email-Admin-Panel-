<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css?v=<?php echo time(); ?>">
</head>
<body>
<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/Listing.php';
require_once __DIR__ . '/../../models/Message.php';

// Check if user is logged in as landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$landlordId = $_SESSION['user_id'];
$listingModel = new Listing();
$messageModel = new Message();

// Get all listings for this landlord
$listings = $listingModel->getByLandlord($landlordId);

// Get inquiry count for each listing
foreach ($listings as &$listing) {
    $inquiryCount = $messageModel->getInquiryCountForListing($listing['listing_id']);
    $listing['inquiries'] = $inquiryCount;
    
    // Get first image
    if (!empty($listing['images'])) {
        $listing['image'] = is_array($listing['images']) ? $listing['images'][0] : json_decode($listing['images'], true)[0] ?? 'https://via.placeholder.com/800x600?text=No+Image';
    } else {
        $listing['image'] = 'https://via.placeholder.com/800x600?text=No+Image';
    }
    
    // Format location
    $listing['location'] = $listing['city'] . ', ' . $listing['state'];
}
?>
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">My Listings</h1>
                    <p class="page-subtitle">Manage your property listings</p>
                </div>
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/add_listing.php" class="btn btn-primary btn-md">
                    <i data-lucide="plus" style="width: 1.25rem; height: 1.25rem;"></i>
                    Add New Listing
                </a>
            </div>

            <!-- Search & Filters -->
            <div style="margin-bottom: 2rem;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" class="search-input-clean" placeholder="Search my listings...">
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
                    <select class="form-select" style="flex: 1; height: 2.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Rented</option>
                        <option>Pending</option>
                    </select>
                    <select class="form-select" style="flex: 1; height: 2.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                        <option>Sort By</option>
                        <option>Newest</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- Listings Grid -->
            <div class="listings-grid">
                <?php
                // Listings already loaded from database at top of file
                if (empty($listings)):
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem 1rem;">
                    <i data-lucide="home" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.2); margin: 0 auto 1rem;"></i>
                    <h3 style="color: rgba(0,0,0,0.6); margin: 0;">No listings yet</h3>
                    <p style="color: rgba(0,0,0,0.5); margin: 0.5rem 0 0 0;">Create your first listing to get started</p>
                </div>
                <?php
                endif;

                foreach ($listings as $index => $listing): 
                    $statusClass = 'status-' . $listing['status'];
                ?>
                <div class="glass-card animate-slide-up" style="padding: 0; animation-delay: <?php echo $index * 0.1; ?>s;">
                    <!-- Image -->
                    <div class="listing-image-container">
                        <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image">
                        <div class="status-badge <?php echo $statusClass; ?>">
                            <?php echo ucfirst($listing['status']); ?>
                        </div>
                        <button class="menu-btn" onclick="toggleMenu(<?php echo $listing['id']; ?>)" style="position: absolute; top: 0.75rem; right: 0.75rem; padding: 0.5rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(4px); border-radius: 9999px; border: none; cursor: pointer; transition: all 0.2s;">
                            <i data-lucide="more-vertical" style="width: 1rem; height: 1rem; color: #000;"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="menu-<?php echo $listing['id']; ?>" class="dropdown-menu" style="display: none; position: absolute; top: 3rem; right: 0.75rem; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; z-index: 10; min-width: 8rem;">
                            <button style="display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.5rem 1rem; border: none; background: transparent; cursor: pointer; font-size: 0.875rem; color: #000; text-align: left;">
                                <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i> Edit
                            </button>
                            <button style="display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.5rem 1rem; border: none; background: transparent; cursor: pointer; font-size: 0.875rem; color: #000; text-align: left;">
                                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i> View
                            </button>
                            <button style="display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.5rem 1rem; border: none; background: transparent; cursor: pointer; font-size: 0.875rem; color: #dc2626; text-align: left;">
                                <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i> Delete
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #000; margin-bottom: 0.25rem;"><?php echo $listing['title']; ?></h3>
                            <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; color: rgba(0,0,0,0.6);">
                                <i data-lucide="map-pin" style="width: 1rem; height: 1rem;"></i>
                                <?php echo $listing['location']; ?>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; color: rgba(0,0,0,0.6);">
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                <?php echo $listing['views']; ?> views
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <i data-lucide="coins" style="width: 1rem; height: 1rem;"></i>
                                <?php echo $listing['inquiries']; ?> inquiries
                            </div>
                        </div>

                        <!-- Price -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2);">
                            <div>
                                <span style="font-size: 1.5rem; font-weight: 700; color: var(--deep-blue);">₱<?php echo $listing['price']; ?></span>
                                <span style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">/month</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; padding-top: 0.5rem;">
                            <button class="btn btn-primary btn-sm" style="flex: 1;">
                                <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i>
                                Edit
                            </button>
                            <button class="btn btn-glass btn-sm" style="flex: 1;">
                                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                View
                            </button>
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

        function toggleMenu(id) {
            const menu = document.getElementById(`menu-${id}`);
            const allMenus = document.querySelectorAll('.dropdown-menu');
            
            // Close all other menus
            allMenus.forEach(m => {
                if (m.id !== `menu-${id}`) {
                    m.style.display = 'none';
                }
            });

            // Toggle current menu
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
                menu.classList.add('animate-slide-up');
            } else {
                menu.style.display = 'none';
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.menu-btn') && !e.target.closest('.dropdown-menu')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    m.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
