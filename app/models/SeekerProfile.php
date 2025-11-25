<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * SeekerProfile Model
 * Handles room seeker profile data
 */
class SeekerProfile extends BaseModel {
    protected $table = 'seeker_profiles';

    /**
     * Override primary key name
     * @return string
     */
    protected function getPrimaryKey() {
        return 'profile_id';
    }

    /**
     * Get profile by user ID
     * @param int $userId
     * @return array|false
     */
    public function getByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update or create profile for user
     * @param int $userId
     * @param array $data
     * @return bool|int
     */
    public function updateOrCreate($userId, $data) {
        $existing = $this->getByUserId($userId);
        
        $data['user_id'] = $userId;
        
        if ($existing) {
            return $this->update($existing['profile_id'], $data);
        } else {
            return $this->create($data);
        }
    }

    /**
     * Get seekers with profiles for matching
     * @param int $excludeUserId User to exclude
     * @param array $filters Optional filters
     * @return array
     */
    public function getSeekersForMatching($excludeUserId, $filters = []) {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.profile_photo, u.bio,
                       sp.*
                FROM users u
                INNER JOIN {$this->table} sp ON u.user_id = sp.user_id
                WHERE u.role = 'room_seeker'
                  AND u.is_active = 1
                  AND u.user_id != :exclude_user_id";
        
        $params = [':exclude_user_id' => $excludeUserId];
        
        // Add optional filters
        if (!empty($filters['budget_min'])) {
            $sql .= " AND sp.budget >= :budget_min";
            $params[':budget_min'] = $filters['budget_min'];
        }
        
        if (!empty($filters['budget_max'])) {
            $sql .= " AND sp.budget <= :budget_max";
            $params[':budget_max'] = $filters['budget_max'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND sp.preferred_location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
