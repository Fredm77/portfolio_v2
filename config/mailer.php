<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/bootstrap.php';

/** Shared Gmail SMTP setup for portfolio emails. */
function createMailer(): PHPMailer
{
    $host = config_env('MAIL_HOST');
    $port = (int) config_env('MAIL_PORT', '0');
    $username = config_env('MAIL_USERNAME');
    $password = config_env('MAIL_PASSWORD');
    $fromEmail = config_env('MAIL_FROM_ADDRESS');
    $fromName = config_env('MAIL_FROM_NAME');
    $encryption = strtolower((string) config_env('MAIL_ENCRYPTION'));

    if (!$host || !$port || !$username || !$password || !$fromEmail || !$fromName) {
        throw new RuntimeException('SMTP mail configuration is incomplete.');
    }

    $secure = match ($encryption) {
        'tls' => PHPMailer::ENCRYPTION_STARTTLS,
        'ssl' => PHPMailer::ENCRYPTION_SMTPS,
        default => throw new RuntimeException('SMTP encryption must be tls or ssl.'),
    };

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = $secure;
    $mail->Port = $port;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($fromEmail, $fromName);

    return $mail;
}
