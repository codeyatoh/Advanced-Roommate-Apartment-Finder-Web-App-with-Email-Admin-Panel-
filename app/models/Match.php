<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Match Model
 * Handles roommate matching logic - pass/match actions and mutual match detection
 */
class RoommateMatch extends BaseModel {
    protected $table = 'roommate_matches';

    /**
     * Record a pass or match action
     * @param int $seekerId User performing the action
     * @param int $targetId User being evaluated
     * @param string $action 'pass' or 'match'
     * @return bool|array Returns array with is_mutual flag if successful
     */
    public function recordAction($seekerId, $targetId, $action) {
        // Validate action
        if (!in_array($action, ['pass', 'match'])) {
            return false;
        }

        // Check if already exists
        $sql = "SELECT match_id, action FROM {$this->table} 
                WHERE seeker_id = :seeker_id AND target_seeker_id = :target_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':target_id', $targetId, PDO::PARAM_INT);
        $stmt->execute();
        
        $existing = $stmt->fetch();

        if ($existing) {
            // If already matched, return success (idempotent)
            if ($existing['action'] === 'match' && $action === 'match') {
                // Check if it's mutual (just in case it wasn't updated before)
                $isMutual = $this->checkAndUpdateMutualMatch($seekerId, $targetId);
                return [
                    'success' => true,
                    'is_mutual' => $isMutual
                ];
            }

            // If passed before, but now matching (changed mind), update it
            if ($existing['action'] === 'pass' && $action === 'match') {
                $sql = "UPDATE {$this->table} SET action = 'match', created_at = NOW() 
                        WHERE match_id = :match_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':match_id', $existing['match_id'], PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    // Check for mutual match after update
                    $isMutual = $this->checkAndUpdateMutualMatch($seekerId, $targetId);
                    return [
                        'success' => true,
                        'is_mutual' => $isMutual
                    ];
                }
            }

            // Otherwise (e.g. trying to pass a match, or pass a pass), just return false or ignore
            return false;
        }

        // Insert the action
        $sql = "INSERT INTO {$this->table} (seeker_id, target_seeker_id, action) 
                VALUES (:seeker_id, :target_id, :action)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':target_id', $targetId, PDO::PARAM_INT);
        $stmt->bindValue(':action', $action);
        
        if (!$stmt->execute()) {
            return false;
        }

        // If this is a match, check for mutual match
        $isMutual = false;
        if ($action === 'match') {
            $isMutual = $this->checkAndUpdateMutualMatch($seekerId, $targetId);
        }

        return [
            'success' => true,
            'is_mutual' => $isMutual
        ];
    }

    /**
     * Check if both users matched each other and update is_mutual flag
     * @param int $seekerId
     * @param int $targetId
     * @return bool True if mutual match
     */
    private function checkAndUpdateMutualMatch($seekerId, $targetId) {
        // Check if target also matched seeker
        $sql = "SELECT match_id FROM {$this->table} 
                WHERE seeker_id = :target_id 
                  AND target_seeker_id = :seeker_id 
                  AND action = 'match'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':target_id', $targetId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // It's a mutual match! Update both records
            // Note: Using unique parameter names because emulate_prepares is false
            $sql = "UPDATE {$this->table} 
                    SET is_mutual = 1 
                    WHERE (seeker_id = :s1 AND target_seeker_id = :t1)
                       OR (seeker_id = :s2 AND target_seeker_id = :t2)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':s1', $seekerId, PDO::PARAM_INT);
            $stmt->bindValue(':t1', $targetId, PDO::PARAM_INT);
            $stmt->bindValue(':s2', $targetId, PDO::PARAM_INT);
            $stmt->bindValue(':t2', $seekerId, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        }
        
        return false;
    }

    /**
     * Get users who matched with the current user (pending matches)
     * @param int $seekerId
     * @return array
     */
    public function getPendingMatches($seekerId) {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.profile_photo, 
                       sp.occupation, rm.created_at
                FROM {$this->table} rm
                INNER JOIN users u ON rm.seeker_id = u.user_id
                LEFT JOIN seeker_profiles sp ON u.user_id = sp.user_id
                WHERE rm.target_seeker_id = :seeker_id 
                  AND rm.action = 'match'
                  AND rm.is_mutual = 0
                ORDER BY rm.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get mutual matches (both users matched each other)
     * @param int $seekerId
     * @return array
     */
    public function getMutualMatches($seekerId) {
        $sql = "SELECT 
                    CASE 
                        WHEN rm.seeker_id = :seeker_id1 THEN rm.target_seeker_id
                        ELSE rm.seeker_id
                    END as match_user_id,
                    u.first_name, u.last_name, u.profile_photo, u.role,
                    sp.occupation, MAX(rm.created_at) as created_at
                FROM {$this->table} rm
                INNER JOIN users u ON (
                    CASE 
                        WHEN rm.seeker_id = :seeker_id2 THEN rm.target_seeker_id
                        ELSE rm.seeker_id
                    END
                ) = u.user_id
                LEFT JOIN seeker_profiles sp ON u.user_id = sp.user_id
                WHERE (rm.seeker_id = :seeker_id3 OR rm.target_seeker_id = :seeker_id4)
                  AND rm.is_mutual = 1
                GROUP BY match_user_id
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id1', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id2', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id3', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id4', $seekerId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get seekers that haven't been rated yet (for swiping)
     * @param int $seekerId Current user ID
     * @param int $limit Number of profiles to fetch
     * @param string|null $gender Filter by gender (optional)
     * @return array
     */
    public function getUnseenProfiles($seekerId, $limit = 10, $gender = null) {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.profile_photo, u.bio, u.gender,
                       sp.occupation, sp.budget, sp.move_in_date,
                       sp.sleep_schedule, sp.social_level, sp.cleanliness,
                       sp.preferences
                FROM users u
                LEFT JOIN seeker_profiles sp ON u.user_id = sp.user_id
                WHERE u.role = 'room_seeker'
                  AND u.user_id != :seeker_id
                  AND u.is_active = 1
                  AND u.user_id NOT IN (
                      SELECT target_seeker_id 
                      FROM {$this->table} 
                      WHERE seeker_id = :seeker_id2
                  )";
        
        // Add gender filter if specified and is male/female
        if ($gender && in_array(strtolower($gender), ['male', 'female'])) {
            $sql .= " AND u.gender = :gender";
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id2', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        if ($gender && in_array(strtolower($gender), ['male', 'female'])) {
            $stmt->bindValue(':gender', $gender);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get count of unread match notifications
     * @param int $seekerId
     * @return int
     */
    public function getUnreadMatchCount($seekerId) {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE target_seeker_id = :seeker_id
                  AND action = 'match'
                  AND is_notification_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    /**
     * Mark match notifications as read
     * @param int $seekerId
     * @return bool
     */
    public function markNotificationsRead($seekerId) {
        $sql = "UPDATE {$this->table}
                SET is_notification_read = 1
                WHERE target_seeker_id = :seeker_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
