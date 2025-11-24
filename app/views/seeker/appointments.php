<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - RoomFinder</title>
    
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
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/appointments.module.css">
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
                        My Appointments
                    </h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">
                        Manage your property viewing schedule
                    </p>
                </div>

                <!-- Appointments List -->
                <div class="appointments-list">
                    <?php 
                    $appointments = [
                        [
                            'id' => 1,
                            'property' => 'Modern Studio Downtown',
                            'landlord' => 'David Martinez',
                            'date' => 'Tomorrow',
                            'time' => '2:00 PM',
                            'location' => '123 Market St, San Francisco',
                            'status' => 'confirmed',
                            'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400'
                        ],
                        [
                            'id' => 2,
                            'property' => 'Cozy Apartment',
                            'landlord' => 'Lisa Wong',
                            'date' => 'Feb 2, 2024',
                            'time' => '10:00 AM',
                            'location' => '456 Oak Ave, Oakland',
                            'status' => 'pending',
                            'image' => 'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=400'
                        ],
                        [
                            'id' => 3,
                            'property' => 'Spacious Loft',
                            'landlord' => 'John Smith',
                            'date' => 'Jan 28, 2024',
                            'time' => '3:00 PM',
                            'location' => '789 Pine St, Berkeley',
                            'status' => 'completed',
                            'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400'
                        ]
                    ];
                    
                    foreach ($appointments as $index => $apt): 
                    ?>
                    <div class="card card-glass appointment-card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="appointment-card-content" style="display: flex; flex-direction: column; gap: 1.25rem; padding: 1.25rem;">
                            <!-- Image -->
                            <div class="appointment-image">
                                <img src="<?php echo $apt['image']; ?>" alt="<?php echo $apt['property']; ?>">
                            </div>

                            <!-- Content -->
                            <div class="appointment-details">
                                <div class="appointment-header">
                                    <div class="appointment-title">
                                        <h3><?php echo $apt['property']; ?></h3>
                                        <div class="appointment-landlord">
                                            <i data-lucide="user"></i>
                                            <span>with <?php echo $apt['landlord']; ?></span>
                                        </div>
                                    </div>
                                    <span class="appointment-status <?php echo $apt['status']; ?>">
                                        <?php if ($apt['status'] === 'confirmed'): ?>
                                            <i data-lucide="check-circle"></i>
                                        <?php elseif ($apt['status'] === 'pending'): ?>
                                            <i data-lucide="alert-circle"></i>
                                        <?php else: ?>
                                            <i data-lucide="check-circle"></i>
                                        <?php endif; ?>
                                        <?php echo ucfirst($apt['status']); ?>
                                    </span>
                                </div>

                                <div class="appointment-info-grid">
                                    <div class="appointment-info-item">
                                        <div class="appointment-info-icon">
                                            <i data-lucide="calendar"></i>
                                        </div>
                                        <div class="appointment-info-text">
                                            <p>Date</p>
                                            <p><?php echo $apt['date']; ?></p>
                                        </div>
                                    </div>
                                    <div class="appointment-info-item">
                                        <div class="appointment-info-icon">
                                            <i data-lucide="clock"></i>
                                        </div>
                                        <div class="appointment-info-text">
                                            <p>Time</p>
                                            <p><?php echo $apt['time']; ?></p>
                                        </div>
                                    </div>
                                    <div class="appointment-info-item">
                                        <div class="appointment-info-icon">
                                            <i data-lucide="map-pin"></i>
                                        </div>
                                        <div class="appointment-info-text">
                                            <p>Location</p>
                                            <p><?php echo $apt['location']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($apt['status'] === 'confirmed'): ?>
                                <div class="appointment-actions">
                                    <button class="btn btn-glass btn-sm">Reschedule</button>
                                    <button class="btn btn-ghost btn-sm">Cancel</button>
                                </div>
                                <?php elseif ($apt['status'] === 'pending'): ?>
                                <div class="appointment-actions">
                                    <button class="btn btn-primary btn-sm">Confirm</button>
                                    <button class="btn btn-ghost btn-sm">Decline</button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <style>
                    @media (min-width: 768px) {
                        .appointment-card-content {
                            flex-direction: row !important;
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
