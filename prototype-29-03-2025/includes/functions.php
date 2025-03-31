<?php
// File: includes/functions.php
// Helper functions for the authentication system

/**
 * Sanitize user input to prevent XSS attacks
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect to a URL
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Generate a random token for password reset
 * @param int $length Length of the token
 * @return string Random token
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Display error message
 * @param string $message Error message to display
 */
function display_error($message) {
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Display success message
 * @param string $message Success message to display
 */
function display_success($message) {
    echo '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool True if email is valid, false otherwise
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>