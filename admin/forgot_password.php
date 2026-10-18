<?php
require_once __DIR__ . '/password_reset_helpers.php';
password_reset_start_session();
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->getConnection();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if (!password_reset_valid_csrf())
        $error = 'Security token mismatch. Please try again.';
    else {
        $email = trim((string) ($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error = 'Enter a valid email address.';
        elseif (!$db)
            $error = 'Unable to process your request right now. Please try again later.';
        else {
            password_reset_clear_session();
            try {
                $result = password_reset_send_otp($db, $email);
            } catch (Throwable $exception) {
                error_log('Password reset request error: ' . $exception->getMessage());
                $error = 'Something went wrong. Please try again.';
                $result = null;
            }

            if ($result === 'not_found') {
                $error = 'No account found for this email address.';
            } elseif ($result === 'sent') {
                $_SESSION['password_reset_message'] =
                    'Verification code sent. Please check your email.';
                header('Location: verify_otp.php');
                exit;
            } elseif ($result === 'mail_failed') {
                $error = 'Unable to send the verification code. Please try again.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
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
    <title>Forgot Password - MUKIZA FRED</title>
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
            <h1 class="auth-card__title">Reset your password</h1>
            <p class="text-small text-muted">Enter your account email and we’ll send a verification code.</p>
        </div><?php if ($error): ?>
            <div class="auth-alert auth-alert--danger" role="alert"><i data-lucide="alert-circle"
                    aria-hidden="true"></i><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php endif; ?>
        <form method="post" novalidate><input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group" style="margin-bottom:var(--space-6)"><label class="form-label" for="email">Email
                    address</label><input class="form-control" id="email" name="email" type="email" autocomplete="email"
                    required autofocus></div><button class="btn btn-primary" style="width:100%" type="submit">Send
                verification code</button>
        </form><a class="auth-card__back link-underline text-small" href="login.php">← Back to sign in</a>
    </main>
    <script>lucide.createIcons();</script>
</body>

</html>
