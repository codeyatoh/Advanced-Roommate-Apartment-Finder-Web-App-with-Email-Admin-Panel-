<?php
session_start();
require_once __DIR__ . '/../../models/Listing.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$listingId = $_GET['listing_id'] ?? 0;
$listingModel = new Listing();
// Use getWithImages to fetch image data
$listing = $listingModel->getWithImages($listingId);

if (!$listing) {
    die("Listing not found");
}

$primaryImage = !empty($listing['images']) ? $listing['images'][0]['image_url'] : 'https://via.placeholder.com/150?text=No+Image';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Request - <?php echo htmlspecialchars($listing['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <style>
        .booking-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .property-card {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            align-items: center;
        }

        .property-image {
            width: 80px;
            height: 80px;
            border-radius: 0.5rem;
            object-fit: cover;
        }

        .property-info h2 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            line-height: 1.4;
        }

        .property-info p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        .price-tag {
            font-weight: 700;
            color: #10b981;
            margin-top: 0.5rem !important;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.75rem;
            background: white;
        }

        .payment-option:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .payment-option.selected {
            border-color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .payment-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: #f3f4f6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
        }

        .payment-option.selected .payment-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .radio-custom {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            margin-left: auto;
            position: relative;
        }

        .payment-option.selected .radio-custom {
            border-color: #3b82f6;
        }

        .payment-option.selected .radio-custom::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 0.625rem;
            height: 0.625rem;
            background: #3b82f6;
            border-radius: 50%;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 1.25rem;
            height: 1.25rem;
        }

        .input-with-icon input {
            padding-left: 3rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="booking-container">
        <!-- Back Link -->
        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/room_details.php?id=<?php echo $listingId; ?>" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500;">
            <i data-lucide="arrow-left" style="width: 1.25rem; height: 1.25rem;"></i>
            Back to Property
        </a>

        <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 2rem; color: #111827;">Request to Rent</h1>
        
        <!-- Property Summary -->
        <div class="property-card">
            <img src="<?php echo $primaryImage; ?>" alt="Property" class="property-image">
            <div class="property-info">
                <h2><?php echo htmlspecialchars($listing['title']); ?></h2>
                <p><i data-lucide="map-pin" style="width: 0.875rem; height: 0.875rem; display: inline; vertical-align: text-top;"></i> <?php echo htmlspecialchars($listing['location']); ?></p>
                <p class="price-tag">₱<?php echo number_format($listing['price']); ?> / month</p>
            </div>
        </div>

        <form action="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/RentalController.php" method="POST">
            <input type="hidden" name="action" value="rent">
            <input type="hidden" name="listing_id" value="<?php echo $listingId; ?>">

            <!-- Move-in Date -->
            <div style="margin-bottom: 2rem;">
                <h3 class="section-title">Move-in Date</h3>
                <div class="input-with-icon">
                    <i data-lucide="calendar-days"></i>
                    <input type="date" id="start_date" name="start_date" class="form-input" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%;">
                </div>
            </div>

            <!-- Payment Method -->
            <div style="margin-bottom: 2rem;">
                <h3 class="section-title"><i data-lucide="wallet"></i> Payment Method</h3>
                
                <div class="payment-option selected" onclick="selectPayment('stripe')">
                    <div class="payment-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">Pay with Card</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Secure payment via Stripe</div>
                    </div>
                    <div class="radio-custom"></div>
                    <input type="radio" name="payment_method" value="stripe" id="stripe" checked style="display: none;">
                </div>

                <div class="payment-option" onclick="selectPayment('cash')">
                    <div class="payment-icon">
                        <i data-lucide="banknote"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">Cash / Bank Transfer</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Upload proof of payment later</div>
                    </div>
                    <div class="radio-custom"></div>
                    <input type="radio" name="payment_method" value="cash" id="cash" style="display: none;">
                </div>
            </div>

            <!-- Total Summary -->
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #4b5563;">
                    <span>Monthly Rent</span>
                    <span>₱<?php echo number_format($listing['price']); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.125rem; color: #111827; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <span>Total to pay now</span>
                    <span>₱<?php echo number_format($listing['price']); ?></span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                Proceed to Payment <i data-lucide="arrow-right" style="width: 1.25rem; height: 1.25rem;"></i>
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function selectPayment(method) {
            // Update visual state
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            
            if (method === 'stripe') {
                document.querySelector('input[value="stripe"]').closest('.payment-option').classList.add('selected');
                document.getElementById('stripe').checked = true;
            } else {
                document.querySelector('input[value="cash"]').closest('.payment-option').classList.add('selected');
                document.getElementById('cash').checked = true;
            }
        }
    </script>
</body>
</html>
