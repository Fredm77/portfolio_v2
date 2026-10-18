<?php
// admin/setup.php
session_start();
require_once '../config/database.php';

$db = (new Database())->getConnection();

// Check if users already exist
$userCount = 0;
if ($db) {
    $countStmt = $db->query("SELECT COUNT(*) FROM users");
    $userCount = $countStmt->fetchColumn();
}

// If users exist, this script should not run.
if ($userCount > 0) {
    die("Setup already completed. Please delete setup.php for security reasons, then go to <a href='login.php'>login.php</a>.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $hashed])) {
            $success = "Administrator created successfully! Please delete setup.php immediately, then log in.";
        } else {
            $error = "Failed to create user.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Setup - MUKIZA FRED</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-base);
            margin: 0;
        }

        .setup-card {
            background: var(--bg-surface);
            border: 1px solid var(--accent);
            border-radius: var(--radius-lg);
            padding: var(--space-8);
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-accent);
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: var(--type-small);
            margin-bottom: var(--space-4);
        }

        .alert-danger {
            background: var(--danger-bg);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .alert-success {
            background: var(--support-bg);
            border: 1px solid var(--support);
            color: var(--support);
        }
    </style>
</head>

<body>
    <div class="setup-card">
        <h2 style="margin-top: 0; color: var(--accent);">Initial Setup</h2>
        <p class="text-small text-muted" style="margin-bottom: var(--space-6);">Create the primary administrator
            account.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" class="btn btn-primary">Go to Login</a>
            </div>
        <?php else: ?>

            <form action="setup.php" method="POST">
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-6);">
                    <label for="password" class="form-label">Password (min 8 chars)</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Create
                    Administrator</button>
            </form>

        <?php endif; ?>
    </div>
</body>

</html>