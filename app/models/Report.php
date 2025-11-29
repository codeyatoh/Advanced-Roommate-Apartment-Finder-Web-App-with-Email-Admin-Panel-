<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Report Model
 * Handles user/listing reports
 */
class Report extends BaseModel {
    protected $table = 'reports';

    /**
     * Get all reports with details
     * @param string $status Optional filter by status
     * @return array
     */
    public function getAll($conditions = [], $orderBy = 'created_at DESC') {
        $sql = "SELECT 
                    r.*,
                    CONCAT(reporter.first_name, ' ', reporter.last_name) as reporter_name,
                    CONCAT(reported_user.first_name, ' ', reported_user.last_name) as reported_user_name,
                    l.title as reported_listing_title
                FROM {$this->table} r
                INNER JOIN users reporter ON r.reporter_id = reporter.user_id
                LEFT JOIN users reported_user ON r.reported_user_id = reported_user.user_id
                LEFT JOIN listings l ON r.reported_listing_id = l.listing_id";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', array_map(fn($k) => "r.$k = :$k", array_keys($conditions)));
        }
        
        $sql .= " ORDER BY r.{$orderBy}";

        $stmt = $this->conn->prepare($sql);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Search reports with filters
     * @param array $filters
     * @return array
     */
    public function searchReports($filters = []) {
        $sql = "SELECT 
                    r.*,
                    reporter.first_name as reporter_first, reporter.last_name as reporter_last, reporter.profile_photo as reporter_photo,
                    reported_user.first_name as reported_first, reported_user.last_name as reported_last,
                    l.title as listing_title
                FROM {$this->table} r
                LEFT JOIN users reporter ON r.reporter_id = reporter.user_id
                LEFT JOIN users reported_user ON r.reported_user_id = reported_user.user_id
                LEFT JOIN listings l ON r.reported_listing_id = l.listing_id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                reporter.first_name LIKE :search1 OR 
                reporter.last_name LIKE :search2 OR 
                reported_user.first_name LIKE :search3 OR 
                reported_user.last_name LIKE :search4 OR 
                l.title LIKE :search5
            )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
            $params[':search4'] = $searchTerm;
            $params[':search5'] = $searchTerm;
        }

        if (!empty($filters['type']) && $filters['type'] !== 'All Types') {
            // Map display types to db types if needed, or assume they match
            // UI: Listing Reports, User Reports, Message Reports
            // DB: listing, user, message
            $typeMap = [
                'Listing Reports' => 'listing',
                'User Reports' => 'user',
                'Message Reports' => 'message'
            ];
            if (isset($typeMap[$filters['type']])) {
                $sql .= " AND r.report_type = :type";
                $params[':type'] = $typeMap[$filters['type']];
            }
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All Status') {
            $sql .= " AND r.status = :status";
            $params[':status'] = strtolower($filters['status']);
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get pending reports count
     * @return int
     */
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE status = 'pending'";
        
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Update report status
     * @param int $reportId
     * @param string $status
     * @return bool
     */
    public function updateStatus($reportId, $status) {
        $validStatuses = ['pending', 'resolved', 'dismissed'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update($reportId, ['status' => $status]);
    }

    /**
     * Get report statistics
     * @return array
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_reports,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reports,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_reports,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed_reports,
                    SUM(CASE WHEN report_type = 'listing' THEN 1 ELSE 0 END) as listing_reports,
                    SUM(CASE WHEN report_type = 'user' THEN 1 ELSE 0 END) as user_reports,
                    SUM(CASE WHEN report_type = 'message' THEN 1 ELSE 0 END) as message_reports
                FROM {$this->table}";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetch();
    }

    /**
     * Get reports by type
     * @param string $type
     * @return array
     */
    public function getByType($type) {
        return $this->getAll(['report_type' => $type]);
    }

    /**
     * Get pending reports
     * @param int $limit
     * @return array
     */
    public function getPending($limit = 5) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'pending' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Create a new report
     * @param array $data
     * @return bool|int
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (reporter_id, reported_user_id, reported_listing_id, report_type, category, description, status) 
                VALUES (:reporter_id, :reported_user_id, :reported_listing_id, :report_type, :category, :description, :status)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':reporter_id', $data['reporter_id'], PDO::PARAM_INT);
        $stmt->bindValue(':reported_user_id', $data['reported_user_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':reported_listing_id', $data['reported_listing_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':report_type', $data['report_type']);
        $stmt->bindValue(':category', $data['category']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':status', $data['status'] ?? 'pending');
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
}
