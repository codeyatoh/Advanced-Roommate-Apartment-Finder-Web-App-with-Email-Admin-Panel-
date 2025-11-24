<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management - RoomFinder Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/admin.module.css">
</head>
<body>
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">Reports Management</h1>
                <p class="page-subtitle">Review and resolve user complaints and reports</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #eab308;">
                            <i data-lucide="clock" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">12</p>
                    <p class="stat-label">Pending</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #3b82f6;">
                            <i data-lucide="alert-triangle" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">5</p>
                    <p class="stat-label">Investigating</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #22c55e;">
                            <i data-lucide="check-circle" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">45</p>
                    <p class="stat-label">Resolved</p>
                </div>

                <div class="glass-card stat-card animate-slide-up" style="animation-delay: 0.4s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="stat-icon-wrapper" style="background-color: #ef4444;">
                            <i data-lucide="x-circle" class="stat-icon"></i>
                        </div>
                    </div>
                    <p class="stat-value">8</p>
                    <p class="stat-label">High Priority</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; @media (min-width: 768px) { flex-direction: row; }">
                        <div style="position: relative; flex: 1;">
                            <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                            <input type="text" class="form-input" placeholder="Search reports..." style="padding-left: 2.75rem;">
                        </div>
                        <select class="form-select">
                            <option>All Types</option>
                            <option>Listing Reports</option>
                            <option>User Reports</option>
                            <option>Message Reports</option>
                        </select>
                        <select class="form-select">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Investigating</option>
                            <option>Resolved</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Reports List -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php
                $reports = [
                    [
                        'id' => 1234,
                        'type' => 'listing',
                        'category' => 'Inappropriate Content',
                        'reporter' => 'Sarah Johnson',
                        'reporterAvatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                        'reported' => 'Modern Studio Downtown',
                        'reportedUser' => 'David Martinez',
                        'description' => 'The listing contains misleading information about the property size and amenities.',
                        'status' => 'pending',
                        'priority' => 'high',
                        'submittedDate' => '2 hours ago',
                    ],
                    [
                        'id' => 1235,
                        'type' => 'user',
                        'category' => 'Harassment',
                        'reporter' => 'Mike Chen',
                        'reporterAvatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                        'reported' => 'John Smith',
                        'reportedUser' => 'John Smith',
                        'description' => 'User sent inappropriate messages and refused to stop after being asked.',
                        'status' => 'investigating',
                        'priority' => 'high',
                        'submittedDate' => '5 hours ago',
                    ],
                    [
                        'id' => 1236,
                        'type' => 'listing',
                        'category' => 'Scam/Fraud',
                        'reporter' => 'Emily Rodriguez',
                        'reporterAvatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400',
                        'reported' => 'Luxury Apartment',
                        'reportedUser' => 'Lisa Wong',
                        'description' => 'Listing appears to be a scam. Property does not exist at the listed address.',
                        'status' => 'resolved',
                        'priority' => 'high',
                        'submittedDate' => '1 day ago',
                    ],
                    [
                        'id' => 1237,
                        'type' => 'message',
                        'category' => 'Spam',
                        'reporter' => 'Alex Thompson',
                        'reporterAvatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400',
                        'reported' => 'Spam Messages',
                        'reportedUser' => 'Maria Garcia',
                        'description' => 'User is sending spam messages to multiple people.',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'submittedDate' => '3 hours ago',
                    ],
                ];

                foreach ($reports as $index => $report): 
                    $typeIcon = '';
                    switch ($report['type']) {
                        case 'listing': $typeIcon = 'home'; break;
                        case 'user': $typeIcon = 'user'; break;
                        case 'message': $typeIcon = 'message-square'; break;
                        default: $typeIcon = 'alert-triangle';
                    }

                    $statusClass = '';
                    switch ($report['status']) {
                        case 'pending': $statusClass = 'status-warning'; break;
                        case 'investigating': $statusClass = 'status-info'; break;
                        case 'resolved': $statusClass = 'status-success'; break;
                        default: $statusClass = 'status-neutral';
                    }

                    $priorityClass = '';
                    switch ($report['priority']) {
                        case 'high': $priorityClass = 'priority-high'; break;
                        case 'medium': $priorityClass = 'priority-medium'; break;
                        default: $priorityClass = 'status-success';
                    }
                ?>
                <div class="glass-card animate-slide-up" style="padding: 1.25rem; animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="listing-card-content">
                        <!-- Icon -->
                        <div style="flex-shrink: 0;">
                            <div style="width: 3rem; height: 3rem; background-color: #fee2e2; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="<?php echo $typeIcon; ?>" style="width: 1.5rem; height: 1.5rem; color: #dc2626;"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #000;">Report #<?php echo $report['id']; ?></h3>
                                        <span class="priority-badge <?php echo $priorityClass; ?>"><?php echo ucfirst($report['priority']); ?></span>
                                        <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($report['status']); ?></span>
                                    </div>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7); margin-bottom: 0.25rem;">
                                        <span style="font-weight: 600;">Category:</span> <?php echo $report['category']; ?>
                                    </p>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);">
                                        <span style="font-weight: 600;">Reported:</span> <?php echo $report['reported']; ?> by <?php echo $report['reportedUser']; ?>
                                    </p>
                                </div>
                                <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5); flex-shrink: 0;"><?php echo $report['submittedDate']; ?></p>
                            </div>

                            <!-- Reporter Info -->
                            <div class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; padding: 0.75rem;">
                                <img src="<?php echo $report['reporterAvatar']; ?>" alt="<?php echo $report['reporter']; ?>" style="width: 2rem; height: 2rem; border-radius: 9999px; object-fit: cover;">
                                <div>
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #000;">Reported by <?php echo $report['reporter']; ?></p>
                                </div>
                            </div>

                            <!-- Description -->
                            <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7); margin-bottom: 1rem; padding: 0.75rem; background-color: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                                <?php echo $report['description']; ?>
                            </p>

                            <!-- Actions -->
                            <?php if ($report['status'] === 'pending'): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <button class="btn btn-primary btn-sm">
                                    <i data-lucide="alert-triangle" style="width: 1rem; height: 1rem;"></i>
                                    Start Investigation
                                </button>
                                <button class="btn btn-glass btn-sm">View Details</button>
                                <button class="btn btn-ghost btn-sm" style="color: #dc2626;">Dismiss</button>
                            </div>
                            <?php elseif ($report['status'] === 'investigating'): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <button class="btn btn-primary btn-sm">
                                    <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                    Mark Resolved
                                </button>
                                <button class="btn btn-glass btn-sm">Add Note</button>
                            </div>
                            <?php elseif ($report['status'] === 'resolved'): ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #15803d;">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Report resolved and user notified</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
