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
$matchModel = new MatchModel();

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
                <!-- Empty State -->
                <div class="card card-glass" style="padding: 3rem; text-align: center;">
                    <i data-lucide="message-square-x" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.3); margin: 0 auto 1rem;"></i>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">No Messages Yet</h3>
                    <p style="color: rgba(0,0,0,0.6); margin-bottom: 1.5rem;">Start matching with roommates or contact landlords to begin conversations!</p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="roommate_finder.php" class="btn btn-primary">Find Roommates</a>
                        <a href="browse_rooms.php" class="btn btn-glass">Browse Rooms</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-glass messages-container" style="padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="messages-grid">
                        <!-- Conversations Panel -->
                        <div class="conversations-panel">
                            <div class="conversations-search">
                                <div class="form-input-wrapper">
                                    <i data-lucide="search" class="form-input-icon" style="width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4); z-index: 10;"></i>
                                    <input type="text" class="form-input" placeholder="Search messages..." style="font-size: 0.875rem;" id="searchMessages">
                                </div>
                            </div>
                            <div class="conversations-list">
                                <?php foreach ($conversations as $conv): 
                                    $isActive = $conv['other_user_id'] == $selectedConversationId;
                                    $userName = htmlspecialchars($conv['other_user_name']);
                                    $avatar = !empty($conv['other_user_photo']) 
                                        ? htmlspecialchars($conv['other_user_photo'])
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=10b981&color=fff';
                                    
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
                                    <div class="conversation-content">
                                        <div class="conversation-avatar">
                                            <img src="<?php echo $avatar; ?>" alt="<?php echo $userName; ?>">
                                        </div>
                                        <div class="conversation-details">
                                            <div class="conversation-header">
                                                <h3 class="conversation-name"><?php echo $userName; ?></h3>
                                                <span class="conversation-time"><?php echo $timeAgo; ?></span>
                                            </div>
                                            <p class="conversation-message"><?php echo htmlspecialchars($conv['last_message']); ?></p>
                                        </div>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                        <div class="conversation-unread"><?php echo $conv['unread_count']; ?></div>
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
                        ?>
                        <div class="chat-panel">
                            <div class="chat-header">
                                <div class="chat-header-info">
                                    <div class="chat-header-avatar">
                                        <img src="<?php echo $selectedAvatar; ?>" alt="<?php echo $selectedUserName; ?>">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <h3 class="chat-header-name" style="margin: 0;"><?php echo $selectedUserName; ?></h3>
                                            <?php if ($relationshipType === 'roommate'): ?>
                                            <span style="padding: 0.125rem 0.5rem; background: rgba(16, 185, 129, 0.15); color: var(--green); border-radius: 9999px; font-size: 0.625rem; font-weight: 600; line-height: 1;">
                                                Matched Roommate
                                            </span>
                                            <?php elseif ($relationshipType === 'landlord'): ?>
                                            <span style="padding: 0.125rem 0.5rem; background: rgba(59, 130, 246, 0.15); color: var(--blue); border-radius: 9999px; font-size: 0.625rem; font-weight: 600; line-height: 1;">
                                                Landlord
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: rgba(0,0,0,0.5);">
                                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                                <i data-lucide="mail" style="width: 0.875rem; height: 0.875rem;"></i>
                                                <span><?php echo htmlspecialchars($selectedUser['email']); ?></span>
                                            </div>
                                            <?php if (!empty($selectedUser['phone'])): ?>
                                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                                <i data-lucide="phone" style="width: 0.875rem; height: 0.875rem;"></i>
                                                <span><?php echo htmlspecialchars($selectedUser['phone']); ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-messages" id="chatMessages">
                                <?php foreach ($messages as $msg): 
                                    $isSent = $msg['sender_id'] == $userId;
                                    $timestamp = new DateTime($msg['created_at']);
                                    $timeDisplay = $timestamp->format('g:i A');
                                ?>
                                <div class="message-wrapper <?php echo $isSent ? 'sent' : 'received'; ?>">
                                    <div class="message-bubble">
                                        <p class="message-text"><?php echo nl2br(htmlspecialchars($msg['message_content'])); ?></p>
                                        <span class="message-time"><?php echo $timeDisplay; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="chat-input-container">
                                <form id="messageForm" style="display: flex; align-items: center; gap: 0.75rem;">
                                    <input type="hidden" name="receiver_id" value="<?php echo $selectedConversationId; ?>">
                                    <button type="button" class="chat-input-icon-btn">
                                        <i data-lucide="paperclip"></i>
                                    </button>
                                    <button type="button" class="chat-input-icon-btn">
                                        <i data-lucide="image"></i>
                                    </button>
                                    <button type="button" class="chat-input-icon-btn">
                                        <i data-lucide="smile"></i>
                                    </button>
                                    <input type="text" name="message" class="chat-input" placeholder="Type a message..." id="messageInput" autocomplete="off">
                                    <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem;">
                                        <i data-lucide="send" style="width: 1.125rem; height: 1.125rem;"></i>
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
