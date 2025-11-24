<?php
session_start();
require_once __DIR__ . '/../models/Notification.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

$notificationModel = new Notification();

switch ($action) {
    case 'markAsRead':
        $notifId = $_GET['id'] ?? 0;
        if ($notifId) {
            $success = $notificationModel->markAsRead($notifId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        }
        break;
    
    case 'markAllAsRead':
        $success = $notificationModel->markAllAsRead($userId);
        echo json_encode(['success' => $success]);
        break;
    
    case 'getUnreadCount':
        $count = $notificationModel->getUnreadCount($userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;
    
    case 'getNotifications':
        $limit = $_GET['limit'] ?? 10;
        $notifications = $notificationModel->getUserNotifications($userId, $limit);
        echo json_encode(['success' => true, 'notifications' => $notifications]);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
