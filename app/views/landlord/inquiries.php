<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
</head>
<body>
<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Listing.php';

// Check if user is logged in as landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: /Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/login.php');
    exit;
}

$landlordId = $_SESSION['user_id'];
$messageModel = new Message();
$userModel = new User();
$listingModel = new Listing();

// Get all inquiries (conversations) for this landlord
$inquiries = $messageModel->getLandlordInquiries($landlordId);

// Format inquiries with additional data
foreach ($inquiries as &$inquiry) {
    // Get user details
    $user = $userModel->getById($inquiry['other_user_id']);
    $inquiry['tenant'] = $user['first_name'] . ' ' . $user['last_name'];
    $inquiry['email'] = $user['email'] ?? '';
    $inquiry['phone'] = $user['phone'] ?? '';
    $inquiry['avatar'] = $user['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($inquiry['tenant']) . '&background=10b981&color=fff';
    
    // Get listing details
    if (!empty($inquiry['listing_id'])) {
        $listing = $listingModel->getById($inquiry['listing_id']);
        $inquiry['property'] = $listing['title'] ?? 'Deleted Listing';
    } else {
        $inquiry['property'] = 'General Inquiry';
    }
    
    // Format time
    $time = new DateTime($inquiry['last_message_time']);
    $now = new DateTime();
    $diff = $now->diff($time);
    if ($diff->days > 0) {
        $inquiry['time'] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        $inquiry['time'] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } else {
        $inquiry['time'] = max(1, $diff->i) . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    }
    
    // Add additional fields for compatibility
    $inquiry['id'] = $inquiry['other_user_id'] . '_' . ($inquiry['listing_id'] ?? '0'); // Unique ID
    $inquiry['message'] = $inquiry['last_message'];
    $inquiry['unread'] = $inquiry['unread_count'] > 0;
}
?>
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <div>
                    <h1 class="page-title">Inquiries</h1>
                    <p class="page-subtitle">Manage messages from potential tenants</p>
                </div>
            </div>

            <div class="inquiries-layout animate-slide-up">
                <!-- Inquiries List -->
                <div class="inquiries-sidebar">
                    <div style="padding: 0.75rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                        <div style="position: relative;">
                            <i data-lucide="search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4); z-index: 10;"></i>
                            <input type="text" class="form-input" placeholder="Search inquiries..." style="padding-left: 2.25rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem;">
                        </div>
                    </div>
                    <div style="flex: 1; overflow-y: auto;">
                        <?php
                        // Inquiries already loaded from database at top of file
                        if (empty($inquiries)):
                        ?>
                        <div style="padding: 4rem 1rem; text-align: center;">
                            <i data-lucide="inbox" style="width: 3rem; height: 3rem; color: rgba(0,0,0,0.2); margin: 0 auto 1rem;"></i>
                            <p style="color: rgba(0,0,0,0.5); font-size: 0.875rem;">No inquiries yet</p>
                        </div>
                        <?php
                        endif;

                        foreach ($inquiries as $index => $inquiry): 
                            $isSelected = $index === 0; // Default select first
                            $bgClass = $isSelected ? 'background-color: rgba(96, 165, 250, 0.3);' : '';
                        ?>
                        <div class="inquiry-item-container" data-id="<?php echo $inquiry['id']; ?>" style="padding: 0.75rem; cursor: pointer; transition: all 0.2s; <?php echo $bgClass; ?>" onclick="selectInquiry(this, <?php echo htmlspecialchars(json_encode($inquiry)); ?>)">
                            <div style="display: flex; align-items: flex-start; gap: 0.625rem;">
                                <img src="<?php echo $inquiry['avatar']; ?>" alt="<?php echo $inquiry['tenant']; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.125rem;">
                                        <h3 style="font-weight: 600; font-size: 0.875rem; color: #000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $inquiry['tenant']; ?></h3>
                                        <span style="font-size: 0.75rem; color: rgba(0,0,0,0.5); flex-shrink: 0; margin-left: 0.5rem;"><?php echo $inquiry['time']; ?></span>
                                    </div>
                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $inquiry['property']; ?></p>
                                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $inquiry['message']; ?></p>
                                </div>
                                <?php if ($inquiry['unread']): ?>
                                <div style="width: 0.5rem; height: 0.5rem; background-color: var(--deep-blue); border-radius: 9999px; flex-shrink: 0; margin-top: 0.5rem;"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Inquiry Details -->
                <div class="inquiries-main">
                    <!-- Header -->
                    <div style="padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <img id="detailAvatar" src="<?php echo $inquiries[0]['avatar']; ?>" alt="" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                <div>
                                    <h3 id="detailTenant" style="font-weight: 600; color: #000;"><?php echo $inquiries[0]['tenant']; ?></h3>
                                    <p id="detailProperty" style="font-size: 0.75rem; color: rgba(0,0,0,0.6);"><?php echo $inquiries[0]['property']; ?></p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <!-- Buttons removed as per request -->
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: rgba(0,0,0,0.6);">
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <i data-lucide="mail" style="width: 0.75rem; height: 0.75rem;"></i>
                                <span id="detailEmail"><?php echo $inquiries[0]['email']; ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <i data-lucide="phone" style="width: 0.75rem; height: 0.75rem;"></i>
                                <span id="detailPhone"><?php echo $inquiries[0]['phone']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                        <div style="max-width: 80%; background-color: rgba(96, 165, 250, 0.3); border-radius: 1rem; padding: 0.75rem 1rem;">
                            <p id="detailMessage" style="font-size: 0.875rem; color: #000; line-height: 1.625; margin-bottom: 0.5rem;">
                                <?php echo $inquiries[0]['message']; ?>
                            </p>
                            <p id="detailTime" style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">
                                <?php echo $inquiries[0]['time']; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Reply Input -->
                    <div style="padding: 1rem; border-top: 1px solid rgba(0,0,0,0.1);">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <button type="button" title="Attach file" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                <i data-lucide="paperclip" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                            <button type="button" title="Attach image" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                <i data-lucide="image" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                            <div style="flex: 1; position: relative;">
                                <button type="button" title="Emoji" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s; z-index: 1;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                    <i data-lucide="smile" style="width: 1.25rem; height: 1.25rem;"></i>
                                </button>
                                <input type="text" class="form-input" placeholder="Type your reply..." style="width: 100%; padding-left: 2.75rem; font-size: 0.875rem;">
                            </div>
                            <button class="btn btn-primary btn-sm">
                                <i data-lucide="send" style="width: 1rem; height: 1rem;"></i>
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function selectInquiry(element, data) {
            // Update UI selection
            document.querySelectorAll('.inquiry-item-container').forEach(el => {
                el.style.backgroundColor = 'transparent';
            });
            element.style.backgroundColor = 'rgba(96, 165, 250, 0.3)';

            // Update Details View
            document.getElementById('detailAvatar').src = data.avatar;
            document.getElementById('detailTenant').textContent = data.tenant;
            document.getElementById('detailProperty').textContent = data.property;
            document.getElementById('detailEmail').textContent = data.email;
            document.getElementById('detailPhone').textContent = data.phone;
            document.getElementById('detailMessage').textContent = data.message;
            document.getElementById('detailTime').textContent = data.time;
        }
    </script>
</body>
</html>
