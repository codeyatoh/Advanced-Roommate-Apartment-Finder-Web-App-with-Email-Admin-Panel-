<?php
require_once __DIR__ . '/BaseModel.php';

class LandlordProfile extends BaseModel {
    protected $table = 'landlord_profiles';

    /**
     * Get landlord profile by user ID
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
     * Create landlord profile
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (user_id, company_name, business_license, office_address, 
                 website_url, description, operating_hours, verification_documents, 
                 created_at, updated_at) 
                VALUES 
                (:user_id, :company_name, :business_license, :office_address, 
                 :website_url, :description, :operating_hours, :verification_documents, 
                 CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':company_name', $data['company_name'] ?? null);
        $stmt->bindValue(':business_license', $data['business_license'] ?? null);
        $stmt->bindValue(':office_address', $data['office_address'] ?? null);
        $stmt->bindValue(':website_url', $data['website_url'] ?? null);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':operating_hours', $data['operating_hours'] ?? null);
        $stmt->bindValue(':verification_documents', $data['verification_documents'] ?? null);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update landlord profile
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateByUserId($userId, $data) {
        $sql = "UPDATE {$this->table} 
                SET company_name = :company_name,
                    business_license = :business_license,
                    office_address = :office_address,
                    website_url = :website_url,
                    description = :description,
                    operating_hours = :operating_hours,
                    verification_documents = :verification_documents,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':company_name', $data['company_name'] ?? null);
        $stmt->bindValue(':business_license', $data['business_license'] ?? null);
        $stmt->bindValue(':office_address', $data['office_address'] ?? null);
        $stmt->bindValue(':website_url', $data['website_url'] ?? null);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':operating_hours', $data['operating_hours'] ?? null);
        $stmt->bindValue(':verification_documents', $data['verification_documents'] ?? null);
        
        return $stmt->execute();
    }

    /**
     * Create or update landlord profile
     * @param int $userId
     * @param array $data
     * @return int|bool
     */
    public function createOrUpdate($userId, $data) {
        $existing = $this->getByUserId($userId);
        
        if ($existing) {
            return $this->updateByUserId($userId, $data);
        } else {
            $data['user_id'] = $userId;
            return $this->create($data);
        }
    }

    /**
     * Delete landlord profile
     * @param int $userId
     * @return bool
     */
    public function deleteByUserId($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
