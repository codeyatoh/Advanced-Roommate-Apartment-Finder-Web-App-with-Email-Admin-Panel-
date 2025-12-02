<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Match.php';

// Get current user
$userId = $_SESSION['user_id'] ?? 1; // Fallback for development
$userName = $_SESSION['first_name'] ?? 'User';

$userModel = new User();
$matchModel = new RoommateMatch();

// Get profile completion
$completion = $userModel->getProfileCompletion($userId);

// Get current user profile for matching
$currentUserProfile = $userModel->getUserWithProfile($userId);
$myPreferences = [];
if (!empty($currentUserProfile['profile']['preferences'])) {
    $myPrefsData = json_decode($currentUserProfile['profile']['preferences'], true);
    if (is_array($myPrefsData)) {
        $myPreferences = $myPrefsData;
    }
}

// Get unseenprofiles for swiping (filtered by gender)
$userGender = $currentUserProfile['gender'] ?? null;
$unseenProfiles = $matchModel->getUnseenProfiles($userId, 1, $userGender);
$currentProfile = !empty($unseenProfiles) ? $unseenProfiles[0] : null;

// Get pending matches (who liked you)
$pendingMatches = $matchModel->getPendingMatches($userId);

// Get mutual matches
$mutualMatches = $matchModel->getMutualMatches($userId);
$myPreferences = [];
if (!empty($currentUserProfile['profile']['preferences'])) {
    $myPrefsData = json_decode($currentUserProfile['profile']['preferences'], true);
    if (is_array($myPrefsData)) {
        $myPreferences = $myPrefsData;
    }
}

