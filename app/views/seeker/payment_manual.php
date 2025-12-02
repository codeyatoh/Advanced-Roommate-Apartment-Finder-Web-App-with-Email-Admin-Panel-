<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}
$rentalId = $_GET['rental_id'] ?? 0;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proof'])) {
    $uploadDir = __DIR__ . '/../../../public/uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['proof'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'payment_' . $rentalId . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    $dbPath = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/uploads/payments/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $db = new Database();
        $conn = $db->getConnection();

        // Insert Payment Record
        $sql = "INSERT INTO payments (rental_id, user_id, amount, method, proof_image, status) 
                SELECT :rental_id, :user_id, rent_amount, 'bank_transfer', :proof_image, 'pending'
                FROM rentals WHERE rental_id = :rental_id_check";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->bindValue(':rental_id_check', $rentalId);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':proof_image', $dbPath);
        
        if ($stmt->execute()) {
            // Notify Landlord
            require_once __DIR__ . '/../../models/Notification.php';
            $notifModel = new Notification();
            
            // Get landlord ID
            $stmt = $conn->prepare("SELECT landlord_id, listing_id FROM rentals WHERE rental_id = ?");
            $stmt->execute([$rentalId]);
            $rental = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rental) {
                $notifModel->create([
                    'user_id' => $rental['landlord_id'],
                    'related_user_id' => $_SESSION['user_id'],
                    'type' => 'payment_received',
                    'title' => 'Payment Proof Uploaded',
                    'message' => 'A tenant has uploaded a proof of payment. Please verify it.',
                    'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php'
                ]);
            }

            // Set flash message and redirect
            $_SESSION['flash_message'] = 'Payment proof uploaded successfully! Your landlord will review it shortly.';
            $_SESSION['flash_type'] = 'success';
            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php');
            exit;
        }
    }
}

// Fetch Landlord Payment Methods
$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT u.payment_methods 
        FROM rentals r 
        JOIN users u ON r.landlord_id = u.user_id 
        WHERE r.rental_id = :rental_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':rental_id', $rentalId);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$paymentMethods = [];
if ($result && !empty($result['payment_methods'])) {
    $paymentMethods = json_decode($result['payment_methods'], true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Payment Instructions - RoomFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }
        
        .status-card {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .status-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d97706;
            flex-shrink: 0;
        }

        .method-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
        }

        .method-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .method-icon {
            width: 3rem;
            height: 3rem;
            background: #f3f4f6;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
        }

        .copy-btn {
            margin-left: auto;
            padding: 0.5rem;
            border-radius: 0.375rem;
            color: #6b7280;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .upload-zone {
            border: 2px dashed #e5e7eb;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-zone:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="payment-container">
        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500;">
            <i data-lucide="arrow-left" style="width: 1.25rem; height: 1.25rem;"></i>
            Back to Dashboard
        </a>

        <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 2rem; color: #111827;">Payment Instructions</h1>
        
        <!-- Status Alert -->
        <div class="status-card">
            <div class="status-icon">
                <i data-lucide="clock" style="width: 1.5rem; height: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="color: #92400e; font-weight: 700; margin-bottom: 0.25rem;">Payment Pending</h3>
                <p style="color: #b45309; font-size: 0.875rem; line-height: 1.5;">
                    Your rental request has been submitted. To finalize your booking, please transfer the payment using one of the methods below and upload your proof of payment.
                </p>
            </div>
        </div>

        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Available Payment Methods</h2>

        <div style="display: grid; gap: 1rem; margin-bottom: 2.5rem;">
            <?php if (empty($paymentMethods)): ?>
                <div style="text-align: center; padding: 2rem; background: #f9fafb; border-radius: 0.75rem; color: #6b7280;">
                    No payment methods configured by landlord. Please contact them directly.
                </div>
            <?php else: ?>
                <?php foreach ($paymentMethods as $method): 
                    $icon = $method['type'] === 'bank' ? 'landmark' : 'smartphone';
                ?>
                <div class="method-card">
                    <div class="method-icon">
                        <i data-lucide="<?php echo $icon; ?>" style="width: 1.5rem; height: 1.5rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;"><?php echo htmlspecialchars($method['name']); ?></div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.125rem;">Name: <?php echo htmlspecialchars($method['account_name']); ?></div>
                        <div style="font-family: monospace; font-size: 1rem; color: #111827; margin-top: 0.25rem;"><?php echo htmlspecialchars($method['account_number']); ?></div>
                    </div>
                    <button class="copy-btn" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($method['account_number']); ?>')" title="Copy Number">
                        <i data-lucide="copy" style="width: 1.25rem; height: 1.25rem;"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Submit Proof of Payment</h2>

        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="upload-zone" onclick="document.getElementById('proof').click()">
                <i data-lucide="upload-cloud" style="width: 3rem; height: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p style="font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Click to upload screenshot</p>
                <p style="font-size: 0.875rem; color: #9ca3af;">PNG, JPG up to 5MB</p>
                <input type="file" id="proof" name="proof" accept="image/*" required style="display: none;" onchange="updateFileName(this)">
                <p id="file-name" style="margin-top: 1rem; font-size: 0.875rem; color: #2563eb; font-weight: 500;"></p>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i data-lucide="send" style="width: 1.25rem; height: 1.25rem;"></i>
                Submit Proof
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function updateFileName(input) {
            const fileName = input.files[0]?.name;
            if (fileName) {
                document.getElementById('file-name').textContent = 'Selected: ' + fileName;
            }
        }
    </script>
</body>
</html>
