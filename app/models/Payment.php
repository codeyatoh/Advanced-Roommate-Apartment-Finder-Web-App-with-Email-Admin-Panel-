<?php
require_once __DIR__ . '/BaseModel.php';

class Payment extends BaseModel {
    protected $table = 'payments';

    protected function getPrimaryKey() {
        return 'payment_id';
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (rental_id, user_id, amount, method, transaction_id, status, proof_image) 
                VALUES (:rental_id, :user_id, :amount, :method, :transaction_id, :status, :proof_image)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':rental_id', $data['rental_id'] ?? null);
        $stmt->bindValue(':user_id', $data['user_id']);
        $stmt->bindValue(':amount', $data['amount']);
        $stmt->bindValue(':method', $data['method']);
        $stmt->bindValue(':transaction_id', $data['transaction_id'] ?? null);
        $stmt->bindValue(':status', $data['status'] ?? 'pending');
        $stmt->bindValue(':proof_image', $data['proof_image'] ?? null);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
