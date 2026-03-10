<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Mail sending configuration using PHPMailer

function sendMail(string $to, string $subject, string $body, bool $isHtml = true): bool
{
    global $config;

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host = $config['mail']['smtp_host'];
        $mailer->Port = $config['mail']['smtp_port'];
        $mailer->SMTPAuth = !empty($config['mail']['smtp_user']);
        $mailer->Username = $config['mail']['smtp_user'];
        $mailer->Password = $config['mail']['smtp_password'];
        $mailer->SMTPSecure = $config['mail']['smtp_secure'] ?? PHPMailer::ENCRYPTION_STARTTLS;

        $mailer->setFrom($config['mail']['from_address'], $config['mail']['from_name']);
        $mailer->addAddress($to);

        $mailer->isHTML($isHtml);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
        if (!$isHtml) {
            $mailer->AltBody = $body;
        }

        // Encourage TLS/SSL; allow opportunistic STARTTLS when available
        if ($mailer->SMTPSecure === 'tls' || $mailer->SMTPSecure === PHPMailer::ENCRYPTION_STARTTLS) {
            $mailer->SMTPAutoTLS = true;
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ];

        return $mailer->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $mailer->ErrorInfo);
        return false;
    }
}
