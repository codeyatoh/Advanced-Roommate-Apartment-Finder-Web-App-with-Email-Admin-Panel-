<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to your RoomFinder account to browse rooms, find roommates, or manage your listings.">
    <title>Login - RoomFinder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/login.module.css">
</head>

<body>
    <div id="login-page" class="login-page">
        <div class="login-container animate-slide-up">
            <!-- Header -->
            <div class="login-header">
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to continue to RoomFinder</p>
            </div>

            <!-- Login Card -->
            <div class="card card-glass-strong login-card">
                <form id="login-form" class="login-form">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="form-input-wrapper">
                            <i data-lucide="mail" class="form-input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="your@email.com"
                                required />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="form-input-wrapper">
                            <i data-lucide="lock" class="form-input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Enter your password"
                                required />
                            <button type="button" class="password-toggle">
                                <i data-lucide="eye" class="eye-icon"></i>
                                <i data-lucide="eye-off" class="eye-off-icon" style="display: none;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="login-options">
                        <label class="login-remember">
                            <input
                                type="checkbox"
                                name="remember"
                                class="form-checkbox" />
                            <span class="login-remember-label">Remember me</span>
                        </label>
                        <a href="/forgot-password" class="login-forgot-link">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="login-divider">
                    <div class="login-divider-line">
                        <div class="login-divider-border"></div>
                    </div>
                    <div class="login-divider-text">
                        <span>Or continue with</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="login-social-grid">
                    <button id="google-login" class="btn btn-glass btn-md login-social-button">
                        <svg class="login-social-icon" viewBox="0 0 24 24">
                            <path
                                fill="currentColor"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path
                                fill="currentColor"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path
                                fill="currentColor"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path
                                fill="currentColor"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Google
                    </button>

                    <button id="facebook-login" class="btn btn-glass btn-md login-social-button">
                        <svg class="login-social-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Facebook
                    </button>
                </div>
            </div>

            <!-- Sign Up Link -->
            <p class="login-footer">
                Don't have an account?
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/register.php" class="login-footer-link">Sign up for free</a>
            </p>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <!-- JavaScript Files -->
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/js/forms.js"></script>
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/js/login.js"></script>
</body>

</html>