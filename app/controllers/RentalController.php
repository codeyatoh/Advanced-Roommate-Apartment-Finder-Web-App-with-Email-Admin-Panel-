<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Listing.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../services/StripeService.php';

class RentalController {
    private $rentalModel;
    private $paymentModel;
    private $listingModel;
    private $notificationModel;
    private $stripeService;

    public function __construct() {
        $this->rentalModel = new Rental();
        $this->paymentModel = new Payment();
        $this->listingModel = new Listing();
        $this->notificationModel = new Notification();
        $this->stripeService = new StripeService();
    }

    public function requestRent($data) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
            exit;
        }

        $listingId = $data['listing_id'];
        $listing = $this->listingModel->getById($listingId);
        
        if (!$listing) {
            die("Listing not found");
        }

        // Check if room still has available beds
        $bedsAvailable = $listing['bedrooms'] - $listing['current_roommates'];
        if ($bedsAvailable <= 0) {
            $_SESSION['error'] = "This room is now FULL. All beds are already occupied.";
            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/room_details.php?id=' . $listingId);
            exit;
        }

        // Create Rental Record
        $rentalId = $this->rentalModel->create([
            'listing_id' => $listingId,
            'tenant_id' => $_SESSION['user_id'],
            'landlord_id' => $listing['landlord_id'],
            'start_date' => $data['start_date'],
            'rent_amount' => $listing['price']
        ]);

        if (!$rentalId) {
            die("Failed to create rental request");
        }

        // Handle Payment
        if ($data['payment_method'] === 'stripe') {
            // Use a controller action for success to handle DB updates
            $successUrl = 'http://localhost/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/RentalController.php?action=payment_success&rental_id=' . $rentalId;
            
            $session = $this->stripeService->createCheckoutSession(
                $listing['price'],
                'php',
                $successUrl,
                'http://localhost/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/room_details.php?id=' . $listingId
            );

            if (isset($session['id'])) {
                // Save pending payment
                $this->paymentModel->create([
                    'rental_id' => $rentalId,
                    'user_id' => $_SESSION['user_id'],
                    'amount' => $listing['price'],
                    'method' => 'stripe',
                    'transaction_id' => $session['id'],
                    'status' => 'pending'
                ]);
                
                header('Location: ' . $session['url']);
                exit;
            } else {
                die("Stripe session creation failed");
            }
        } else {
            // Manual Payment (Cash/Bank)
            // Notify landlord about pending request
            $this->notificationModel->create([
                'user_id' => $listing['landlord_id'],
                'related_user_id' => $_SESSION['user_id'],
                'type' => 'rent_request',
                'title' => 'New Rent Request',
                'message' => 'New rent request (Cash/Bank) for ' . $listing['title'],
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php' // Assuming this page exists or will exist
            ]);

            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/payment_manual.php?rental_id=' . $rentalId);
            exit;
        }
    }

    public function paymentSuccess($data) {
        session_start();
        $rentalId = $data['rental_id'];
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT r.*, l.title, l.landlord_id 
                FROM rentals r 
                JOIN listings l ON r.listing_id = l.listing_id 
                WHERE r.rental_id = :rental_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->execute();
        $rental = $stmt->fetch();

        if ($rental) {
            // Update Payment Status
            $sql = "UPDATE payments SET status = 'completed' WHERE rental_id = :rental_id AND method = 'stripe'";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rental_id', $rentalId);
            $stmt->execute();

            // Increment current_roommates count
            $sql = "UPDATE listings SET current_roommates = current_roommates + 1 WHERE listing_id = :listing_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':listing_id', $rental['listing_id']);
            $stmt->execute();

            // Update Rental Status to active
            $sql = "UPDATE rentals SET status = 'active' WHERE rental_id = :rental_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rental_id', $rentalId);
            $stmt->execute();

            // Notify Landlord
            $this->notificationModel->create([
                'user_id' => $rental['landlord_id'],
                'related_user_id' => $rental['tenant_id'],
                'type' => 'payment_received',
                'title' => 'Rent Payment Received',
                'message' => 'Payment received for ' . $rental['title'],
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php'
            ]);

            // Prepare Email Receipt for Tenant
            // Fetch tenant details
            $sql = "SELECT email, first_name, last_name FROM users WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':user_id', $rental['tenant_id']);
            $stmt->execute();
            $tenant = $stmt->fetch();

            if ($tenant) {
                // Create pending email notification log
                $notifId = $this->notificationModel->create([
                    'user_id' => $rental['tenant_id'],
                    'type' => 'email',
                    'title' => 'Payment Receipt',
                    'message' => 'Receipt for ' . $rental['title'],
                    'status' => 'pending'
                ]);

                $_SESSION['email_receipt'] = [
                    'notification_id' => $notifId,
                    'to_email' => $tenant['email'],
                    'to_name' => $tenant['first_name'] . ' ' . $tenant['last_name'],
                    'room_title' => $rental['title'],
                    'amount' => $rental['rent_amount'],
                    'date' => date('F j, Y'),
                    'transaction_id' => 'STRIPE-' . time() // Placeholder if actual ID not handy
                ];
            }
        }

        // Redirect to success view
        header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/payment_success.php?rental_id=' . $rentalId);
        exit;
    }

    public function confirmPayment($data) {
        session_start();
        // Check if landlord
        // Allow fallback for dev/testing to match rentals.php
        $userId = $_SESSION['user_id'] ?? 2;
        $userRole = $_SESSION['role'] ?? 'landlord';

        if (!$userId || $userRole !== 'landlord') {
            // Handle unauthorized
            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
            exit;
        }
        
        $rentalId = $data['rental_id'];
        $db = new Database();
        $conn = $db->getConnection();

        // Update Payment Status
        $sql = "UPDATE payments SET status = 'completed' WHERE rental_id = :rental_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->execute();

        // Increment current_roommates count (if not already done)
        // We need to fetch listing_id first
        $sql = "SELECT r.listing_id, r.tenant_id, r.landlord_id, r.rent_amount, l.title 
                FROM rentals r 
                JOIN listings l ON r.listing_id = l.listing_id
                WHERE r.rental_id = :rental_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->execute();
        $rental = $stmt->fetch();

        if ($rental) {
            // Verify landlord owns this rental
            if ($rental['landlord_id'] !== $userId) {
                die("Unauthorized");
            }

            $sql = "UPDATE listings SET current_roommates = current_roommates + 1 WHERE listing_id = :listing_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':listing_id', $rental['listing_id']);
            $stmt->execute();

            // Update Rental Status
            $sql = "UPDATE rentals SET status = 'active' WHERE rental_id = :rental_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rental_id', $rentalId);
            $stmt->execute();

            // Notify Tenant (System Notification)
            $this->notificationModel->create([
                'user_id' => $rental['tenant_id'],
                'related_user_id' => $rental['landlord_id'],
                'type' => 'system',
                'title' => 'Payment Confirmed',
                'message' => 'Your rent payment has been confirmed by the landlord.',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php'
            ]);

            // Prepare Email Receipt for Tenant
            // Fetch tenant details
            $sql = "SELECT email, first_name, last_name FROM users WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':user_id', $rental['tenant_id']);
            $stmt->execute();
            $tenant = $stmt->fetch();

            if ($tenant) {
                // Create pending email notification log
                $notifId = $this->notificationModel->create([
                    'user_id' => $rental['tenant_id'],
                    'type' => 'email',
                    'title' => 'Payment Receipt',
                    'message' => 'Receipt for ' . $rental['title'],
                    'status' => 'pending'
                ]);

                $_SESSION['email_receipt'] = [
                    'notification_id' => $notifId,
                    'to_email' => $tenant['email'],
                    'to_name' => $tenant['first_name'] . ' ' . $tenant['last_name'],
                    'room_title' => $rental['title'],
                    'amount' => $rental['rent_amount'],
                    'date' => date('F j, Y'),
                    'transaction_id' => 'MANUAL-' . time()
                ];
            }
        }

        header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php');
        exit;
    }

    public function declinePayment($data) {
        session_start();
        // Allow fallback for dev/testing
        $userId = $_SESSION['user_id'] ?? 2;
        $userRole = $_SESSION['role'] ?? 'landlord';

        if (!$userId || $userRole !== 'landlord') {
            header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
            exit;
        }

        $rentalId = $data['rental_id'];
        $db = new Database();
        $conn = $db->getConnection();

        // Update Payment Status to Rejected
        $sql = "UPDATE payments SET status = 'failed' WHERE rental_id = :rental_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->execute();

        // Get rental info for notification
        $sql = "SELECT r.tenant_id, r.landlord_id, l.title 
                FROM rentals r 
                JOIN listings l ON r.listing_id = l.listing_id
                WHERE r.rental_id = :rental_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':rental_id', $rentalId);
        $stmt->execute();
        $rental = $stmt->fetch();

        if ($rental) {
            if ($rental['landlord_id'] !== $userId) {
                die("Unauthorized");
            }

            // Notify Tenant
            $this->notificationModel->create([
                'user_id' => $rental['tenant_id'],
                'related_user_id' => $rental['landlord_id'],
                'type' => 'system',
                'title' => 'Payment Declined',
                'message' => 'Your rent payment for ' . $rental['title'] . ' was declined. Please contact the landlord.',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php'
            ]);
        }

        header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php');
        exit;
    }

    public function removeTenant($data) {
        session_start();
        header('Content-Type: application/json');

        try {
            // Check if user is landlord
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
                throw new Exception('Unauthorized');
            }

            $rentalId = $data['rental_id'] ?? null;
            $reason = $data['reason'] ?? '';

            if (!$rentalId) {
                throw new Exception('Rental ID is required');
            }

            // FIX: Use getConnection() instead of accessing protected property conn directly
            $conn = $this->rentalModel->getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
            
            $conn->beginTransaction();

            // Get rental details
            $sql = "SELECT r.*, l.landlord_id, l.listing_id, l.title as listing_title, u.first_name, u.last_name
                    FROM rentals r
                    JOIN listings l ON r.listing_id = l.listing_id
                    JOIN users u ON r.tenant_id = u.user_id
                    WHERE r.rental_id = :rental_id AND r.status = 'active'";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rental_id', $rentalId, PDO::PARAM_INT);
            $stmt->execute();
            $rental = $stmt->fetch();

            if (!$rental) {
                throw new Exception('Rental not found or already cancelled');
            }

            // Verify landlord owns this listing
            if ($rental['landlord_id'] !== $_SESSION['user_id']) {
                throw new Exception('Unauthorized - You do not own this listing');
            }

            // Update rental status to cancelled
            $sql = "UPDATE rentals 
                    SET status = 'cancelled', 
                        end_date = CURDATE()
                    WHERE rental_id = :rental_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rental_id', $rentalId, PDO::PARAM_INT);
            $stmt->execute();

            // Decrement current_roommates count
            $sql = "UPDATE listings 
                    SET current_roommates = GREATEST(0, current_roommates - 1)
                    WHERE listing_id = :listing_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':listing_id', $rental['listing_id'], PDO::PARAM_INT);
            $stmt->execute();

            // Create notification for tenant
            $notificationMessage = "You have been removed from " . $rental['listing_title'];
            if (!empty($reason)) {
                $notificationMessage .= ". Reason: " . $reason;
            }

            $this->notificationModel->create([
                'user_id' => $rental['tenant_id'],
                'related_user_id' => $_SESSION['user_id'],
                'type' => 'system',
                'title' => 'Rental Cancelled',
                'message' => $notificationMessage,
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/browse_rooms.php'
            ]);

            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Tenant removed successfully',
                'tenant_name' => $rental['first_name'] . ' ' . $rental['last_name']
            ]);

        } catch (Throwable $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Remove tenant error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// Handle Request
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action) {
    $controller = new RentalController();
    
    if ($action === 'rent' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->requestRent($_POST);
    } elseif ($action === 'payment_success') {
        $controller->paymentSuccess($_GET);
    } elseif ($action === 'confirm_payment') {
        $controller->confirmPayment($_GET);
    } elseif ($action === 'decline_payment') {
        $controller->declinePayment($_GET);
    } elseif ($action === 'remove_tenant' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->removeTenant($_POST);
    }
}
?>
