<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to RoomFinder - Find your perfect room and roommate">
    <title>Login - RoomFinder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to RoomFinder - Find your perfect room and roommate">
    <title>Login - RoomFinder</title>

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
            <!-- Header -->
            <div class="auth-header">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to continue to RoomFinder</p>
            </div>

            <!-- Login Card -->
            <div class="auth-card">
                <form method="POST" action="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AuthController.php" class="auth-form">
                    <input type="hidden" name="action" value="login">

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
                                placeholder="Enter your password"
                                required
                            >
                            <i data-lucide="lock" class="form-input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i data-lucide="eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="auth-options">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/forgot-password.php" class="auth-link">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <div class="auth-divider-text">
                        <span>Or continue with</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="social-buttons">
                    <button type="button" class="social-btn" onclick="loginWithGoogle()">
                        <svg viewBox="0 0 24 24">
                            <path
                                fill="currentColor"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            />
                            <path
                                fill="currentColor"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            />
                            <path
                                fill="currentColor"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            />
                        </svg>
                        Google
                    </button>
                    <button type="button" class="social-btn" onclick="loginWithFacebook()">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Facebook
                    </button>
                </div>
            </div>

            <!-- Sign Up Link -->
            <p class="auth-footer">
                Don't have an account?
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/register.php">
                    Sign up for free
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

    <!-- Password Toggle Script -->
    <script>
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

        // Social Login Functions
        function loginWithGoogle() {
            // Implement Google OAuth
            console.log('Login with Google');
        }

        function loginWithFacebook() {
            // Implement Facebook OAuth
            console.log('Login with Facebook');
        }

        // Login Form Handler
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form');
            const submitBtn = loginForm.querySelector('button[type="submit"]');

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Button loading state
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Logging in...';
                submitBtn.disabled = true;

                const formData = new FormData(this);
                formData.append('action', 'login');

                fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/AuthController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast('Login successful! Redirecting...', 'success');
                        
                        // Redirect based on role
                        setTimeout(() => {
                            if (data.role === 'admin') {
                                window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/admin/dashboard.php';
                            } else if (data.role === 'room_seeker') {
                                window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/seeker/dashboard.php';
                            } else if (data.role === 'landlord') {
                                window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/dashboard.php';
                            }
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
