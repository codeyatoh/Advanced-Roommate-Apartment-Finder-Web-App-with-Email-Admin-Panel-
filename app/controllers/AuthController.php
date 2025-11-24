<?php
session_start();
require_once '../config/database.php';

class AuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function register($data) {
        $first_name = htmlspecialchars(strip_tags($data['first_name']));
        $last_name = htmlspecialchars(strip_tags($data['last_name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = htmlspecialchars(strip_tags($data['password']));
        $role = htmlspecialchars(strip_tags($data['role']));
        
        // Validate role
        $allowed_roles = ['room_seeker', 'landlord'];
        if (!in_array($role, $allowed_roles)) {
            return ['status' => 'error', 'message' => 'Invalid role selected.'];
        }

        // Check if email exists
        $query = "SELECT user_id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => 'Email already exists.'];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (:first_name, :last_name, :email, :password_hash, :role)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            // Registration successful
            return ['status' => 'success', 'message' => 'Registration successful! Please login.', 'role' => $role];
        } else {
            return ['status' => 'error', 'message' => 'Registration failed. Please try again.'];
        }
    }

    public function login($email, $password) {
        $email = htmlspecialchars(strip_tags($email));
        
        $query = "SELECT user_id, first_name, last_name, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password_hash'])) {
                if ($row['is_active'] == 0) {
                    return ['status' => 'error', 'message' => 'Your account is deactivated.'];
                }

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                $_SESSION['email'] = $row['email'];

                return ['status' => 'success', 'role' => $row['role']];
            } else {
                return ['status' => 'error', 'message' => 'Invalid password.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'User not found.'];
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php");
        exit;
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();

    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $result = $auth->register($_POST);
        echo json_encode($result);
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $result = $auth->login($_POST['email'], $_POST['password']);
        echo json_encode($result);
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new AuthController();
    $auth->logout();
}
?>
