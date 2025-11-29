<?php
require_once __DIR__ . '/../../models/Listing.php';
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/ActivityLog.php';
require_once __DIR__ . '/../../config/Database.php';

class DashboardController {
    private $listingModel;
    private $messageModel;
    private $appointmentModel;
    private $userModel;
    private $activityLogModel;

    public function __construct() {
        $this->listingModel = new Listing();
        $this->messageModel = new Message();
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    public function getDashboardData($landlordId) {
        // Fetch stats
        $landlordStats = $this->listingModel->getLandlordStats($landlordId);
        $pendingAppointmentsCount = $this->appointmentModel->getPendingCount($landlordId);
        $unreadMessagesCount = $this->messageModel->getUnreadCount($landlordId);

        // Fetch content
        $pendingViewings = $this->appointmentModel->getPendingForLandlord($landlordId);
        $recentInquiries = $this->messageModel->getLandlordInquiries($landlordId);
        $recentActivity = $this->activityLogModel->getRecent($landlordId, 5);

        // Calculate performance metrics from Rentals table
        $monthlyRevenue = 0;
        $occupiedBedrooms = 0;
        $totalBedrooms = 0;
        
        // Get active rentals for this landlord with COMPLETED payments
        $db = new Database();
        $conn = $db->getConnection();
        
        // Calculate Monthly Revenue (Sum of rent from active, paid rentals)
        $sql = "SELECT SUM(r.rent_amount) as total_revenue
                FROM rentals r 
                WHERE r.landlord_id = :landlord_id 
                AND r.status = 'active'
                AND EXISTS (
                    SELECT 1 FROM payments p 
                    WHERE p.rental_id = r.rental_id 
                    AND p.status = 'completed'
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $monthlyRevenue = $result['total_revenue'] ?? 0;

        // Calculate Total Bedrooms (Sum of bedrooms from all landlord listings)
        $sql = "SELECT COALESCE(SUM(bedrooms), 0) as total_bedrooms
                FROM listings
                WHERE landlord_id = :landlord_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalBedrooms = $result['total_bedrooms'] ?? 0;

        // Calculate Occupied Bedrooms (Sum of current_roommates from listings with active, paid rentals)
        $sql = "SELECT COALESCE(SUM(l.current_roommates), 0) as occupied_bedrooms
                FROM listings l
                WHERE l.landlord_id = :landlord_id
                AND EXISTS (
                    SELECT 1 FROM rentals r
                    WHERE r.listing_id = l.listing_id
                    AND r.status = 'active'
                    AND EXISTS (
                        SELECT 1 FROM payments p
                        WHERE p.rental_id = r.rental_id
                        AND p.status = 'completed'
                    )
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':landlord_id', $landlordId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $occupiedBedrooms = $result['occupied_bedrooms'] ?? 0;

        // Get total listings count
        $activeListings = $this->listingModel->getByLandlord($landlordId);
        $totalListings = count($activeListings);
        
        $occupancyRate = $totalBedrooms > 0 ? round(($occupiedBedrooms / $totalBedrooms) * 100) : 0;

        return [
            'stats' => [
                'active_listings' => $landlordStats['active_listings'] ?? 0,
                'total_listings' => $landlordStats['total_listings'] ?? 0,
                'new_inquiries' => $unreadMessagesCount,
                'pending_viewings' => $pendingAppointmentsCount
            ],
            'pending_viewings' => $pendingViewings,
            'recent_inquiries' => array_slice($recentInquiries, 0, 5),
            'recent_activity' => $this->getRealLandlordActivities($landlordId),
            'performance' => [
                'monthly_revenue' => $monthlyRevenue,
                'occupancy_rate' => $occupancyRate,
                'occupied_bedrooms' => $occupiedBedrooms,
                'total_bedrooms' => $totalBedrooms
            ]
        ];
    }

    private function getRealLandlordActivities($landlordId) {
        require_once __DIR__ . '/../../config/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        $activities = [];
        
        // 1. Appointment activities (approved, declined, completed)
        $sql = "SELECT 'appointment' as type, a.appointment_id as id, a.status, a.created_at, a.updated_at,
                       l.title as listing_title, u.first_name, u.last_name
                FROM appointments a
                LEFT JOIN listings l ON a.listing_id = l.listing_id
                LEFT JOIN users u ON a.seeker_id = u.user_id
                WHERE l.landlord_id = :landlord_id
                ORDER BY a.updated_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':landlord_id' => $landlordId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tenantName = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $action = '';
            $icon = 'calendar';
            switch($row['status']) {
                case 'pending': $action = 'New viewing request'; break;
                case 'confirmed': $action = 'Approved viewing'; $icon = 'check'; break;
                case 'completed': $action = 'Completed viewing'; $icon = 'check-circle'; break;
                case 'declined': $action = 'Declined viewing'; $icon = 'x'; break;
            }
            $activities[] = [
                'action' => $action,
                'description' => $action . ' from ' . trim($tenantName) . ' for "' . ($row['listing_title'] ?? 'Listing') . '"',
                'created_at' => $row['updated_at'],
                'icon' => $icon,
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/appointments.php'
            ];
        }
        
        // 2. Payment activities
        $sql = "SELECT 'payment' as type, p.payment_id as id, p.created_at, p.status,
                       l.title as listing_title, u.first_name, u.last_name
                FROM payments p
                LEFT JOIN rentals r ON p.rental_id = r.rental_id
                LEFT JOIN listings l ON r.listing_id = l.listing_id
                LEFT JOIN users u ON p.user_id = u.user_id
                WHERE r.landlord_id = :landlord_id
                ORDER BY p.created_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':landlord_id' => $landlordId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tenantName = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $action = $row['status'] === 'completed' ? 'Payment confirmed' : 'Payment proof received';
            $icon = $row['status'] === 'completed' ? 'check-circle' : 'credit-card';
            $activities[] = [
                'action' => $action,
                'description' => $action . ' from ' . trim($tenantName) . ' for "' . ($row['listing_title'] ?? 'Rental') . '"',
                'created_at' => $row['created_at'],
                'icon' => $icon,
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php'
            ];
        }
        
        // 3. New rentals
        $sql = "SELECT 'rental' as type, r.rental_id as id, r.created_at,
                       l.title as listing_title, u.first_name, u.last_name
                FROM rentals r
                LEFT JOIN listings l ON r.listing_id = l.listing_id
                LEFT JOIN users u ON r.tenant_id = u.user_id
                WHERE r.landlord_id = :landlord_id
                ORDER BY r.created_at DESC LIMIT 3";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':landlord_id' => $landlordId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tenantName = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $activities[] = [
                'action' => 'New rental',
                'description' => 'New tenant ' . trim($tenantName) . ' for "' . ($row['listing_title'] ?? 'Listing') . '"',
                'created_at' => $row['created_at'],
                'icon' => 'home',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/rentals.php'
            ];
        }
        
        // 4. Messages received
        $sql = "SELECT 'message' as type, m.message_id as id, m.created_at,
                       u.first_name, u.last_name, l.title as listing_title
                FROM messages m
                LEFT JOIN users u ON m.sender_id = u.user_id
                LEFT JOIN listings l ON m.listing_id = l.listing_id
                WHERE m.receiver_id = :landlord_id
                ORDER BY m.created_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':landlord_id' => $landlordId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $senderName = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $activities[] = [
                'action' => 'New message',
                'description' => 'Message from ' . trim($senderName) . ($row['listing_title'] ? ' about "' . $row['listing_title'] . '"' : ''),
                'created_at' => $row['created_at'],
                'icon' => 'message-square',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/inquiries.php'
            ];
        }
        
        // Sort all activities by created_at and limit to 5
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, 5);
    }
}
