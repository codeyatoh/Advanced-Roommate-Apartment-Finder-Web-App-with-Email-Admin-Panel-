<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Listing.php';
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/SavedListing.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/ActivityLog.php';

class DashboardController {
    private $userModel;
    private $listingModel;
    private $messageModel;
    private $appointmentModel;
    private $savedListingModel;
    private $matchModel;
    private $activityLogModel;

    public function __construct() {
        $this->userModel = new User();
        $this->listingModel = new Listing();
        $this->messageModel = new Message();
        $this->appointmentModel = new Appointment();
        $this->savedListingModel = new SavedListing();
        $this->matchModel = new RoommateMatch();
        $this->activityLogModel = new ActivityLog();
    }

    public function getDashboardData($userId) {
        // Fetch stats
        $unreadMessages = $this->messageModel->getUnreadCount($userId);
        $upcomingAppointments = $this->appointmentModel->getUpcoming($userId, 'seeker');
        $savedCount = $this->savedListingModel->getCount($userId);
        
        // Matches count (approximate based on mutual matches)
        $matches = $this->matchModel->getMutualMatches($userId);
        $matchCount = count($matches);

        // Fetch content
        $recommendedListings = $this->listingModel->getAvailable(2); // Get 2 listings
        $savedListings = $this->savedListingModel->getSavedListings($userId);
        
        // Get REAL recent activities from various sources
        $recentActivity = $this->getRealActivities($userId);

        return [
            'stats' => [
                'unread_messages' => $unreadMessages,
                'upcoming_appointments' => count($upcomingAppointments),
                'saved_rooms' => $savedCount,
                'matches' => $matchCount
            ],
            'upcoming_appointments' => $upcomingAppointments,
            'recommended_listings' => $recommendedListings,
            'saved_listings' => $savedListings,
            'recent_activity' => $recentActivity,
            'matches_list' => array_slice($matches, 0, 3)
        ];
    }

    private function getRealActivities($userId) {
        require_once __DIR__ . '/../../config/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        $activities = [];
        
        // 1. Appointments (booked, confirmed, completed, cancelled)
        $sql = "SELECT 'appointment' as type, a.appointment_id as id, a.status, 
                       a.created_at, a.updated_at, l.title as listing_title, a.appointment_date, a.appointment_time
                FROM appointments a
                LEFT JOIN listings l ON a.listing_id = l.listing_id
                WHERE a.seeker_id = :user_id
                ORDER BY a.created_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $action = '';
            $icon = 'calendar';
            switch($row['status']) {
                case 'pending': $action = 'Requested viewing'; break;
                case 'confirmed': $action = 'Viewing confirmed'; break;
                case 'completed': $action = 'Completed viewing'; $icon = 'check-circle'; break;
                case 'cancelled': $action = 'Cancelled viewing'; $icon = 'x-circle'; break;
                case 'declined': $action = 'Viewing declined'; $icon = 'x-circle'; break;
            }
            $activities[] = [
                'action' => $action,
                'description' => $action . ' for "' . ($row['listing_title'] ?? 'Listing') . '"',
                'created_at' => $row['updated_at'] ?? $row['created_at'],
                'icon' => $icon,
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/appointments.php'
            ];
        }
        
        // 2. Saved listings
        $sql = "SELECT 'saved' as type, sl.created_at, l.title as listing_title, l.listing_id
                FROM saved_listings sl
                LEFT JOIN listings l ON sl.listing_id = l.listing_id
                WHERE sl.user_id = :user_id
                ORDER BY sl.created_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $activities[] = [
                'action' => 'Saved listing',
                'description' => 'Saved "' . ($row['listing_title'] ?? 'Listing') . '"',
                'created_at' => $row['created_at'],
                'icon' => 'heart',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/browse_rooms.php'
            ];
        }
        
        // 3. Payment submissions
        $sql = "SELECT 'payment' as type, p.payment_id as id, p.created_at, p.status, l.title as listing_title
                FROM payments p
                LEFT JOIN rentals r ON p.rental_id = r.rental_id
                LEFT JOIN listings l ON r.listing_id = l.listing_id
                WHERE p.user_id = :user_id
                ORDER BY p.created_at DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $action = $row['status'] === 'completed' ? 'Payment confirmed' : 'Submitted payment proof';
            $activities[] = [
                'action' => $action,
                'description' => $action . ' for "' . ($row['listing_title'] ?? 'Rental') . '"',
                'created_at' => $row['created_at'],
                'icon' => 'credit-card',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php'
            ];
        }
        
        // 4. Roommate matches
        $sql = "SELECT 'match' as type, rm.match_id as id, rm.created_at, 
                       u.first_name, u.last_name
                FROM roommate_matches rm
                LEFT JOIN users u ON (CASE WHEN rm.seeker_id = :user_id THEN rm.target_seeker_id ELSE rm.seeker_id END) = u.user_id
                WHERE (rm.seeker_id = :user_id_2 OR rm.target_seeker_id = :user_id_3)
                AND rm.is_mutual = 1
                ORDER BY rm.created_at DESC LIMIT 3";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':user_id_2' => $userId,
            ':user_id_3' => $userId
        ]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $activities[] = [
                'action' => 'New match',
                'description' => 'Matched with ' . trim($name),
                'created_at' => $row['created_at'],
                'icon' => 'users',
                'link' => '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/roommate_finder.php'
            ];
        }
        
        // Sort all activities by created_at and limit to 5
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, 5);
    }
}
