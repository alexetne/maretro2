<?php
declare(strict_types=1);

/**
 * Admin user management controller.
 */
class UserController
{
    private UserRepository $users;
    private AuditService $audit;

    public function __construct(UserRepository $users, AuditService $audit)
    {
        $this->users = $users;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }
        return ['users' => $this->users->findAll()];
    }

    public function show(int $id): array
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }
        return ['user' => $this->users->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $email = sanitizeEmail($_POST['email'] ?? '');
        $name = sanitizeString($_POST['name'] ?? '');
        $role = sanitizeString($_POST['role'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!validateEmail($email) || !validateRequired($password)) {
            flash('error', 'Email and password are required.');
            redirectBack();
        }

        $data = ['email' => $email, 'name' => $name, 'role' => $role, 'password' => password_hash($password, PASSWORD_DEFAULT)];
        $created = $this->users->create($data);
        if ($created && isset($created['id'])) {
            $this->audit->logCreate((int)user()['id'], 'user', (int)$created['id'], $created);
            flash('success', 'User created.');
            redirect('/admin/users');
        }
        flash('error', 'Unable to create user.');
        redirectBack();
    }

    public function update(int $id): void
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $email = sanitizeEmail($_POST['email'] ?? '');
        $name = sanitizeString($_POST['name'] ?? '');
        $role = sanitizeString($_POST['role'] ?? '');
        $data = ['email' => $email, 'name' => $name, 'role' => $role];
        $old = $this->users->findById($id) ?? [];
        if ($this->users->update($id, $data)) {
            $this->audit->logUpdate((int)user()['id'], 'user', $id, $old, $data);
            flash('success', 'User updated.');
            redirect('/admin/users');
        }
        flash('error', 'Unable to update user.');
        redirectBack();
    }

    public function delete(int $id): void
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $old = $this->users->findById($id) ?? [];
        if ($this->users->delete($id)) {
            $this->audit->logDelete((int)user()['id'], 'user', $id, $old);
            flash('success', 'User deleted.');
            redirect('/admin/users');
        }
        flash('error', 'Unable to delete user.');
        redirectBack();
    }
}
