<?php
require_once __DIR__ . '/../models/Message.php';

class MessageController {
    private $messageModel;

    public function __construct() {
        $this->messageModel = new Message();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'send':
                $this->sendMessage();
                break;
            case 'getConversation':
                $this->getConversation();
                break;
            case 'getNewMessages':
                $this->getNewMessages();
                break;
            default:
                // Handle other actions or 404
                break;
        }
    }

    private function sendMessage() {
        // Set timezone to Philippine Time
        date_default_timezone_set('Asia/Manila');
        
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            // Fallback to $_POST if not JSON
            $data = $_POST;
        }

        $receiverId = $data['receiver_id'] ?? null;
        $messageContent = $data['message'] ?? null;
        $listingId = $data['listing_id'] ?? null;

        if (!$receiverId || !$messageContent) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $messageData = [
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'message_content' => $messageContent,
            'listing_id' => $listingId,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->messageModel->send($messageData);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        exit;
    }

    private function getConversation() {
        // Set timezone to Philippine Time
        date_default_timezone_set('Asia/Manila');
        
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $landlordId = $_SESSION['user_id'];
        $otherUserId = $_GET['other_user_id'] ?? null;

        if (!$otherUserId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing other_user_id parameter']);
            exit;
        }

        $messages = $this->messageModel->getConversation($landlordId, $otherUserId);

        echo json_encode(['success' => true, 'messages' => $messages]);
        exit;
    }

    private function getNewMessages() {
        // Set timezone to Philippine Time
        date_default_timezone_set('Asia/Manila');
        
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'];
        $otherUserId = $_GET['other_user_id'] ?? null;
        $lastMessageId = $_GET['last_message_id'] ?? 0;

        if (!$otherUserId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing other_user_id parameter']);
            exit;
        }

        // Fetch new messages after the last_message_id
        $sql = "SELECT m.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                    u.profile_photo as sender_photo
                FROM messages m
                LEFT JOIN users u ON m.sender_id = u.user_id
                WHERE m.message_id > :last_message_id
                  AND ((m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2_dup AND m.receiver_id = :user1_dup))
                ORDER BY m.created_at ASC";
        
        $stmt = $this->messageModel->conn->prepare($sql);
        $stmt->bindValue(':last_message_id', $lastMessageId, PDO::PARAM_INT);
        $stmt->bindValue(':user1', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':user2', $otherUserId, PDO::PARAM_INT);
        $stmt->bindValue(':user2_dup', $otherUserId, PDO::PARAM_INT);
        $stmt->bindValue(':user1_dup', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();
        $newMessages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ensure associative array

        echo json_encode(['success' => true, 'messages' => $newMessages ?: []]);
        exit;
    }
}

// Instantiate and handle request if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'MessageController.php') {
    $controller = new MessageController();
    $controller->handleRequest();
}
