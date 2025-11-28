<?php
// Start output buffering to prevent accidental output
ob_start();

session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Match.php';

class MatchController {
    private $matchModel;

    public function __construct() {
        $this->matchModel = new RoommateMatch();
    }

    /**
     * Handle pass/match action
     */
    public function recordAction() {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Not authenticated']);
        }

        $seekerId = $_SESSION['user_id'];
        $targetId = $_POST['target_id'] ?? null;
        $action = $_POST['action'] ?? null;

        // Validate input
        if (!$targetId || !$action) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Missing required parameters']);
        }

        if (!in_array($action, ['pass', 'match'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid action']);
        }

        try {
            // Record the action
            $result = $this->matchModel->recordAction($seekerId, $targetId, $action);

            if ($result === false) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Failed to record action or already rated']);
            }

            // Success response
            $response = [
                'status' => 'success',
                'action' => $action,
                'is_mutual' => $result['is_mutual']
            ];

            if ($result['is_mutual']) {
                $response['message'] = "It's a match! 🎉";
            } else if ($action === 'match') {
                $response['message'] = 'Match recorded!';
            } else {
                $response['message'] = 'Passed';
            }

            $this->jsonResponse($response);

        } catch (Exception $e) {
            error_log("Match Error: " . $e->getMessage());
            $this->jsonResponse(['status' => 'error', 'message' => 'An internal error occurred']);
        }
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Not authenticated']);
        }

        try {
            $result = $this->matchModel->markNotificationsRead($_SESSION['user_id']);
            $this->jsonResponse([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Notifications marked as read' : 'Failed to update'
            ]);
        } catch (Exception $e) {
            error_log("Notification Error: " . $e->getMessage());
            $this->jsonResponse(['status' => 'error', 'message' => 'An internal error occurred']);
        }
    }

    /**
     * Helper to send JSON response and exit
     */
    private function jsonResponse($data) {
        // Clear any previous output
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new MatchController();

    // Route based on action parameter
    $endpoint = $_POST['endpoint'] ?? '';

    switch ($endpoint) {
        case 'record_action':
            $controller->recordAction();
            break;

        case 'mark_read':
            $controller->markNotificationsRead();
            break;

        default:
            // Use the helper method even here
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid endpoint']);
            exit;
            break;
    }
}
?>
