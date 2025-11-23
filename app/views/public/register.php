<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create your RoomFinder account. Choose between Room Seeker or Landlord account to get started.">
    <title>Register - RoomFinder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/register.module.css">
</head>

<body>
    <div id="register-page" class="register-page">
        <div class="register-container animate-slide-up">
            <!-- Header -->
            <div class="register-header">
                <h1 class="register-title">Join RoomFinder</h1>
                <p class="register-subtitle">Create your account to get started</p>
            </div>

            <!-- Role Selection -->
            <div id="role-selection" style="display: grid;">
                <div class="role-selection-grid">
                    <!-- Room Seeker Card -->
                    <div id="role-seeker" class="card card-glass-strong role-card card-hover">
                        <div class="role-icon-wrapper">
                            <i data-lucide="users" class="role-icon"></i>
                        </div>
                        <div>
                            <h2 class="role-title">Room Seeker</h2>
                            <p class="role-description">
                                Looking for a room or roommate? Browse listings and connect with landlords.
                            </p>
                        </div>
                        <ul class="role-features">
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Browse verified listings
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Find compatible roommates
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Schedule viewings
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Chat with landlords
                            </li>
                        </ul>
                        <button class="btn btn-primary btn-lg" style="width: 100%;">
                            Continue as Seeker
                        </button>
                    </div>

                    <!-- Landlord Card -->
                    <div id="role-landlord" class="card card-glass-strong role-card card-hover">
                        <div class="role-icon-wrapper">
                            <i data-lucide="home" class="role-icon"></i>
                        </div>
                        <div>
                            <h2 class="role-title">Landlord</h2>
                            <p class="role-description">
                                Have a room to rent? List your property and connect with verified tenants.
                            </p>
                        </div>
                        <ul class="role-features">
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                List unlimited properties
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Manage inquiries easily
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Schedule viewings
                            </li>
                            <li class="role-feature">
                                <div class="role-feature-dot"></div>
                                Verified tenant profiles
                            </li>
                        </ul>
                        <button class="btn btn-primary btn-lg" style="width: 100%;">
                            Continue as Landlord
                        </button>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div id="registration-form" style="display: none;">
                <div class="card card-glass-strong register-form-card">
                    <button id="back-to-role" class="register-back-button">
                        ← Back to role selection
                    </button>

                    <div class="register-form-header">
                        <div class="register-form-icon-wrapper">
                            <i id="form-role-icon" data-lucide="users" class="register-form-icon"></i>
                        </div>
                        <div>
                            <h2 id="form-role-title" class="register-form-title">Room Seeker Account</h2>
                            <p class="register-form-subtitle">Complete your registration</p>
                        </div>
                    </div>

                    <form id="register-form" class="register-form">
                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="form-input-wrapper">
                                <i data-lucide="user" class="form-input-icon"></i>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-input"
                                    placeholder="John Doe"
                                    required />
                            </div>
                        </div>

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
                                    placeholder="Create a strong password"
                                    required />
                                <button type="button" class="password-toggle">
                                    <i data-lucide="eye" class="eye-icon"></i>
                                    <i data-lucide="eye-off" class="eye-off-icon" style="display: none;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <div class="form-input-wrapper">
                                <i data-lucide="lock" class="form-input-icon"></i>
                                <input
                                    type="password"
                                    id="confirmPassword"
                                    name="confirmPassword"
                                    class="form-input"
                                    placeholder="Re-enter your password"
                                    required />
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-group">
                            <label class="form-checkbox-wrapper">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    class="form-checkbox"
                                    required />
                                <span class="form-checkbox-label">
                                    I agree to the
                                    <a href="/terms" style="color: var(--green); text-decoration: none;">Terms of Service</a>
                                    and
                                    <a href="/privacy" style="color: var(--green); text-decoration: none;">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            Create Account
                        </button>
                    </form>

                    <p class="register-footer">
                        Already have an account?
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/login.php" class="register-footer-link">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <!-- JavaScript Files -->
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/js/forms.js"></script>
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/js/register.js"></script>
</body>

</html>