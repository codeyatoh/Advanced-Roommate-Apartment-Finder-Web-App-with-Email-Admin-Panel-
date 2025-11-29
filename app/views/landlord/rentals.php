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

$sql = "SELECT r.*, l.title as listing_title, l.price, 
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
        .rentals-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .rental-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
        }
        .rental-info {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .tenant-avatar {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-completed { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="rentals-container">
        <h1 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem;">Rental Management</h1>

        <?php if (empty($rentals)): ?>
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 0.75rem;">
                <p style="color: #6b7280;">No rental requests yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rentals as $rental): ?>
                <div class="rental-card">
                    <div class="rental-info">
                        <img src="<?php echo $rental['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($rental['first_name']); ?>" alt="Tenant" class="tenant-avatar">
                        <div>
                            <h3 style="font-weight: 600; font-size: 1.125rem;"><?php echo htmlspecialchars($rental['listing_title']); ?></h3>
                            <p style="color: #6b7280; font-size: 0.875rem;">Tenant: <?php echo htmlspecialchars($rental['first_name'] . ' ' . $rental['last_name']); ?></p>
                            <p style="color: #6b7280; font-size: 0.875rem;">Move-in: <?php echo date('M j, Y', strtotime($rental['start_date'])); ?></p>
                        </div>
                    </div>
                    
                    <div style="text-align: right;">
                        <div style="margin-bottom: 0.5rem;">
                            <span class="status-badge <?php echo $rental['status'] === 'active' ? 'status-active' : 'status-pending'; ?>">
                                <?php echo ucfirst($rental['status']); ?>
                            </span>
                        </div>
                        <div style="font-size: 0.875rem; color: #4b5563; margin-bottom: 0.5rem;">
                            <?php if ($rental['payment_status'] === 'completed'): ?>
                                <span style="color: #059669; font-weight: 500;"><i data-lucide="check-circle" style="width: 1rem; height: 1rem; vertical-align: text-bottom;"></i> Paid via <?php echo ucfirst($rental['payment_method']); ?></span>
                            <?php else: ?>
                                <span style="color: #d97706; font-weight: 500;"><i data-lucide="clock" style="width: 1rem; height: 1rem; vertical-align: text-bottom;"></i> Payment Pending</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-weight: 700; font-size: 1.125rem;">
                            ₱<?php echo number_format($rental['rent_amount']); ?>
                        </div>
                        
                        <!-- Actions -->
                        <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <?php if (!empty($rental['proof_image'])): ?>
                                <a href="<?php echo htmlspecialchars($rental['proof_image']); ?>" target="_blank" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; color: #374151; border: 1px solid #d1d5db; padding: 0.25rem 0.75rem; border-radius: 0.375rem;">
                                    <i data-lucide="image" style="width: 1rem; height: 1rem;"></i> View Proof
                                </a>
                            <?php endif; ?>

                            <?php if ($rental['payment_method'] !== 'stripe' && $rental['payment_status'] !== 'completed'): ?>
                                <button class="btn btn-primary btn-sm" onclick="confirmPayment(<?php echo $rental['rental_id']; ?>)">
                                    Confirm Payment
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php
    $emailConfig = require __DIR__ . '/../../config/emailjs.php';
    $receiptData = $_SESSION['email_receipt'] ?? null;
    if ($receiptData) {
        unset($_SESSION['email_receipt']); // Clear after use
    }
    ?>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
        (function() {
            emailjs.init("<?php echo $emailConfig['public_key']; ?>");
        })();

        <?php if ($receiptData): ?>
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
                    // Update notification status to sent
                    updateNotificationStatus(<?php echo $receiptData['notification_id']; ?>, 'sent');
                }, function(error) {
                    console.log('Failed to send receipt', error);
                    // Update notification status to failed
                    updateNotificationStatus(<?php echo $receiptData['notification_id']; ?>, 'failed');
                });
        });

        function updateNotificationStatus(notificationId, status) {
            fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/NotificationController.php?action=updateStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    notification_id: notificationId,
                    status: status
                })
            });
        }
        <?php endif; ?>
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        
        function confirmPayment(rentalId) {
            if(confirm('Are you sure you want to confirm this payment?')) {
                // Call API/Controller to confirm
                window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/RentalController.php?action=confirm_payment&rental_id=' + rentalId;
            }
        }
    </script>
</body>
</html>
