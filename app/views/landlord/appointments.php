<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewing Appointments - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
</head>
<body>
<?php
// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Start session and load models
session_start();
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Listing.php';

// Check if user is logged in as landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$landlordId = $_SESSION['user_id'];

// Update last viewed timestamp for appointments badge
$_SESSION['last_viewed_appointments_landlord'] = time();

$appointmentModel = new Appointment();
$userModel = new User();
$listingModel = new Listing();

// Get filters from URL
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? 'All Status';
$dateFilter = $_GET['date'] ?? 'All Dates';

// Get all appointments for this landlord's listings
$appointments = $appointmentModel->getLandlordAppointments($landlordId);

// Apply filters
$filteredAppointments = [];
foreach ($appointments as $appt) {
    // Get tenant and listing details for filtering
    $tenant = $userModel->getById($appt['seeker_id']);
    $listing = $listingModel->getWithImages($appt['listing_id']);
    
    $tenantName = $tenant ? ($tenant['first_name'] . ' ' . $tenant['last_name']) : 'Deleted User';
    $propertyName = $listing ? $listing['title'] : 'Deleted Listing';
    
    // Apply search filter
    if ($search !== '') {
        $searchLower = strtolower($search);
        $matchesSearch = (
            stripos($tenantName, $search) !== false ||
            stripos($propertyName, $search) !== false ||
            stripos($appt['appointment_date'], $search) !== false
        );
        if (!$matchesSearch) continue;
    }
    
    // Apply status filter
    if ($statusFilter !== 'All Status') {
        if (strtolower($appt['status']) !== strtolower($statusFilter)) {
            continue;
        }
    }
    
    // Apply date filter
    if ($dateFilter !== 'All Dates') {
        $apptDate = new DateTime($appt['appointment_date']);
        $now = new DateTime();
        $matchesDate = false;
        
        switch ($dateFilter) {
            case 'Today':
                $matchesDate = $apptDate->format('Y-m-d') === $now->format('Y-m-d');
                break;
            case 'This Week':
                $weekStart = (clone $now)->modify('monday this week')->setTime(0, 0);
                $weekEnd = (clone $weekStart)->modify('+6 days')->setTime(23, 59, 59);
                $matchesDate = $apptDate >= $weekStart && $apptDate <= $weekEnd;
                break;
            case 'This Month':
                $matchesDate = $apptDate->format('Y-m') === $now->format('Y-m');
                break;
            case 'Upcoming':
                $matchesDate = $apptDate >= $now;
                break;
            case 'Past':
                $matchesDate = $apptDate < $now;
                break;
        }
        
        if (!$matchesDate) continue;
    }
    
    $filteredAppointments[] = $appt;
}

// Use filtered appointments
$appointments = $filteredAppointments;

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$totalAppointments = count($appointments);
$totalPages = ceil($totalAppointments / $limit);
$offset = ($page - 1) * $limit;

// Slice appointments for current page
$paginatedAppointments = array_slice($appointments, $offset, $limit);

