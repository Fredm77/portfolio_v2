<?php
require_once __DIR__ . '/password_reset_helpers.php';
password_reset_start_session();
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->getConnection();
$error = '';
$notice = $_SESSION['password_reset_message'] ?? '';
unset($_SESSION['password_reset_message']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!password_reset_valid_csrf())
        $error = 'Security token mismatch. Please try again.';
    elseif (!$db)
        $error = 'Unable to process your request right now. Please try again later.';
    elseif (($_POST['action'] ?? '') === 'resend') {
        $lastSent = (int) ($_SESSION['password_reset_last_sent_at'] ?? 0);
        if ($lastSent && time() - $lastSent < RESET_RESEND_COOLDOWN_SECONDS)
            $error = 'Please wait a minute before requesting another code.';
        else {
            $email = (string) ($_SESSION['password_reset_requested_email'] ?? '');
            if ($email === '') {
                $error = 'Something went wrong. Please try again.';
            } else {
                $result = password_reset_send_otp($db, $email);
                if ($result === 'sent') {
                    $notice = 'Verification code sent. Please check your email.';
                } elseif ($result === 'mail_failed') {
                    $error = 'Unable to send the verification code. Please try again.';
                } else {
                    $error = 'Something went wrong. Please try again.';
                }
            }
        }
    } else {
        $otp = preg_replace('/\D/', '', (string) ($_POST['otp'] ?? ''));
        $userId = $_SESSION['password_reset_user_id'] ?? null;
        if (strlen($otp) !== 6 || !is_int($userId))
            $error = 'The code is invalid or has expired. Request a new code and try again.';
        else {
            $stmt = $db->prepare('SELECT id, reset_otp, reset_otp_expires_at FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $expired = !$user || empty($user['reset_otp_expires_at']) || strtotime($user['reset_otp_expires_at']) < time();
            if ($expired)
                $error = 'This code has expired. Request a new code and try again.';
            elseif (empty($user['reset_otp']) || !password_verify($otp, $user['reset_otp']))
                $error = 'The verification code is incorrect.';
            else {
                $db->prepare('UPDATE users SET reset_verified_at = NOW() WHERE id = ?')->execute([$user['id']]);
                session_regenerate_id(true);
                $_SESSION['password_reset_verified_user_id'] = (int) $user['id'];
                $_SESSION['password_reset_verified_at'] = time();
                $_SESSION['password_reset_verified_expires_at'] = time() + RESET_VERIFICATION_TTL_SECONDS;
                header('Location: reset_password.php');
                exit;
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
    <title>Verify Code - MUKIZA FRED</title>
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
            <h1 class="auth-card__title">Verify your code</h1>
            <p class="text-small text-muted">Enter the six-digit code sent to your email.</p>
        </div><?php if ($notice): ?>
            <div class="auth-alert auth-alert--success" role="status"><i data-lucide="mail-check"
                    aria-hidden="true"></i><span><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php endif; ?><?php if ($error): ?>
            <div class="auth-alert auth-alert--danger" role="alert"><i data-lucide="alert-circle"
                    aria-hidden="true"></i><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php endif; ?>
        <form method="post"><input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action"
                value="verify">
            <div class="form-group" style="margin-bottom:var(--space-6)"><label class="form-label"
                    for="otp">Verification code</label><input class="form-control" id="otp" name="otp"
                    inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required
                    autofocus></div><button class="btn btn-primary" style="width:100%" type="submit">Verify
                code</button>
        </form>
        <form method="post" style="text-align:center;margin-top:var(--space-4)"><input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action"
                value="resend"><button class="btn btn-ghost text-small" type="submit">Resend verification code</button>
        </form><a class="auth-card__back link-underline text-small" href="forgot_password.php">Use a different email</a>
    </main>
    <script>lucide.createIcons();</script>
</body>

</html>
