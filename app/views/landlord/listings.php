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

// Get filters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'All Status';
$sort = $_GET['sort'] ?? 'Newest';

// Get filtered listings
$listings = $listingModel->getLandlordListings($landlordId, [
    'search' => $search,
    'status' => $status === 'All Status' ? '' : $status,
    'sort' => $sort
]);

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$totalListings = count($listings);
$totalPages = ceil($totalListings / $limit);
$offset = ($page - 1) * $limit;

// Slice listings for current page
$paginatedListings = array_slice($listings, $offset, $limit);

// Get inquiry count for each listing (only for current page)
foreach ($paginatedListings as $key => $listing) {
    $paginatedListings[$key]['inquiries'] = $messageModel->getInquiryCountForListing($listing['listing_id']);
    $paginatedListings[$key]['image'] = $listing['primary_image'] ?? 'https://via.placeholder.com/800x600?text=No+Image';
    $paginatedListings[$key]['display_location'] = $listing['location'] ?? 'Unknown location';
    
    // Determine display status
    if ($listing['availability_status'] === 'occupied' || $listing['availability_status'] === 'rented') {
        $paginatedListings[$key]['status'] = 'rented';
    } elseif ($listing['approval_status'] === 'pending') {
        $paginatedListings[$key]['status'] = 'pending';
    } elseif ($listing['approval_status'] === 'rejected') {
        $paginatedListings[$key]['status'] = 'rejected';
    } else {
        $paginatedListings[$key]['status'] = 'active';
    }
    
    $paginatedListings[$key]['views'] = $listing['views'] ?? 0;
}

// Replace original array with paginated one for display
$listings = $paginatedListings;
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
            <form method="GET" action="" style="margin-bottom: 2rem;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" name="search" class="search-input-clean" placeholder="Search my listings..." value="<?php echo htmlspecialchars($search); ?>">
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
                <div id="filterOptions" style="display: <?php echo ($status !== 'All Status' || $sort !== 'Newest') ? 'flex' : 'none'; ?>; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select name="status" class="form-select" style="flex: 1; height: 2.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                        <option <?php echo $status === 'All Status' ? 'selected' : ''; ?>>All Status</option>
                        <option <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option <?php echo $status === 'Rented' ? 'selected' : ''; ?>>Rented</option>
                        <option <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option <?php echo $status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    <select name="sort" class="form-select" style="flex: 1; height: 2.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                        <option <?php echo $sort === 'Newest' ? 'selected' : ''; ?>>Newest</option>
                        <option <?php echo $sort === 'Price: Low to High' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option <?php echo $sort === 'Price: High to Low' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>
            </form>

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
                    $statusLabel = ucfirst(str_replace('_', ' ', $listing['status']));
                ?>
                <div class="glass-card animate-slide-up" style="padding: 0; animation-delay: <?php echo $index * 0.1; ?>s;">
                    <!-- Image -->
                    <div class="listing-image-container">
                        <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image">
                        <div class="status-badge <?php echo $statusClass; ?>">
                            <?php echo $statusLabel; ?>
                        </div>

                    </div>

                    <!-- Content -->
                    <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #000; margin-bottom: 0.25rem;"><?php echo $listing['title']; ?></h3>
                            <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; color: rgba(0,0,0,0.6);">
                                <i data-lucide="map-pin" style="width: 1rem; height: 1rem;"></i>
                                <?php echo $listing['display_location']; ?>
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

                        <?php if ($listing['status'] === 'pending'): ?>
                            <div style="font-size: 0.875rem; color: #92400e; background: rgba(251, 191, 36, 0.15); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                                Awaiting admin approval before publishing.
                            </div>
                        <?php elseif ($listing['status'] === 'rejected' && !empty($listing['admin_note'])): ?>
                            <div style="font-size: 0.875rem; color: #b91c1c; background: rgba(248, 113, 113, 0.15); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                                Admin note: <?php echo htmlspecialchars($listing['admin_note']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; padding-top: 0.5rem;">

                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/view_listing.php?id=<?php echo $listing['listing_id']; ?>" class="btn btn-glass btn-sm" style="flex: 1; text-decoration: none; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                View
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                    <?php for ($i = 1; $i <= max(1, $totalPages); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>" 
                           class="btn btn-sm" 
                           style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $page ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