// Mark match notifications as read
require_once __DIR__ . '/../../models/Notification.php';
$notificationModel = new Notification();
$notificationModel->markAsReadByType($userId, 'match');
?>
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
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>
        <div style="padding-top: 5rem; padding-bottom: 2rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1400px; margin: 0 auto;">
                <div style="margin-bottom: 1rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 0.25rem;">Find Your Perfect Roommate</h1>
                    <p style="color: rgba(0, 0, 0, 0.6); font-size: 0.875rem;">Review profiles and find your match</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    <!-- Profile Card -->
                    <div style="grid-column: 1/-1;">
                        <?php if ($currentProfile): 
                            $fullName = htmlspecialchars($currentProfile['first_name'] . ' ' . $currentProfile['last_name']);
                            $occupation = htmlspecialchars($currentProfile['occupation'] ?? 'Room Seeker');
                            $bio = htmlspecialchars($currentProfile['bio'] ?? 'No bio available yet.');
                            $photo = !empty($currentProfile['profile_photo']) 
                                ? htmlspecialchars($currentProfile['profile_photo']) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=10b981&color=fff&size=800';
                            
                            // Parse preferences if available
                            $preferences = [];
                            if (!empty($currentProfile['preferences'])) {
                                $prefsData = json_decode($currentProfile['preferences'], true);
                                if (is_array($prefsData)) {
                                    $preferences = $prefsData;
                                }
                            }

                            // Calculate Match Percentage
                            $matchPercentage = 0;
                            if (!empty($myPreferences) && !empty($preferences)) {
                                $intersection = array_intersect($myPreferences, $preferences);
                                $union = array_unique(array_merge($myPreferences, $preferences));
                                if (count($union) > 0) {
                                    $matchPercentage = round((count($intersection) / count($union)) * 100);
                                }
                            }
                            
                            // Determine match color
                            $matchColor = '#10b981'; // Green (High)
                            if ($matchPercentage < 50) $matchColor = '#ef4444'; // Red (Low)
                            else if ($matchPercentage < 80) $matchColor = '#f59e0b'; // Orange (Medium)
                        ?>
                        <div class="card card-glass" style="overflow: hidden; height: 100%;">
                            <div class="profile-card-content" style="display: flex; flex-direction: column; gap: 1.5rem; padding: 1.5rem; height: 100%;">
                                <!-- Photo -->
                                <div class="profile-image-container" style="width: 100%; flex-shrink: 0;">
                                    <div style="position: relative; border-radius: 1rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); height: 100%;">
                                        <a href="match_profile.php?user_id=<?php echo $currentProfile['user_id']; ?>" style="display: block; height: 100%; cursor: pointer;">
                                            <img src="<?php echo $photo; ?>" alt="<?php echo $fullName; ?>" class="profile-image" style="width: 100%; height: 400px; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        </a>
                                        <!-- Match Badge -->
                                        <div style="position: absolute; top: 1rem; right: 1rem; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); padding: 0.5rem 1rem; border-radius: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 0.5rem; z-index: 10;">
                                            <div style="position: relative; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center;">
                                                <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e5e7eb" stroke-width="4" />
                                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="<?php echo $matchColor; ?>" stroke-width="4" stroke-dasharray="<?php echo $matchPercentage; ?>, 100" />
                                                </svg>
                                                <span style="position: absolute; font-size: 0.75rem; font-weight: 700; color: <?php echo $matchColor; ?>;"><?php echo $matchPercentage; ?>%</span>
                                            </div>
                                            <span style="font-weight: 700; font-size: 0.875rem; color: #1f2937;">Match</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Details -->
                                <div style="flex: 1; display: flex; flex-direction: column; gap: 1rem; justify-content: center;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                        <div>
                                            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin: 0 0 0.25rem 0;"><?php echo $fullName; ?></h2>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0, 0, 0, 0.7);">
                                                <i data-lucide="briefcase" style="width: 1rem; height: 1rem;"></i>
                                                <span style="font-size: 0.875rem;"><?php echo $occupation; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 style="font-size: 0.75rem; font-weight: 700; color: rgba(0, 0, 0, 0.6); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">About</h3>
                                        <p style="color: rgba(0, 0, 0, 0.8); line-height: 1.5; margin: 0; font-size: 0.95rem;"><?php echo $bio; ?></p>
                                    </div>
                                    <?php if (!empty($preferences)): ?>
                                    <div style="flex: 1;">
                                        <h3 style="font-size: 0.75rem; font-weight: 700; color: rgba(0, 0, 0, 0.6); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">Preferences</h3>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                            <?php foreach ($preferences as $pref): ?>
                                            <span style="padding: 0.375rem 0.75rem; background: var(--glass-bg-subtle); backdrop-filter: blur(8px); border-radius: 9999px; font-size: 0.8rem; color: #000000; font-weight: 500;"><?php echo htmlspecialchars($pref); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div style="display: flex; gap: 0.75rem; padding-top: 0.5rem;">
                                        <button class="btn btn-glass btn-lg" style="flex: 1;" onclick="handleAction('pass', <?php echo $currentProfile['user_id']; ?>)">
                                            <i data-lucide="x" class="btn-icon"></i>
                                            Pass
                                        </button>
                                        <button class="btn btn-primary btn-lg" style="flex: 1;" onclick="handleAction('match', <?php echo $currentProfile['user_id']; ?>)">
                                            <i data-lucide="heart" class="btn-icon"></i>
                                            Match
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="card card-glass" style="padding: 3rem; text-align: center;">
                            <i data-lucide="users" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.3); margin: 0 auto 1rem;"></i>
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">No More Profiles</h3>
                            <p style="color: rgba(0,0,0,0.6);">You've reviewed all available profiles. Check back later for new seekers!</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <!-- Profile Completion -->
                        <div class="profile-completion-banner <?php echo $completion['percentage'] < 100 ? 'incomplete' : 'complete'; ?>" style="padding: 1rem; border-radius: 1rem; background: <?php echo $completion['percentage'] < 100 ? 'rgba(234,179,8,0.1)' : 'rgba(16,185,129,0.1)'; ?>; border: 2px solid <?php echo $completion['percentage'] < 100 ? 'rgba(234,179,8,0.3)' : 'rgba(16,185,129,0.3)'; ?>;">
                            <div class="profile-completion-content">
                                <div class="profile-completion-icon <?php echo $completion['percentage'] < 100 ? 'incomplete' : 'complete'; ?>">
                                    <i data-lucide="<?php echo $completion['percentage'] < 100 ? 'alert-circle' : 'check-circle'; ?>"></i>
                                </div>
                                <div class="profile-completion-text">
                                    <div class="profile-completion-header">
                                        <h3 class="profile-completion-title"><?php echo $completion['percentage'] < 100 ? 'Complete Your Profile' : 'Profile Complete!'; ?></h3>
                                        <span class="profile-completion-percentage <?php echo $completion['percentage'] < 100 ? 'incomplete' : 'complete'; ?>"><?php echo $completion['percentage']; ?>%</span>
                                    </div>
                                    <div class="profile-completion-progress"><div class="profile-completion-progress-bar" style="width: <?php echo $completion['percentage']; ?>%;"></div></div>
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
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;"><?php echo count($pendingMatches); ?> people liked you</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php if (empty($pendingMatches)): ?>
                                    <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 1rem; font-size: 0.875rem;">No pending matches yet</p>
                                <?php else: ?>
                                    <?php foreach ($pendingMatches as $person): 
                                        $pName = htmlspecialchars($person['first_name'] . ' ' . $person['last_name']);
                                        $pPhoto = !empty($person['profile_photo']) 
                                            ? htmlspecialchars($person['profile_photo']) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($pName) . '&background=10b981&color=fff';
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--glass-bg-subtle); border-radius: 0.5rem;">
                                        <img src="<?php echo $pPhoto; ?>" alt="<?php echo $pName; ?>" style="width: 3rem; height: 3rem; border-radius: 9999px; object-fit: cover;">
                                        <div style="flex: 1; min-width: 0;">
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $pName; ?></p>
                                            <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo htmlspecialchars($person['occupation'] ?? 'Room Seeker'); ?></p>
                                        </div>
                                        <button onclick="handleAction('match', <?php echo $person['user_id']; ?>)" style="width: 2rem; height: 2rem; border-radius: 50%; background: #ef4444; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Match Back">
                                            <i data-lucide="heart" style="width: 1rem; height: 1rem; fill: white;"></i>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                                    <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); margin: 0;"><?php echo count($mutualMatches); ?> mutual matches</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php if (empty($mutualMatches)): ?>
                                    <p style="color: rgba(0,0,0,0.5); text-align: center; padding: 1rem; font-size: 0.875rem;">No matches yet</p>
                                <?php else: ?>
                                    <?php foreach ($mutualMatches as $person): 
                                        $mName = htmlspecialchars($person['first_name'] . ' ' . $person['last_name']);
                                        $mPhoto = !empty($person['profile_photo']) 
                                            ? htmlspecialchars($person['profile_photo']) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($mName) . '&background=10b981&color=fff';
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--glass-bg-subtle); border-radius: 0.5rem;">
                                        <a href="match_profile.php?user_id=<?php echo $person['match_user_id']; ?>" style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0; text-decoration: none; color: inherit;">
                                            <div style="position: relative;">
                                                <img src="<?php echo $mPhoto; ?>" alt="<?php echo $mName; ?>" style="width: 3rem; height: 3rem; border-radius: 9999px; object-fit: cover;">
                                                <div style="position: absolute; bottom: -0.25rem; right: -0.25rem; width: 1rem; height: 1rem; background: #10b981; border-radius: 9999px; border: 2px solid white;"></div>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <p style="font-size: 0.875rem; font-weight: 600; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $mName; ?></p>
                                                <p style="font-size: 0.75rem; color: rgba(0, 0, 0, 0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;">
                                                    <?php 
                                                    if (!empty($person['role']) && $person['role'] === 'landlord') {
                                                        echo 'Landlord';
                                                    } else {
                                                        echo htmlspecialchars($person['occupation'] ?? 'Room Seeker');
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </a>
                                        <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                            <a href="messages.php?user_id=<?php echo $person['match_user_id']; ?>" style="text-decoration: none; display: flex; align-items: center; justify-content: center; padding: 0.25rem; border-radius: 0.25rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(16, 185, 129, 0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                                                <i data-lucide="message-square" style="width: 1rem; height: 1rem; color: #10b981;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($mutualMatches)): ?>
                            <button class="btn btn-ghost btn-sm" style="width: 100%; margin-top: 0.75rem;" onclick="window.location.href='messages.php'">View All Messages</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <style>
                    @media (min-width: 1024px) {
                        div[style*="grid-template-columns: 1fr"] {
                            grid-template-columns: 2.5fr 1fr !important;
                        }
                        div[style*="grid-column: 1/-1"] {
                            grid-column: 1/2 !important;
                        }
                        div > div[style*="flex-direction: column"][style*="gap: 1rem"] {
                            grid-column: 2/3 !important;
                        }
                        
                        /* Horizontal Card Layout */
                        .profile-card-content {
                            flex-direction: row !important;
                            align-items: stretch;
                        }
                        .profile-image-container {
                            width: 350px !important;
                            height: auto !important;
                        }
                        .profile-image {
                            height: 100% !important;
                            min-height: 400px;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/js/toast-helper.js"></script>
    
    <script>
        function handleAction(action, targetId) {
            const formData = new FormData();
            formData.append('endpoint', 'record_action');
            formData.append('action', action);
            formData.append('target_id', targetId);

            fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/MatchController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.is_mutual) {
                        showToast(data.message, 'success');
                    }
                    // Reload page to show next profile
                    setTimeout(() => {
                        window.location.reload();
                    }, data.is_mutual ? 1500 : 500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            });
        }
    </script>
</body>
</html>
