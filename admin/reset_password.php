<?php
require_once __DIR__ . '/password_reset_helpers.php';
password_reset_start_session();
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->getConnection();
if (!$db || !password_reset_verified_user($db)) {
    header('Location: forgot_password.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!password_reset_valid_csrf())
        $error = 'Security token mismatch. Please try again.';
    else {
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password))
            $error = 'Use at least 8 characters, including uppercase, lowercase, and a number.';
        elseif (!hash_equals($password, $confirm))
            $error = 'Passwords do not match.';
        else {
            $user = password_reset_verified_user($db);
            if (!$user) {
                header('Location: forgot_password.php');
                exit;
            }
            $stmt = $db->prepare('UPDATE users SET password_hash = ?, reset_otp = NULL, reset_otp_expires_at = NULL, reset_verified_at = NULL WHERE id = ?');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            password_reset_clear_session();
            session_regenerate_id(true);
            header('Location: login.php?password_reset=1');
            exit;
        }
    }
}
$csrf = password_reset_csrf_token();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Choose Password - MUKIZA FRED</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="auth-page">
    <main class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Choose a new password</h1>
            <p class="text-small text-muted">Use at least 8 characters with uppercase, lowercase, and a number.</p>
        </div><?php if ($error): ?>
            <div class="auth-alert auth-alert--danger" role="alert"><i data-lucide="alert-circle"
                    aria-hidden="true"></i><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php endif; ?>
        <form method="post"><input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group"><label class="form-label" for="password">New password</label><input
                    class="form-control" id="password" name="password" type="password" autocomplete="new-password"
                    minlength="8" required></div>
            <div class="form-group" style="margin-bottom:var(--space-6)"><label class="form-label"
                    for="confirm_password">Confirm new password</label><input class="form-control" id="confirm_password"
                    name="confirm_password" type="password" autocomplete="new-password" minlength="8" required></div>
            <button class="btn btn-primary" style="width:100%" type="submit">Reset password</button>
        </form>
    </main>
    <script>lucide.createIcons();</script>
</body>

</html>