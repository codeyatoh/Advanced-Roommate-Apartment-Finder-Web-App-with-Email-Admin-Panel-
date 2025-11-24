<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Message Model
 * Handles messages/inquiries between users
 */
class Message extends BaseModel {
    protected $table = 'messages';

    /**
     * Get conversation between two users
     * @param int $user1
     * @param int $user2
     * @return array
     */
    public function getConversation($user1, $user2) {
        $sql = "SELECT m.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                    u.profile_photo as sender_photo
                FROM {$this->table} m
                LEFT JOIN users u ON m.sender_id = u.user_id
                WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2_dup AND m.receiver_id = :user1_dup)
                ORDER BY m.created_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user1', $user1, PDO::PARAM_INT);
        $stmt->bindValue(':user2', $user2, PDO::PARAM_INT);
        $stmt->bindValue(':user2_dup', $user2, PDO::PARAM_INT);
        $stmt->bindValue(':user1_dup', $user1, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all conversations for a user
     * @param int $userId
     * @return array
     */
    public function getUserConversations($userId) {
        $sql = "SELECT 
                    CASE 
                        WHEN m.sender_id = :user_id1 THEN m.receiver_id 
                        ELSE m.sender_id 
                    END as other_user_id,
                    CONCAT(u.first_name, ' ', u.last_name) as other_user_name,
                    u.profile_photo as other_user_photo,
                    u.role as other_user_role,
                    m.message_content as last_message,
                    m.created_at as last_message_time,
                    m.is_read,
                    m.listing_id,
                    l.title as listing_title
                FROM {$this->table} m
                LEFT JOIN users u ON (
                    CASE 
                        WHEN m.sender_id = :user_id2 THEN m.receiver_id 
                        ELSE m.sender_id 
                    END = u.user_id
                )
                LEFT JOIN listings l ON m.listing_id = l.listing_id
                WHERE m.message_id IN (
                    SELECT MAX(message_id)
                    FROM {$this->table}
                    WHERE sender_id = :user_id3 OR receiver_id = :user_id4
                    GROUP BY 
                        CASE 
                            WHEN sender_id = :user_id5 THEN receiver_id 
                            ELSE sender_id 
                        END
                )
                ORDER BY m.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id1', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id2', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id3', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id4', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id5', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get unread message count for user
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE receiver_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Mark messages as read
     * @param int $userId
     * @param int $otherUserId
     * @return bool
     */
    public function markAsRead($userId, $otherUserId) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1 
                WHERE receiver_id = :user_id AND sender_id = :other_user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':other_user_id', $otherUserId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Send message
     * @param array $data
     * @return int|false
     */
    public function send($data) {
        return $this->create($data);
    }

    /**
     * Get inquiries for landlord (grouped by conversation)
     * @param int $landlordId
     * @return array
     */
    public function getLandlordInquiries($landlordId) {
        $sql = "SELECT 
                    m.sender_id as other_user_id,
                    m.listing_id,
                    MAX(m.message_content) as last_message,
                    MAX(m.created_at) as last_message_time,
                    SUM(CASE WHEN m.is_read = 0 THEN 1 ELSE 0 END) as unread_count
                FROM {$this->table} m
                WHERE m.receiver_id = :landlord_id
                GROUP BY m.sender_id, m.listing_id
                ORDER BY MAX(m.created_at) DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Alias for getUserConversations
     * @param int $userId
     * @return array
     */
    public function getConversations($userId) {
        // Get conversations
        $conversations = $this->getUserConversations($userId);
        
        // Add unread count for each conversation
        foreach ($conversations as &$conv) {
            // Skip if other_user_id is null
            if (!empty($conv['other_user_id'])) {
                $unreadCount = $this->getUnreadCountFromUser($userId, $conv['other_user_id']);
                $conv['unread_count'] = $unreadCount;
            } else {
                $conv['unread_count'] = 0;
            }
        }
        
        return $conversations;
    }

    /**
     * Get unread count from specific user
     * @param int $userId Current user
     * @param int $otherUserId Other user
     * @return int
     */
    public function getUnreadCountFromUser($userId, $otherUserId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE receiver_id = :user_id 
                  AND sender_id = :other_user_id 
                  AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':other_user_id', $otherUserId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
