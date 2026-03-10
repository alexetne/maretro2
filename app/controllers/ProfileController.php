<?php
declare(strict_types=1);

/**
 * Profile management for authenticated user.
 */
class ProfileController
{
    private UserRepository $users;
    private AuthService $auth;
    private AuditService $audit;

    public function __construct(UserRepository $users, AuthService $auth, AuditService $audit)
    {
        $this->users = $users;
        $this->auth = $auth;
        $this->audit = $audit;
    }

    public function show(): array
    {
        requireAuth();
        return ['user' => user()];
    }

    public function update(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $current = user();
        $id = (int)$current['id'];

        $data = [
            'email' => sanitizeEmail($_POST['email'] ?? ''),
            'name' => sanitizeString($_POST['name'] ?? ''),
            'phone' => sanitizeString($_POST['phone'] ?? ''),
        ];

        if (!validateEmail($data['email'])) {
            flash('error', 'Invalid email.');
            redirectBack();
        }

        $old = $this->users->findById($id) ?? [];
        if ($this->users->update($id, $data)) {
            $this->audit->logUpdate($id, 'user_profile', $id, $old, $data);
            flash('success', 'Profile updated.');
            redirect('/profile');
        }
        flash('error', 'Update failed.');
        redirectBack();
    }

    public function changePassword(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $current = user();
        $id = (int)$current['id'];
        $oldPassword = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirmation'] ?? '';

        if ($new === '' || $new !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirectBack();
        }

        $dbUser = $this->users->findById($id);
        if (!$dbUser || !$this->auth->verifyPassword($oldPassword, $dbUser['password'] ?? '')) {
            flash('error', 'Current password incorrect.');
            redirectBack();
        }

        $hash = $this->auth->hashPassword($new);
        if ($this->users->update($id, ['password' => $hash])) {
            $this->audit->logUpdate($id, 'user_password', $id, [], ['password_changed' => true]);
            flash('success', 'Password updated.');
            redirect('/profile');
        }
        flash('error', 'Unable to change password.');
        redirectBack();
    }
}
