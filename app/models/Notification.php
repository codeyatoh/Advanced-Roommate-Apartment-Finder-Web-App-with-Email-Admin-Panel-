<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Notification Model
 * Handles user notifications for matches, messages, appointments, etc.
 */
class Notification extends BaseModel {
    protected $table = 'notifications';

    /**
     * Override primary key name
     * @return string
     */
    protected function getPrimaryKey() {
        return 'notification_id';
    }

    /**
     * Get unread notifications for a user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnread($userId, $limit = 10) {
        $sql = "SELECT n.*, 
                       u.first_name, u.last_name, u.profile_photo
                FROM {$this->table} n
                LEFT JOIN users u ON n.related_user_id = u.user_id
                WHERE n.user_id = :user_id 
                  AND n.is_read = 0
                ORDER BY n.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get all notifications for a user (read and unread)
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUserNotifications($userId, $limit = 20) {
        $sql = "SELECT n.*, 
                       u.first_name, u.last_name, u.profile_photo
                FROM {$this->table} n
                LEFT JOIN users u ON n.related_user_id = u.user_id
                WHERE n.user_id = :user_id
                ORDER BY n.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get unread count for a user
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    /**
     * Mark notification as read
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW() 
                WHERE notification_id = :notification_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':notification_id', $notificationId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Mark all notifications as read for a user
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Create a new notification
     * @param array $data
     * @return bool|int
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (user_id, type, title, message, related_id, related_user_id) 
                VALUES (:user_id, :type, :title, :message, :related_id, :related_user_id)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':message', $data['message']);
        $stmt->bindValue(':related_id', $data['related_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':related_user_id', $data['related_user_id'] ?? null, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Delete old read notifications
     * @param int $daysOld
     * @return bool
     */
    public function deleteOldRead($daysOld = 30) {
        $sql = "DELETE FROM {$this->table} 
                WHERE is_read = 1 
                  AND read_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':days', $daysOld, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
