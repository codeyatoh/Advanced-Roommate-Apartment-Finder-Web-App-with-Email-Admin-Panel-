<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Roommate - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/profile-card.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/profile-completion-banner.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/match-modal.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <div style="margin-bottom: 1.5rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">Find Your Perfect Roommate</h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">Review profiles and find your match</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                    <!-- Profile Card -->
                    <div style="grid-column: 1/-1;">
                        <div class="card card-glass" style="overflow: hidden;">
                            <div style="display: flex; flex-direction: column; gap: 1.5rem; padding: 1.5rem;">
                                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                    <!-- Photo -->
                                    <div style="width: 100%; flex-shrink: 0;">
                                        <div style="position: relative; border-radius: 1rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=800" alt="Sarah Johnson" style="width: 100%; height: 600px; object-fit: cover;">
                                        </div>
                                    </div>
                                    <!-- Details -->
                                    <div style="flex: 1; display: flex; flex-direction: column; gap: 1.25rem;">
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                            <div>
                                                <h2 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin: 0 0 0.25rem 0;">Sarah Johnson, 25</h2>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0, 0, 0, 0.7);">
                                                    <i data-lucide="briefcase" style="width: 1rem; height: 1rem;"></i>
                                                    <span style="font-size: 0.875rem;">Software Engineer</span>
                                                </div>
                                            </div>
                                            <div style="padding: 0.5rem 1rem; background: rgba(16,185,129,0.2); border-radius: 9999px; display: flex; align-items: center; gap: 0.5rem;">
                                                <i data-lucide="trending-up" style="width: 1.25rem; height: 1.25rem; color: #10b981;"></i>
                                                <span style="font-weight: 700; color: #10b981; font-size: 1.125rem;">92%</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 style="font-size: 0.875rem; font-weight: 700; color: rgba(0, 0, 0, 0.6); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">About</h3>
                                            <p style="color: rgba(0, 0, 0, 0.8); line-height: 1.6; margin: 0;">Love hiking, cooking, and good coffee. Looking for a clean and respectful roommate who enjoys a balanced lifestyle.</p>
                                        </div>
                                        <div style="flex: 1;">
                                            <h3 style="font-size: 0.875rem; font-weight: 700; color: rgba(0, 0, 0, 0.6); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.75rem 0;">Interests</h3>
                                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                <?php foreach (['Hiking', 'Cooking', 'Coffee', 'Yoga', 'Reading'] as $interest): ?>
                                                <span style="padding: 0.5rem 1rem; background: var(--glass-bg-subtle); backdrop-filter: blur(8px); border-radius: 9999px; font-size: 0.875rem; color: #000000; font-weight: 500;"><?php echo $interest; ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 0.75rem; padding-top: 1rem;">
                                            <button class="btn btn-glass btn-lg" style="flex: 1; border: 2px solid #f87171;">
                                                <i data-lucide="x" style="width: 1.25rem; height: 1.25rem; color: #ef4444;"></i>
                                                Pass
                                            </button>
                                            <button class="btn btn-primary btn-lg" style="flex: 1; background: #10b981;">
                                                <i data-lucide="heart" style="width: 1.25rem; height: 1.25rem;"></i>
                                                Match
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <!-- Profile Completion -->
                        <div class="profile-completion-banner incomplete" style="padding: 1rem; border-radius: 1rem; background: rgba(234,179,8,0.1); border: 2px solid rgba(234,179,8,0.3);">
                            <div class="profile-completion-content">
                                <div class="profile-completion-icon incomplete">
                                    <i data-lucide="alert-circle"></i>
                                </div>
                                <div class="profile-completion-text">
                                    <div class="profile-completion-header">
                                        <h3 class="profile-completion-title">Complete Your Profile</h3>
                                        <span class="profile-completion-percentage incomplete">75%</span>
                                    </div>
                                    <div class="profile-completion-progress"><div class="profile-completion-progress-bar" style="width: 75%;"></div></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Matches -->
                        <div class="card card-glass" style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <div style="width: 2rem; height: 2rem; background: rgba(234,179,8,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="clock" style="width: 1rem; height: 1rem; color: #ca8a04;"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #000000; margin: 0;">Pending Matches</h3>
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;">2 people liked you</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php 
                                $pending = [
                                    ['name' => 'Alex Kim', 'age' => 27, 'occupation' => 'Teacher', 'compatibility' => 90, 'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400'],
                                    ['name' => 'Jessica Lee', 'age' => 24, 'occupation' => 'Nurse', 'compatibility' => 87, 'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=400']
                                ];
                                foreach ($pending as $person): ?>
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--glass-bg-subtle); border-radius: 0.5rem; cursor: pointer;">
                                    <img src="<?php echo $person['image']; ?>" alt="<?php echo $person['name']; ?>" style="width: 3rem; height: 3rem; border-radius: 9999px; object-fit: cover;">
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $person['name']; ?>, <?php echo $person['age']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $person['occupation']; ?></p>
                                    </div>
                                    <div style="font-size: 0.75rem; font-weight: 700; color: #10b981;"><?php echo $person['compatibility']; ?>%</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Matched -->
                        <div class="card card-glass" style="padding: 1rem; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <div style="width: 2rem; height: 2rem; background: rgba(16,185,129,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="heart" style="width: 1rem; height: 1rem; color: #10b981;"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #000000; margin: 0;">Matched</h3>
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;">2 mutual matches</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php 
                                $matched = [
                                    ['name' => 'David Martinez', 'age' => 29, 'lastMessage' => 'Hey! Want to grab coffee this week?', 'time' => '2h ago', 'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400'],
                                    ['name' => 'Lisa Wong', 'age' => 26, 'lastMessage' => 'That sounds great! See you then', 'time' => '1d ago', 'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400']
                                ];
                                foreach ($matched as $person): ?>
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--glass-bg-subtle); border-radius: 0.5rem; cursor: pointer;">
                                    <div style="position: relative;">
                                        <img src="<?php echo $person['image']; ?>" alt="<?php echo $person['name']; ?>" style="width: 3rem; height: 3rem; border-radius: 9999px; object-fit: cover;">
                                        <div style="position: absolute; bottom: -0.25rem; right: -0.25rem; width: 1rem; height: 1rem; background: #10b981; border-radius: 9999px; border: 2px solid white;"></div>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $person['name']; ?>, <?php echo $person['age']; ?></p>
                                        <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $person['lastMessage']; ?></p>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                        <i data-lucide="message-square" style="width: 1rem; height: 1rem; color: #10b981; margin-bottom: 0.25rem;"></i>
                                        <span style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.5);"><?php echo $person['time']; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="btn btn-ghost btn-sm" style="width: 100%; margin-top: 0.75rem;">View All Messages</button>
                        </div>
                    </div>
                </div>

                <style>
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"] {
                            grid-template-columns: 2fr 1fr !important;
                        }
                        div[style*="grid-column: 1/-1"] {
                            grid-column: 1/2 !important;
                        }
                        div > div[style*="flex-direction: column"][style*="gap: 1rem"] {
                            grid-column: 2/3 !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
