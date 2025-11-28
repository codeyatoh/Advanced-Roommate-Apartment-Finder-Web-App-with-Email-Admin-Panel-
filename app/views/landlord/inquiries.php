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
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/messaging-shared.module.css">
</head>
<body>
<?php
// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

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
    
    // Check if user exists
    if ($user && is_array($user)) {
        $inquiry['tenant'] = $user['first_name'] . ' ' . $user['last_name'];
        $inquiry['email'] = $user['email'] ?? '';
        $inquiry['phone'] = $user['phone'] ?? '';
        $inquiry['avatar'] = $user['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($inquiry['tenant']) . '&background=10b981&color=fff';
    } else {
        // User not found - use defaults
        $inquiry['tenant'] = 'Deleted User';
        $inquiry['email'] = '';
        $inquiry['phone'] = '';
        $inquiry['avatar'] = 'https://ui-avatars.com/api/?name=DeletedUser&background=ef4444&color=fff';
    }
    
    // Get listing details
    if (!empty($inquiry['listing_id'])) {
        $listing = $listingModel->getById($inquiry['listing_id']);
        $inquiry['property'] = ($listing && is_array($listing)) ? ($listing['title'] ?? 'Deleted Listing') : 'Deleted Listing';
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

// Get full conversation for first inquiry (if exists)
$firstInquiryMessages = [];
if (!empty($inquiries)) {
    $firstInquiryMessages = $messageModel->getConversation(
        $landlordId,
        $inquiries[0]['other_user_id']
    );
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

            <div class="messaging-container inquiries-layout animate-slide-up">
                <div class="messaging-sidebar inquiries-sidebar">
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

                <div class="messaging-main inquiries-main">
                    <?php if (!empty($inquiries)): ?>
                    <div class="messaging-header" style="padding-bottom: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <img id="detailAvatar" src="<?php echo $inquiries[0]['avatar']; ?>" alt="" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                <div>
                                    <h3 id="detailTenant" style="font-weight: 600; color: #000;"><?php echo $inquiries[0]['tenant']; ?></h3>
                                    <!-- Old property line removed to avoid duplicate ID -->
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <!-- Buttons removed as per request -->
                            </div>
                        </div>
                        
                        <!-- Context Banner -->
                        <div style="background-color: var(--softBlue-20); padding: 0.75rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="width: 3rem; height: 3rem; background: #fff; border-radius: 0.25rem; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="home" style="width: 1.5rem; height: 1.5rem; color: var(--primary);"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6); margin: 0;">Inquiry regarding:</p>
                                <p id="detailProperty" style="font-size: 0.875rem; font-weight: 600; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo $inquiries[0]['property']; ?>
                                </p>
                            </div>
                            <a id="detailLink" href="view_listing.php?id=<?php echo $inquiries[0]['listing_id'] ?? '#'; ?>" class="btn btn-ghost btn-sm" style="font-size: 0.75rem; text-decoration: none;">View Room</a>
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

                    <div class="messaging-content" id="chatMessages">
                        <?php if (empty($firstInquiryMessages)): ?>
                            <div style="text-align: center; padding: 2rem; color: rgba(0,0,0,0.4);">
                                <p>No messages yet in this conversation.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($firstInquiryMessages as $msg): 
                            $isSent = $msg['sender_id'] == $landlordId;
                            $timestamp = new DateTime($msg['created_at']);
                            $timeDisplay = $timestamp->format('g:i A');
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
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
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
                                <input type="text" id="messageInput" class="form-input" placeholder="Type your reply..." style="width: 100%; padding-left: 1rem; font-size: 0.875rem;">
                            </div>
                            <button id="sendBtn" class="btn btn-primary btn-sm">
                                <i data-lucide="send" style="width: 1rem; height: 1rem;"></i>
                                Send
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Empty State -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 4rem 2rem; text-align: center;">
                        <i data-lucide="message-square" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.2); margin-bottom: 1rem;"></i>
                        <h3 style="color: rgba(0,0,0,0.6); margin: 0 0 0.5rem 0; font-size: 1.125rem;">No inquiry selected</h3>
                        <p style="color: rgba(0,0,0,0.5); margin: 0; font-size: 0.875rem;">Select an inquiry from the list to view details</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        let currentReceiverId = null;
        let currentListingId = null;

        // Helper function to render a single message
        function renderMessage(msg, isSent) {
            const timestamp = new Date(msg.created_at);
            const timeDisplay = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            
            const alignment = isSent ? 'display: flex; justify-content:flex-end;' : '';
            const bgColor = isSent ? 'background-color: var(--deep-blue); color: white;' : 'background-color: rgba(96, 165, 250, 0.3);';
            const textColor = isSent ? 'color: white;' : 'color: #000;';
            const timeColor = isSent ? 'color: rgba(255,255,255,0.7);' : 'color: rgba(0,0,0,0.5);';
            
            return `
                <div style="margin-bottom: 1rem; ${alignment}" data-message-id="${msg.message_id}">
                    <div style="max-width: 80%; ${bgColor} border-radius: 1rem; padding: 0.75rem 1rem;">
                        <p style="font-size: 0.875rem; ${textColor} line-height: 1.625; margin: 0 0 0.5rem 0;">${msg.message_content.replace(/\n/g, '<br>')}</p>
                        <p style="font-size: 0.75rem; ${timeColor} margin: 0;">${timeDisplay}</p>
                    </div>
                </div>
            `;
        }

        // Function to fetch and display conversation
        async function fetchConversation(otherUserId) {
            try {
                const response = await fetch(`/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/MessageController.php?action=getConversation&other_user_id=${otherUserId}`);
                const result = await response.json();
                
                if (result.success) {
                    const chatMessages = document.getElementById('chatMessages');
                    const landlordId = <?php echo $landlordId; ?>;
                    
                    if (result.messages.length === 0) {
                        chatMessages.innerHTML = '<div style="text-align: center; padding: 2rem; color: rgba(0,0,0,0.4);"><p>No messages yet in this conversation.</p></div>';
                    } else {
                        chatMessages.innerHTML = result.messages.map(msg => {
                            const isSent = msg.sender_id == landlordId;
                            return renderMessage(msg, isSent);
                        }).join('');
                        
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                } else {
                    console.error('Failed to fetch conversation:', result.message);
                }
            } catch (error) {
                console.error('Error fetching conversation:', error);
            }
        }

        function selectInquiry(element, data) {
            // Update UI selection
            document.querySelectorAll('.inquiry-item-container').forEach(el => {
                el.style.backgroundColor = 'transparent';
            });
            element.style.backgroundColor = 'rgba(96, 165, 250, 0.3)';

            // Stop current polling
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }

            // Store current context
            currentReceiverId = data.other_user_id;
            currentListingId = data.listing_id;

            // Update Details View
            document.getElementById('detailAvatar').src = data.avatar;
            document.getElementById('detailTenant').textContent = data.tenant;
            document.getElementById('detailProperty').textContent = data.property;
            document.getElementById('detailEmail').textContent = data.email;
            document.getElementById('detailPhone').textContent = data.phone;
            
            // Fetch and display full conversation
            fetchConversation(data.other_user_id).then(() => {
                // Reset lastMessageId based on fetched conversation
                const chatMessages = document.getElementById('chatMessages');
                const allMessages = chatMessages.querySelectorAll('[data-message-id]');
                if (allMessages.length > 0) {
                    lastMessageId = Math.max(...Array.from(allMessages).map(el => parseInt(el.dataset.messageId)));
                } else {
                    lastMessageId = 0;
                }
                
                // Restart polling for new conversation
                pollingInterval = setInterval(checkForNewMessages, 3000);
            });
            
            // Update Link
            const link = document.getElementById('detailLink');
            if (data.listing_id) {
                link.href = 'view_listing.php?id=' + data.listing_id;
                link.style.display = 'inline-flex';
            } else {
                link.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sendBtn = document.getElementById('sendBtn');
            const messageInput = document.getElementById('messageInput');

            // Real-time messaging variables  
            let lastMessageId = <?php 
                if (!empty($firstInquiryMessages)) {
                    echo max(array_column($firstInquiryMessages, 'message_id'));
                } else {
                    echo '0';
                }
            ?>;
            let pollingInterval = null;
            const landlordId = <?php echo $landlordId; ?>;

            // Auto-select first inquiry on page load
            <?php if (!empty($inquiries)): ?>
            currentReceiverId = <?php echo $inquiries[0]['other_user_id']; ?>;
            currentListingId = <?php echo $inquiries[0]['listing_id'] ?? 'null'; ?>;
            <?php endif; ?>

            // Poll for new messages
            async function checkForNewMessages() {
                if (!currentReceiverId) return;
                
                try {
                    const response = await fetch(`/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/MessageController.php?action=getNewMessages&other_user_id=${currentReceiverId}&last_message_id=${lastMessageId}`);
                    const result = await response.json();
                    
                    if (result.success && result.messages.length > 0) {
                        result.messages.forEach(msg => {
                            // Prevent duplicates
                            if (msg.message_id > lastMessageId) {
                                const isSent = msg.sender_id == landlordId;
                                chatMessages.insertAdjacentHTML('beforeend', renderMessage(msg, isSent));
                                lastMessageId = Math.max(lastMessageId, parseInt(msg.message_id));
                            }
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                } catch (error) {
                    console.error('Error checking for new messages:', error);
                }
            }

            // Start polling
            function startPolling() {
                if (currentReceiverId && !pollingInterval) {
                    pollingInterval = setInterval(checkForNewMessages, 2000); // Poll every 2 seconds
                }
            }

            // Stop polling
            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }

            async function sendMessage() {
                const message = messageInput.value.trim();
                
                if (!message) return;
                if (!currentReceiverId) {
                    alert('Please select an inquiry first.');
                    return;
                }

                // Disable UI
                sendBtn.disabled = true;
                messageInput.disabled = true;

                try {
                    const response = await fetch('/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/MessageController.php?action=send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            receiver_id: currentReceiverId,
                            message: message,
                            listing_id: currentListingId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Clear input
                        messageInput.value = '';
                        
                        // Append message immediately
                        const newMsg = {
                            message_id: Date.now(),
                            sender_id: landlordId,
                            message_content: message,
                            created_at: new Date().toISOString()
                        };
                        chatMessages.insertAdjacentHTML('beforeend', renderMessage(newMsg, true));
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        
                        // Re-enable UI
                        sendBtn.disabled = false;
                        messageInput.disabled = false;
                    } else {
                        alert(result.message || 'Failed to send message');
                        sendBtn.disabled = false;
                        messageInput.disabled = false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while sending the message');
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                }
            }

            if (sendBtn && messageInput) {
                sendBtn.addEventListener('click', sendMessage);

                messageInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });
            }

            // Start polling on page load
            startPolling();

            // Stop polling when leaving the page
            window.addEventListener('beforeunload', stopPolling);
        });
    </script>
</body>
</html>
