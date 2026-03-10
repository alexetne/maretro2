<?php
declare(strict_types=1);

/**
 * Centralized notifications using MailService.
 */
class NotificationService
{
    private MailService $mail;

    public function __construct(MailService $mail)
    {
        $this->mail = $mail;
    }

    public function notifyPaymentRecorded(array $paymentData): bool
    {
        if (empty($paymentData['email'])) {
            return false;
        }
        $subject = 'Payment Recorded';
        $body = 'A payment has been recorded for your retrocession: ' . json_encode($paymentData);
        return $this->mail->send($paymentData['email'], $subject, nl2br($body));
    }

    public function notifyMonthlyStatement(array $statementData): bool
    {
        if (empty($statementData['email'])) {
            return false;
        }
        $subject = 'Monthly Statement Available';
        $body = 'Your monthly statement is ready.';
        return $this->mail->send($statementData['email'], $subject, nl2br($body));
    }

    public function notifyPasswordReset(string $email, string $resetLink): bool
    {
        return $this->mail->sendPasswordReset($email, $resetLink);
    }
}
