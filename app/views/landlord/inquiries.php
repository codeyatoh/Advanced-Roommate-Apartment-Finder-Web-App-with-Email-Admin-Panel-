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
                            <i data-lucide="search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: rgba(0,0,0,0.4);"></i>
                            <input type="text" class="form-input" placeholder="Search inquiries..." style="padding-left: 2.25rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem;">
                        </div>
                    </div>
                    <div style="flex: 1; overflow-y: auto;">
                        <?php
                        $inquiries = [
                            [
                                'id' => 1,
                                'tenant' => 'Sarah Johnson',
                                'property' => 'Modern Studio Downtown',
                                'message' => 'Hi, I am very interested in this property. Is it still available? I would love to schedule a viewing.',
                                'time' => '2 hours ago',
                                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                                'email' => 'sarah.j@email.com',
                                'phone' => '+1 (555) 123-4567',
                                'unread' => true,
                            ],
                            [
                                'id' => 2,
                                'tenant' => 'Mike Chen',
                                'property' => 'Cozy Apartment',
                                'message' => 'Is this property available for February 1st? I am looking for a place near downtown.',
                                'time' => '5 hours ago',
                                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                                'email' => 'mike.chen@email.com',
                                'phone' => '+1 (555) 234-5678',
                                'unread' => true,
                            ],
                            [
                                'id' => 3,
                                'tenant' => 'Emily Rodriguez',
                                'property' => 'Spacious Loft',
                                'message' => 'Thank you for the quick response! Looking forward to viewing.',
                                'time' => '1 day ago',
                                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400',
                                'email' => 'emily.r@email.com',
                                'phone' => '+1 (555) 345-6789',
                                'unread' => false,
                            ],
                        ];

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
                                <button class="btn btn-glass btn-sm" style="font-size: 0.75rem;" onclick="console.log('Email')">
                                    <i data-lucide="mail" style="width: 1rem; height: 1rem;"></i>
                                    Email
                                </button>
                                <button class="btn btn-glass btn-sm" style="font-size: 0.75rem;" onclick="console.log('SMS')">
                                    <i data-lucide="message-square" style="width: 1rem; height: 1rem;"></i>
                                    SMS
                                </button>
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
                            <input type="text" class="form-input" placeholder="Type your reply..." style="flex: 1; font-size: 0.875rem;">
                            <button class="btn btn-primary btn-sm">
                                <i data-lucide="send" style="width: 1rem; height: 1rem;"></i>
                                Send
                            </button>
                        </div>
                        <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Tenant will receive your reply via email and SMS</p>
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
