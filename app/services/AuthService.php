<?php
declare(strict_types=1);

use RuntimeException;
use PDOException;

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/session.php';

/**
 * Authentication service handling login/register/logout.
 */
class AuthService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Attempt login with email/password.
     *
     * @param string $email
     * @param string $password
     * @return array Authenticated user data.
     *
     * @throws RuntimeException on failure
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new RuntimeException('Invalid credentials.');
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            throw new RuntimeException('User is inactive.');
        }

        $hash = $user['password'] ?? '';
        if (!$this->verifyPassword($password, $hash)) {
            throw new RuntimeException('Invalid credentials.');
        }

        loginUser($user);
        return $user;
    }

    /**
     * Register a new user and return its id.
     *
     * @param array $data
     * @return int
     *
     * @throws RuntimeException on failure
     */
    public function register(array $data): int
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new RuntimeException('Email and password are required.');
        }

        if ($this->users->findByEmail($data['email'])) {
            throw new RuntimeException('Email already in use.');
        }

        $data['password'] = $this->hashPassword($data['password']);
        $created = $this->users->create($data);
        if (!$created || !isset($created['id'])) {
            throw new RuntimeException('Unable to register user.');
        }

        return (int)$created['id'];
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        logoutUser();
    }

    /**
     * Hash a password.
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify a password against a hash.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
