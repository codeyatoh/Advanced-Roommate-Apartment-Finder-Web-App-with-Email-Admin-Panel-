<?php
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
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            exit;
        }

        $seekerId = $_SESSION['user_id'];
        $targetId = $_POST['target_id'] ?? null;
        $action = $_POST['action'] ?? null;

        // Validate input
        if (!$targetId || !$action) {
            echo json_encode([
 'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            exit;
        }

        if (!in_array($action, ['pass', 'match'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            exit;
        }

        // Record the action
        $result = $this->matchModel->recordAction($seekerId, $targetId, $action);

        if ($result === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to record action or already rated'
            ]);
            exit;
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

        echo json_encode($response);
        exit;
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            exit;
        }

        $result = $this->matchModel->markNotificationsRead($_SESSION['user_id']);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Notifications marked as read' : 'Failed to update'
        ]);
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
            echo json_encode(['status' => 'error', 'message' => 'Invalid endpoint']);
            break;
    }
}
?>
