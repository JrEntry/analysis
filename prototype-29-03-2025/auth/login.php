<?php
// File: auth/login.php
// Login page
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('auth/my-account.php');
}

// Check if form is submitted
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // No need to sanitize password

    // Validating inputs
    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        // Check if user exists
        $sql = "SELECT user_id, username, password_hash, full_name FROM user WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];

                // Update last login timestamp
                $update_sql = "UPDATE user SET last_login = NOW() WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $user['user_id']);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect to dashboard
                redirect('/my-account.php');
            } else {
                $error_message = 'Invalid username or password.';
            }
        } else {
            $error_message = 'Invalid username or password.';
        }

        $stmt->close();
    }
}

// Include header
include '../includes/header.php';
?>

<div class="row justify-content-center align-items-center min-vh-100"
    style="background-image: url('/assets/images/solar-panels-bg.jpg'); background-size: cover; background-position: center;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow rounded-3">
            <div class="card-body p-5">
                <h1 class="text-center text-success mb-4">Welcome Back</h1>
                <p class="text-center mb-4">Enter your credentials to access your account</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control form-control-lg login-page-input" id="username"
                            name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control form-control-lg login-page-input" id="password"
                            name="password" required>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Log In</button>
                    </div>
                </form>

                <div class="text-center mt-4 bg-success-subtle p-3 rounded">
                    <p class="mb-2">Forgot your password? <a href="reset-password.php" class="text-warning">Reset
                            Here</a></p>
                    <p class="mb-0">Already have an account? <a href="register.php" class="text-warning">Create One
                            Here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>