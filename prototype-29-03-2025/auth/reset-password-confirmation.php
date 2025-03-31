<?php
// File: auth/reset-password-confirm.php
// Password reset confirmation page
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/my-account.php');
}

// Check if token and email are provided
if (!isset($_GET['token']) || !isset($_GET['email'])) {
    redirect('/auth/reset-password.php');
}

$token = $_GET['token'];
$email = sanitize_input($_GET['email']);
$valid_token = false;
$user_id = null;
$error_message = '';
$success_message = '';

// Validate token
$sql = "SELECT pr.reset_id, pr.user_id, pr.expires_at, pr.token_hash 
        FROM password_reset pr 
        JOIN user u ON pr.user_id = u.user_id 
        WHERE u.email = ? 
        ORDER BY pr.created_at DESC 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $reset = $result->fetch_assoc();
    
    // Check if token is expired
    if (strtotime($reset['expires_at']) > time()) {
        // Verify token
        if (password_verify($token, $reset['token_hash'])) {
            $valid_token = true;
            $user_id = $reset['user_id'];
        }
    }
}

$stmt->close();

// Process password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if (empty($password)) {
        $error_message = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        // Hash new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password
        $update_sql = "UPDATE user SET password_hash = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $password_hash, $user_id);
        
        if ($update_stmt->execute()) {
            // Delete all reset tokens for this user
            $delete_sql = "DELETE FROM password_reset WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            $success_message = 'Your password has been reset successfully. You can now <a href="login.php">login</a> with your new password.';
        } else {
            $error_message = 'Failed to reset password. Please try again.';
        }
        
        $update_stmt->close();
    }
}

// Include header
include '../includes/header.php';
?>

<div class="row justify-content-center align-items-center min-vh-100" style="background-image: url('/assets/images/solar-panels-bg.jpg'); background-size: cover; background-position: center;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow rounded-3">
            <div class="card-body p-5">
                <h2 class="text-center text-success mb-4">Create New Password</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif ($valid_token): ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?token=' . urlencode($token) . '&email=' . urlencode($email)); ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Reset Password</button>
                    </div>
                </form>
                
                <?php else: ?>
                
                <div class="alert alert-danger">
                    The password reset link is invalid or has expired. Please <a href="reset-password.php">request a new password reset</a>.
                </div>
                
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