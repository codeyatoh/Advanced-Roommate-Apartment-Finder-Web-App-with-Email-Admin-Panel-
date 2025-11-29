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
                       u.first_name, u.last_name, u.profile_photo,
                       TIMESTAMPDIFF(SECOND, n.created_at, NOW()) as seconds_ago
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
                       u.first_name, u.last_name, u.profile_photo,
                       TIMESTAMPDIFF(SECOND, n.created_at, NOW()) as seconds_ago
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
                (user_id, type, title, message, related_id, related_user_id, status) 
                VALUES (:user_id, :type, :title, :message, :related_id, :related_user_id, :status)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':message', $data['message']);
        $stmt->bindValue(':related_id', $data['related_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':related_user_id', $data['related_user_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':status', $data['status'] ?? 'unread'); // Default to unread or pending
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update notification status
     * @param int $notificationId
     * @param string $status
     * @return bool
     */
    public function updateStatus($notificationId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE notification_id = :notification_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':notification_id', $notificationId, PDO::PARAM_INT);
        return $stmt->execute();
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

    /**
     * Get unread count by type
     * @param int $userId
     * @param string $type
     * @return int
     */
    public function getUnreadCountByType($userId, $type) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE user_id = :user_id 
                  AND type = :type 
                  AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    /**
     * Mark notifications of a specific type as read
     * @param int $userId
     * @param string $type
     * @return bool
     */
    public function markAsReadByType($userId, $type) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = :user_id 
                  AND type = :type 
                  AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        
        return $stmt->execute();
    }


    /**
     * Search notifications for admin
     * @param array $filters
     * @return array
     */
    public function searchNotifications($filters = []) {
        $sql = "SELECT n.*, 
                    u.first_name, u.last_name, u.email as user_email, u.phone
                FROM {$this->table} n
                LEFT JOIN users u ON n.user_id = u.user_id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                n.title LIKE :search1 OR 
                n.message LIKE :search2 OR 
                u.first_name LIKE :search3 OR 
                u.last_name LIKE :search4 OR 
                u.email LIKE :search5
            )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
            $params[':search4'] = $searchTerm;
            $params[':search5'] = $searchTerm;
        }

        if (!empty($filters['type']) && $filters['type'] !== 'All Types') {
            $sql .= " AND n.type = :type";
            $params[':type'] = strtolower($filters['type']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All Status') {
            $sql .= " AND n.status = :status";
            $params[':status'] = strtolower($filters['status']);
        }

        $sql .= " ORDER BY n.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    /**
     * Get dashboard statistics
     * @return array
     */
    public function getDashboardStats() {
        $today = date('Y-m-d');
        $stats = [
            'emails_sent_today' => 0,
            'failed_deliveries' => 0,
            'pending_queue' => 0
        ];

        // Emails sent today
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE DATE(created_at) = :today AND type = 'email'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':today', $today);
        $stmt->execute();
        $stats['emails_sent_today'] = (int)($stmt->fetch()['count'] ?? 0);

        // Failed deliveries
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'failed'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['failed_deliveries'] = (int)($stmt->fetch()['count'] ?? 0);

        // Pending queue
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['pending_queue'] = (int)($stmt->fetch()['count'] ?? 0);

        return $stats;
    }
}
