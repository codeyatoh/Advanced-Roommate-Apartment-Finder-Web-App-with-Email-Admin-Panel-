<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-card.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/room-details.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/match-modal.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php 
        session_start();
        require_once __DIR__ . '/../../models/Listing.php';
        require_once __DIR__ . '/../../models/SavedListing.php';
        
        // Check if user is logged in as seeker
        // if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
        //     header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
        //     exit;
        // }
        
        // Fallback for development/demo mode to match dashboard behavior
        $userId = $_SESSION['user_id'] ?? 1;

        $listingId = $_GET['id'] ?? 0;
        $listingModel = new Listing();
        $savedListingModel = new SavedListing();
        
        $listing = $listingModel->getWithImages($listingId);

        if (!$listing) {
            echo "<div style='padding: 2rem; text-align: center;'>Listing not found.</div>";
            exit;
        }

        // Increment view count
        $listingModel->incrementViews($listingId);

        $isSaved = $savedListingModel->isSaved($userId, $listingId);

        include __DIR__ . '/../includes/navbar.php'; 
        ?>
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <!-- Back Button -->
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php" class="btn btn-ghost btn-sm" style="margin-bottom: 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <i data-lucide="arrow-left" class="btn-icon"></i>
                    Back to Dashboard
                </a>

                <!-- Image Gallery -->
                <div style="margin-bottom: 3rem; animation: slideUp 0.3s ease-out;">
                    <div class="room-gallery-main">
                        <?php 
                        $primaryImage = !empty($listing['images']) ? $listing['images'][0]['image_url'] : 'https://via.placeholder.com/1200x800?text=No+Image';
                        ?>
                        <img src="<?php echo $primaryImage; ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                    </div>
                    <?php if (!empty($listing['images']) && count($listing['images']) > 1): ?>
                    <div class="room-gallery-thumbnails">
                        <?php foreach ($listing['images'] as $index => $img): ?>
                        <div class="room-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $img['image_url']; ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    <!-- Main Content -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Title & Location -->
                        <div>
                            <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin: 0 0 0.75rem 0;"><?php echo htmlspecialchars($listing['title']); ?></h1>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0,0,0,0.6); margin-bottom: 1rem;">
                                <i data-lucide="map-pin" style="width: 1.25rem; height: 1.25rem;"></i>
                                <span style="font-size: 1.125rem;"><?php echo htmlspecialchars($listing['location']); ?></span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <div class="room-stats-grid">
                                <div class="room-stat-item">
                                    <div class="room-stat-icon"><i data-lucide="bed"></i></div>
                                    <p class="room-stat-label">Bedrooms</p>
                                    <p class="room-stat-value"><?php echo $listing['bedrooms'] ?? 0; ?></p>
                                </div>
                                <div class="room-stat-item">
                                    <div class="room-stat-icon"><i data-lucide="bath"></i></div>
                                    <p class="room-stat-label">Bathrooms</p>
                                    <p class="room-stat-value"><?php echo $listing['bathrooms'] ?? 0; ?></p>
                                </div>
                                <div class="room-stat-item">
                                    <div class="room-stat-icon"><i data-lucide="users"></i></div>
                                    <p class="room-stat-label">Roommates</p>
                                    <p class="room-stat-value"><?php echo $listing['current_roommates'] ?? 0; ?></p>
                                </div>
                                <div class="room-stat-item">
                                    <div class="room-stat-icon"><i data-lucide="calendar"></i></div>
                                    <p class="room-stat-label">Available</p>
                                    <p class="room-stat-value"><?php echo $listing['available_from'] ? date('M d', strtotime($listing['available_from'])) : 'Now'; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">About this room</h2>
                            <div style="display: flex; flex-direction: column; gap: 1rem; color: rgba(0,0,0,0.7); line-height: 1.6;">
                                <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <?php if (!empty($listing['amenities'])): ?>
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">Amenities & Features</h2>
                            <div class="amenities-grid">
                                <?php 
                                $amenityMap = [
                                    'wifi' => ['label' => 'WiFi', 'icon' => 'wifi'],
                                    'parking' => ['label' => 'Parking', 'icon' => 'car'],
                                    'kitchen' => ['label' => 'Kitchen', 'icon' => 'coffee'],
                                    'gym' => ['label' => 'Gym', 'icon' => 'dumbbell'],
                                    'air_conditioning' => ['label' => 'Air Conditioning', 'icon' => 'wind'],
                                    'heating' => ['label' => 'Heating', 'icon' => 'flame'],
                                    'washer_dryer' => ['label' => 'Washer/Dryer', 'icon' => 'shirt'],
                                    'dishwasher' => ['label' => 'Dishwasher', 'icon' => 'utensils'],
                                    'elevator' => ['label' => 'Elevator', 'icon' => 'arrow-up-circle'],
                                    'balcony' => ['label' => 'Balcony/Patio', 'icon' => 'sun'],
                                    'pool' => ['label' => 'Pool', 'icon' => 'waves'],
                                    'security' => ['label' => 'Security System', 'icon' => 'shield-check'],
                                    'tv' => ['label' => 'TV', 'icon' => 'tv'],
                                    'essentials' => ['label' => 'Essentials', 'icon' => 'package']
                                ];

                                foreach ($listing['amenities'] as $amenityValue): 
                                    // Fallback if the value isn't in our map
                                    $item = $amenityMap[$amenityValue] ?? ['label' => ucwords(str_replace('_', ' ', $amenityValue)), 'icon' => 'check'];
                                ?>
                                <div class="amenity-item">
                                    <div class="amenity-icon"><i data-lucide="<?php echo $item['icon']; ?>"></i></div>
                                    <span class="amenity-label"><?php echo htmlspecialchars($item['label']); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- House Rules -->
                        <?php if (!empty($listing['house_rules_data'])): ?>
                        <div class="card card-glass" style="padding: 1.5rem;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0 0 1rem 0;">House Rules</h2>
                            <div class="house-rules-list">
                                <?php 
                                $houseRuleIcons = [
                                    'smoking_allowed' => 'cigarette',
                                    'pets_allowed' => 'paw-print',
                                    'no_parties' => 'music',
                                    'no_guests' => 'users',
                                    'clean_up' => 'trash-2',
                                    'no_shoes' => 'footprints',
                                    'recycling_required' => 'recycle',
                                    'keep_common_areas_clean' => 'sparkles',
                                    'turn_off_lights' => 'lightbulb'
                                ];
                                
                                foreach ($listing['house_rules_data'] as $key => $value): 
                                    if ($key === 'pets_details' || $key === 'quiet_hours_start' || $key === 'quiet_hours_end') continue;
                                    // Skip if value is false/0 or string "0"
                                    if (!$value || $value === '0') continue;

                                    $label = ucwords(str_replace('_', ' ', $key));
                                    $icon = $houseRuleIcons[$key] ?? 'check-circle';
                                ?>
                                <div class="house-rule-item">
                                    <div class="house-rule-icon allowed">
                                        <i data-lucide="<?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="house-rule-content">
                                        <span class="house-rule-text"><?php echo $label; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (!empty($listing['house_rules_data']['pets_details'])): ?>
                                <div class="house-rule-item">
                                    <div class="house-rule-icon allowed"><i data-lucide="paw-print"></i></div>
                                    <div class="house-rule-content">
                                        <span class="house-rule-text">Pets: <?php echo htmlspecialchars($listing['house_rules_data']['pets_details']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($listing['house_rules_data']['quiet_hours_start']) && !empty($listing['house_rules_data']['quiet_hours_end'])): ?>
                                <div class="house-rule-item">
                                    <div class="house-rule-icon allowed"><i data-lucide="moon"></i></div>
                                    <div class="house-rule-content">
                                        <span class="house-rule-text">Quiet Hours: <?php echo date('g:i A', strtotime($listing['house_rules_data']['quiet_hours_start'])); ?> - <?php echo date('g:i A', strtotime($listing['house_rules_data']['quiet_hours_end'])); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <div class="card card-glass-strong booking-card" style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <div class="booking-price">
                                    <span class="booking-price-amount">₱<?php echo number_format($listing['price']); ?></span>
                                    <span class="booking-price-period">/month</span>
                                </div>
                                <p class="booking-price-note">
                                    <?php echo $listing['utilities_included'] ? 'Utilities included' : 'Utilities not included'; ?> 
                                    • Security deposit: ₱<?php echo number_format($listing['security_deposit']); ?>
                                </p>
                            </div>

                            <?php 
                            $bedsAvailable = $listing['bedrooms'] - $listing['current_roommates'];
                            $isFull = $bedsAvailable <= 0;
                            ?>

                            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <?php if ($isFull): ?>
                                    <!-- FULL STATE -->
                                    <div class="btn btn-lg" style="width: 100%; background: #fee2e2; color: #ef4444; border: 2px solid #ef4444; cursor: not-allowed; display: flex; justify-content: center; align-items: center; gap: 0.5rem; pointer-events: none;">
                                        <i data-lucide="ban" class="btn-icon"></i>
                                        ROOM FULL - No Beds Available
                                    </div>
                                <?php else: ?>
                                    <!-- AVAILABLE STATE -->
                                    <a href="messages.php?user_id=<?php echo $listing['landlord_id']; ?>&listing_id=<?php echo $listingId; ?>" class="btn btn-primary btn-lg" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 0.5rem; text-decoration: none;">
                                        <i data-lucide="message-circle" class="btn-icon"></i>
                                        Message Landlord
                                    </a>
                                    
                                    <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/rent_request.php?listing_id=<?php echo $listingId; ?>" class="btn btn-primary btn-lg" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 0.5rem; text-decoration: none; background: #10b981; border-color: #10b981;">
                                        <i data-lucide="credit-card" class="btn-icon"></i>
                                        Rent Now (<?php echo $bedsAvailable; ?> bed<?php echo $bedsAvailable != 1 ? 's' : ''; ?> available)
                                    </a>
                                <?php endif; ?>

                                <?php 
                                require_once __DIR__ . '/../../models/Appointment.php';
                                $appointmentModel = new Appointment();
                                $hasPendingRequest = $appointmentModel->hasPendingRequest($userId, $listingId);
                                ?>
                                <button class="btn btn-glass btn-lg" 
                                        style="width: 100%; border-color: var(--primary); color: var(--primary);"
                                        <?php echo ($hasPendingRequest || $isFull) ? 'disabled' : 'onclick="openModal()"'; ?>>
                                    <i data-lucide="calendar" class="btn-icon"></i>
                                    <?php echo $hasPendingRequest ? 'Already Requested' : ($isFull ? 'Room Full' : 'Request Viewing'); ?>
                                </button>
                            </div>
                            
                            <div style="text-align: center;">
                                <button id="saveBtn" class="btn btn-ghost btn-sm <?php echo $isSaved ? 'active' : ''; ?>" 
                                        data-listing-id="<?php echo $listingId; ?>"
                                        style="color: <?php echo $isSaved ? '#ef4444' : 'rgba(0,0,0,0.5)'; ?>;">
                                    <i data-lucide="heart" style="width: 1rem; height: 1rem; margin-right: 0.5rem;" 
                                       <?php echo $isSaved ? 'fill="#ef4444" stroke="#ef4444"' : ''; ?>></i>
                                    <span id="saveBtnText"><?php echo $isSaved ? 'Saved to Favorites' : 'Save to Favorites'; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"][style*="gap: 2rem"] {
                            grid-template-columns: 2fr 1fr !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>

    <!-- Request Viewing Modal -->
    <div id="viewingModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Request Viewing</h3>
                <button onclick="closeModal()" class="modal-close">
                    <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <p class="modal-description">
                    Schedule a time to visit this property. The landlord will receive your request and confirm the appointment.
                </p>
                
                <form id="viewingForm" style="display: flex; flex-direction: column; gap: 1rem;">
                    <input type="hidden" name="listing_id" value="<?php echo $listingId; ?>">
                    
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Preferred Date</label>
                        <input type="date" name="date" required class="form-input" min="<?php echo date('Y-m-d'); ?>" style="background: #f9fafb; border-color: #e5e7eb;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Preferred Time</label>
                        <input type="time" name="time" required class="form-input" style="background: #f9fafb; border-color: #e5e7eb;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Message (Optional)</label>
                        <textarea name="message" class="form-input" rows="3" placeholder="Hi, I'm interested in viewing this room..." style="background: #f9fafb; border-color: #e5e7eb; resize: vertical;"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button onclick="closeModal()" class="btn btn-ghost" style="color: #6b7280;">Cancel</button>
                <button type="submit" form="viewingForm" class="btn btn-primary" style="background: #3b82f6; border-color: #3b82f6;">
                    Send Request
                </button>
            </div>
            
            <!-- Rent Now Button (Added) -->

        </div>
    </div>

    <style>
        /* Admin Modal Styles Replicated */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
        }

        .modal-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f3f4f6;
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
    </style>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/js/toast-helper.js"></script>
    <script>
        lucide.createIcons();

        // Image Gallery Logic
        document.addEventListener('DOMContentLoaded', () => {
            const mainImage = document.querySelector('.room-gallery-main img');
            const thumbnails = document.querySelectorAll('.room-thumbnail');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Update main image src
                    const newSrc = this.querySelector('img').src;
                    mainImage.src = newSrc;

                    // Update active state
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Modal Logic
            const modal = document.getElementById('viewingModal');
            const requestBtn = document.querySelector('button[onclick="openModal()"]') || document.querySelector('.btn-glass[style*="color: var(--primary)"]');
            
            // Attach click handler to the Request Viewing button
            if (requestBtn) {
                requestBtn.onclick = function(e) {
                    e.preventDefault();
                    modal.classList.add('show');
                };
            }

            window.closeModal = function() {
                modal.classList.remove('show');
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }

            // Form Submission
            const viewingForm = document.getElementById('viewingForm');
            if (viewingForm) {
                viewingForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Find the submit button inside the footer
                    const submitBtn = document.querySelector('button[type="submit"][form="viewingForm"]');
                    const originalText = submitBtn.innerText;
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Sending...';

                    try {
                        const formData = new FormData(this);
                        
                        const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AppointmentController.php?action=book', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            showToast(result.message, 'success');
                            closeModal();
                            this.reset();
                            
                            // Update the main "Request Viewing" button to "Already Requested"
                            const requestBtn = document.querySelector('button[onclick="openModal()"]') || document.querySelector('.btn-glass[style*="color: var(--primary)"]');
                            if (requestBtn) {
                                requestBtn.innerText = 'Already Requested';
                                requestBtn.disabled = true;
                                requestBtn.removeAttribute('onclick');
                                // Re-add icon if needed, though innerText replaces content. 
                                // Let's reconstruct the innerHTML to keep the icon
                                requestBtn.innerHTML = '<i data-lucide="calendar" class="btn-icon"></i> Already Requested';
                                lucide.createIcons();
                            }
                        } else {
                            showToast(result.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('An error occurred. Please try again.', 'error');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                });
            }

            // Save to Favorites Logic
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const listingId = this.dataset.listingId;
                    if (!listingId) return;

                    const icon = this.querySelector('svg') || this.querySelector('i');
                    const textSpan = document.getElementById('saveBtnText');
                    
                    // Optimistic UI update
                    const isActive = this.classList.contains('active');
                    
                    if (isActive) {
                        this.classList.remove('active');
                        this.style.color = 'rgba(0,0,0,0.5)';
                        if (textSpan) textSpan.textContent = 'Save to Favorites';
                        if (icon) {
                            icon.setAttribute('fill', 'none');
                            icon.setAttribute('stroke', 'currentColor');
                            icon.style.fill = 'none';
                            icon.style.stroke = 'currentColor';
                        }
                    } else {
                        this.classList.add('active');
                        this.style.color = '#ef4444';
                        if (textSpan) textSpan.textContent = 'Saved to Favorites';
                        if (icon) {
                            icon.setAttribute('fill', '#ef4444');
                            icon.setAttribute('stroke', '#ef4444');
                            icon.style.fill = '#ef4444';
                            icon.style.stroke = '#ef4444';
                        }
                    }

                    try {
                        const formData = new FormData();
                        formData.append('listing_id', listingId);

                        const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ListingController.php?action=toggle_save', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Ensure state matches server response
                            if (result.action === 'added') {
                                this.classList.add('active');
                                this.style.color = '#ef4444';
                                if (textSpan) textSpan.textContent = 'Saved to Favorites';
                                if (icon) {
                                    icon.setAttribute('fill', '#ef4444');
                                    icon.setAttribute('stroke', '#ef4444');
                                    icon.style.fill = '#ef4444';
                                    icon.style.stroke = '#ef4444';
                                }
                            } else {
                                this.classList.remove('active');
                                this.style.color = 'rgba(0,0,0,0.5)';
                                if (textSpan) textSpan.textContent = 'Save to Favorites';
                                if (icon) {
                                    icon.setAttribute('fill', 'none');
                                    icon.setAttribute('stroke', 'currentColor');
                                    icon.style.fill = 'none';
                                    icon.style.stroke = 'currentColor';
                                }
                            }
                        } else {
                            // Revert on failure
                            if (isActive) {
                                this.classList.add('active');
                                this.style.color = '#ef4444';
                                if (textSpan) textSpan.textContent = 'Saved to Favorites';
                                if (icon) {
                                    icon.setAttribute('fill', '#ef4444');
                                    icon.setAttribute('stroke', '#ef4444');
                                    icon.style.fill = '#ef4444';
                                    icon.style.stroke = '#ef4444';
                                }
                            } else {
                                this.classList.remove('active');
                                this.style.color = 'rgba(0,0,0,0.5)';
                                if (textSpan) textSpan.textContent = 'Save to Favorites';
                                if (icon) {
                                    icon.setAttribute('fill', 'none');
                                    icon.setAttribute('stroke', 'currentColor');
                                    icon.style.fill = 'none';
                                    icon.style.stroke = 'currentColor';
                                }
                            }
                            console.error('Failed to toggle save:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        // Revert
                        if (isActive) {
                            this.classList.add('active');
                            this.style.color = '#ef4444';
                            if (textSpan) textSpan.textContent = 'Saved to Favorites';
                            if (icon) {
                                    icon.setAttribute('fill', '#ef4444');
                                    icon.setAttribute('stroke', '#ef4444');
                                    icon.style.fill = '#ef4444';
                                    icon.style.stroke = '#ef4444';
                            }
                        } else {
                            this.classList.remove('active');
                            this.style.color = 'rgba(0,0,0,0.5)';
                            if (textSpan) textSpan.textContent = 'Save to Favorites';
                            if (icon) {
                                icon.setAttribute('fill', 'none');
                                icon.setAttribute('stroke', 'currentColor');
                                icon.style.fill = 'none';
                                icon.style.stroke = 'currentColor';
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
