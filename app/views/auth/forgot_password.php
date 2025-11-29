<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../../views/seeker/dashboard.php');
    exit;
}

// Load EmailJS config
$emailJsConfig = require_once __DIR__ . '/../../../config/emailjs.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset your RoomFinder password">
    <title>Forgot Password - RoomFinder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/login.module.css">
    
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-back-link">
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php" class="auth-back-link-btn">
                    ← Back to Login
                </a>
            </div>

            <!-- Header -->
            <div class="auth-header">
                <h1 class="auth-title">Forgot Password?</h1>
                <p class="auth-subtitle" id="subtitle">No worries! Enter your email and we'll send you an OTP code.</p>
            </div>

            <!-- Step 1: Email Input -->
            <div class="auth-card" id="emailStep">
                <form id="emailForm" class="auth-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="your@email.com" 
                                required
                            >
                            <i data-lucide="mail" class="form-input-icon"></i>
                        </div>
                    </div>

                    <div style="background-color: var(--color-info-bg, #eff6ff); border-left: 4px solid var(--color-info, #3b82f6); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                        <p style="color: var(--color-info-dark, #1e40af); font-size: 0.875rem; line-height: 1.5; margin: 0;">
                            💡 The OTP code will be valid for 15 minutes. Check your inbox and spam folder.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" id="sendOtpBtn" style="width: 100%;">
                        Send OTP
                    </button>
                </form>
            </div>

            <!-- Step 2: OTP Verification + Password Reset -->
            <div class="auth-card" id="otpStep" style="display: none;">
                <form id="otpForm" class="auth-form">
                    <!-- OTP Input -->
                    <div class="form-group">
                        <label for="otp" class="form-label">Enter OTP Code</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="text" 
                                id="otp" 
                                name="otp" 
                                class="form-input" 
                                placeholder="000000" 
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                style="letter-spacing: 8px; font-size: 24px; text-align: center; font-family: 'Courier New', monospace;"
                            >
                            <i data-lucide="key" class="form-input-icon"></i>
                        </div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                            Enter the 6-digit code sent to <strong id="userEmail"></strong>
                        </p>
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Create a new password" 
                                required
                            >
                            <i data-lucide="lock" class="form-input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i data-lucide="eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                class="form-input" 
                                placeholder="Confirm your password" 
                                required
                            >
                            <i data-lucide="lock-keyhole" class="form-input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i data-lucide="eye" id="confirm_password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" id="resetBtn" style="width: 100%;">
                        Reset Password
                    </button>

                    <button type="button" class="btn btn-secondary btn-lg" onclick="backToEmail()" style="width: 100%; margin-top: 0.5rem;">
                        ← Use Different Email
                    </button>
                </form>
            </div>

            <!-- Sign In Link -->
            <p class="auth-footer">
                Remember your password?
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php">
                    Sign in
                </a>
            </p>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/js/toast-helper.js"></script>
    
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    
    <script>
        // Initialize EmailJS
        emailjs.init('<?php echo $emailJsConfig['public_key']; ?>');

        const emailForm = document.getElementById('emailForm');
        const otpForm = document.getElementById('otpForm');
        const emailStep = document.getElementById('emailStep');
        const otpStep = document.getElementById('otpStep');
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const resetBtn = document.getElementById('resetBtn');
        const emailInput = document.getElementById('email');
        const otpInput = document.getElementById('otp');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const userEmailSpan = document.getElementById('userEmail');

        let userEmail = '';

        // Step 1: Send OTP
        emailForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            userEmail = emailInput.value.trim();

            if (!userEmail) {
                showToast('Please enter your email address', 'error');
                return;
            }

            const originalBtnText = sendOtpBtn.innerHTML;
            sendOtpBtn.innerHTML = 'Sending OTP...';
            sendOtpBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('email', userEmail);

                const response = await fetch('../../controllers/PasswordResetController.php?action=send_otp', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success && data.emailData) {
                    try {
                        // Send OTP via EmailJS
                        await emailjs.send(
                            '<?php echo $emailJsConfig['service_id']; ?>',
                            '<?php echo $emailJsConfig['otp_template_id']; ?>',
                            data.emailData
                        );

                        // Update notification status to sent
                        if (data.notification_id) {
                            fetch('../../controllers/NotificationController.php?action=updateStatus', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    notification_id: data.notification_id,
                                    status: 'sent'
                                })
                            });
                        }

                        showToast('OTP code sent to your email!', 'success');
                        
                        // Switch to OTP step
                        emailStep.style.display = 'none';
                        otpStep.style.display = 'block';
                        userEmailSpan.textContent = userEmail;
                        document.querySelector('.auth-title').textContent = 'Verify OTP';
                        document.getElementById('subtitle').textContent = 'Enter the code we sent to your email.';
                    } catch (emailError) {
                        console.error('EmailJS Error:', emailError);
                        
                        // Update notification status to failed
                        if (data.notification_id) {
                            fetch('../../controllers/NotificationController.php?action=updateStatus', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    notification_id: data.notification_id,
                                    status: 'failed'
                                })
                            });
                        }
                        
                        showToast('Failed to send email. Please try again.', 'error');
                    }
                } else {
                    showToast(data.message || 'Failed to send OTP', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to send OTP. Please try again.', 'error');
            } finally {
                sendOtpBtn.innerHTML = originalBtnText;
                sendOtpBtn.disabled = false;
            }
        });

        // Step 2: Reset Password with OTP
        otpForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const otp = otpInput.value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (!otp || otp.length !== 6) {
                showToast('Please enter a valid 6-digit OTP', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }

            if (password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }

            const originalBtnText = resetBtn.innerHTML;
            resetBtn.innerHTML = 'Resetting...';
            resetBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('email', userEmail);
                formData.append('otp', otp);
                formData.append('password', password);
                formData.append('confirm_password', confirmPassword);

                const response = await fetch('../../controllers/PasswordResetController.php?action=reset', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php';
                    }, 2000);
                } else {
                    showToast(data.message, 'error');
                    resetBtn.innerHTML = originalBtnText;
                    resetBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to reset password. Please try again.', 'error');
                resetBtn.innerHTML = originalBtnText;
                resetBtn.disabled = false;
            }
        });

        // Back to email step
        function backToEmail() {
            emailStep.style.display = 'block';
            otpStep.style.display = 'none';
            document.querySelector('.auth-title').textContent = 'Forgot Password?';
            document.getElementById('subtitle').textContent = 'No worries! Enter your email and we\'ll send you an OTP code.';
            otpInput.value = '';
            passwordInput.value = '';
            confirmPasswordInput.value = '';
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }

        // Auto-format OTP input (only numbers)
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>
