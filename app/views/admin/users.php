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
    <?php
    // Start session and load models
    session_start();
    require_once __DIR__ . '/../../models/User.php';
    require_once __DIR__ . '/../../models/Listing.php';
    require_once __DIR__ . '/../../models/Report.php';
    
    $userModel = new User();
    $listingModel = new Listing();
    $reportModel = new Report();
    
    // Handle Actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $userId = $_POST['user_id'] ?? null;
            
            if ($userId) {
                switch ($_POST['action']) {
                    case 'verify':
                        $userModel->verifyUser($userId);
                        break;
                    case 'ban':
                        $userModel->banUser($userId);
                        break;
                    case 'unban':
                        $userModel->unbanUser($userId);
                        break;
                    case 'update':
                        $data = [
                            'first_name' => $_POST['first_name'],
                            'last_name' => $_POST['last_name'],
                            'email' => $_POST['email'],
                            'phone' => $_POST['phone']
                        ];
                        $userModel->updateUser($userId, $data);
                        break;
                }
                // Redirect to refresh page and clear post data
                header("Location: users.php" . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
                exit;
            }
        }
    }
    
    // Get filters
    $search = $_GET['search'] ?? '';
    $role = $_GET['role'] ?? 'All Roles';
    $status = $_GET['status'] ?? 'All Status';

    // Get users with filters
    $usersData = $userModel->searchUsers([
        'search' => $search,
        'role' => $role,
        'status' => $status
    ]);
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 5;
    $totalUsers = count($usersData);
    $totalPages = ceil($totalUsers / $limit);
    $offset = ($page - 1) * $limit;
    $usersData = array_slice($usersData, $offset, $limit);
    
    // Format user data with additional stats
    $users = [];
    foreach ($usersData as $userData) {
        // Get listing count if landlord
        $listingCount = 0;
        if ($userData['role'] === 'landlord') {
            $sql = "SELECT COUNT(*) as count FROM listings WHERE landlord_id = :user_id";
            $stmt = $listingModel->getConnection()->prepare($sql);
            $stmt->bindValue(':user_id', $userData['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $listingCount = $result['count'] ?? 0;
        }
        
        // Get report count (reported by others)
        $sql = "SELECT COUNT(*) as count FROM reports WHERE reported_user_id = :user_id";
        $stmt = $reportModel->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userData['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $reportCount = $result['count'] ?? 0;
        
        // Format data
        $users[] = [
            'id' => $userData['user_id'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'name' => $userData['first_name'] . ' ' . $userData['last_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? 'N/A',
            'role' => $userData['role'],
            'status' => $userData['is_verified'] ? 'verified' : ($userData['is_active'] ? 'pending' : 'banned'),
            'joinDate' => date('M j, Y', strtotime($userData['created_at'])),
            'avatar' => $userData['profile_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($userData['first_name'] . ' ' . $userData['last_name']) . '&background=10b981&color=fff',
            'listings' => $listingCount,
            'reports' => $reportCount,
        ];
    }
    ?>
    <div class="admin-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="admin-container">
            <!-- Header -->
            <div class="page-header animate-slide-up">
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage user accounts and permissions</p>
            </div>

            <!-- Search & Filters -->
            <!-- Search & Filters -->
            <form method="GET" action="users.php" class="glass-card animate-slide-up" style="padding: 1rem; margin-bottom: 1.5rem; background: transparent; border: none; box-shadow: none;">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" name="search" class="search-input-clean" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="search-actions">
                        <button type="button" class="btn-filters" onclick="document.getElementById('filterOptions').style.display = document.getElementById('filterOptions').style.display === 'none' ? 'flex' : 'none'">
                            <i data-lucide="sliders-horizontal" style="width: 1rem; height: 1rem;"></i>
                            Filters
                        </button>
                        <button type="submit" class="btn-search">
                            Search
                        </button>
                    </div>
                </div>
                
                <!-- Expanded Filters -->
                <div id="filterOptions" style="display: <?php echo ($role !== 'All Roles' || $status !== 'All Status') ? 'flex' : 'none'; ?>; gap: 1rem; margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <select name="role" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $role === 'All Roles' ? 'selected' : ''; ?>>All Roles</option>
                        <option <?php echo $role === 'Seekers' ? 'selected' : ''; ?>>Seekers</option>
                        <option <?php echo $role === 'Landlords' ? 'selected' : ''; ?>>Landlords</option>
                    </select>
                    <select name="status" class="form-select-sm" style="flex: 1;">
                        <option <?php echo $status === 'All Status' ? 'selected' : ''; ?>>All Status</option>
                        <option <?php echo $status === 'Verified' ? 'selected' : ''; ?>>Verified</option>
                        <option <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option <?php echo $status === 'Banned' ? 'selected' : ''; ?>>Banned</option>
                    </select>
                </div>
            </form>

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
                            // Users already loaded from database above
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
                                            <button class="btn btn-ghost btn-sm" onclick='handleEdit(<?php echo htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8'); ?>)'>
                                                <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i>
                                            </button>
                                            <button class="btn btn-ghost btn-sm" style="color: #dc2626;" onclick="handleBan(<?php echo $user['id']; ?>)">
                                                <i data-lucide="ban" style="width: 1rem; height: 1rem;"></i>
                                            </button>
                                        <?php elseif ($user['status'] === 'banned'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="handleUnban(<?php echo $user['id']; ?>)">
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
            
            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="chevron-left" style="width: 1rem; height: 1rem;"></i>
                    Prev
                </a>
                <?php else: ?>
                <button class="btn btn-glass btn-sm" disabled style="opacity: 0.5; cursor: not-allowed; display: flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="chevron-left" style="width: 1rem; height: 1rem;"></i>
                    Prev
                </button>
                <?php endif; ?>

                <!-- Page Numbers -->
                <div style="display: flex; gap: 0.25rem;">
                    <?php for ($i = 1; $i <= max(1, $totalPages); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>" 
                           class="btn btn-sm" 
                           style="text-decoration: none; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; padding: 0; border: 1px solid rgba(0,0,0,0.1); <?php echo $i === $page ? 'background-color: #2563eb; color: white; border-color: #2563eb;' : 'background-color: white; color: #1f2937;'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-glass btn-sm" style="text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                    Next
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                </a>
                <?php else: ?>
                <button class="btn btn-glass btn-sm" disabled style="opacity: 0.5; cursor: not-allowed; display: flex; align-items: center; gap: 0.25rem;">
                    Next
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals -->
    <!-- Verify Modal -->
    <div id="verifyModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Verify User</h3>
                <button class="close-modal" onclick="closeModal('verifyModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to verify this user? They will gain full access to the platform.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('verifyModal')">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="verify">
                    <input type="hidden" name="user_id" id="verifyUserId">
                    <button type="submit" class="btn btn-primary">Verify User</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Ban Modal -->
    <div id="banModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" style="color: #dc2626;">Ban User</h3>
                <button class="close-modal" onclick="closeModal('banModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to ban this user? They will lose access to their account immediately.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('banModal')">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="ban">
                    <input type="hidden" name="user_id" id="banUserId">
                    <button type="submit" class="btn btn-primary" style="background-color: #dc2626; border-color: #dc2626;">Ban User</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Unban Modal -->
    <div id="unbanModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Unban User</h3>
                <button class="close-modal" onclick="closeModal('unbanModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to unban this user? Their account access will be restored.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('unbanModal')">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="unban">
                    <input type="hidden" name="user_id" id="unbanUserId">
                    <button type="submit" class="btn btn-primary">Unban User</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit User</h3>
                <button class="close-modal" onclick="closeModal('editModal')" style="color: #6b7280; padding: 0.25rem; background: transparent; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" class="form-input" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" class="form-input" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-input" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone</label>
                        <input type="text" name="phone" id="editPhone" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        console.log('Admin Users Script Loaded');

        // Define functions globally
        window.openModal = function(modalId) {
            console.log('Opening modal:', modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
            } else {
                console.error('Modal not found:', modalId);
            }
        }

        window.closeModal = function(modalId) {
            console.log('Closing modal:', modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
            }
        }

        window.handleVerify = function(id) {
            console.log('handleVerify called with id:', id);
            const input = document.getElementById('verifyUserId');
            if (input) {
                input.value = id;
                openModal('verifyModal');
            } else {
                console.error('verifyUserId input not found');
            }
        }

        window.handleUnban = function(id) {
            console.log('handleUnban called with id:', id);
            document.getElementById('unbanUserId').value = id;
            openModal('unbanModal');
        }

        window.handleBan = function(id) {
            console.log('handleBan called with id:', id);
            document.getElementById('banUserId').value = id;
            openModal('banModal');
        }

        window.handleEdit = function(user) {
            console.log('handleEdit called with user:', user);
            try {
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editFirstName').value = user.first_name;
                document.getElementById('editLastName').value = user.last_name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editPhone').value = user.phone;
                openModal('editModal');
            } catch (e) {
                console.error('Error in handleEdit:', e);
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('show');
            }
        }

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>


</body>
</html>
