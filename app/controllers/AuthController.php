<?php
declare(strict_types=1);

/**
 * Handles authentication flow.
 */
class AuthController
{
    private AuthService $auth;
    private AuditService $audit;

    public function __construct(AuthService $auth, AuditService $audit)
    {
        $this->auth = $auth;
        $this->audit = $audit;
    }

    public function showLogin(): array
    {
        return ['title' => 'Login'];
    }

    public function login(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!validateEmail($email) || !validateRequired($password)) {
            flash('error', 'Email and password are required.');
            redirectBack();
        }

        try {
            $user = $this->auth->login($email, $password);
            $this->audit->logLogin((int)$user['id'], $_SERVER['REMOTE_ADDR'] ?? '');
            flash('success', 'Welcome back!');
            redirect('/dashboard');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirectBack();
        }
    }

    public function showRegister(): array
    {
        return ['title' => 'Register'];
    }

    public function register(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';
        $name = sanitizeString($_POST['name'] ?? '');

        if (!validateEmail($email) || !validateRequired($password) || $password !== $confirm) {
            flash('error', 'Please provide a valid email and matching passwords.');
            redirectBack();
        }

        try {
            $userId = $this->auth->register(['email' => $email, 'password' => $password, 'name' => $name]);
            $this->audit->logCreate($userId, 'user', $userId, ['email' => $email]);
            flash('success', 'Account created. Please log in.');
            redirect('/login');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirectBack();
        }
    }

    public function logout(): void
    {
        $current = user();
        if ($current) {
            $this->audit->logLogin((int)$current['id'], $_SERVER['REMOTE_ADDR'] ?? '');
        }
        $this->auth->logout();
        flash('success', 'Logged out.');
        redirect('/login');
    }
}
