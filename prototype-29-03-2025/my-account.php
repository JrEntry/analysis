<?php
// File: my-account.php
// User dashboard for Rolsa Technologies

// Include configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!is_logged_in()) {
    redirect('/auth/login.php');
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// Placeholder data (in a real app, this would come from the database)
$energy_saved = "{User's}"; // kWh
$money_saved = 999; // £
$carbon_reduction = 33.3; // %
$car_miles_saved = 43.3; // %

// Get upcoming bookings
$upcoming_bookings = [];
if ($conn) {
    $bookings_sql = "SELECT c.consultation_id, c.preferred_datetime, c.service_interest, c.status, 
                     i.installation_id, i.scheduled_date, i.status as installation_status
                     FROM consultation c
                     LEFT JOIN installation i ON c.consultation_id = i.consultation_id
                     WHERE c.user_id = ? AND (c.status != 'completed' OR i.status != 'completed')
                     ORDER BY COALESCE(i.scheduled_date, c.preferred_datetime) ASC
                     LIMIT 5";
    
    $stmt = $conn->prepare($bookings_sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $upcoming_bookings[] = $row;
        }
        
        $stmt->close();
    }
}

// If no bookings from DB, use placeholder data
if (empty($upcoming_bookings)) {
    $upcoming_bookings = [
        [
            'type' => 'Consultation',
            'description' => 'Solar Panel System',
            'date' => '2025-03-15',
            'time_start' => '10:00',
            'time_end' => '12:00',
            'status' => 'confirmed'
        ],
        [
            'type' => 'Installation',
            'description' => 'Solar Panel Installation',
            'date' => '2025-03-28',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'status' => 'pending'
        ]
    ];
}

// Include header with a simplified layout (no main container)
include 'includes/header.php';
?>

