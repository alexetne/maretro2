<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/mail.php';

/**
 * Service wrapping mail sending.
 */
class MailService
{
    /**
     * Generic send.
     */
    public function send(string $to, string $subject, string $body): bool
    {
        return function_exists('sendMail') ? sendMail($to, $subject, $body) : false;
    }

    /**
     * Password reset email.
     */
    public function sendPasswordReset(string $to, string $resetLink): bool
    {
        $subject = 'Password Reset Request';
        $body = "Hello,\n\nUse the following link to reset your password:\n{$resetLink}\n\nIf you did not request this, please ignore.";
        return $this->send($to, $subject, nl2br($body));
    }

    /**
     * Payment notification email.
     *
     * @param string $to
     * @param array $paymentData
     */
    public function sendPaymentNotification(string $to, array $paymentData): bool
    {
        $amount = number_format((float)($paymentData['amount'] ?? 0), 2, '.', ' ');
        $retroId = $paymentData['retrocession_id'] ?? '';
        $subject = 'Payment Recorded';
        $body = "A payment of {$amount} € has been recorded for retrocession #{$retroId}.";
        return $this->send($to, $subject, nl2br($body));
    }
}
