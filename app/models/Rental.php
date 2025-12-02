<?php
require_once __DIR__ . '/BaseModel.php';

class Rental extends BaseModel {
    protected $table = 'rentals';

    protected function getPrimaryKey() {
        return 'rental_id';
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (listing_id, tenant_id, landlord_id, start_date, rent_amount, status) 
                VALUES (:listing_id, :tenant_id, :landlord_id, :start_date, :rent_amount, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $data['listing_id']);
        $stmt->bindValue(':tenant_id', $data['tenant_id']);
        $stmt->bindValue(':landlord_id', $data['landlord_id']);
        $stmt->bindValue(':start_date', $data['start_date']);
        $stmt->bindValue(':rent_amount', $data['rent_amount']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getByTenant($tenantId) {
        $sql = "SELECT r.*, l.title as listing_title, l.location 
                FROM {$this->table} r 
                JOIN listings l ON r.listing_id = l.listing_id 
                WHERE r.tenant_id = :tenant_id 
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getUnseenCount($landlordId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE landlord_id = :landlord_id AND is_seen = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function markAllAsSeen($landlordId) {
        $sql = "UPDATE {$this->table} SET is_seen = 1 WHERE landlord_id = :landlord_id AND is_seen = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId);
        return $stmt->execute();
    }
}