<div class="container-fluid py-4 bg-brand-light">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow rounded h-100">
                <div class="card-body text-center pt-4 pb-2">
                    <!-- User Profile Avatar & Info -->
                    <div class="mb-4">
                        <div class="rounded-circle bg-brand-green text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px; height:80px; font-size:2rem;">
                            <?php echo strtoupper(substr($full_name ?? 'U', 0, 1)); ?>
                        </div>
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($username); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($username); ?></p>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="text-start">
                        <a href="my-account.php" class="d-block p-3 text-brand-green text-decoration-none fw-bold bg-brand-light-green rounded mb-2">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        
                        <a href="my-bookings.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-calendar-check me-2"></i> My Bookings
                        </a>
                        
                        <a href="energy-tracking.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-lightning-charge me-2"></i> Energy Tracking
                        </a>
                        
                        <a href="carbon-footprint.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-globe-americas me-2"></i> Carbon Footprint
                        </a>
                        
                        <a href="account-settings.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-gear me-2"></i> Account Settings
                        </a>
                        
                        <hr class="my-3">
                        
                        <a href="support.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-question-circle me-2"></i> Support
                        </a>
                        
                        <a href="auth/logout.php" class="d-block p-3 text-brand-dark text-decoration-none fw-medium mb-2">
                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Energy Stats Cards -->
            <div class="row mb-4">
                <!-- Energy Savings Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow" style="background-color: var(--yellow);">
                        <div class="card-body p-4">
                            <h5 class="text-brand-dark mb-4">Energy Savings</h5>
                            <h2 class="display-4 fw-bold text-brand-dark"><?php echo $energy_saved; ?> kWh</h2>
                        </div>
                    </div>
                </div>
                
                <!-- Money Saved Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow bg-brand-green">
                        <div class="card-body p-4">
                            <h5 class="text-white mb-4">Money Saved</h5>
                            <h2 class="display-4 fw-bold text-white">£ <?php echo $money_saved; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Bookings Section -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Upcoming Bookings</h5>
                    <hr class="mb-4">
                    
                    <?php if (empty($upcoming_bookings)): ?>
                        <p>You don't have any upcoming bookings.</p>
                    <?php else: ?>
                        <?php foreach ($upcoming_bookings as $booking): ?>
                            <div class="row mb-4 p-3 bg-light rounded">
                                <!-- Date -->
                                <div class="col-md-2 text-center mb-3 mb-md-0">
                                    <?php 
                                        $date = isset($booking['date']) ? $booking['date'] : (isset($booking['preferred_datetime']) ? date('Y-m-d', strtotime($booking['preferred_datetime'])) : '');
                                        $day = date('d', strtotime($date));
                                        $month = date('M', strtotime($date));
                                    ?>
                                    <div class="bg-white rounded shadow-sm p-2">
                                        <h3 class="text-brand-green mb-0"><?php echo $day; ?></h3>
                                        <p class="text-uppercase mb-0 text-brand-dark"><?php echo $month; ?></p>
                                    </div>
                                </div>
                                
                                <!-- Booking Details -->
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <h6 class="fw-bold"><?php echo htmlspecialchars($booking['type'] ?? ($booking['installation_id'] ? 'Installation' : 'Consultation')); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($booking['description'] ?? $booking['service_interest'] ?? 'Service Appointment'); ?></p>
                                    <?php 
                                        $time_start = isset($booking['time_start']) ? $booking['time_start'] : (isset($booking['preferred_datetime']) ? date('H:i', strtotime($booking['preferred_datetime'])) : '');
                                        $time_end = isset($booking['time_end']) ? $booking['time_end'] : '';
                                        $time_display = $time_start;
                                        if ($time_end) $time_display .= ' - ' . $time_end . ' PM';
                                        else $time_display .= ' AM';
                                    ?>
                                    <p class="text-muted mb-0"><?php echo $time_display; ?></p>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
                                    <?php 
                                        $status = $booking['status'] ?? 'pending';
                                        $badge_class = 'bg-secondary';
                                        if (strtolower($status) == 'confirmed') $badge_class = 'bg-brand-yellow text-brand-dark';
                                        if (strtolower($status) == 'pending') $badge_class = 'bg-light text-dark border';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2">
                                        <?php echo ucfirst(htmlspecialchars($status)); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Energy Usage & Generation Chart -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Energy Usage & Generation</h5>
                    <hr class="mb-4">
                    
                    <!-- Simple chart placeholder - would be replaced with actual chart library in production -->
                    <div class="bg-light p-4 rounded">
                        <svg viewBox="0 0 500 200" width="100%" height="200">
                            <!-- X and Y axes -->
                            <line x1="50" y1="150" x2="450" y2="150" stroke="#ccc" stroke-width="2" />
                            <line x1="50" y1="20" x2="50" y2="150" stroke="#ccc" stroke-width="2" />
                            
                            <!-- Energy Generation Line -->
                            <polyline 
                                points="50,130 100,120 150,110 200,100 250,80 300,70 350,60 400,50 450,40" 
                                fill="none" 
                                stroke="var(--green)" 
                                stroke-width="3" 
                                stroke-linejoin="round" 
                                stroke-linecap="round" 
                            />
                            
                            <!-- Time periods (months) -->
                            <text x="50" y="170" font-size="10" text-anchor="middle">Jan</text>
                            <text x="150" y="170" font-size="10" text-anchor="middle">Mar</text>
                            <text x="250" y="170" font-size="10" text-anchor="middle">May</text>
                            <text x="350" y="170" font-size="10" text-anchor="middle">Jul</text>
                            <text x="450" y="170" font-size="10" text-anchor="middle">Sep</text>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Environmental Impact Section -->
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Your Environmental Impact</h5>
                    <hr class="mb-4">
                    
                    <div class="row">
                        <!-- Carbon Reduction Goal -->
                        <div class="col-md-6 mb-4">
                            <div class="bg-light p-4 rounded">
                                <h6 class="fw-bold mb-3">Carbon Reduction Goal</h6>
                                
                                <!-- Progress bar -->
                                <div class="mb-3">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $carbon_reduction; ?>%;" aria-valuenow="<?php echo $carbon_reduction; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="fw-bold mb-0"><?php echo $carbon_reduction; ?>%</h4>
                                    <span class="text-muted small">of annual target achieved</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Car Miles Saved -->
                        <div class="col-md-6 mb-4">
                            <div class="bg-light p-4 rounded">
                                <h6 class="fw-bold mb-3">Car Miles Saved</h6>
                                
                                <!-- Progress bar -->
                                <div class="mb-3">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $car_miles_saved; ?>%; background-color: var(--yellow);" aria-valuenow="<?php echo $car_miles_saved; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="fw-bold mb-0"><?php echo $car_miles_saved; ?>%</h4>
                                    <span class="text-muted small">of annual target achieved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>