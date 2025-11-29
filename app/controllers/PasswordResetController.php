<?php
require_once __DIR__ . '/../config/database.php';

class PasswordResetController {
    private $db;
    private $conn;
    
    public function __construct() {
        // Set timezone to match MySQL server
        date_default_timezone_set('Asia/Manila');
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Generate and send OTP code
     */
    public function sendOTP() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            return;
        }
        
        // Check if user exists
        $sql = "SELECT user_id, first_name, last_name, email FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            // Don't reveal if email exists for security
            echo json_encode(['success' => true, 'message' => 'If your email is registered, you will receive an OTP code']);
            return;
        }
        
        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Invalidate old tokens for this user
        $sql = "UPDATE password_reset_tokens SET used = 1 WHERE user_id = :user_id AND used = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user['user_id']]);
        
        // Save new OTP
        $sql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user['user_id'],
            ':token' => $otp,
            ':expires_at' => $expiresAt
        ]);
        
        // Create pending notification log
        $notificationId = null;
        try {
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification();
            $notificationId = $notificationModel->create([
                'user_id' => $user['user_id'],
                'type' => 'email',
                'title' => 'Password Reset OTP',
                'message' => 'Your OTP code is ' . $otp,
                'related_id' => null,
                'related_user_id' => null,
                'status' => 'pending'
            ]);
        } catch (Exception $e) {
            // Log error but continue with OTP sending
            error_log("Failed to create notification log: " . $e->getMessage());
        }
        
        // Return data for EmailJS
        echo json_encode([
            'success' => true,
            'message' => 'OTP code has been sent to your email',
            'notification_id' => $notificationId,
            'emailData' => [
                'to_email' => $user['email'],
                'to_name' => $user['first_name'] . ' ' . $user['last_name'],
                'otp_code' => $otp
            ]
        ]);
    }
    
    /**
     * Verify OTP code
     */
    public function verifyOTP() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $otp = $_POST['otp'] ?? '';
        
        if (!$email || !$otp) {
            echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
            return;
        }
        
        // Verify OTP
        $sql = "SELECT prt.*, u.email, u.first_name 
                FROM password_reset_tokens prt
                JOIN users u ON prt.user_id = u.user_id
                WHERE u.email = :email 
                AND prt.token = :otp 
                AND prt.used = 0 
                AND prt.expires_at > NOW()";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':otp' => $otp
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code']);
        }
    }
    
    /**
     * Reset password with valid OTP
     */
    public function resetPassword() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $otp = $_POST['otp'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($email) || empty($otp) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            return;
        }
        
        if (strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            return;
        }
        
        // Verify OTP again
        $sql = "SELECT prt.*, u.user_id 
                FROM password_reset_tokens prt
                JOIN users u ON prt.user_id = u.user_id
                WHERE u.email = :email 
                AND prt.token = :otp 
                AND prt.used = 0 
                AND prt.expires_at > NOW()";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':otp' => $otp
        ]);
        
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tokenData) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code']);
            return;
        }
        
        // Update password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':password_hash' => $passwordHash,
            ':user_id' => $tokenData['user_id']
        ]);
        
        if ($success) {
            // Mark OTP as used
            $sql = "UPDATE password_reset_tokens SET used = 1 WHERE token = :otp";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':otp' => $otp]);
            
            echo json_encode(['success' => true, 'message' => 'Password successfully reset! You can now login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
        }
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new PasswordResetController();
    
    switch ($_GET['action']) {
        case 'send_otp':
            $controller->sendOTP();
            break;
        case 'verify_otp':
            $controller->verifyOTP();
            break;
        case 'reset':
            $controller->resetPassword();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}



