<?php

const RESET_OTP_TTL_SECONDS = 600;
const RESET_VERIFICATION_TTL_SECONDS = 900;
const RESET_RESEND_COOLDOWN_SECONDS = 60;

function password_reset_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), 'httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function password_reset_csrf_token(): string
{
    if (empty($_SESSION['csrf_token']))
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function password_reset_valid_csrf(): bool
{
    return !empty($_POST['csrf_token']) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token']);
}

function password_reset_clear_session(): void
{
    unset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_requested_email'], $_SESSION['password_reset_requested_at'], $_SESSION['password_reset_last_sent_at'], $_SESSION['password_reset_verified_user_id'], $_SESSION['password_reset_verified_at'], $_SESSION['password_reset_verified_expires_at']);
}

function password_reset_send_otp(PDO $db, string $email): string
{
    // Check whether the email address exists in the users table
    $stmt = $db->prepare(
        'SELECT id, username, email FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Email is not registered
    if (!$user) {
        unset($_SESSION['password_reset_user_id']);

        return 'not_found';
    }

    // Generate a secure 6-digit OTP
    $otp = (string) random_int(100000, 999999);

    // OTP expires after 10 minutes
    $expiresAt = date(
        'Y-m-d H:i:s',
        time() + RESET_OTP_TTL_SECONDS
    );

    // Hash OTP before storing it in the database
    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);

    // Save OTP and expiration time
    $update = $db->prepare(
        'UPDATE users
         SET reset_otp = ?,
             reset_otp_expires_at = ?,
             reset_verified_at = NULL
         WHERE id = ?'
    );

    $update->execute([
        $hashedOtp,
        $expiresAt,
        $user['id']
    ]);

    // Store reset information in the session
    $_SESSION['password_reset_user_id'] = (int) $user['id'];
    $_SESSION['password_reset_requested_email'] = $user['email'];
    $_SESSION['password_reset_requested_at'] = time();
    $_SESSION['password_reset_last_sent_at'] = time();

    try {
        // Load the existing PHPMailer configuration
        require_once __DIR__ . '/../config/mailer.php';

        // Create the configured PHPMailer instance
        $mail = createMailer();

        $mail->addAddress(
            $user['email'],
            $user['username']
        );

        $mail->isHTML(true);

        $mail->Subject = 'Your FRED. Admin Password Reset Code';

        $safeName = htmlspecialchars(
            $user['username'],
            ENT_QUOTES,
            'UTF-8'
        );

        $mail->Body =
            '<div style="font-family:Arial,sans-serif;color:#111721;line-height:1.6">
                <h2 style="margin:0 0 16px">
                    FRED<span style="color:#C2924E">.</span> Admin
                </h2>

                <p>Hello ' . $safeName . ',</p>

                <p>
                    A password reset request was received for your administrator account.
                    Please use the verification code below to continue:
                </p>

                <p style="font-size:30px;letter-spacing:8px;font-weight:700;color:#111721">
                    ' . $otp . '
                </p>

                <p>
                    This verification code will expire in 10 minutes.
                    Do not share this code with anyone.
                </p>

                <p>
                    If you did not request a password reset, you can safely ignore this email.
                </p>
            </div>';

        $mail->AltBody =
            "FRED. Admin Password Reset Code: {$otp}\n\n" .
            "This verification code will expire in 10 minutes. " .
            "Do not share this code with anyone.\n\n" .
            "If you did not request a password reset, you can safely ignore this email.";

        $mail->send();

        // OTP was successfully sent
        return 'sent';

    } catch (Throwable $exception) {

        // Log the technical error instead of showing it to the user
        error_log(
            'Password reset email error: ' .
            $exception->getMessage()
        );

        // Remove the invalid OTP if email delivery failed
        $clear = $db->prepare(
            'UPDATE users
             SET reset_otp = NULL,
                 reset_otp_expires_at = NULL,
                 reset_verified_at = NULL
             WHERE id = ?'
        );

        $clear->execute([$user['id']]);

        // Remove reset session information
        unset(
            $_SESSION['password_reset_user_id'],
            $_SESSION['password_reset_requested_email'],
            $_SESSION['password_reset_requested_at'],
            $_SESSION['password_reset_last_sent_at']
        );

        return 'mail_failed';
    }

}




function password_reset_verified_user(PDO $db): ?array
{
    $userId = $_SESSION['password_reset_verified_user_id'] ?? null;
    $expiresAt = $_SESSION['password_reset_verified_expires_at'] ?? 0;
    if (!is_int($userId) || time() > $expiresAt) {
        password_reset_clear_session();
        return null;
    }
    $stmt = $db->prepare('SELECT id, reset_verified_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return ($user && !empty($user['reset_verified_at'])) ? $user : null;
}
