<?php
// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

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
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/messaging-shared.module.css">
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


                <?php if (empty($conversations) && !$selectedUser): ?>
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
                <div class="messaging-container card card-glass" style="box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="messaging-layout">
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

                            // Get Listing Context
                            $listingContext = null;
                            $listingId = $_GET['listing_id'] ?? null;
                            if ($listingId) {
                                require_once __DIR__ . '/../../models/Listing.php';
                                $listingModel = new Listing();
                                $listingContext = $listingModel->getById($listingId);
                            }
                        ?>
                        <div class="messaging-main chat-panel">
                            <div class="messaging-header" style="padding-bottom: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?php echo $selectedAvatar; ?>" alt="<?php echo $selectedUserName; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <h3 style="font-weight: 600; color: #000; margin: 0;"><?php echo $selectedUserName; ?></h3>
                                                <span class="role-badge <?php echo $selectedBadgeClass; ?>"><?php echo $selectedBadgeLabel; ?></span>
                                            </div>
                                            <?php 
                                            // Get listing title from context or conversation history
                                            $listingTitle = '';
                                            if ($listingContext) {
                                                $listingTitle = $listingContext['title'];
                                            } else {
                                                foreach ($conversations as $conv) {
                                                    if ($conv['other_user_id'] == $selectedConversationId && !empty($conv['listing_title'])) {
                                                        $listingTitle = $conv['listing_title'];
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            if ($listingTitle): ?>
                                            <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin: 0;">
                                                <?php echo $listingContext ? 'Inquiring about: ' : 'Regarding: '; ?>
                                                <span style="font-weight: 600;"><?php echo htmlspecialchars($listingTitle); ?></span>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Context Banner -->
                                <?php if ($listingContext): ?>
                                <div style="background-color: var(--softBlue-20); padding: 0.75rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                    <div style="width: 3rem; height: 3rem; background: #fff; border-radius: 0.25rem; overflow: hidden; flex-shrink: 0;">
                                        <?php 
                                        // We need to fetch image if not in getById, but for now placeholder or simple check
                                        // Ideally getWithImages should be used or a separate query, but let's keep it simple for now
                                        ?>
                                        <i data-lucide="home" style="width: 1.5rem; height: 1.5rem; margin: 0.75rem; color: var(--primary);"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 0.875rem; font-weight: 600; margin: 0 0 0.125rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($listingContext['title']); ?>
                                        </p>
                                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin: 0;">
                                            ₱<?php echo number_format($listingContext['price']); ?>/mo • <?php echo htmlspecialchars($listingContext['location']); ?>
                                        </p>
                                    </div>
                                    <a href="room_details.php?id=<?php echo $listingContext['listing_id']; ?>" class="btn btn-ghost btn-sm" style="font-size: 0.75rem; text-decoration: none;">View Room</a>
                                </div>
                                <?php endif; ?>

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

                            <div class="messaging-content" id="chatMessages">
                                <?php if (empty($messages)): ?>
                                    <div style="text-align: center; padding: 2rem; color: rgba(0,0,0,0.4);">
                                        <p>No messages yet. Start the conversation!</p>
                                    </div>
                                <?php endif; ?>
                                
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

                            <div class="messaging-input">
                                <form id="messageForm" style="display: flex; gap: 0.5rem;">
                                    <input type="hidden" name="receiver_id" value="<?php echo $selectedConversationId; ?>">
                                    <?php if ($listingContext): ?>
                                        <input type="hidden" name="listing_id" value="<?php echo $listingContext['listing_id']; ?>">
                                    <?php endif; ?>
                                    
                                    <button type="button" title="Attach file" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                        <i data-lucide="paperclip" style="width: 1.25rem; height: 1.25rem;"></i>
                                    </button>
                                    <button type="button" title="Attach image" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                        <i data-lucide="image" style="width: 1.25rem; height: 1.25rem;"></i>
                                    </button>
                                    <button type="button" title="Emoji" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: rgba(0,0,0,0.6); transition: color 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='rgba(0,0,0,0.6)'">
                                        <i data-lucide="smile" style="width: 1.25rem; height: 1.25rem;"></i>
                                    </button>
                                    <div style="flex: 1; position: relative;">
                                        <input type="text" name="message" class="form-input" 
                                               placeholder="Type your reply..." 
                                               value="<?php echo $listingContext && empty($messages) ? 'Hi, I am interested in ' . htmlspecialchars($listingContext['title']) . '. Is it still available?' : ''; ?>"
                                               style="width: 100%; padding-left: 1rem; font-size: 0.875rem;" id="messageInput" autocomplete="off">
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

        // Real-time messaging variables
        let lastMessageId = <?php 
            if (!empty($messages)) {
                echo max(array_column($messages, 'message_id'));
            } else {
                echo '0';
            }
        ?>;
        let pollingInterval = null;
        const currentUserId = <?php echo $userId; ?>;
        const receiverId = <?php echo $selectedConversationId ?? 'null'; ?>;

        // Helper function to render a message
        function renderMessage(msg, isSent) {
            const timestamp = new Date(msg.created_at);
            const timeDisplay = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            
            const alignment = isSent ? 'display: flex; justify-content: flex-end;' : '';
            const bgColor = isSent ? 'background-color: var(--deep-blue); color: white;' : 'background-color: rgba(96, 165, 250, 0.3);';
            const textColor = isSent ? 'color: white;' : 'color: #000;';
            const timeColor = isSent ? 'color: rgba(255,255,255,0.7);' : 'color: rgba(0,0,0,0.5);';
            
            return `
                <div style="margin-bottom: 1rem; ${alignment}">
                    <div style="max-width: 80%; ${bgColor} border-radius: 1rem; padding: 0.75rem 1rem;">
                        <p style="font-size: 0.875rem; ${textColor} line-height: 1.625; margin: 0 0 0.5rem 0;">${msg.message_content.replace(/\n/g, '<br>')}</p>
                        <p style="font-size: 0.75rem; ${timeColor} margin: 0;">${timeDisplay}</p>
                    </div>
                </div>
            `;
        }

        // Append message to chat
        function appendMessage(msg) {
            if (!chatMessages) return;
            const isSent = msg.sender_id == currentUserId;
            chatMessages.insertAdjacentHTML('beforeend', renderMessage(msg, isSent));
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Poll for new messages
        async function checkForNewMessages() {
            if (!receiverId) return;
            
            try {
                const response = await fetch(`../../controllers/MessageController.php?action=getNewMessages&other_user_id=${receiverId}&last_message_id=${lastMessageId}`);
                const result = await response.json();
                
                if (result.success && result.messages.length > 0) {
                    result.messages.forEach(msg => {
                        appendMessage(msg);
                        lastMessageId = Math.max(lastMessageId, msg.message_id);
                    });
                }
            } catch (error) {
                console.error('Error checking for new messages:', error);
            }
        }

        // Start polling
        function startPolling() {
            if (receiverId && !pollingInterval) {
                pollingInterval = setInterval(checkForNewMessages, 3000);
            }
        }

        // Stop polling
        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        // Handle message form submission
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const messageInput = document.getElementById('messageInput');
                const message = messageInput.value.trim();
                const receiverIdInput = messageForm.querySelector('[name="receiver_id"]').value;
                const listingIdInput = messageForm.querySelector('[name="listing_id"]');
                const listingId = listingIdInput ? listingIdInput.value : null;
                
                if (!message) return;
                
                // Send message via AJAX to MessageController
                try {
                    const response = await fetch('../../controllers/MessageController.php?action=send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            receiver_id: receiverIdInput,
                            message: message,
                            listing_id: listingId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Clear input
                        messageInput.value = '';
                        
                        // Append message immediately
                        const newMsg = {
                            message_id: Date.now(), // Temporary ID
                            sender_id: currentUserId,
                            message_content: message,
                            created_at: new Date().toISOString()
                        };
                        appendMessage(newMsg);
                    } else {
                        alert('Failed to send message: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('An error occurred while sending the message.');
                }
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

        // Start polling on page load
        startPolling();

        // Stop polling when leaving the page
        window.addEventListener('beforeunload', stopPolling);
    </script>
</body>
</html>
