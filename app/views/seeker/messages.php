<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Match.php';

// Get current user
$userId = $_SESSION['user_id'] ?? 1;

$messageModel = new Message();
$userModel = new User();
$matchModel = new RoommateMatch();

// Get all conversations (grouped by other user)
$conversations = $messageModel->getConversations($userId);

// Get selected conversation ID from URL
$selectedConversationId = $_GET['user_id'] ?? null;

// If no conversation selected, select the first one
if (!$selectedConversationId && !empty($conversations)) {
    $selectedConversationId = $conversations[0]['other_user_id'];
}

// Get messages for selected conversation
$messages = [];
$selectedUser = null;
if ($selectedConversationId) {
    $messages = $messageModel->getConversation($userId, $selectedConversationId);
    $selectedUser = $userModel->getById($selectedConversationId);
    
    // Mark messages as read
    $messageModel->markAsRead($userId, $selectedConversationId);
    
    // Check relationship type (landlord or matched roommate)
    $relationshipType = 'user';
    
    // Check if mutual match
    $mutualMatches = $matchModel->getMutualMatches($userId);
    foreach ($mutualMatches as $match) {
        if ($match['match_user_id'] == $selectedConversationId) {
            $relationshipType = 'roommate';
            break;
        }
    }
    
    // Check if landlord (if user is landlord role)
    if ($selectedUser && $selectedUser['role'] === 'landlord') {
        $relationshipType = 'landlord';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/messages.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>
        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">Messages</h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">Chat with landlords and potential roommates</p>
                </div>


                <?php if (empty($conversations)): ?>
                <!-- Empty State with Two-Panel Layout --><div class="card card-glass messages-container" style="padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="messages-grid">
                        <!-- Conversations Panel -->
                        <div class="conversations-panel">
                            <div class="conversations-search">
                                <div style="position: relative;">
                                    <i data-lucide="search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4); z-index: 10;"></i>
                                    <input type="text" class="form-input" placeholder="Search messages..." style="padding-left: 2.25rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem;" id="searchMessages">
                                </div>
                            </div>
                            <div style="padding: 4rem 1rem; text-align: center;">
                                <i data-lucide="inbox" style="width: 3rem; height: 3rem; color: rgba(0,0,0,0.2); margin: 0 auto 1rem;"></i>
                                <p style="color: rgba(0,0,0,0.5); font-size: 0.875rem;">No messages yet</p>
                            </div>
                        </div>

                        <!-- Empty Chat Panel -->
                        <div class="chat-panel" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 4rem 2rem; text-align: center;">
                            <i data-lucide="message-square" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.2); margin-bottom: 1rem;"></i>
                            <h3 style="color: rgba(0,0,0,0.6); margin: 0 0 0.5rem 0; font-size: 1.125rem;">No message selected</h3>
                            <p style="color: rgba(0,0,0,0.5); margin: 0; font-size: 0.875rem;">Start matching with roommates or contact landlords to begin conversations!</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-glass messages-container" style="padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="messages-grid">
                        <!-- Conversations Panel -->
                        <div class="conversations-panel">
                            <div class="conversations-search">
                                <div style="position: relative;">
                                    <i data-lucide="search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4); z-index: 10;"></i>
                                    <input type="text" class="form-input" placeholder="Search messages..." style="padding-left: 2.25rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem;" id="searchMessages">
                                </div>
                            </div>
                            <div class="conversations-list">
                                <?php foreach ($conversations as $conv): 
                                    $isActive = $conv['other_user_id'] == $selectedConversationId;
                                    $userName = htmlspecialchars($conv['other_user_name'] ?? 'Unknown User');
                                    $avatar = !empty($conv['other_user_photo']) 
                                        ? htmlspecialchars($conv['other_user_photo'])
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=10b981&color=fff';
                                    
                                    // Role Badge Logic
                                    $role = $conv['other_user_role'] ?? 'seeker';
                                    $badgeLabel = $role === 'landlord' ? 'Landlord' : 'Matched';
                                    $badgeClass = $role === 'landlord' ? 'landlord' : 'seeker';
                                    
                                    // Calculate time ago
                                    $timestamp = strtotime($conv['last_message_time']);
                                    $diff = time() - $timestamp;
                                    if ($diff < 60) {
                                        $timeAgo = 'Just now';
                                    } elseif ($diff < 3600) {
                                        $timeAgo = floor($diff / 60) . 'm ago';
                                    } elseif ($diff < 86400) {
                                        $timeAgo = floor($diff / 3600) . 'h ago';
                                    } else {
                                        $timeAgo = floor($diff / 86400) . 'd ago';
                                    }
                                ?>
                                <a href="?user_id=<?php echo $conv['other_user_id']; ?>" class="conversation-item <?php echo $isActive ? 'active' : ''; ?>" style="text-decoration: none; color: inherit;">
                                                <div style="display: flex; align-items: flex-start; gap: 0.625rem;">
                                                    <img src="<?php echo $avatar; ?>" alt="<?php echo $userName; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.125rem;">
                                                            <h3 style="font-weight: 600; font-size: 0.875rem; color: #000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo $userName; ?></h3>
                                                            <span style="font-size: 0.75rem; color: rgba(0,0,0,0.5); flex-shrink: 0; margin-left: 0.5rem;"><?php echo $timeAgo; ?></span>
                                                        </div>
                                                        <?php if (!empty($conv['listing_title'])): ?>
                                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($conv['listing_title']); ?></p>
                                                        <?php endif; ?>
                                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;"><?php echo htmlspecialchars($conv['last_message']); ?></p>
                                                    </div>
                                                    <?php if ($conv['unread_count'] > 0): ?>
                                                    <div style="width: 0.5rem; height: 0.5rem; background-color: var(--deep-blue); border-radius: 9999px; flex-shrink: 0; margin-top: 0.5rem;"></div>
                                                    <?php endif; ?>
                                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Chat Panel -->
                        <?php if ($selectedUser): 
                            $selectedUserName = htmlspecialchars($selectedUser['first_name'] . ' ' . $selectedUser['last_name']);
                            $selectedAvatar = !empty($selectedUser['profile_photo'])
                                ? htmlspecialchars($selectedUser['profile_photo'])
                                : 'https://ui-avatars.com/api/?name=' . urlencode($selectedUserName) . '&background=10b981&color=fff';
                            
                            // Role Badge Logic for Header
                            $selectedRole = $selectedUser['role'] ?? 'seeker';
                            $selectedBadgeLabel = $selectedRole === 'landlord' ? 'Landlord' : 'Matched';
                            $selectedBadgeClass = $selectedRole === 'landlord' ? 'landlord' : 'seeker';
                        ?>
                        <div class="chat-panel">
                            <!-- Header -->
                            <div style="padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?php echo $selectedAvatar; ?>" alt="<?php echo $selectedUserName; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <h3 style="font-weight: 600; color: #000; margin: 0;"><?php echo $selectedUserName; ?></h3>
                                                <span class="role-badge <?php echo $selectedBadgeClass; ?>"><?php echo $selectedBadgeLabel; ?></span>
                                            </div>
                                            <?php 
                                            // Get listing title if exists in conversation
                                            $listingTitle = '';
                                            foreach ($conversations as $conv) {
                                                if ($conv['other_user_id'] == $selectedConversationId && !empty($conv['listing_title'])) {
                                                    $listingTitle = $conv['listing_title'];
                                                    break;
                                                }
                                            }
                                            if ($listingTitle): ?>
                                            <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin: 0;"><?php echo htmlspecialchars($listingTitle); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: rgba(0,0,0,0.6);">
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <i data-lucide="mail" style="width: 0.75rem; height: 0.75rem;"></i>
                                        <span><?php echo htmlspecialchars($selectedUser['email']); ?></span>
                                    </div>
                                    <?php if (!empty($selectedUser['phone'])): ?>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <i data-lucide="phone" style="width: 0.75rem; height: 0.75rem;"></i>
                                        <span><?php echo htmlspecialchars($selectedUser['phone']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div style="flex: 1; overflow-y: auto; padding: 1rem;" id="chatMessages">
                                <?php foreach ($messages as $msg): 
                                    $isSent = $msg['sender_id'] == $userId;
                                    $timestamp = new DateTime($msg['created_at']);
                                    $timeDisplay = $timestamp->format('g:i A');
                                    $dateDisplay = $timestamp->format('M j, Y');
                                ?>
                                <div style="margin-bottom: 1rem; <?php echo $isSent ? 'display: flex; justify-content: flex-end;' : ''; ?>">
                                    <div style="max-width: 80%; <?php echo !$isSent ? 'background-color: rgba(96, 165, 250, 0.3);' : 'background-color: var(--deep-blue); color: white;'; ?> border-radius: 1rem; padding: 0.75rem 1rem;">
                                        <p style="font-size: 0.875rem; <?php echo $isSent ? 'color: white;' : 'color: #000;'; ?> line-height: 1.625; margin: 0 0 0.5rem 0;"><?php echo nl2br(htmlspecialchars($msg['message_content'])); ?></p>
                                        <p style="font-size: 0.75rem; <?php echo $isSent ? 'color: rgba(255,255,255,0.7);' : 'color: rgba(0,0,0,0.5);'; ?> margin: 0;"><?php echo $timeDisplay; ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Reply Input -->
                            <div style="padding: 1rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <form id="messageForm" style="display: flex; gap: 0.5rem;">
                                    <input type="hidden" name="receiver_id" value="<?php echo $selectedConversationId; ?>">
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
                                        <input type="text" name="message" class="form-input" placeholder="Type your reply..." style="width: 100%; padding-left: 2.75rem; font-size: 0.875rem;" id="messageInput" autocomplete="off">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i data-lucide="send" style="width: 1rem; height: 1rem;"></i>
                                        Send
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="chat-panel" style="display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center; color: rgba(0,0,0,0.5);">
                                <i data-lucide="message-square" style="width: 3rem; height: 3rem; margin: 0 auto 1rem;"></i>
                                <p>Select a conversation to view messages</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    
    <script>
        // Auto-scroll to bottom on load
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Handle message form submission
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const messageInput = document.getElementById('messageInput');
                const message = messageInput.value.trim();
                const receiverId = messageForm.querySelector('[name="receiver_id"]').value;
                
                if (!message) return;
                
                // TODO: Send message via AJAX to MessageController
                console.log('Sending message:', message, 'to user:', receiverId);
                
                // For now, just reload the page
                window.location.reload();
            });
        }

        // Search messages
        const searchInput = document.getElementById('searchMessages');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const conversations = document.querySelectorAll('.conversation-item');
                
                conversations.forEach(conv => {
                    const name = conv.querySelector('.conversation-name').textContent.toLowerCase();
                    const message = conv.querySelector('.conversation-message').textContent.toLowerCase();
                    
                    if (name.includes(query) || message.includes(query)) {
                        conv.style.display = '';
                    } else {
                        conv.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
