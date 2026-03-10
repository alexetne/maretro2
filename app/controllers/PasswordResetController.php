<?php
declare(strict_types=1);

/**
 * Password reset flow controller.
 */
class PasswordResetController
{
    private MailService $mail;
    private AuthService $auth;
    private UserRepository $users;

    // For demo purposes, store tokens in session; replace with persistent store in production.
    public function __construct(MailService $mail, AuthService $auth, UserRepository $users)
    {
        $this->mail = $mail;
        $this->auth = $auth;
        $this->users = $users;
    }

    public function showForgotForm(): array
    {
        return ['title' => 'Forgot Password'];
    }

    public function sendResetLink(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $email = sanitizeEmail($_POST['email'] ?? '');
        if (!validateEmail($email)) {
            flash('error', 'Invalid email.');
            redirectBack();
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            flash('error', 'No account found for this email.');
            redirectBack();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['password_reset'][$token] = ['user_id' => $user['id'], 'expires' => time() + 3600];
        $link = (env('APP_URL', '') ?: '') . '/reset-password?token=' . urlencode($token);
        $this->mail->sendPasswordReset($email, $link);
        flash('success', 'Reset link sent if the email exists.');
        redirect('/login');
    }

    public function showResetForm(string $token): array
    {
        return ['token' => $token];
    }

    public function resetPassword(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $token = $_POST['token'] ?? '';
        $entry = $_SESSION['password_reset'][$token] ?? null;
        if (!$entry || ($entry['expires'] ?? 0) < time()) {
            flash('error', 'Reset token is invalid or expired.');
            redirect('/forgot-password');
        }

        $new = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';
        if ($new === '' || $new !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirectBack();
        }

        $hash = $this->auth->hashPassword($new);
        $this->users->update((int)$entry['user_id'], ['password' => $hash]);
        unset($_SESSION['password_reset'][$token]);
        flash('success', 'Password updated. Please log in.');
        redirect('/login');
    }
}
