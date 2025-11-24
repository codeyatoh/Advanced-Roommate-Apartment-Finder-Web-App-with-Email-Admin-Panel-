<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create your RoomFinder account - Find your perfect room and roommate">
    <title>Register - RoomFinder</title>

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
        <div class="auth-container auth-container-wide">
            <!-- Header -->
            <div class="auth-header">
                <h1 class="auth-title">Join RoomFinder</h1>
                <p class="auth-subtitle">Create your account to get started</p>
            </div>

            <!-- Step 1: Role Selection -->
            <div id="role-selection" class="role-grid">
                <!-- Room Seeker Card -->
                <div class="role-card" onclick="selectRole('seeker')">
                    <div class="role-icon">
                        <i data-lucide="users"></i>
                    </div>
                    <div>
                        <h2 class="role-title">Room Seeker</h2>
                        <p class="role-description">
                            Looking for a room or roommate? Browse listings and connect with landlords.
                        </p>
                    </div>
                    <ul class="role-features">
                        <li>Browse verified listings</li>
                        <li>Find compatible roommates</li>
                        <li>Schedule viewings</li>
                        <li>Chat with landlords</li>
                    </ul>
                    <button type="button" class="btn btn-primary btn-lg" style="width: 100%;">
                        Continue as Seeker
                    </button>
                </div>

                <!-- Landlord Card -->
                <div class="role-card" onclick="selectRole('landlord')">
                    <div class="role-icon">
                        <i data-lucide="home"></i>
                    </div>
                    <div>
                        <h2 class="role-title">Landlord</h2>
                        <p class="role-description">
                            Have a room to rent? List your property and connect with verified tenants.
                        </p>
                    </div>
                    <ul class="role-features">
                        <li>List unlimited properties</li>
                        <li>Manage inquiries easily</li>
                        <li>Schedule viewings</li>
                        <li>Verified tenant profiles</li>
                    </ul>
                    <button type="button" class="btn btn-primary btn-lg" style="width: 100%;">
                        Continue as Landlord
                    </button>
                </div>
            </div>

            <!-- Step 2: Registration Form -->
            <div id="registration-form" class="auth-card" style="display: none;">
                <div class="registration-header">
                    <button type="button" class="back-button" onclick="backToRoleSelection()">
                        ← Back to role selection
                    </button>
                    <div class="registration-role-info">
                        <div class="registration-role-icon" id="role-icon-display">
                            <i data-lucide="users" id="role-icon"></i>
                        </div>
                        <div class="registration-role-text">
                            <h2 id="role-title-display">Room Seeker Account</h2>
                            <p>Complete your registration</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AuthController.php" class="auth-form" style="margin-top: 1.5rem;">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="role" id="selected-role" value="">

                    <!-- First Name & Last Name -->
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <div class="form-input-wrapper">
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    class="form-input" 
                                    placeholder="John"
                                    required
                                >
                                <i data-lucide="user" class="form-input-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <div class="form-input-wrapper">
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    class="form-input" 
                                    placeholder="Doe"
                                    required
                                >
                                <i data-lucide="user" class="form-input-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Email Input -->
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

                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Create a strong password"
                                required
                            >
                            <i data-lucide="lock" class="form-input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i data-lucide="eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="form-group">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="confirm-password" 
                                name="confirm_password" 
                                class="form-input" 
                                placeholder="Re-enter your password"
                                required
                            >
                            <i data-lucide="lock" class="form-input-icon"></i>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <label class="terms-checkbox">
                        <input type="checkbox" name="terms" required>
                        <span>
                            I agree to the
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/terms.php">Terms of Service</a>
                            and
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/privacy.php">Privacy Policy</a>
                        </span>
                    </label>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Create Account
                    </button>
                </form>

                <!-- Sign In Link -->
                <p class="auth-footer" style="margin-top: 1.5rem; font-size: 0.875rem;">
                    Already have an account?
                    <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php">
                        Sign in
                    </a>
                </p>
            </div>
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

    <!-- Registration Scripts -->
    <script>
        // Select Role Function
        function selectRole(role) {
            document.getElementById('role-selection').style.display = 'none';
            document.getElementById('registration-form').style.display = 'block';
            
            // Update role display
            const roleIcon = document.getElementById('role-icon');
            const roleTitle = document.getElementById('role-title-display');
            
            if (role === 'seeker') {
                document.getElementById('selected-role').value = 'room_seeker';
                roleIcon.setAttribute('data-lucide', 'users');
                roleTitle.textContent = 'Room Seeker Account';
            } else {
                document.getElementById('selected-role').value = 'landlord';
                roleIcon.setAttribute('data-lucide', 'home');
                roleTitle.textContent = 'Landlord Account';
            }
            
            lucide.createIcons();
        }

        // Back to Role Selection
        function backToRoleSelection() {
            document.getElementById('role-selection').style.display = 'grid';
            document.getElementById('registration-form').style.display = 'none';
            document.getElementById('selected-role').value = '';
        }

        // Password Toggle Function
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

        // Registration Form Handler
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.querySelector('form');
            const submitBtn = registerForm.querySelector('button[type="submit"]');

            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                if (password !== confirmPassword) {
                    showToast('Passwords do not match', 'error');
                    return;
                }

                // Button loading state
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Creating Account...';
                submitBtn.disabled = true;

                const formData = new FormData(this);
                formData.append('action', 'register');

                fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AuthController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast('Registration successful! Redirecting to login...', 'success');
                        
                        // Redirect to login page
                        setTimeout(() => {
                            window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php';
                        }, 1500);
                    } else {
                        showToast(data.message, 'error');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>

</html>
