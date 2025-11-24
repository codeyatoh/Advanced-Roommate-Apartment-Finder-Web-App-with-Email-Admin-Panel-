<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - RoomFinder Admin</title>
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
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage user accounts and permissions</p>
            </div>

            <!-- Search & Filters -->
            <div class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; @media (min-width: 768px) { flex-direction: row; }">
                        <div style="position: relative; flex: 1;">
                            <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                            <input type="text" class="form-input" placeholder="Search users..." style="padding-left: 2.75rem;">
                        </div>
                        <select class="form-select">
                            <option>All Roles</option>
                            <option>Seekers</option>
                            <option>Landlords</option>
                        </select>
                        <select class="form-select">
                            <option>All Status</option>
                            <option>Verified</option>
                            <option>Pending</option>
                            <option>Banned</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="glass-card animate-slide-up" style="padding: 0; overflow: hidden;">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Activity</th>
                                <th>Joined</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $users = [
                                [
                                    'id' => 1,
                                    'name' => 'Sarah Johnson',
                                    'email' => 'sarah.j@email.com',
                                    'phone' => '+1 (555) 123-4567',
                                    'role' => 'seeker',
                                    'status' => 'verified',
                                    'joinDate' => 'Jan 15, 2024',
                                    'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                                    'listings' => 0,
                                    'reports' => 0,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'David Martinez',
                                    'email' => 'david.m@email.com',
                                    'phone' => '+1 (555) 234-5678',
                                    'role' => 'landlord',
                                    'status' => 'verified',
                                    'joinDate' => 'Dec 10, 2023',
                                    'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400',
                                    'listings' => 5,
                                    'reports' => 0,
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Mike Chen',
                                    'email' => 'mike.c@email.com',
                                    'phone' => '+1 (555) 345-6789',
                                    'role' => 'seeker',
                                    'status' => 'pending',
                                    'joinDate' => 'Jan 20, 2024',
                                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                                    'listings' => 0,
                                    'reports' => 0,
                                ],
                                [
                                    'id' => 4,
                                    'name' => 'Emily Rodriguez',
                                    'email' => 'emily.r@email.com',
                                    'phone' => '+1 (555) 456-7890',
                                    'role' => 'landlord',
                                    'status' => 'banned',
                                    'joinDate' => 'Nov 5, 2023',
                                    'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400',
                                    'listings' => 2,
                                    'reports' => 3,
                                ],
                            ];

                            foreach ($users as $user): 
                                $statusClass = '';
                                switch ($user['status']) {
                                    case 'verified': $statusClass = 'status-success'; break;
                                    case 'pending': $statusClass = 'status-warning'; break;
                                    case 'banned': $statusClass = 'status-error'; break;
                                }

                                $roleClass = $user['role'] === 'landlord' ? 'background-color: #dbeafe; color: #1d4ed8;' : 'background-color: #f3e8ff; color: #7e22ce;';
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?php echo $user['avatar']; ?>" alt="<?php echo $user['name']; ?>" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                        <div>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #000;"><?php echo $user['name']; ?></p>
                                            <p style="font-size: 0.75rem; color: rgba(0,0,0,0.6);">ID: <?php echo $user['id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: rgba(0,0,0,0.7);">
                                            <i data-lucide="mail" style="width: 0.75rem; height: 0.75rem;"></i>
                                            <?php echo $user['email']; ?>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: rgba(0,0,0,0.7);">
                                            <i data-lucide="phone" style="width: 0.75rem; height: 0.75rem;"></i>
                                            <?php echo $user['phone']; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; <?php echo $roleClass; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.75rem; color: rgba(0,0,0,0.7);">
                                        <p><?php echo $user['listings']; ?> listings</p>
                                        <p style="color: #dc2626;"><?php echo $user['reports']; ?> reports</p>
                                    </div>
                                </td>
                                <td>
                                    <p style="font-size: 0.875rem; color: rgba(0,0,0,0.7);"><?php echo $user['joinDate']; ?></p>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <?php if ($user['status'] === 'pending'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="handleVerify(<?php echo $user['id']; ?>)">
                                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                                Verify
                                            </button>
                                        <?php elseif ($user['status'] === 'verified'): ?>
                                            <button class="btn btn-ghost btn-sm" onclick="handleEdit(<?php echo $user['id']; ?>)">
                                                <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i>
                                            </button>
                                            <button class="btn btn-ghost btn-sm" style="color: #dc2626;" onclick="handleBan(<?php echo $user['id']; ?>)">
                                                <i data-lucide="ban" style="width: 1rem; height: 1rem;"></i>
                                            </button>
                                        <?php elseif ($user['status'] === 'banned'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="handleVerify(<?php echo $user['id']; ?>)">
                                                Unban
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function handleVerify(id) {
            console.log('Verify user:', id);
            // Add verify logic here
        }

        function handleBan(id) {
            console.log('Ban user:', id);
            // Add ban logic here
        }

        function handleEdit(id) {
            console.log('Edit user:', id);
            // Add edit logic here
        }
    </script>
</body>
</html>
