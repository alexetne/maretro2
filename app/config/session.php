<?php
declare(strict_types=1);

// Secure session configuration and startup

function configureSession(array $config): void
{
    $cookieParams = [
        'lifetime' => $config['lifetime'] ?? 0,
        'path' => $config['path'] ?? '/',
        'domain' => $config['domain'] ?: '',
        'secure' => ($config['secure'] ?? false) || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => $config['httponly'] ?? true,
        'samesite' => $config['samesite'] ?? 'Lax',
    ];

    session_name($config['name'] ?? 'app_session');
    session_set_cookie_params($cookieParams);
}

function startSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function regenerateSessionId(bool $deleteOldSession = true): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id($deleteOldSession);
    }
}
