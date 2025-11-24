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

                <div class="card card-glass messages-container" style="padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="messages-grid">
                        <!-- Conversations Panel -->
                        <div class="conversations-panel">
                            <div class="conversations-search">
                                <div class="form-input-wrapper">
                                    <i data-lucide="search" class="form-input-icon"></i>
                                    <input type="text" class="form-input" placeholder="Search messages..." style="font-size: 0.875rem;">
                                </div>
                            </div>
                            <div class="conversations-list">
                                <?php 
                                $conversations = [
                                    ['id' => 1, 'name' => 'David Martinez', 'lastMessage' => 'The apartment is still available. Would you like to schedule a viewing?', 'time' => '2m ago', 'unread' => 2, 'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400', 'online' => true],
                                    ['id' => 2, 'name' => 'Sarah Johnson', 'lastMessage' => 'Thanks for your interest! When can you move in?', 'time' => '1h ago', 'unread' => 0, 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400', 'online' => true],
                                    ['id' => 3, 'name' => 'Lisa Wong', 'lastMessage' => 'The viewing is confirmed for tomorrow at 10 AM', 'time' => '3h ago', 'unread' => 0, 'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400', 'online' => false]
                                ];
                                foreach ($conversations as $conv): ?>
                                <div class="conversation-item <?php echo $conv['id'] == 1 ? 'active' : ''; ?>">
                                    <div class="conversation-content">
                                        <div class="conversation-avatar">
                                            <img src="<?php echo $conv['avatar']; ?>" alt="<?php echo $conv['name']; ?>">
                                            <?php if ($conv['online']): ?>
                                            <div class="conversation-online"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conversation-details">
                                            <div class="conversation-header">
                                                <h3 class="conversation-name"><?php echo $conv['name']; ?></h3>
                                                <span class="conversation-time"><?php echo $conv['time']; ?></span>
                                            </div>
                                            <p class="conversation-message"><?php echo $conv['lastMessage']; ?></p>
                                        </div>
                                        <?php if ($conv['unread'] > 0): ?>
                                        <div class="conversation-unread"><?php echo $conv['unread']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Chat Panel -->
                        <div class="chat-panel">
                            <div class="chat-header">
                                <div class="chat-header-info">
                                    <div class="chat-header-avatar">
                                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400" alt="David Martinez">
                                    </div>
                                    <div>
                                        <h3 class="chat-header-name">David Martinez</h3>
                                        <p class="chat-header-status">Online</p>
                                    </div>
                                </div>
                                <div class="chat-header-actions">
                                    <button class="btn btn-glass btn-sm" style="width: 2rem; height: 2rem; padding: 0;">
                                        <i data-lucide="phone" style="width: 1rem; height: 1rem;"></i>
                                    </button>
                                    <button class="btn btn-glass btn-sm" style="width: 2rem; height: 2rem; padding: 0;">
                                        <i data-lucide="video" style="width: 1rem; height: 1rem;"></i>
                                    </button>
                                    <button class="btn btn-glass btn-sm" style="width: 2rem; height: 2rem; padding: 0;">
                                        <i data-lucide="more-vertical" style="width: 1rem; height: 1rem;"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="chat-messages">
                                <?php 
                                $messages = [
                                    ['id' => 1, 'sender' => 'other', 'text' => 'Hi! I saw your inquiry about the studio apartment.', 'time' => '10:30 AM'],
                                    ['id' => 2, 'sender' => 'user', 'text' => 'Yes, I am very interested! Is it still available?', 'time' => '10:32 AM'],
                                    ['id' => 3, 'sender' => 'other', 'text' => 'The apartment is still available. Would you like to schedule a viewing?', 'time' => '10:35 AM'],
                                    ['id' => 4, 'sender' => 'user', 'text' => 'That would be great! When are you available?', 'time' => '10:36 AM']
                                ];
                                foreach ($messages as $msg): ?>
                                <div class="message-wrapper <?php echo $msg['sender']; ?>">
                                    <div class="message-bubble <?php echo $msg['sender']; ?>">
                                        <p class="message-text"><?php echo $msg['text']; ?></p>
                                        <p class="message-time"><?php echo $msg['time']; ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="chat-input">
                                <div class="chat-input-form">
                                    <input type="text" class="form-input" placeholder="Type a message..." style="flex: 1; font-size: 0.875rem;">
                                    <button class="btn btn-primary btn-sm">
                                        <i data-lucide="send" class="btn-icon"></i>
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
