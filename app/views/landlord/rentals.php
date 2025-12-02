<?php
session_start();
require_once __DIR__ . '/../../models/Rental.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Listing.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    // In a real app, redirect to login or error
    // For now, assume landlord is logged in or use a default for testing if needed, 
    // but strictly we should redirect.
    // header('Location: /login.php');
    // exit;
}

$landlordId = $_SESSION['user_id'] ?? 2; // Fallback for dev

// Fetch Rentals
$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT r.*, l.title as listing_title, l.price, l.location,
               (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image,
               u.first_name, u.last_name, u.profile_photo,
               p.status as payment_status, p.method as payment_method, p.transaction_id, p.created_at as payment_date, p.proof_image
        FROM rentals r
        JOIN listings l ON r.listing_id = l.listing_id
        JOIN users u ON r.tenant_id = u.user_id
        LEFT JOIN payments p ON r.rental_id = p.rental_id
        WHERE r.landlord_id = :landlord_id
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':landlord_id', $landlordId);
$stmt->execute();
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark all as seen
$rentalModel = new Rental();
$rentalModel->markAllAsSeen($landlordId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - RoomFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
    <style>
        /* Copied/Adapted from admin.module.css for Listing Card Hierarchy */
        .listing-card-row {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1rem;
        }

        .listing-card-row:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        @media (min-width: 768px) {
            .listing-card-row {
                flex-direction: row;
                align-items: stretch;
                gap: 1rem;
            }
        }

        .listing-image-wrapper {
            position: relative;
            width: 100%;
            height: 10rem;
            flex-shrink: 0;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            .listing-image-wrapper {
                width: 9rem;
                height: auto;
                min-height: 6rem;
            }
        }

        .listing-image-sm {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .listing-card-row:hover .listing-image-sm {
            transform: scale(1.05);
        }

        .status-badge-overlay {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.65rem;
            font-weight: 700;
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .listing-content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.25rem;
            min-width: 0;
            padding-top: 0.125rem;
            padding-bottom: 0.125rem;
        }

        .listing-header {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .listing-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        .listing-price {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--deep-blue);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .listing-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            background: rgba(0,0,0,0.03);
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
        }

        .meta-item i {
            width: 0.8rem;
            height: 0.8rem;
            color: #9ca3af;
        }

        .landlord-profile-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        .landlord-avatar-xs {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 1px solid white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .landlord-info-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .landlord-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
        }

        .landlord-role-badge {
            font-size: 0.65rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .listing-actions-col {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
            align-items: center;
            padding-left: 0;
            border-left: none;
        }

        @media (min-width: 768px) {
            .listing-actions-col {
                flex-direction: column;
                justify-content: center;
                padding-left: 1rem;
                border-left: 1px solid rgba(0,0,0,0.06);
                min-width: 50px;
            }
        }
        
        /* Status Colors */
        .status-success { background-color: #dcfce7; color: #15803d; }
        .status-warning { background-color: #fef9c3; color: #a16207; }
        .status-error { background-color: #fee2e2; color: #b91c1c; }
        .status-info { background-color: #dbeafe; color: #1d4ed8; }
        .status-neutral { background-color: rgba(0, 0, 0, 0.1); color: rgba(0, 0, 0, 0.6); }

        /* Single Column Grid Override */
        .listings-grid {
            display: grid;
            grid-template-columns: 1fr !important; /* Force single column */
            gap: 1rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="landlord-page">
        <div class="landlord-container">
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">Rental Management</h1>
                    <p class="page-subtitle">Manage your active rentals and payment requests</p>
                </div>
            </div>

            <?php if (empty($rentals)): ?>
                <div class="glass-card animate-slide-up" style="text-align: center; padding: 3rem;">
                    <div style="width: 4rem; height: 4rem; background: rgba(0,0,0,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="home" style="width: 2rem; height: 2rem; color: #9ca3af;"></i>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No rentals yet</h3>
                    <p style="color: #6b7280;">When tenants rent your properties, they will appear here.</p>
                </div>
            <?php else: ?>
                <div class="listings-grid">
                    <?php foreach ($rentals as $index => $rental): 
                        $statusClass = '';
                        $displayStatus = $rental['status'];
                        $isAutoDeclined = false;

                        // Auto-decline logic for missing proof on manual payments
                        if ($rental['payment_method'] !== 'stripe' && empty($rental['proof_image']) && $rental['payment_status'] !== 'completed' && $rental['status'] !== 'cancelled') {
                            $isAutoDeclined = true;
                            $displayStatus = 'Declined (No Proof)';
                            $statusClass = 'status-error';
                        } elseif ($rental['status'] === 'active') {
                            $statusClass = 'status-success';
                        } elseif ($rental['status'] === 'pending') {
                            $statusClass = 'status-warning';
                        } elseif ($rental['status'] === 'cancelled') {
                            $statusClass = 'status-error';
                        } else {
                            $statusClass = 'status-neutral';
                        }

                        // Determine payment status display
                        $paymentStatusHtml = '';
                        if ($rental['payment_status'] === 'completed') {
                            $paymentStatusHtml = '<span class="meta-item" style="color: #059669; background: #d1fae5;"><i data-lucide="check-circle" style="color: #059669;"></i> Paid via ' . ucfirst($rental['payment_method']) . '</span>';
                        } elseif ($rental['payment_status'] === 'failed') {
                            $paymentStatusHtml = '<span class="meta-item" style="color: #b91c1c; background: #fee2e2;"><i data-lucide="x-circle" style="color: #b91c1c;"></i> Payment Declined</span>';
                        } else {
                            $paymentStatusHtml = '<span class="meta-item" style="color: #d97706; background: #fef3c7;"><i data-lucide="clock" style="color: #d97706;"></i> Payment Pending</span>';
                        }
                    ?>
                    <div class="listing-card-row animate-slide-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <!-- Image Section -->
                        <div class="listing-image-wrapper">
                            <img src="<?php echo $rental['primary_image'] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800'; ?>" alt="<?php echo htmlspecialchars($rental['listing_title']); ?>" class="listing-image-sm">
                            <span class="status-badge-overlay <?php echo $statusClass; ?>"><?php echo ucfirst($displayStatus); ?></span>
                        </div>

                        <!-- Main Content -->
                        <div class="listing-content-wrapper">
                            <div class="listing-header">
                                <h3 class="listing-title"><?php echo htmlspecialchars($rental['listing_title']); ?></h3>
                                <div class="listing-price">
                                    ₱<?php echo number_format($rental['rent_amount']); ?>
                                    <span style="font-size: 0.8rem; font-weight: 500; color: #6b7280;">/month</span>
                                </div>
                            </div>
                            
                            <div class="listing-meta-row">
                                <span class="meta-item"><i data-lucide="map-pin"></i> <?php echo htmlspecialchars($rental['location']); ?></span>
                                <span class="meta-item"><i data-lucide="calendar"></i> Move-in: <?php echo date('M j, Y', strtotime($rental['start_date'])); ?></span>
                                <?php echo $paymentStatusHtml; ?>
                            </div>

                            <!-- Tenant Info -->
                            <div class="landlord-profile-row">
                                <img src="<?php echo $rental['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($rental['first_name'] . ' ' . $rental['last_name']); ?>" alt="Tenant" class="landlord-avatar-xs">
                                <div class="landlord-info-text">
                                    <span class="landlord-name"><?php echo htmlspecialchars($rental['first_name'] . ' ' . $rental['last_name']); ?></span>
                                    <span class="landlord-role-badge">Tenant</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="listing-actions-col">
                            <?php if (!empty($rental['proof_image'])): ?>
                                <button class="icon-btn icon-btn-neutral" title="View Proof" onclick="handleViewProof('<?php echo htmlspecialchars($rental['proof_image']); ?>')">
                                    <i data-lucide="image" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                            <?php endif; ?>

                            <?php if ($rental['payment_method'] !== 'stripe' && $rental['payment_status'] !== 'completed' && $rental['payment_status'] !== 'failed' && $rental['status'] !== 'cancelled' && !$isAutoDeclined): ?>
                                <button class="icon-btn icon-btn-primary" title="Confirm Payment" onclick="handleConfirmPayment(<?php echo $rental['rental_id']; ?>)">
                                    <i data-lucide="check" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                                <button class="icon-btn icon-btn-danger" title="Decline Payment" onclick="handleDeclinePayment(<?php echo $rental['rental_id']; ?>)">
                                    <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                            <?php elseif ($isAutoDeclined): ?>
                                <button class="icon-btn icon-btn-neutral" title="Auto-Declined (No Proof)" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    <i data-lucide="x-circle" style="width: 1.25rem; height: 1.25rem; color: #ef4444;"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Proof Modal -->
    <div id="viewProofModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 800px; width: 90%;">
            <div class="modal-header">
                <h3 class="modal-title">Payment Proof</h3>
                <button class="close-modal" onclick="closeModal('viewProofModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body" style="padding: 0; display: flex; justify-content: center; background: #f3f4f6;">
                <img id="proofImage" src="" alt="Payment Proof" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>

    <!-- Confirm Payment Modal -->
    <div id="confirmPaymentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Payment</h3>
                <button class="close-modal" onclick="closeModal('confirmPaymentModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to confirm this payment? This will mark the rental as active and notify the tenant.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('confirmPaymentModal')">Cancel</button>
                <button id="confirmBtn" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>

    <!-- Decline Payment Modal -->
    <div id="declinePaymentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Decline Payment</h3>
                <button class="close-modal" onclick="closeModal('declinePaymentModal')"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to decline this payment? This will mark the payment as failed and notify the tenant.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal('declinePaymentModal')">Cancel</button>
                <button id="declineBtn" class="btn btn-primary" style="background-color: #dc2626;">Decline Payment</button>
            </div>
        </div>
    </div>

    <?php
    $emailConfig = require __DIR__ . '/../../../config/emailjs.php';
    $receiptData = $_SESSION['email_receipt'] ?? null;
    if ($receiptData) {
        unset($_SESSION['email_receipt']); // Clear after use
    }
    ?>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        
        // EmailJS Init
        (function() {
            emailjs.init("<?php echo $emailConfig['public_key']; ?>");
        })();

        <?php if ($receiptData): ?>
        // Send Receipt Email
        document.addEventListener('DOMContentLoaded', function() {
            const templateParams = {
                to_email: "<?php echo $receiptData['to_email']; ?>",
                to_name: "<?php echo $receiptData['to_name']; ?>",
                room_title: "<?php echo $receiptData['room_title']; ?>",
                amount: "<?php echo number_format($receiptData['amount'], 2); ?>",
                date: "<?php echo $receiptData['date']; ?>",
                transaction_id: "<?php echo $receiptData['transaction_id']; ?>",
                message: "Thank you for your payment! Here is your receipt for " + "<?php echo $receiptData['room_title']; ?>"
            };

            emailjs.send("<?php echo $emailConfig['service_id']; ?>", "<?php echo $emailConfig['payment_template_id']; ?>", templateParams)
                .then(function(response) {
                    console.log('Receipt sent!', response.status, response.text);
                    updateNotificationStatus(<?php echo $receiptData['notification_id']; ?>, 'sent');
                }, function(error) {
                    console.log('Failed to send receipt', error);
                    updateNotificationStatus(<?php echo $receiptData['notification_id']; ?>, 'failed');
                });
        });

        function updateNotificationStatus(notificationId, status) {
            fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/NotificationController.php?action=updateStatus', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notification_id: notificationId, status: status })
            });
        }
        <?php endif; ?>

        // Modal Logic
        let currentRentalId = null;

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            currentRentalId = null;
        }

        // Close on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('show');
                currentRentalId = null;
            }
        }

        function handleConfirmPayment(rentalId) {
            currentRentalId = rentalId;
            openModal('confirmPaymentModal');
        }

        function handleDeclinePayment(rentalId) {
            currentRentalId = rentalId;
            openModal('declinePaymentModal');
        }

        function handleViewProof(imageUrl) {
            document.getElementById('proofImage').src = imageUrl;
            openModal('viewProofModal');
        }

        document.getElementById('confirmBtn').addEventListener('click', function() {
            if (!currentRentalId) return;
            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Confirming...';
            lucide.createIcons();
            window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/RentalController.php?action=confirm_payment&rental_id=' + currentRentalId;
        });

        document.getElementById('declineBtn').addEventListener('click', function() {
            if (!currentRentalId) return;
            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Declining...';
            lucide.createIcons();
            window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/RentalController.php?action=decline_payment&rental_id=' + currentRentalId;
        });
    </script>
</body>
</html>
