<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../../views/seeker/dashboard.php');
    exit;
}

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: forgot_password.php');
    exit;
}

// Verify token
require_once __DIR__ . '/../../controllers/PasswordResetController.php';
$controller = new PasswordResetController();
$tokenData = $controller->verifyToken($token);

if (!$tokenData) {
    $_SESSION['error'] = 'Invalid or expired reset link';
    header('Location: forgot_password.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RoomFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide@latest/dist/lucide.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-family: 'Pacifico', cursive;
            font-size: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .logo p {
            color: #6b7280;
            font-size: 14px;
        }

        .welcome-back {
            margin-bottom: 10px;
        }

        .welcome-back h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }

        .welcome-back p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 45px 12px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-strength {
            margin-top: 8px;
            display: flex;
            gap: 5px;
        }

        .strength-bar {
            height: 4px;
            flex: 1;
            background: #e5e7eb;
            border-radius: 2px;
            transition: background 0.3s;
        }

        .strength-bar.active {
            background: #10b981;
        }

        .strength-bar.medium {
            background: #f59e0b;
        }

        .strength-bar.weak {
            background: #ef4444;
        }

        .strength-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .requirements {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .requirements h4 {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }

        .requirements ul {
            list-style: none;
            padding: 0;
        }

        .requirements li {
            font-size: 12px;
            color: #6b7280;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .requirements li.valid {
            color: #10b981;
        }

        .requirements li i {
            width: 14px;
            height: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>RoomFinder</h1>
            <p>Password Reset</p>
        </div>

        <div class="welcome-back">
            <h2>Create New Password</h2>
            <p class="subtitle">Hi <?php echo htmlspecialchars($tokenData['first_name']); ?>! Please create a strong password for your account.</p>
        </div>

        <div class="requirements">
            <h4>Password Requirements:</h4>
            <ul>
                <li id="req-length"><i data-lucide="circle"></i> At least 6 characters</li>
                <li id="req-match"><i data-lucide="circle"></i> Passwords match</li>
            </ul>
        </div>

        <form id="resetPasswordForm">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <i data-lucide="lock" class="input-icon" style="width: 18px; height: 18px;"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter new password" 
                        required
                    >
                    <i data-lucide="eye" class="toggle-password" id="togglePassword" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="password-strength">
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-wrapper">
                    <i data-lucide="lock-keyhole" class="input-icon" style="width: 18px; height: 18px;"></i>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Confirm password" 
                        required
                    >
                    <i data-lucide="eye" class="toggle-password" id="toggleConfirmPassword" style="width: 18px; height: 18px;"></i>
                </div>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                Reset Password
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        lucide.createIcons();

        const form = document.getElementById('resetPasswordForm');
        const submitBtn = document.getElementById('submitBtn');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const strengthBars = document.querySelectorAll('.strength-bar');

        // Toggle password visibility
        [togglePassword, toggleConfirmPassword].forEach((toggle, index) => {
            toggle.addEventListener('click', function() {
                const input = index === 0 ? passwordInput : confirmPasswordInput;
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                this.setAttribute('data-lucide', type === 'password' ? 'eye' : 'eye-off');
                lucide.createIcons();
            });
        });

        // Password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            strengthBars.forEach((bar, index) => {
                bar.classList.remove('active', 'weak', 'medium');
                if (index < strength) {
                    bar.classList.add(strength < 2 ? 'weak' : strength < 3 ? 'medium' : 'active');
                }
            });

            validateRequirements();
        });

        confirmPasswordInput.addEventListener('input', validateRequirements);

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            return strength;
        }

        function validateRequirements() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Length requirement
            const lengthReq = document.getElementById('req-length');
            if (password.length >= 6) {
                lengthReq.classList.add('valid');
                lengthReq.querySelector('i').setAttribute('data-lucide', 'check-circle');
            } else {
                lengthReq.classList.remove('valid');
                lengthReq.querySelector('i').setAttribute('data-lucide', 'circle');
            }

            // Match requirement
            const matchReq = document.getElementById('req-match');
            if (password && confirmPassword && password === confirmPassword) {
                matchReq.classList.add('valid');
                matchReq.querySelector('i').setAttribute('data-lucide', 'check-circle');
            } else {
                matchReq.classList.remove('valid');
                matchReq.querySelector('i').setAttribute('data-lucide', 'circle');
            }

            lucide.createIcons();
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Resetting...';

            try {
                const formData = new FormData(form);
                
                const response = await fetch('../../controllers/PasswordResetController.php?action=reset', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '../../../login.php';
                    }, 2000);
                } else {
                    showToast(data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Reset Password';
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to reset password. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reset Password';
            }
        });

        function showToast(message, type) {
            Toastify({
                text: message,
                duration: 4000,
                gravity: 'top',
                position: 'right',
                style: {
                    background: type === 'success' ? '#10b981' : '#ef4444',
                    borderRadius: '10px',
                    fontFamily: 'Poppins, sans-serif'
                }
            }).showToast();
        }
    </script>
</body>
</html>
