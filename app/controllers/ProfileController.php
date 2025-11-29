<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors as HTML
ini_set('log_errors', 1);

try {
    session_start();
    header('Content-Type: application/json');
    
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../models/SeekerProfile.php';
    require_once __DIR__ . '/../models/LandlordProfile.php';
    
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

$userModel = new User();
$seekerProfileModel = new SeekerProfile();
$landlordProfileModel = new LandlordProfile();

// Handle GET request for profile completion
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_completion') {
    $completion = $userModel->getProfileCompletion($userId);
    echo json_encode(['completion' => $completion['percentage']]);
    exit;
}

// Handle POST request to save profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_GET['action'] ?? null;

        // Handle password change if provided
        if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $currentUser = $userModel->getById($userId);
            
            // Verify current password
            if (!password_verify($_POST['current_password'], $currentUser['password_hash'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ]);
                exit;
            }
            
            // Validate new password
            if (strlen($_POST['new_password']) < 8) {
                echo json_encode([
                    'success' => false,
                    'message' => 'New password must be at least 8 characters'
                ]);
                exit;
            }
            
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'New passwords do not match'
                ]);
                exit;
            }
            
            // Update password
            $userModel->update($userId, [
                'password_hash' => password_hash($_POST['new_password'], PASSWORD_DEFAULT)
            ]);
        }

        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
                $profilePhotoUrl = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/uploads/profiles/' . $filename;
            } else {
                $profilePhotoUrl = null;
            }
        } else {
            $profilePhotoUrl = null;
        }

        // Prepare user data
        $userData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'gender' => $_POST['gender'] ?? null
        ];
        
        // Add profile photo if uploaded
        if ($profilePhotoUrl !== null) {
            $userData['profile_photo'] = $profilePhotoUrl;
        }
        
        // Update user basic information
        $userModel->update($userId, $userData);



        // Handle Payment Methods (Landlord only)
        if (isset($_POST['payment_methods']) && is_array($_POST['payment_methods'])) {
            try {
                // Structure payment methods with type field
                $paymentMethodsArray = [];
                
                if (isset($_POST['payment_methods']['bank'])) {
                    $paymentMethodsArray[] = array_merge(
                        ['type' => 'bank'],
                        $_POST['payment_methods']['bank']
                    );
                }
                
                if (isset($_POST['payment_methods']['ewallet'])) {
                    $paymentMethodsArray[] = array_merge(
                        ['type' => 'ewallet'],
                        $_POST['payment_methods']['ewallet']
                    );
                }
                
                // Convert to JSON only if we have data
                if (!empty($paymentMethodsArray)) {
                    $paymentMethodsJson = json_encode($paymentMethodsArray);
                    
                    // Update user's payment_methods column
                    $result = $userModel->update($userId, [
                        'payment_methods' => $paymentMethodsJson
                    ]);
                    
                    if (!$result) {
                        throw new Exception('Failed to update payment methods');
                    }
                }
            } catch (Exception $e) {
                error_log('Payment methods save error: ' . $e->getMessage());
                // Continue execution - don't fail the entire save
            }
        }

        // Check if this is a landlord update
        if (isset($_POST['company_name']) || isset($_POST['business_license'])) {
             $landlordData = [
                'company_name' => $_POST['company_name'] ?? null,
                'business_license' => $_POST['business_license'] ?? null,
                'office_address' => $_POST['office_address'] ?? null,
                'website_url' => $_POST['website_url'] ?? null,
                'description' => $_POST['description'] ?? null,
                'operating_hours' => $_POST['operating_hours'] ?? null,
                'verification_documents' => null,
            ];

            $landlordProfileModel->createOrUpdate($userId, $landlordData);

            echo json_encode([
                'success' => true,
                'message' => 'Landlord profile updated successfully',
            ]);
            exit;
        } else {
            // Seeker profile update (existing behaviour)
            $profileData = [
                'user_id' => $userId,
                'occupation' => $_POST['occupation'] ?? '',
                'preferred_location' => $_POST['location'] ?? '',
                'budget' => !empty($_POST['budget']) ? (float)$_POST['budget'] : null,
                'move_in_date' => !empty($_POST['move_in_date']) ? $_POST['move_in_date'] : null,
                'sleep_schedule' => $_POST['sleep_schedule'] ?? null,
                'social_level' => $_POST['social_level'] ?? null,
                'guests_preference' => $_POST['guests'] ?? null,
                'cleanliness' => $_POST['cleanliness'] ?? null,
                'work_schedule' => $_POST['work_schedule'] ?? null,
                'noise_level' => $_POST['noise_level'] ?? null,
                'preferences' => isset($_POST['preferences']) ? json_encode($_POST['preferences']) : '[]'
            ];

            // Check if profile exists
            $existingProfile = $seekerProfileModel->getByUserId($userId);
            
            if ($existingProfile) {
                // Update existing profile
                $seekerProfileModel->update($existingProfile['profile_id'], $profileData);
            } else {
                // Create new profile
                $seekerProfileModel->create($profileData);
            }

            // Get updated profile completion
            $completion = $userModel->getProfileCompletion($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'completion' => $completion['percentage']
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating profile: ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);

} catch (Throwable $e) {
    // Catch all errors including fatal errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
