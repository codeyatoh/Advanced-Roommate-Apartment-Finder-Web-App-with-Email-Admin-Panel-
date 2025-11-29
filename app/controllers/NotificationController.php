<?php
session_start();
require_once __DIR__ . '/../models/Notification.php';

// Check if user is logged in for protected routes
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    return $_SESSION['user_id'];
}

$action = $_GET['action'] ?? '';
$notificationModel = new Notification();

switch ($action) {
    case 'markAsRead':
        checkAuth();
        $notifId = $_GET['id'] ?? 0;
        if ($notifId) {
            $success = $notificationModel->markAsRead($notifId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        }
        break;
    
    case 'markAllAsRead':
        $userId = checkAuth();
        $success = $notificationModel->markAllAsRead($userId);
        echo json_encode(['success' => $success]);
        break;
    
    case 'getUnreadCount':
        $userId = checkAuth();
        $count = $notificationModel->getUnreadCount($userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;
    
    case 'getNotifications':
        $userId = checkAuth();
        $limit = $_GET['limit'] ?? 10;
        $notifications = $notificationModel->getUserNotifications($userId, $limit);
        echo json_encode(['success' => true, 'notifications' => $notifications]);
        break;

    case 'updateStatus':
        // Public endpoint for EmailJS callbacks
        $data = json_decode(file_get_contents('php://input'), true);
        $notificationId = $data['notification_id'] ?? 0;
        $status = $data['status'] ?? '';
        
        if ($notificationId && $status) {
            $success = $notificationModel->updateStatus($notificationId, $status);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