// Format appointment data
foreach ($paginatedAppointments as &$appt) {
    // Get tenant details
    $tenant = $userModel->getById($appt['seeker_id']);
    if ($tenant && is_array($tenant)) {
        $appt['tenant'] = $tenant['first_name'] . ' ' . $tenant['last_name'];
        $appt['email'] = $tenant['email'] ?? '';
        $appt['phone'] = $tenant['phone'] ?? '';
        $appt['tenantAvatar'] = $tenant['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($appt['tenant']) . '&background=3b82f6&color=fff';
    } else {
        $appt['tenant'] = 'Deleted User';
        $appt['email'] = '';
        $appt['phone'] = '';
        $appt['tenantAvatar'] = 'https://ui-avatars.com/api/?name=DeletedUser&background=ef4444&color=fff';
    }
    
    // Get listing details
    $listing = $listingModel->getWithImages($appt['listing_id']);
    if ($listing && is_array($listing)) {
        $appt['property'] = $listing['title'];
        $appt['location'] = $listing['location'];
        
        // Get first image
        if (!empty($listing['images']) && is_array($listing['images'])) {
            // listing_images table has image_url column
            $firstImage = $listing['images'][0];
            $appt['propertyImage'] = $firstImage['image_url'] ?? 'https://via.placeholder.com/400x300?text=No+Image';
        } else {
            $appt['propertyImage'] = 'https://via.placeholder.com/400x300?text=No+Image';
        }
    } else {
        $appt['property'] = 'Deleted Listing';
        $appt['location'] = 'N/A';
        $appt['propertyImage'] = 'https://via.placeholder.com/400x300?text=Deleted';
    }
    
    // Format date
    $apptDate = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);
    $now = new DateTime();
    $tomorrow = new DateTime('tomorrow');
    
    if ($apptDate->format('Y-m-d') === $now->format('Y-m-d')) {
        $appt['date'] = 'Today';
    } elseif ($apptDate->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
        $appt['date'] = 'Tomorrow';
    } else {
        $appt['date'] = $apptDate->format('M j, Y');
    }
    
    // Format time
    $appt['time'] = $apptDate->format('g:i A');
    
    // Format requested date
    $created = new DateTime($appt['created_at']);
    $diff = $now->diff($created);
    if ($diff->days > 0) {
        $appt['requestedDate'] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        $appt['requestedDate'] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } else {
        $appt['requestedDate'] = max(1, $diff->i) . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    }
}
unset($appt); // Break reference

