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
    <?php
    // Start session and load models
    session_start();
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/User.php';
    
    $listingModel = new Listing();
    $userModel = new User();
    
    // Get filters
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? 'All Status';

    // Get listings with filters
    $listingsData = $listingModel->searchAllListings([
        'search' => $search,
        'status' => $status
    ]);
    
    // Helper function for time ago
    function time_elapsed($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'just now';
        if ($diff < 3600) {
            $mins = floor($diff/60);
            return $mins . ' ' . ($mins == 1 ? 'hour' : 'hours') . ' ago';
        }
        if ($diff < 86400) {
            $hours = floor($diff/3600);
            return $hours . ' ' . ($hours == 1 ? 'hour' : 'hours') . ' ago';
        }
        $days = floor($diff/86400);
        return $days . ' ' . ($days == 1 ? 'day' : 'days') . ' ago';
    }
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 5;
    $totalListings = count($listingsData);
    $totalPages = ceil($totalListings / $limit);
    $offset = ($page - 1) * $limit;
    $listingsData = array_slice($listingsData, $offset, $limit);
    
    // Format listing data
    $listings = [];
    foreach ($listingsData as $listingData) {
        $listings[] = [
            'id' => $listingData['listing_id'],
            'image' => $listingData['primary_image'] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
            'title' => $listingData['title'],
            'landlord' => $listingData['first_name'] . ' ' . $listingData['last_name'],
            'landlordAvatar' => $listingData['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($listingData['first_name'] . ' ' . $listingData['last_name']) . '&background=3b82f6&color=fff',
            'location' => $listingData['location'],
            'price' => $listingData['price'],
            'status' => $listingData['approval_status'],
            'admin_note' => $listingData['admin_note'],
            'submittedDate' => time_elapsed($listingData['created_at']),
            'views' => 0, // listing_views table doesn't exist yet
        ];
    }
    ?>
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Listing Management</h1>
                <p class="page-subtitle">Review and approve property listings</p>
            </div>

            <!-- Search & Filters -->
            <!-- Search & Filters -->
            <form method="GET" action="listings.php" class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem; background: transparent; border: none; box-shadow: none;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" name="search" class="search-input-clean" placeholder="Search listings..." value="<?php echo htmlspecialchars($search); ?>">
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
                <div id="filterOptions" style="display: <?php echo ($status !== 'All Status') ? 'flex' : 'none'; ?>; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select name="status" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $status === 'All Status' ? 'selected' : ''; ?>>All Status</option>
                        <option <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option <?php echo $status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option <?php echo $status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
            </form>

            <!-- Listings Grid -->
            <div class="listings-grid">
                <?php
                // Listings already loaded from database above
                foreach ($listings as $index => $listing): 
                    $statusClass = '';
                    // Map database status to display status
                    $displayStatus = $listing['status'];
                    switch ($listing['status']) {
                        case 'approved': 
                            $statusClass = 'status-success'; 
                            $displayStatus = 'approved';
                            break;
                        case 'pending': 
                            $statusClass = 'status-warning';
                            $displayStatus = 'pending';
                            break;
                        case 'rejected': 
                            $statusClass = 'status-error';
                            $displayStatus = 'rejected';
                            break;
                        default: $statusClass = 'status-neutral';
                    }
                ?>
                <div class="glass-card animate-slide-up listing-card-row" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <!-- Image Section -->
                    <div class="listing-image-wrapper">
                        <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image-sm">
                        <span class="status-badge-overlay <?php echo $statusClass; ?>"><?php echo ucfirst($displayStatus); ?></span>
                    </div>

                    <!-- Main Content -->
                    <div class="listing-content-wrapper">
                        <div class="listing-header">
                            <h3 class="listing-title"><?php echo $listing['title']; ?></h3>
                            <div class="listing-price">
                                ₱<?php echo number_format($listing['price']); ?>
                                <span style="font-size: 0.8rem; font-weight: 500; color: #6b7280;">/month</span>
                            </div>
                        </div>
                        
                        <div class="listing-meta-row">
                            <span class="meta-item"><i data-lucide="map-pin"></i> <?php echo $listing['location']; ?></span>
                            <span class="meta-item"><i data-lucide="eye"></i> <?php echo $listing['views']; ?> views</span>
                            <span class="meta-item"><i data-lucide="clock"></i> <?php echo $listing['submittedDate']; ?></span>
                        </div>

                        <!-- Landlord Info (Small Details) -->
                        <div class="landlord-profile-row">
                            <img src="<?php echo $listing['landlordAvatar']; ?>" alt="Landlord" class="landlord-avatar-xs">
                            <div class="landlord-info-text">
                                <span class="landlord-name"><?php echo $listing['landlord']; ?></span>
                                <span class="landlord-role-badge">Property Owner</span>
                            </div>
                        </div>
                        
                        <?php if (!empty($listing['admin_note'])): ?>
                            <div class="admin-note-sm">
                                <i data-lucide="alert-circle" style="width: 1rem; height: 1rem; flex-shrink: 0;"></i>
                                <span>Note: <?php echo htmlspecialchars($listing['admin_note']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="listing-actions-col">
                        <?php if ($listing['status'] === 'pending'): ?>
                            <button class="icon-btn icon-btn-primary" title="Approve" onclick="handleApprove(<?php echo $listing['id']; ?>)">
                                <i data-lucide="check" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                            <button class="icon-btn icon-btn-danger" title="Reject" onclick="handleReject(<?php echo $listing['id']; ?>)">
                                <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                        <?php elseif ($listing['status'] === 'approved'): ?>
                            <button class="icon-btn icon-btn-danger" title="Unpublish" onclick="handleReject(<?php echo $listing['id']; ?>)">
                                <i data-lucide="ban" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                        <?php elseif ($listing['status'] === 'rejected'): ?>
                            <button class="icon-btn icon-btn-primary" title="Re-Approve" onclick="handleApprove(<?php echo $listing['id']; ?>)">
                                <i data-lucide="rotate-ccw" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                        <?php endif; ?>
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/view_listing.php?id=<?php echo $listing['id']; ?>" class="icon-btn icon-btn-neutral" title="View Details">
                            <i data-lucide="eye" style="width: 1.25rem; height: 1.25rem;"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                           class="btn btn-sm" 
                           style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $page ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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

    <!-- Approve Confirmation Modal -->
    <div id="approveModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Approve Listing</h3>
                <button class="close-modal" onclick="closeModal('approveModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this listing? It will become immediately visible to all room seekers.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('approveModal')">Cancel</button>
                <button id="confirmApproveBtn" class="btn btn-primary" style="background: #10b981; border-color: #10b981; color: white;">Approve Listing</button>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Reject Listing</h3>
                <button class="close-modal" onclick="closeModal('rejectModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 1rem;">Please provide a reason for rejecting this listing. This note will be sent to the landlord.</p>
                <textarea id="rejectReason" class="form-input" rows="4" placeholder="e.g., Photos are unclear, Description contains prohibited content..." style="width: 100%; resize: vertical;"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('rejectModal')">Cancel</button>
                <button id="confirmRejectBtn" class="btn btn-primary" style="background: #dc2626; border-color: #dc2626; color: white;">Reject Listing</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        const ADMIN_STATUS_ENDPOINT = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ListingController.php?action=updateStatus';
        let currentListingId = null;

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            if (modalId === 'rejectModal') {
                document.getElementById('rejectReason').value = '';
            }
            currentListingId = null;
        }

        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('show');
                currentListingId = null;
            }
        }

        function handleApprove(id) {
            currentListingId = id;
            openModal('approveModal');
        }

        function handleReject(id) {
            currentListingId = id;
            document.getElementById('rejectReason').value = ''; // Clear previous reason
            openModal('rejectModal');
        }

        // Confirm Approve
        document.getElementById('confirmApproveBtn').addEventListener('click', async function() {
            if (!currentListingId) return;
            
            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Approving...';
            lucide.createIcons();
            
            await updateListingStatus(currentListingId, 'approved');
        });

        // Confirm Reject
        document.getElementById('confirmRejectBtn').addEventListener('click', async function() {
            if (!currentListingId) return;

            const reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                alert('Please provide a rejection reason.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Rejecting...';
            lucide.createIcons();
            
            await updateListingStatus(currentListingId, 'rejected', reason);
        });

        async function updateListingStatus(id, status, note = '') {
            try {
                const params = new URLSearchParams();
                params.append('listing_id', id);
                params.append('status', status);
                if (note) {
                    params.append('note', note);
                }

                const response = await fetch(ADMIN_STATUS_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params
                });

                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Unable to update listing.');
                    // Reset buttons if failed
                    if (status === 'approved') {
                        const btn = document.getElementById('confirmApproveBtn');
                        btn.disabled = false;
                        btn.innerHTML = 'Approve Listing';
                    } else {
                        const btn = document.getElementById('confirmRejectBtn');
                        btn.disabled = false;
                        btn.innerHTML = 'Reject Listing';
                    }
                }
            } catch (error) {
                console.error(error);
                alert('Something went wrong while updating the listing.');
            }
        }
    </script>
</body>
</html>
