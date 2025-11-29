<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Listing Model
 * Handles property listings and images
 */
class Listing extends BaseModel {
    protected $table = 'listings';
    protected $primaryKey = 'listing_id';

    /**
     * Get all available listings
     * @return array
     */
    public function getAvailable() {
        $sql = "SELECT l.*, 
                       (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image,
                       (l.bedrooms - l.current_roommates) as beds_available
                FROM {$this->table} l
                WHERE l.approval_status = 'approved'
                  AND l.availability_status = 'available'
                  AND l.current_roommates < l.bedrooms
                ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get listings by landlord
     * @param int $landlordId
     * @return array
     */
    public function getByLandlord($landlordId) {
        $sql = "SELECT l.*, 
                       (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} l
                WHERE l.landlord_id = :landlord_id
                ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get listing with images
     * @param int $listingId
     * @return array|false
     */
    public function getWithImages($listingId) {
        $listing = $this->getById($listingId);
        
        if (!$listing) {
            return false;
        }

        // Parse JSON fields
        if ($listing['amenities']) {
            $listing['amenities'] = json_decode($listing['amenities'], true);
        }
        if ($listing['house_rules_data']) {
            $listing['house_rules_data'] = json_decode($listing['house_rules_data'], true);
        }

        // Get images
        $sql = "SELECT * FROM listing_images WHERE listing_id = :listing_id ORDER BY is_primary DESC, image_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->execute();
        $listing['images'] = $stmt->fetchAll();

        // Get landlord info
        $sql = "SELECT user_id, first_name, last_name, email, phone, profile_photo FROM users WHERE user_id = :landlord_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $listing['landlord_id'], PDO::PARAM_INT);
        $stmt->execute();
        $listing['landlord'] = $stmt->fetch();

        return $listing;
    }

    /**
     * Create listing with images
     * @param array $listingData
     * @param array $imageUrls
     * @return int|false
     */
    public function createWithImages($listingData, $imageUrls = []) {
        // Handle JSON fields
        if (isset($listingData['amenities']) && is_array($listingData['amenities'])) {
            $listingData['amenities'] = json_encode($listingData['amenities']);
        }
        if (isset($listingData['house_rules_data']) && is_array($listingData['house_rules_data'])) {
            $listingData['house_rules_data'] = json_encode($listingData['house_rules_data']);
        }

        $listingId = $this->create($listingData);
        
        if ($listingId && !empty($imageUrls)) {
            foreach ($imageUrls as $index => $url) {
                $this->addImage($listingId, $url, $index === 0);
            }
        }

        return $listingId;
    }

    /**
     * Add image to listing
     * @param int $listingId
     * @param string $imageUrl
     * @param bool $isPrimary
     * @return bool
     */
    public function addImage($listingId, $imageUrl, $isPrimary = false) {
        $sql = "INSERT INTO listing_images (listing_id, image_url, is_primary) 
                VALUES (:listing_id, :image_url, :is_primary)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->bindValue(':image_url', $imageUrl);
        $stmt->bindValue(':is_primary', $isPrimary ? 1 : 0, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Search listings with filters
     * @param array $filters
     * @return array
     */
    public function search($filters = []) {
        $sql = "SELECT l.*, 
                    (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image,
                    CONCAT(u.first_name, ' ', u.last_name) as landlord_name,
                    (l.bedrooms - l.current_roommates) as beds_available
                FROM {$this->table} l
                LEFT JOIN users u ON l.landlord_id = u.user_id
                WHERE l.approval_status = 'approved'
                  AND l.availability_status = 'available'
                  AND l.current_roommates < l.bedrooms";
        
        $params = [];

        if (!empty($filters['min_price'])) {
            $sql .= " AND l.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND l.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND l.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['room_type'])) {
            $sql .= " AND l.room_type = :room_type";
            $params[':room_type'] = $filters['room_type'];
        }

        if (!empty($filters['bedrooms'])) {
            $sql .= " AND l.bedrooms >= :bedrooms";
            $params[':bedrooms'] = $filters['bedrooms'];
        }

        $sql .= " ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Search all listings for admin
     * @param array $filters
     * @return array
     */
    public function searchAllListings($filters = []) {
        $sql = "SELECT l.*, 
                    u.first_name, u.last_name, u.profile_photo,
                    (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} l
                LEFT JOIN users u ON l.landlord_id = u.user_id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (l.title LIKE :search1 OR l.location LIKE :search2)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All Status') {
            $sql .= " AND l.approval_status = :status";
            $params[':status'] = strtolower($filters['status']);
        }

        $sql .= " ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get listing statistics
     * @return array
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_listings,
                    SUM(CASE WHEN approval_status = 'approved' AND availability_status = 'available' THEN 1 ELSE 0 END) as available_listings,
                    SUM(CASE WHEN availability_status = 'occupied' THEN 1 ELSE 0 END) as occupied_listings,
                    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_listings,
                    AVG(price) as average_price
                FROM {$this->table}";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetch();
    }

    /**
     * Get landlord statistics
     * @param int $landlordId
     * @return array
     */
    public function getLandlordStats($landlordId) {
        $sql = "SELECT 
                    COUNT(*) as total_listings,
                    SUM(CASE WHEN approval_status = 'approved' AND availability_status = 'available' THEN 1 ELSE 0 END) as active_listings,
                    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_listings,
                    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected_listings
                FROM {$this->table}
                WHERE landlord_id = :landlord_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get landlord listings with filters
     * @param int $landlordId
     * @param array $filters
     * @return array
     */
    public function getLandlordListings($landlordId, $filters = []) {
        $sql = "SELECT l.*, 
                       (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} l
                WHERE l.landlord_id = :landlord_id";
        
        $params = [':landlord_id' => $landlordId];

        if (!empty($filters['search'])) {
            $sql .= " AND (l.title LIKE :search_title OR l.location LIKE :search_location)";
            $params[':search_title'] = '%' . $filters['search'] . '%';
            $params[':search_location'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Active') {
                $sql .= " AND l.approval_status = 'approved' AND l.availability_status = 'available'";
            } elseif ($filters['status'] === 'Rented') {
                $sql .= " AND l.availability_status IN ('occupied', 'rented')";
            } elseif ($filters['status'] === 'Pending') {
                $sql .= " AND l.approval_status = 'pending'";
            } elseif ($filters['status'] === 'Rejected') {
                $sql .= " AND l.approval_status = 'rejected'";
            }
        }

        // Sort
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'Price: Low to High':
                    $sql .= " ORDER BY l.price ASC";
                    break;
                case 'Price: High to Low':
                    $sql .= " ORDER BY l.price DESC";
                    break;
                case 'Newest':
                default:
                    $sql .= " ORDER BY l.created_at DESC";
                    break;
            }
        } else {
            $sql .= " ORDER BY l.created_at DESC";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get recent listings
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get pending listings
     * @param int $limit
     * @return array
     */
    public function getPending($limit = 5) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE approval_status = 'pending' 
                ORDER BY created_at DESC 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get pending listings count
     * @return int
     */
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE approval_status = 'pending'";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getPendingApprovals() {
        $sql = "SELECT l.*, 
                       u.first_name, u.last_name, u.profile_photo,
                       (SELECT image_url FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} l
                LEFT JOIN users u ON l.landlord_id = u.user_id
                WHERE l.approval_status = 'pending'
                ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateApprovalStatus($listingId, $status, $adminId = null, $note = null) {
        // Calculate fields in PHP to avoid repeated parameter usage in SQL
        $availabilityStatus = null;
        $shouldUpdateApprovedAt = false;

        if ($status === 'approved') {
            $availabilityStatus = 'available';
            $shouldUpdateApprovedAt = true;
        } elseif ($status === 'rejected') {
            $availabilityStatus = 'pending';
        }

        $sql = "UPDATE {$this->table}
                SET approval_status = :status,
                    approved_by = :approved_by,
                    admin_note = :admin_note,
                    updated_at = NOW()";
        
        if ($availabilityStatus) {
            $sql .= ", availability_status = :availability_status";
        }
        
        if ($shouldUpdateApprovedAt) {
            $sql .= ", approved_at = NOW()";
        }

        $sql .= " WHERE listing_id = :listing_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':approved_by', $adminId, PDO::PARAM_INT);
        $stmt->bindValue(':admin_note', $note);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        
        if ($availabilityStatus) {
            $stmt->bindValue(':availability_status', $availabilityStatus);
        }

        return $stmt->execute();
    }
    /**
     * Update listing with images
     * @param int $listingId
     * @param array $listingData
     * @param array $newImageUrls
     * @param array $existingImageIds
     * @return bool
     */
    public function updateWithImages($listingId, $listingData, $newImageUrls = [], $existingImageIds = []) {
        // Handle JSON fields
        if (isset($listingData['amenities']) && is_array($listingData['amenities'])) {
            $listingData['amenities'] = json_encode($listingData['amenities']);
        }
        if (isset($listingData['house_rules_data']) && is_array($listingData['house_rules_data'])) {
            $listingData['house_rules_data'] = json_encode($listingData['house_rules_data']);
        }

        // Update listing details
        $sql = "UPDATE {$this->table} SET 
                title = :title,
                description = :description,
                price = :price,
                security_deposit = :security_deposit,
                location = :location,
                available_from = :available_from,
                utilities_included = :utilities_included,
                room_type = :room_type,
                bedrooms = :bedrooms,
                bathrooms = :bathrooms,
                current_roommates = :current_roommates,
                amenities = :amenities,
                house_rules_data = :house_rules_data,
                approval_status = 'pending',
                availability_status = 'pending',
                updated_at = NOW()
                WHERE listing_id = :listing_id AND landlord_id = :landlord_id";

        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':title', $listingData['title']);
        $stmt->bindValue(':description', $listingData['description']);
        $stmt->bindValue(':price', $listingData['price']);
        $stmt->bindValue(':security_deposit', $listingData['security_deposit']);
        $stmt->bindValue(':location', $listingData['location']);
        $stmt->bindValue(':available_from', $listingData['available_from']);
        $stmt->bindValue(':utilities_included', $listingData['utilities_included']);
        $stmt->bindValue(':room_type', $listingData['room_type']);
        $stmt->bindValue(':bedrooms', $listingData['bedrooms']);
        $stmt->bindValue(':bathrooms', $listingData['bathrooms']);
        $stmt->bindValue(':current_roommates', $listingData['current_roommates']);
        $stmt->bindValue(':amenities', $listingData['amenities']);
        $stmt->bindValue(':house_rules_data', $listingData['house_rules_data']);
        $stmt->bindValue(':landlord_id', $listingData['landlord_id']);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            return false;
        }

        // Handle images
        // 1. Remove images not in $existingImageIds
        if (!empty($existingImageIds)) {
            $placeholders = implode(',', array_fill(0, count($existingImageIds), '?'));
            $sql = "DELETE FROM listing_images WHERE listing_id = ? AND image_id NOT IN ($placeholders)";
            $params = array_merge([$listingId], $existingImageIds);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
        } else {
            // If no existing images kept, delete all for this listing
            $sql = "DELETE FROM listing_images WHERE listing_id = :listing_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
            $stmt->execute();
        }

        // 2. Add new images
        if (!empty($newImageUrls)) {
            foreach ($newImageUrls as $url) {
                $this->addImage($listingId, $url, false); // New images are not primary by default unless logic changes
            }
        }

        // 3. Ensure at least one image is primary
        $sql = "UPDATE listing_images SET is_primary = 1 
                WHERE listing_id = :listing_id1 
                AND image_id = (SELECT min_id FROM (SELECT MIN(image_id) as min_id FROM listing_images WHERE listing_id = :listing_id2) as t)
                AND (SELECT count_primary FROM (SELECT COUNT(*) as count_primary FROM listing_images WHERE listing_id = :listing_id3 AND is_primary = 1) as t2) = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id1', $listingId, PDO::PARAM_INT);
        $stmt->bindValue(':listing_id2', $listingId, PDO::PARAM_INT);
        $stmt->bindValue(':listing_id3', $listingId, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }
    /**
     * Delete listing and its images
     * @param int $listingId
     * @return bool
     */
    public function deleteWithImages($listingId) {
        // Get images first
        $sql = "SELECT image_url FROM listing_images WHERE listing_id = :listing_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete files
        foreach ($images as $imageUrl) {
            // Convert URL to file path
            // URL: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/uploads/listings/filename.jpg
            // Path: __DIR__ . '/../../public/uploads/listings/filename.jpg'
            
            $filename = basename($imageUrl);
            $filePath = __DIR__ . '/../../public/uploads/listings/' . $filename;
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete database records (listing_images should cascade if set up, but we can be explicit if needed)
        // For now, we rely on parent::delete to remove the listing. 
        // If foreign keys are CASCADE, images go too. If not, we might need to delete them manually.
        // Let's delete images manually from DB just in case to be safe.
        $sql = "DELETE FROM listing_images WHERE listing_id = :listing_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->delete($listingId);
    }
    /**
     * Increment listing view count
     * @param int $listingId
     * @return bool
     */
    public function incrementViews($listingId) {
        $sql = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE listing_id = :listing_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':listing_id', $listingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
