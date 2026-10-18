<?php
// admin/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Session security - prevent session fixation
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
} else if (time() - $_SESSION['created_at'] > 1800) {
    // Session regeneration every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function check_csrf() {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }
}

