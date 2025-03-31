<?php
// File: auth/register.php
// Registration page
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('../my-account.php');
}

// Check if form is submitted
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $full_name = sanitize_input($_POST['first_name'] . ' ' . $_POST['last_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $username = sanitize_input($_POST['email']); // Using email as username
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = sanitize_input($_POST['address']);
    $property_type = sanitize_input($_POST['property_type']);
    $property_size = sanitize_input($_POST['property_size']);
    
    // Communication preferences
    $email_updates = isset($_POST['email_updates']) ? 1 : 0;
    $promotional_emails = isset($_POST['promotional_emails']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
    $agree_terms = isset($_POST['agree_terms']) ? 1 : 0;
    
    // Validate inputs
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email) || !is_valid_email($email)) {
        $errors[] = 'A valid email address is required.';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Address and property type are now optional, so no validation required
    
    if (!$agree_terms) {
        $errors[] = 'You must agree to the Terms and Conditions.';
    }
    
    // Check if email already exists
    $check_sql = "SELECT user_id FROM user WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = 'Email address already registered.';
    }
    
    $check_stmt->close();
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert user data
            $user_sql = "INSERT INTO user (full_name, email, username, password_hash, phone_number) 
                          VALUES (?, ?, ?, ?, ?)";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("sssss", $full_name, $email, $username, $password_hash, $phone);
            $user_stmt->execute();
            
            // Get the new user ID
            $user_id = $conn->insert_id;
            
            // Insert customer profile data (if provided)
            if (!empty($address) || !empty($property_type) || isset($_POST['energy_source']) || isset($_POST['monthly_bill'])) {
                $energy_source = isset($_POST['energy_source']) ? sanitize_input($_POST['energy_source']) : null;
                $monthly_bill = isset($_POST['monthly_bill']) ? floatval($_POST['monthly_bill']) : null;
                
                $profile_sql = "INSERT INTO customer_profile (user_id, address, property_type, primary_energy_source, monthly_energy_bill) 
                                VALUES (?, ?, ?, ?, ?)";
                $profile_stmt = $conn->prepare($profile_sql);
                $profile_stmt->bind_param("isssd", $user_id, $address, $property_type, $energy_source, $monthly_bill);
                $profile_stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Set success message
            $success_message = 'Registration successful! You can now <a href="login.php">login</a> to your account.';
            
            // Close statements
            $user_stmt->close();
            $profile_stmt->close();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = 'Registration failed: ' . $e->getMessage();
        }
    } else {
        // Combine all errors
        $error_message = implode('<br>', $errors);
    }
}

// Include header
include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Registration Form -->
        <div class="col-lg-8">
            <div class="card shadow rounded-3">
                <div class="card-body p-4">
                    <h2 class="text-success mb-4">Create Your Account</h2>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php else: ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="registrationForm">
                        <h4 class="text-success mb-3">Personal Info</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        
                        <h4 class="text-success mb-3 mt-4">Account Security</h4>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <h4 class="text-success mb-3 mt-4">Property Information</h4>
                        <div class="mb-3">
                            <label for="address" class="form-label">Home Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="property_type" class="form-label">Property Type</label>
                                <select class="form-select" id="property_type" name="property_type" required>
                                    <option value="">Select Type</option>
                                    <option value="House">House</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Condo">Condo</option>
                                    <option value="Townhouse">Townhouse</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="property_size" class="form-label">Property Size (approx)</label>
                                <input type="text" class="form-control" id="property_size" name="property_size">
                            </div>
                        </div>
                        
                        <h4 class="text-success mb-3 mt-4">Communication Preferences</h4>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="email_updates" name="email_updates" checked>
                            <label class="form-check-label" for="email_updates">Send me email updates about my appointments and services</label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="promotional_emails" name="promotional_emails" checked>
                            <label class="form-check-label" for="promotional_emails">Send me promotional emails about new products and services</label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="sms_notifications" name="sms_notifications" checked>
                            <label class="form-check-label" for="sms_notifications">Send me SMS notifications (standard rates may apply)</label>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">I agree to the Terms and Conditions and Privacy Policy</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Create Account</button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Sign in</a></p>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Benefits Sidebar -->
        <div class="col-lg-4">
            <div class="card bg-success text-white mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-4">Account Benefits</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Track your consultations and installations</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Monitor your energy savings in real-time</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Calculate and track your carbon footprint</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Access personalized energy-saving recommendations</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Manage your green energy products and warranties</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Schedule maintenance and support appointments</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2"></i> Receive special offers and incentives</li>
                    </ul>
                </div>
            </div>
            
            <div class="card bg-light">
                <div class="card-body p-4">
                    <h3 class="text-success mb-3">Join Our Community</h3>
                    <p>Becoming a member gives you access to our community of like-minded individuals passionate about sustainable living.</p>
                    <p class="mb-0"><strong>1,000+ homeowners have already joined!</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>