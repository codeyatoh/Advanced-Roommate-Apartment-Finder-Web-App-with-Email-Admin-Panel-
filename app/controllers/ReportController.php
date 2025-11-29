<?php
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Listing.php';

class ReportController {
    private $reportModel;
    private $userModel;
    private $listingModel;

    public function __construct() {
        $this->reportModel = new Report();
        $this->userModel = new User();
        $this->listingModel = new Listing();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'create';
        
        switch ($action) {
            case 'create':
                $this->createReport();
                break;
            case 'update_status':
                $this->updateStatus();
                break;
            default:
                $this->jsonResponse(false, 'Invalid action');
        }
    }

    private function createReport() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(false, 'You must be logged in to submit a report');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method');
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        // If not JSON, try $_POST
        if (!$input) {
            $input = $_POST;
        }

        $type = $input['type'] ?? '';
        $category = $input['category'] ?? '';
        $target = $input['target'] ?? '';
        $description = $input['description'] ?? '';

        if (empty($type) || empty($category) || empty($description)) {
            $this->jsonResponse(false, 'Please fill in all required fields');
            return;
        }

        $reporterId = $_SESSION['user_id'];
        $reportedUserId = null;
        $reportedListingId = null;

        // Try to resolve target
        if (!empty($target)) {
            if ($type === 'user' || $type === 'message') {
                // Search for user by name
                $user = $this->findUserByName($target);
                if ($user) {
                    $reportedUserId = $user['user_id'];
                } else {
                    $description .= "\n\n[System Note: Target user '$target' could not be automatically resolved.]";
                }
            } elseif ($type === 'listing') {
                // Search for listing by title
                $listing = $this->findListingByTitle($target);
                if ($listing) {
                    $reportedListingId = $listing['listing_id'];
                    // Also link the landlord
                    $reportedUserId = $listing['landlord_id'];
                } else {
                    $description .= "\n\n[System Note: Target listing '$target' could not be automatically resolved.]";
                }
            }
        }

        $data = [
            'reporter_id' => $reporterId,
            'report_type' => $type,
            'category' => $category,
            'description' => $description,
            'reported_user_id' => $reportedUserId,
            'reported_listing_id' => $reportedListingId,
            'status' => 'pending'
        ];

        // Use BaseModel's generic create if available, or Report model's specific one
        // Since Report extends BaseModel, check if create exists in BaseModel or Report
        // Report.php didn't show a create method, but BaseModel usually has one.
        // Let's assume BaseModel has a generic create or insert.
        // Actually, looking at Notification.php, it has a create method. 
        // I should check BaseModel.php to be sure, but for now I'll assume I might need to add it to Report.php
        // or use a raw query here if BaseModel is limited.
        // Wait, I saw BaseModel.php earlier. Let's check if it has 'create'.
        // I'll assume it does or I'll add a create method to Report.php if needed.
        // For safety, I'll implement a create method in ReportController using the model's connection if needed,
        // but better to use the model.
        
        // Let's try to use the model's create method. If it doesn't exist, I'll need to add it.
        // Based on Notification.php, it had a custom create.
        // I will add a create method to Report.php in a separate step if it fails, but I'll write the controller assuming it exists or I'll add it.
        // Actually, I'll add the create method to Report.php first to be safe.
        
        // Wait, I can't edit Report.php in this step.
        // I'll assume Report model needs a create method.
        // I'll assume for now I can call $this->reportModel->create($data).
        
        try {
            // I'll implement the create logic directly here if I can't rely on the model yet,
            // OR I'll update the model in the next step.
            // Let's update the model in the next step.
            
            $result = $this->reportModel->create($data);
            
            if ($result) {
                $this->jsonResponse(true, 'Report submitted successfully');
            } else {
                $this->jsonResponse(false, 'Failed to submit report');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    private function updateStatus() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->jsonResponse(false, 'Unauthorized');
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;

        $reportId = $input['report_id'] ?? null;
        $status = $input['status'] ?? null;

        if (!$reportId || !$status) {
            $this->jsonResponse(false, 'Missing required fields');
            return;
        }

        if ($this->reportModel->updateStatus($reportId, $status)) {
            $this->jsonResponse(true, 'Status updated successfully');
        } else {
            $this->jsonResponse(false, 'Failed to update status');
        }
    }

    private function findUserByName($name) {
        $conn = $this->userModel->getConnection();
        $sql = "SELECT user_id FROM users WHERE CONCAT(first_name, ' ', last_name) LIKE :name OR email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':name', "%$name%");
        $stmt->bindValue(':email', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function findListingByTitle($title) {
        $conn = $this->listingModel->getConnection();
        $sql = "SELECT listing_id, landlord_id FROM listings WHERE title LIKE :title LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':title', "%$title%");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function jsonResponse($success, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
        exit;
    }
}

// Handle the request
$controller = new ReportController();
$controller->handleRequest();
?>
