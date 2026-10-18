<?php
session_start();
header('Content-Type: application/json');

require_once 'config/database.php';

// Validate CSRF
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Honeypot check
if (!empty($_POST['website'])) {
    // Bot detected
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name)) {
    $errors['name'] = 'Name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid email address is required.';
}
if (empty($message) || strlen($message) < 10) {
    $errors['message'] = 'Message must be at least 10 characters long.';
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

// Insert into Database
$db = (new Database())->getConnection();
if ($db) {
    try {
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, 'unread')");
        $stmt->execute([$name, $email, $subject, $message]);
        
        echo json_encode(['status' => 'success', 'message' => 'Message sent! I will reply to ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . ' soon.']);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving your message. Please email me directly.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please email me directly.']);
}

