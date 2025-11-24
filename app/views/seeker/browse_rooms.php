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
                        Find your perfect room from <span style="font-weight: 600; color: #000000;">6 available listings</span>
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
                                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: rgba(0,0,0,0.5); font-weight: 500;">$</span>
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
                    $rooms = [
                        [
                            'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
                            'title' => 'Modern Studio in Downtown',
                            'location' => 'San Francisco, CA',
                            'price' => 1200,
                            'bedrooms' => 1,
                            'bathrooms' => 1,
                            'roommates' => 0,
                            'available' => 'Now'
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=800',
                            'title' => 'Cozy Room with Garden View',
                            'location' => 'Portland, OR',
                            'price' => 850,
                            'bedrooms' => 1,
                            'bathrooms' => 1,
                            'roommates' => 2,
                            'available' => 'Feb 1'
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800',
                            'title' => 'Spacious Loft Near Campus',
                            'location' => 'Austin, TX',
                            'price' => 950,
                            'bedrooms' => 2,
                            'bathrooms' => 1,
                            'roommates' => 1,
                            'available' => 'Jan 15'
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=800',
                            'title' => 'Bright Room in Shared House',
                            'location' => 'Seattle, WA',
                            'price' => 780,
                            'bedrooms' => 1,
                            'bathrooms' => 1,
                            'roommates' => 3,
                            'available' => 'Feb 15'
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800',
                            'title' => 'Luxury Apartment Downtown',
                            'location' => 'New York, NY',
                            'price' => 1800,
                            'bedrooms' => 2,
                            'bathrooms' => 2,
                            'roommates' => 0,
                            'available' => 'Now'
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800',
                            'title' => 'Cozy Studio Near Beach',
                            'location' => 'Los Angeles, CA',
                            'price' => 1100,
                            'bedrooms' => 1,
                            'bathrooms' => 1,
                            'roommates' => 0,
                            'available' => 'Mar 1'
                        ]
                    ];
                    
                    foreach ($rooms as $index => $room): 
                    ?>
                    <div style="animation: slideUp 0.3s ease-out; animation-delay: <?php echo $index * 0.05; ?>s; animation-fill-mode: both;">
                        <a href="room_details.php" style="text-decoration: none; color: inherit; display: block;">
                            <div class="room-card">
                                <!-- Image -->
                                <div class="room-card-image-wrapper">
                                    <img src="<?php echo $room['image']; ?>" alt="<?php echo $room['title']; ?>" class="room-card-image">
                                    <button class="room-card-favorite" onclick="event.preventDefault();">
                                        <i data-lucide="heart"></i>
                                    </button>
                                    <div class="room-card-badge">Available <?php echo $room['available']; ?></div>
                                </div>

                                <!-- Content -->
                                <div class="room-card-content">
                                    <h3 class="room-card-title"><?php echo $room['title']; ?></h3>
                                    <div class="room-card-location">
                                        <i data-lucide="map-pin"></i>
                                        <?php echo $room['location']; ?>
                                    </div>

                                    <!-- Details -->
                                    <div class="room-card-details">
                                        <div class="room-card-detail">
                                            <i data-lucide="bed"></i>
                                            <?php echo $room['bedrooms']; ?> bed
                                        </div>
                                        <div class="room-card-detail">
                                            <i data-lucide="bath"></i>
                                            <?php echo $room['bathrooms']; ?> bath
                                        </div>
                                        <div class="room-card-detail">
                                            <i data-lucide="users"></i>
                                            <?php echo $room['roommates']; ?> roommates
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div style="padding-top: 0.75rem; border-top: 1px solid rgba(0, 0, 0, 0.1);">
                                        <div class="room-card-price">
                                            <span>$<?php echo $room['price']; ?></span>
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
