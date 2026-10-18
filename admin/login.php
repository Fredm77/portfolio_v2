<?php
// admin/login.php
require_once __DIR__ . '/password_reset_helpers.php';
password_reset_start_session();

require_once '../config/database.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

// Generate CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$db = (new Database())->getConnection();

// Check if setup is needed (no users)
$userCount = 0;
if ($db) {
    $countStmt = $db->query("SELECT COUNT(*) FROM users");
    $userCount = $countStmt->fetchColumn();
}

if ($userCount == 0) {
    header("Location: setup.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    // Validate CSRF
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = "Security token mismatch. Please try again.";
    } elseif (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        if ($db) {
            $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Prevent session fixation
                session_regenerate_id(true);

                // A normal sign-in must not inherit any pending reset authorization.
                password_reset_clear_session();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                $_SESSION['created_at'] = time();

                header("Location: index.php");
                exit;
            } else {
                // Do not reveal if email exists
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Database connection failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MUKIZA FRED</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-base);
            margin: 0;
        }

        .login-card {
            background: var(--bg-surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: var(--space-8);
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-lg);
        }

        .login-header {
            text-align: center;
            margin-bottom: var(--space-6);
        }

        .login-title {
            font-family: var(--font-heading);
            font-size: var(--type-h3);
            color: var(--text-primary);
            margin-bottom: var(--space-2);
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: var(--type-small);
            margin-bottom: var(--space-4);
            display: flex;
            align-items: flex-start;
            gap: 8px;
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
    <div class="login-card">
        <div class="login-header">
            <div class="login-title">FRED<span>.</span> Admin</div>
            <div class="text-muted text-small">Sign in to manage your portfolio</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i data-lucide="alert-circle" style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px;"></i>
                <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logged_out'])): ?>
            <div class="alert alert-success">
                <i data-lucide="check" style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px;"></i>
                <span>You have been logged out successfully.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['password_reset'])): ?>
            <div class="alert alert-success" role="status">
                <i data-lucide="check" style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px;"></i>
                <span>Your password has been updated successfully. You can now sign in.</span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <a href="forgot_password.php" class="link-underline text-small" style="align-self: flex-end; margin-top: var(--space-2);">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Sign In</button>
        </form>

        <div style="text-align: center; margin-top: var(--space-6);">
            <a href="../index.php" class="link-underline text-small" style="color: var(--text-muted);">&larr; Back to
                website</a>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>

</html>
