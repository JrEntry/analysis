<?php
// File: auth/reset-password.php
// Password reset request page
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/my-account.php');
}

// Check if form is submitted
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = sanitize_input($_POST['email']);
    
    // Validate input
    if (empty($email) || !is_valid_email($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Check if email exists
        $sql = "SELECT user_id, full_name FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Generate reset token
            $token = generate_token();
            $token_hash = password_hash($token, PASSWORD_DEFAULT);
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database (using a hypothetical password_reset table)
            $reset_sql = "INSERT INTO password_reset (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
            
            try {
                // Create password_reset table if it doesn't exist
                $create_table_sql = "CREATE TABLE IF NOT EXISTS password_reset (
                    reset_id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token_hash VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
                )";
                $conn->query($create_table_sql);
                
                // Insert token
                $reset_stmt = $conn->prepare($reset_sql);
                $reset_stmt->bind_param("iss", $user['user_id'], $token_hash, $expiry);
                $reset_stmt->execute();
                $reset_stmt->close();
                
                // Note: No SMTP server available for the prototype
                // TODO: In production, integrate email sending functionality here
                // For this prototype, we'll just show the token on the page
                $reset_link = "http://{$_SERVER['HTTP_HOST']}/auth/reset-password-confirm.php?token=$token&email=$email";
                
                $success_message = 'Password reset functionality is limited in this prototype.';
                
                // For demo purposes only - in production, this would be sent via email
                $success_message .= '<hr>Demo only: <a href="' . $reset_link . '">Click here to reset your password</a>';
                
            } catch (Exception $e) {
                $error_message = 'Error generating password reset: ' . $e->getMessage();
            }
        } else {
            // Don't reveal if email exists or not for security reasons
            $success_message = 'If your email is registered, you will receive password reset instructions shortly.';
        }
        
        $stmt->close();
    }
}

// Include header
include '../includes/header.php';
?>

<div class="row justify-content-center align-items-center min-vh-100" style="background-image: url('/assets/images/solar-panels-bg.jpg'); background-size: cover; background-position: center;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow rounded-3">
            <div class="card-body p-5">
                <h2 class="text-center text-success mb-4">Reset Your Password</h2>
                <p class="text-center mb-4">Enter your email address below to receive password reset instructions</p>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php else: ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Send Reset Instructions</button>
                    </div>
                </form>
                
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <p class="mb-0"><a href="login.php" class="text-success">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>