// Replace original array with paginated one for display
$appointments = $paginatedAppointments;
?>
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container-wide">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">Viewing Appointments</h1>
                    <p class="page-subtitle">Manage property viewing requests from potential tenants</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <form method="GET" action="appointments.php" class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem; background: transparent; border: none; box-shadow: none;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" name="search" class="search-input-clean" placeholder="Search by property or tenant..." value="<?php echo htmlspecialchars($search); ?>">
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
                <div id="filterOptions" style="display: <?php echo ($statusFilter !== 'All Status' || $dateFilter !== 'All Dates') ? 'flex' : 'none'; ?>; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select name="status" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $statusFilter === 'All Status' ? 'selected' : ''; ?>>All Status</option>
                        <option <?php echo $statusFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option <?php echo $statusFilter === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option <?php echo $statusFilter === 'Declined' ? 'selected' : ''; ?>>Declined</option>
                        <option <?php echo $statusFilter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option <?php echo $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <select name="date" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $dateFilter === 'All Dates' ? 'selected' : ''; ?>>All Dates</option>
                        <option <?php echo $dateFilter === 'Today' ? 'selected' : ''; ?>>Today</option>
                        <option <?php echo $dateFilter === 'This Week' ? 'selected' : ''; ?>>This Week</option>
                        <option <?php echo $dateFilter === 'This Month' ? 'selected' : ''; ?>>This Month</option>
                        <option <?php echo $dateFilter === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option <?php echo $dateFilter === 'Past' ? 'selected' : ''; ?>>Past</option>
                    </select>
                </div>
            </form>

            <!-- Appointments List -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (empty($appointments)): ?>
                    <div class="glass-card" style="padding: 3rem; text-align: center;">
                        <i data-lucide="calendar-x" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.3); margin: 0 auto 1rem;"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">No Appointments Yet</h3>
                        <p style="color: rgba(0,0,0,0.6);">You don't have any viewing requests at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($appointments as $index => $appointment): 
                        $statusColor = '';
                        $statusClass = '';
                        switch($appointment['status']) {
                            case 'pending': 
                                $statusColor = 'background: #fef3c7; color: #92400e;'; 
                                $statusClass = 'status-warning';
                                break;
                            case 'confirmed': 
                                $statusColor = 'background: #dcfce7; color: #166534;'; 
                                $statusClass = 'status-success';
                                break;
                            case 'declined': 
                                $statusColor = 'background: #fee2e2; color: #991b1b;'; 
                                $statusClass = 'status-error';
                                break;
                            case 'completed': 
                                $statusColor = 'background: #f3f4f6; color: #374151;'; 
                                $statusClass = 'status-neutral';
                                break;
                            case 'cancelled': 
                                $statusColor = 'background: #f3f4f6; color: #374151; text-decoration: line-through;'; 
                                $statusClass = 'status-neutral';
                                break;
                        }
                    ?>
                    <div class="appointment-card-row animate-slide-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <!-- Image Section -->
                        <div class="appointment-image-wrapper">
                            <img src="<?php echo $appointment['propertyImage']; ?>" alt="<?php echo $appointment['property']; ?>" class="appointment-image-sm">
                            <span class="status-badge-overlay" style="<?php echo $statusColor; ?>"><?php echo ucfirst($appointment['status']); ?></span>
                        </div>

                        <!-- Main Content -->
                        <div class="appointment-content-wrapper">
                            <div class="appointment-header">
                                <h3 class="appointment-title"><?php echo $appointment['property']; ?></h3>
                            </div>
                            
                            <div class="appointment-meta-row">
                                <span class="meta-item"><i data-lucide="map-pin"></i> <?php echo $appointment['location']; ?></span>
                                <span class="meta-item"><i data-lucide="calendar"></i> <?php echo $appointment['date']; ?></span>
                                <span class="meta-item"><i data-lucide="clock"></i> <?php echo $appointment['time']; ?></span>
                                <span class="meta-item"><i data-lucide="history"></i> Requested <?php echo $appointment['requestedDate']; ?></span>
                            </div>

                            <!-- Tenant Profile -->
                            <div class="tenant-profile-row">
                                <img src="<?php echo $appointment['tenantAvatar']; ?>" alt="<?php echo $appointment['tenant']; ?>" class="tenant-avatar-xs">
                                <div class="tenant-info-text">
                                    <span class="tenant-name"><?php echo $appointment['tenant']; ?></span>
                                    <span class="tenant-role-badge">Potential Tenant</span>
                                </div>
                                <div style="margin-left: auto; display: flex; gap: 0.5rem;">
                                    <a href="mailto:<?php echo $appointment['email']; ?>" class="icon-btn icon-btn-neutral" style="width: 1.5rem; height: 1.5rem;" title="Email Tenant">
                                        <i data-lucide="mail" style="width: 0.8rem; height: 0.8rem;"></i>
                                    </a>
                                    <a href="tel:<?php echo $appointment['phone']; ?>" class="icon-btn icon-btn-neutral" style="width: 1.5rem; height: 1.5rem;" title="Call Tenant">
                                        <i data-lucide="phone" style="width: 0.8rem; height: 0.8rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="appointment-actions-col">
                            <?php if ($appointment['status'] === 'pending'): ?>
                                <button class="icon-btn icon-btn-primary" title="Approve" onclick="openApproveModal(<?php echo $appointment['appointment_id']; ?>)">
                                    <i data-lucide="check" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                                <button class="icon-btn icon-btn-danger" title="Decline" onclick="openDeclineModal(<?php echo $appointment['appointment_id']; ?>)">
                                    <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                            <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                <button class="icon-btn icon-btn-success" title="Mark as Visited" onclick="openCompleteModal(<?php echo $appointment['appointment_id']; ?>)">
                                    <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                                <button class="icon-btn icon-btn-neutral" title="Reschedule" onclick="openRescheduleModal(<?php echo $appointment['appointment_id']; ?>)">
                                    <i data-lucide="calendar-clock" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                                <button class="icon-btn icon-btn-danger" title="Cancel" onclick="openDeclineModal(<?php echo $appointment['appointment_id']; ?>)">
                                    <i data-lucide="ban" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>" 
                           class="btn btn-sm" 
                           style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $page ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
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
                <h3 class="modal-title">Approve Appointment</h3>
                <button class="close-modal" onclick="closeModal('approveModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this viewing request? The tenant will be notified immediately.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('approveModal')">Cancel</button>
                <button id="confirmApproveBtn" class="btn btn-primary">Yes, Approve</button>
            </div>
        </div>
    </div>

    <!-- Decline Confirmation Modal -->
    <div id="declineModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Decline Appointment</h3>
                <button class="close-modal" onclick="closeModal('declineModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to decline this viewing request? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('declineModal')">Cancel</button>
                <button id="confirmDeclineBtn" class="btn btn-danger" style="background: #ef4444; color: white;">Yes, Decline</button>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Reschedule Appointment</h3>
                <button class="close-modal" onclick="closeModal('rescheduleModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 1rem; color: #6b7280;">Propose a new date and time for this viewing.</p>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">New Date</label>
                        <input type="date" id="rescheduleDate" class="form-input" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">New Time</label>
                        <input type="time" id="rescheduleTime" class="form-input" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('rescheduleModal')">Cancel</button>
                <button id="confirmRescheduleBtn" class="btn btn-primary">Confirm Reschedule</button>
            </div>
        </div>
    </div>

    <!-- Complete Appointment Modal -->
    <div id="completeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Mark as Visited</h3>
                <button class="close-modal" onclick="closeModal('completeModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Confirm that the tenant has completed their viewing appointment?</p>
                <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">This will mark the appointment as completed.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('completeModal')">Cancel</button>
                <button id="confirmCompleteBtn" class="btn btn-primary" style="background: #10b981;">Yes, Mark as Visited</button>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    <script>
        let currentAppointmentId = null;

        function openApproveModal(id) {
            currentAppointmentId = id;
            document.getElementById('approveModal').classList.add('show');
        }

        function openDeclineModal(id) {
            currentAppointmentId = id;
            document.getElementById('declineModal').classList.add('show');
        }

        function openRescheduleModal(id) {
            currentAppointmentId = id;
            document.getElementById('rescheduleModal').classList.add('show');
        }

        function openCompleteModal(id) {
            currentAppointmentId = id;
            document.getElementById('completeModal').classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            currentAppointmentId = null;
        }

        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('show');
                currentAppointmentId = null;
            }
        }

        async function updateStatus(id, status) {
            try {
                const formData = new FormData();
                formData.append('appointment_id', id);
                formData.append('status', status);
                
                const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AppointmentController.php?action=update_status', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }

        // Handle Approve
        document.getElementById('confirmApproveBtn').addEventListener('click', function() {
            if (!currentAppointmentId) return;
            
            this.disabled = true;
            this.innerHTML = 'Approving...';
            
            updateStatus(currentAppointmentId, 'confirmed');
        });

        // Handle Decline
        document.getElementById('confirmDeclineBtn').addEventListener('click', function() {
            if (!currentAppointmentId) return;
            
            this.disabled = true;
            this.innerHTML = 'Declining...';
            
            updateStatus(currentAppointmentId, 'declined');
        });

        // Handle Reschedule
        document.getElementById('confirmRescheduleBtn').addEventListener('click', async function() {
            if (!currentAppointmentId) return;
            
            const date = document.getElementById('rescheduleDate').value;
            const time = document.getElementById('rescheduleTime').value;

            if (!date || !time) {
                alert('Please select both date and time.');
                return;
            }

            this.disabled = true;
            this.innerHTML = 'Rescheduling...';

            try {
                const formData = new FormData();
                formData.append('appointment_id', currentAppointmentId);
                formData.append('date', date);
                formData.append('time', time);
                
                const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AppointmentController.php?action=reschedule', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to reschedule');
                    this.disabled = false;
                    this.innerHTML = 'Confirm Reschedule';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
                this.disabled = false;
                this.innerHTML = 'Confirm Reschedule';
            }
        });

        // Handle Complete
        document.getElementById('confirmCompleteBtn').addEventListener('click', function() {
            if (!currentAppointmentId) return;
            
            this.disabled = true;
            this.innerHTML = 'Marking as Visited...';
            
            updateStatus(currentAppointmentId, 'completed');
        });
    </script>
</body>
</html>
