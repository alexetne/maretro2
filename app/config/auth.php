<?php
declare(strict_types=1);

// Authentication helper functions

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isAuthenticated(): bool
{
    return user() !== null;
}

function loginUser(array $user): void
{
    // Do not store sensitive info (password hash) in session
    unset($user['password'], $user['password_hash']);
    $_SESSION['user'] = $user;
    regenerateSessionId();
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        http_response_code(401);
        exit('Unauthorized');
    }
}
