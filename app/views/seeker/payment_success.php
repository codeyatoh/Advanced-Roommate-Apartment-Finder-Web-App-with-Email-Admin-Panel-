<?php
session_start();
require_once __DIR__ . '/../../models/Payment.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$rentalId = $_GET['rental_id'] ?? 0;
// In a real app, verify Stripe session here using the service
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div style="max-width: 600px; margin: 4rem auto; padding: 2rem; text-align: center;">
        <div style="width: 4rem; height: 4rem; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i data-lucide="check" style="width: 2rem; height: 2rem;"></i>
        </div>
        <h1 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 1rem;">Payment Successful!</h1>
        <p style="color: #6b7280; margin-bottom: 2rem;">Your rent payment has been processed successfully. The landlord has been notified.</p>
        
        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>

    <?php
    $emailConfig = require __DIR__ . '/../../../config/emailjs.php';
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
                    console.log('SUCCESS!', response.status, response.text);
                    // Update notification status to sent
                    updateNotificationStatus(<?php echo $receiptData['notification_id']; ?>, 'sent');
                }, function(error) {
                    console.log('FAILED...', error);
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
    <script>lucide.createIcons();</script>
</body>
</html>
