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
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
</head>
<body>
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

            <!-- Listings Grid -->
            <div class="listings-grid">
                <?php
                $listings = [
                    [
                        'id' => 1,
                        'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
                        'title' => 'Modern Studio Downtown',
                        'location' => 'San Francisco, CA',
                        'price' => 1200,
                        'status' => 'active',
                        'views' => 45,
                        'inquiries' => 8,
                    ],
                    [
                        'id' => 2,
                        'image' => 'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=800',
                        'title' => 'Cozy Apartment',
                        'location' => 'Oakland, CA',
                        'price' => 950,
                        'status' => 'active',
                        'views' => 32,
                        'inquiries' => 5,
                    ],
                    [
                        'id' => 3,
                        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800',
                        'title' => 'Spacious Loft',
                        'location' => 'Berkeley, CA',
                        'price' => 1400,
                        'status' => 'rented',
                        'views' => 67,
                        'inquiries' => 12,
                    ],
                    [
                        'id' => 4,
                        'image' => 'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=800',
                        'title' => 'Bright Room in House',
                        'location' => 'San Jose, CA',
                        'price' => 850,
                        'status' => 'active',
                        'views' => 28,
                        'inquiries' => 4,
                    ],
                ];

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
                                <i data-lucide="dollar-sign" style="width: 1rem; height: 1rem;"></i>
                                <?php echo $listing['inquiries']; ?> inquiries
                            </div>
                        </div>

                        <!-- Price -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2);">
                            <div>
                                <span style="font-size: 1.5rem; font-weight: 700; color: var(--deep-blue);">$<?php echo $listing['price']; ?></span>
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
