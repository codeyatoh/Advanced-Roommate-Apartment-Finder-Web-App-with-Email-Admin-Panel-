<?php
// Start session and load models
session_start();
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/Listing.php';
require_once __DIR__ . '/../../models/User.php';

// Get current user
$userId = $_SESSION['user_id'] ?? 1; // Fallback for development

$appointmentModel = new Appointment();
$listingModel = new Listing();
$userModel = new User();

// Fetch appointments for this seeker
$appointments = $appointmentModel->getUpcoming($userId, 'seeker');

// Group appointments by status
$upcomingAppointments = [];
$pastAppointments = [];
$today = date('Y-m-d');

foreach ($appointments as $apt) {
    if ($apt['status'] === 'completed' || $apt['status'] === 'cancelled' || 
        ($apt['appointment_date'] < $today)) {
        $pastAppointments[] = $apt;
    } else {
        $upcomingAppointments[] = $apt;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - RoomFinder</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/cards.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/forms.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/appointments.module.css">
</head>
<body>
    <div style="min-height: 100vh; background: linear-gradient(to bottom right, var(--softBlue-20), var(--neutral), var(--deepBlue-10));">
        <!-- Navbar -->
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div style="padding-top: 6rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <!-- Header -->
                <div style="margin-bottom: 2rem; animation: slideUp 0.3s ease-out;">
                    <h1 style="font-size: 1.875rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                        My Appointments
                    </h1>
                    <p style="color: rgba(0, 0, 0, 0.6);">
                        Manage your property viewing schedule
                    </p>
                </div>

                <!-- Upcoming Appointments -->
                <?php if (!empty($upcomingAppointments)): ?>
                <div style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Upcoming</h2>
                    <div class="appointments-list">
                        <?php foreach ($upcomingAppointments as $index => $apt): 
                            // Get landlord info
                            $landlord = $userModel->getById($apt['landlord_id']);
                            $landlordName = htmlspecialchars($landlord['first_name'] . ' ' . $landlord['last_name']);
                            
                            // Format date
                            $date = new DateTime($apt['appointment_date']);
                            $today = new DateTime();
                            $tomorrow = new DateTime('tomorrow');
                            
                            if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                                $dateDisplay = 'Today';
                            } elseif ($date->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                                $dateDisplay = 'Tomorrow';
                            } else {
                                $dateDisplay = $date->format('M j, Y');
                            }
                            
                            // Format time
                            $time = new DateTime($apt['appointment_time']);
                            $timeDisplay = $time->format('g:i A');
                            
                            // Get property image
                            $image = !empty($apt['primary_image']) 
                                ? htmlspecialchars($apt['primary_image'])
                                : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400';
                        ?>
                        <div class="card card-glass appointment-card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="appointment-card-content" style="display: flex; flex-direction: column; gap: 1.25rem; padding: 1.25rem;">
                                <!-- Image -->
                                <div class="appointment-image">
                                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($apt['title']); ?>">
                                </div>

                                <!-- Content -->
                                <div class="appointment-details">
                                    <div class="appointment-header">
                                        <div class="appointment-title">
                                            <h3><?php echo htmlspecialchars($apt['title']); ?></h3>
                                            <div class="appointment-landlord">
                                                <i data-lucide="user"></i>
                                                <span>with <?php echo $landlordName; ?></span>
                                            </div>
                                        </div>
                                        <span class="appointment-status <?php echo $apt['status']; ?>">
                                            <?php if ($apt['status'] === 'confirmed'): ?>
                                                <i data-lucide="check-circle"></i>
                                            <?php elseif ($apt['status'] === 'pending'): ?>
                                                <i data-lucide="clock"></i>
                                            <?php elseif ($apt['status'] === 'declined'): ?>
                                                <i data-lucide="x-circle"></i>
                                            <?php endif; ?>
                                            <span><?php echo ucfirst($apt['status']); ?></span>
                                        </span>
                                    </div>

                                    <div class="appointment-info">
                                        <div class="appointment-info-item">
                                            <i data-lucide="calendar"></i>
                                            <span><?php echo $dateDisplay; ?></span>
                                        </div>
                                        <div class="appointment-info-item">
                                            <i data-lucide="clock"></i>
                                            <span><?php echo $timeDisplay; ?></span>
                                        </div>
                                        <div class="appointment-info-item">
                                            <i data-lucide="map-pin"></i>
                                            <span><?php echo htmlspecialchars($apt['location']); ?></span>
                                        </div>
                                    </div>

                                    <?php if ($apt['status'] === 'pending'): ?>
                                    <div class="appointment-actions">
                                        <button class="btn btn-ghost btn-sm" onclick="cancelAppointment(<?php echo $apt['appointment_id']; ?>)">
                                            <i data-lucide="x"></i>
                                            Cancel
                                        </button>
                                        <button class="btn btn-glass btn-sm">
                                            <i data-lucide="calendar"></i>
                                            Reschedule
                                        </button>
                                    </div>
                                    <?php elseif ($apt['status'] === 'confirmed'): ?>
                                    <div class="appointment-actions">
                                        <button class="btn btn-ghost btn-sm" onclick="cancelAppointment(<?php echo $apt['appointment_id']; ?>)">
                                            <i data-lucide="x"></i>
                                            Cancel
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Past Appointments -->
                <?php if (!empty($pastAppointments)): ?>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Past</h2>
                    <div class="appointments-list">
                        <?php foreach ($pastAppointments as $index => $apt): 
                            $landlord = $userModel->getById($apt['landlord_id']);
                            $landlordName = htmlspecialchars($landlord['first_name'] . ' ' . $landlord['last_name']);
                            $date = new DateTime($apt['appointment_date']);
                            $dateDisplay = $date->format('M j, Y');
                            $time = new DateTime($apt['appointment_time']);
                            $timeDisplay = $time->format('g:i A');
                            $image = !empty($apt['primary_image']) 
                                ? htmlspecialchars($apt['primary_image'])
                                : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400';
                        ?>
                        <div class="card card-glass appointment-card" style="opacity: 0.7; animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="appointment-card-content" style="display: flex; flex-direction: column; gap: 1.25rem; padding: 1.25rem;">
                                <div class="appointment-image">
                                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($apt['title']); ?>">
                                </div>
                                <div class="appointment-details">
                                    <div class="appointment-header">
                                        <div class="appointment-title">
                                            <h3><?php echo htmlspecialchars($apt['title']); ?></h3>
                                            <div class="appointment-landlord">
                                                <i data-lucide="user"></i>
                                                <span>with <?php echo $landlordName; ?></span>
                                            </div>
                                        </div>
                                        <span class="appointment-status <?php echo $apt['status']; ?>">
                                            <i data-lucide="check-circle"></i>
                                            <span><?php echo ucfirst($apt['status']); ?></span>
                                        </span>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="appointment-info-item">
                                            <i data-lucide="calendar"></i>
                                            <span><?php echo $dateDisplay; ?></span>
                                        </div>
                                        <div class="appointment-info-item">
                                            <i data-lucide="clock"></i>
                                            <span><?php echo $timeDisplay; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Empty State -->
                <?php if (empty($upcomingAppointments) && empty($pastAppointments)): ?>
                <div class="card card-glass" style="padding: 3rem; text-align: center;">
                    <i data-lucide="calendar-x" style="width: 4rem; height: 4rem; color: rgba(0,0,0,0.3); margin: 0 auto 1rem;"></i>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">No Appointments Yet</h3>
                    <p style="color: rgba(0,0,0,0.6);">Browse rooms and schedule viewings to get started!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    
    <script>
        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                // TODO: Implement cancel via AJAX
                alert('Cancel functionality coming soon!');
            }
        }
    </script>
</body>
</html>
