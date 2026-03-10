<?php
declare(strict_types=1);

/**
 * Store a flash message for the next request.
 *
 * @param string $type Message category (e.g. success, error).
 * @param string $message Message text.
 */
function flash(string $type, string $message): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $_SESSION['flash'][$type][] = $message;
}

/**
 * Retrieve and remove a flash message list by type.
 *
 * @param string $type
 * @return array<string> Messages or empty array.
 */
function getFlash(string $type): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return [];
    }
    $messages = $_SESSION['flash'][$type] ?? [];
    unset($_SESSION['flash'][$type]);
    return $messages;
}

/**
 * Check whether flash messages exist for a type.
 *
 * @param string $type
 * @return bool
 */
function hasFlash(string $type): bool
{
    return session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['flash'][$type]);
}

/**
 * Remove flash messages for a given type.
 *
 * @param string $type
 */
function clearFlash(string $type): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    unset($_SESSION['flash'][$type]);
}
