<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find your perfect room and compatible roommates. Browse verified listings, connect with landlords, and discover your ideal living space.">
    <meta name="keywords" content="room finder, roommate finder, apartment rental, room rental, housing">
    <title>RoomFinder - Find Your Perfect Room Today</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/carousel.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/room-card.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/footer.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/css/modules/landing.module.css">
</head>

<body>
    <div class="landing-page">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="navbar-container">
                <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/landing.php" class="navbar-logo">RoomFinder</a>

                <div class="navbar-menu">
                    <ul class="navbar-links">
                        <li>
                            <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/landing.php" class="navbar-link">
                                <i data-lucide="home" class="navbar-icon"></i>
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="#featured-rooms" class="navbar-link">
                                <i data-lucide="search" class="navbar-icon"></i>
                                Browse Rooms
                            </a>
                        </li>
                    </ul>

                    <div class="navbar-actions">
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/login.php" class="btn btn-login">
                            <i data-lucide="log-in" class="btn-icon"></i>
                            Login
                        </a>
                    </div>

                    <button class="navbar-mobile-toggle" aria-label="Toggle menu">
                        <i data-lucide="menu"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="navbar-mobile-menu">
                <ul class="navbar-mobile-links">
                    <li>
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/landing.php" class="navbar-mobile-link">
                            <i data-lucide="home" class="navbar-icon-mobile"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#featured-rooms" class="navbar-mobile-link">
                            <i data-lucide="search" class="navbar-icon-mobile"></i>
                            Browse Rooms
                        </a>
                    </li>
                    <li>
                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/login.php" class="navbar-mobile-link">
                            <i data-lucide="log-in" class="navbar-icon-mobile"></i>
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Hero Section with Carousel -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="carousel">
                    <div class="carousel-container">
                        <div class="carousel-slides">
                            <!-- Slide 1: Find Your Perfect Room Today -->
                            <div class="carousel-slide active">
                                <img src="https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=1600&h=900&fit=crop" alt="Modern apartment interior" />
                                <div class="carousel-overlay carousel-overlay-gradient-dark">
                                    <div class="carousel-content">
                                        <h1 class="carousel-title">Find Your Perfect Room Today</h1>
                                        <p class="carousel-description">Connect with verified landlords and compatible roommates in your area</p>
                                        <div class="carousel-actions">
                                            <a href="#featured-rooms" class="btn btn-login btn-lg">
                                                <i data-lucide="search" class="btn-icon"></i>
                                                Browse Rooms
                                            </a>
                                            <a href="#features" class="btn btn-glass btn-lg">
                                                <i data-lucide="users" class="btn-icon"></i>
                                                Find Roommates
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2: Meet Your Ideal Roommate -->
                            <div class="carousel-slide">
                                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1600&h=900&fit=crop" alt="People meeting as roommates" />
                                <div class="carousel-overlay carousel-overlay-gradient-blue">
                                    <div class="carousel-content">
                                        <h1 class="carousel-title">Meet Your Ideal Roommate</h1>
                                        <p class="carousel-description">Match with compatible people based on lifestyle and preferences</p>
                                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/register.php" class="btn btn-login btn-lg">
                                            <i data-lucide="users" class="btn-icon"></i>
                                            Start Matching
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 3: List Your Property -->
                            <div class="carousel-slide">
                                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1600&h=900&fit=crop" alt="Beautiful property listing" />
                                <div class="carousel-overlay carousel-overlay-gradient-soft">
                                    <div class="carousel-content">
                                        <h1 class="carousel-title">List Your Property</h1>
                                        <p class="carousel-description">Reach thousands of verified room seekers instantly</p>
                                        <a href="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/app/views/public/register.php" class="btn btn-login btn-lg">
                                            <i data-lucide="home" class="btn-icon"></i>
                                            List Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Navigation Dots -->
                        <div class="carousel-dots">
                            <button class="carousel-dot active" aria-label="Go to slide 1"></button>
                            <button class="carousel-dot" aria-label="Go to slide 2"></button>
                            <button class="carousel-dot" aria-label="Go to slide 3"></button>
                        </div>

                        <!-- Carousel Arrows -->
                        <button class="carousel-arrow carousel-arrow-prev" aria-label="Previous slide">
                            <i data-lucide="chevron-left"></i>
                        </button>
                        <button class="carousel-arrow carousel-arrow-next" aria-label="Next slide">
                            <i data-lucide="chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="how-it-works-section">
            <div class="how-it-works-container">
                <div class="card card-glass-strong" style="padding: 3rem;">
                    <div class="how-it-works-header animate-slide-up">
                        <h2 class="how-it-works-title">How It Works</h2>
                        <p class="how-it-works-description">Find your perfect room in three simple steps</p>
                    </div>

                    <div class="how-it-works-grid">
                        <div class="card card-glass how-it-works-card">
                            <div class="how-it-works-icon-wrapper">
                                <i data-lucide="search" class="how-it-works-icon"></i>
                            </div>
                            <h3 class="how-it-works-card-title">1. Search</h3>
                            <p class="how-it-works-card-text">Browse through hundreds of verified room listings in your preferred location</p>
                        </div>

                        <div class="card card-glass how-it-works-card">
                            <div class="how-it-works-icon-wrapper">
                                <i data-lucide="users" class="how-it-works-icon"></i>
                            </div>
                            <h3 class="how-it-works-card-title">2. Connect</h3>
                            <p class="how-it-works-card-text">Message landlords and potential roommates to find the perfect match</p>
                        </div>

                        <div class="card card-glass how-it-works-card">
                            <div class="how-it-works-icon-wrapper">
                                <i data-lucide="calendar" class="how-it-works-icon"></i>
                            </div>
                            <h3 class="how-it-works-card-title">3. Move In</h3>
                            <p class="how-it-works-card-text">Schedule viewings, complete paperwork, and move into your new home</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Rooms Section -->
        <section id="featured-rooms" class="featured-rooms-section">
            <div class="featured-rooms-container">
                <div class="features-container">
                    <div class="card card-glass-strong" style="padding: 3rem;">
                        <div class="features-header">
                            <h2 class="features-title">Why Choose RoomFinder?</h2>
                        </div>

                        <div class="features-grid">
                            <div class="card card-glass-subtle feature-card">
                                <i data-lucide="shield" class="feature-icon"></i>
                                <h3 class="feature-title">Verified Listings</h3>
                                <p class="feature-description">All properties are verified for your safety</p>
                            </div>

                            <div class="card card-glass-subtle feature-card">
                                <i data-lucide="star" class="feature-icon"></i>
                                <h3 class="feature-title">Trusted Reviews</h3>
                                <p class="feature-description">Read real reviews from previous tenants</p>
                            </div>

                            <div class="card card-glass-subtle feature-card">
                                <i data-lucide="users" class="feature-icon"></i>
                                <h3 class="feature-title">Smart Matching</h3>
                                <p class="feature-description">Find compatible roommates with AI</p>
                            </div>

                            <div class="card card-glass-subtle feature-card">
                                <i data-lucide="trending-up" class="feature-icon"></i>
                                <h3 class="feature-title">Best Prices</h3>
                                <p class="feature-description">Competitive rates with no hidden fees</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h3 class="footer-logo">RoomFinder</h3>
                        <p class="footer-description">
                            Find your perfect room and compatible roommates. We connect verified landlords with quality tenants.
                        </p>
                        <div class="footer-social">
                            <a href="#" class="footer-social-link" aria-label="Facebook">
                                <i data-lucide="facebook"></i>
                            </a>
                            <a href="#" class="footer-social-link" aria-label="Twitter">
                                <i data-lucide="twitter"></i>
                            </a>
                            <a href="#" class="footer-social-link" aria-label="Instagram">
                                <i data-lucide="instagram"></i>
                            </a>
                            <a href="#" class="footer-social-link" aria-label="LinkedIn">
                                <i data-lucide="linkedin"></i>
                            </a>
                        </div>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-title">For Seekers</h4>
                        <ul class="footer-links">
                            <li><a href="#" class="footer-link">Browse Rooms</a></li>
                            <li><a href="#" class="footer-link">Find Roommates</a></li>
                            <li><a href="#" class="footer-link">How It Works</a></li>
                            <li><a href="#" class="footer-link">Pricing</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-title">For Landlords</h4>
                        <ul class="footer-links">
                            <li><a href="#" class="footer-link">List Property</a></li>
                            <li><a href="#" class="footer-link">Manage Listings</a></li>
                            <li><a href="#" class="footer-link">Resources</a></li>
                            <li><a href="#" class="footer-link">Support</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-title">Company</h4>
                        <ul class="footer-links">
                            <li><a href="#" class="footer-link">About Us</a></li>
                            <li><a href="#" class="footer-link">Contact</a></li>
                            <li><a href="#" class="footer-link">Privacy Policy</a></li>
                            <li><a href="#" class="footer-link">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p class="footer-copyright">&copy; 2024 RoomFinder. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <!-- JavaScript Files -->
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel/public/assets/js/carousel.js"></script>

    <!-- Mobile Menu Toggle -->
    <script>
        document.querySelector('.navbar-mobile-toggle').addEventListener('click', function() {
            document.querySelector('.navbar-mobile-menu').classList.toggle('active');
        });
    </script>
</body>

</html>